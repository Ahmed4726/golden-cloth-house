<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Customer;
use Illuminate\Validation\ValidationException;

class CouponService
{
    public function findByCode(string $code): Coupon
    {
        $coupon = Coupon::query()
            ->where('code', mb_strtoupper(trim($code)))
            ->first();

        if (! $coupon) {
            throw ValidationException::withMessages([
                'coupon_code' => 'This coupon code is not valid.',
            ]);
        }

        return $coupon;
    }

    public function discountFor(Coupon $coupon, float $subtotal, ?Customer $customer = null): float
    {
        if (! $coupon->active) {
            $this->invalid('This coupon is not active.');
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            $this->invalid('This coupon is not available yet.');
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            $this->invalid('This coupon has expired.');
        }

        if ($subtotal < (float) $coupon->minimum_order_amount) {
            $this->invalid('Your order does not meet the minimum amount for this coupon.');
        }

        if ($coupon->max_redemptions !== null && $coupon->usage_count >= $coupon->max_redemptions) {
            $this->invalid('This coupon has reached its redemption limit.');
        }

        if ($customer && $coupon->per_customer_limit !== null) {
            $redemptions = $customer->orders()->where('coupon_id', $coupon->id)->count();

            if ($redemptions >= $coupon->per_customer_limit) {
                $this->invalid('You have already used this coupon the maximum number of times.');
            }
        }

        $discount = $coupon->discount_type === 'percentage'
            ? $subtotal * (float) $coupon->discount_value / 100
            : min($subtotal, (float) $coupon->discount_value);

        return round(min($subtotal, $discount), 2);
    }

    private function invalid(string $message): never
    {
        throw ValidationException::withMessages(['coupon_code' => $message]);
    }
}
