<?php

namespace App\Services;

use App\Models\Customer;

class CustomerResolver
{
    /**
     * @param  array<string, mixed>  $orderDetails
     */
    public function resolve(array $orderDetails, bool $lockForUpdate = false): ?Customer
    {
        $phone = preg_replace('/\D+/', '', trim((string) ($orderDetails['customer_phone'] ?? ''))) ?? '';
        $email = mb_strtolower(trim((string) ($orderDetails['customer_email'] ?? '')));

        if ($phone === '' && $email === '') {
            return null;
        }

        $identityKey = $phone !== '' ? 'phone:'.$phone : 'email:'.$email;
        $customerQuery = Customer::query()->where('identity_key', $identityKey);

        if ($lockForUpdate) {
            $customerQuery->lockForUpdate();
        }

        $customer = $customerQuery->first() ?? new Customer(['identity_key' => $identityKey]);
        $customer->fill([
            'customer_name' => filled($orderDetails['customer_name'] ?? null)
                ? trim((string) $orderDetails['customer_name'])
                : $customer->customer_name,
            'customer_phone' => filled($orderDetails['customer_phone'] ?? null)
                ? trim((string) $orderDetails['customer_phone'])
                : $customer->customer_phone,
            'phone_normalized' => $phone !== '' ? $phone : $customer->phone_normalized,
            'customer_email' => filled($orderDetails['customer_email'] ?? null)
                ? trim((string) $orderDetails['customer_email'])
                : $customer->customer_email,
            'email_normalized' => $email !== '' ? $email : $customer->email_normalized,
        ])->save();

        return $customer;
    }
}
