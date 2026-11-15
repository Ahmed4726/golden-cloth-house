<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:40'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $coupons = Coupon::query()
            ->withCount('orders')
            ->when($filters['q'] ?? null, fn ($query, $code) => $query->where('code', 'like', '%'.mb_strtoupper(trim($code)).'%'))
            ->when(isset($filters['status']), fn ($query) => $query->where('active', $filters['status'] === 'active'))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('coupons.index', compact('coupons'));
    }

    public function create(): View
    {
        return view('coupons.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        Coupon::create([
            ...$data,
            'code' => mb_strtoupper(trim($data['code'])),
            'active' => $request->boolean('active', true),
        ]);

        return redirect()->route('coupons.index')->with('success', 'Coupon created.');
    }

    public function edit(Coupon $coupon): View
    {
        return view('coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $data = $this->validatedData($request, $coupon);
        $coupon->update([
            ...$data,
            'code' => mb_strtoupper(trim($data['code'])),
            'active' => $request->boolean('active'),
        ]);

        return redirect()->route('coupons.index')->with('success', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return back()->with('success', 'Coupon deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?Coupon $coupon = null): array
    {
        $request->merge([
            'code' => mb_strtoupper(trim((string) $request->input('code', ''))),
        ]);

        return $request->validate([
            'code' => ['required', 'string', 'max:40', 'regex:/^[A-Za-z0-9_-]+$/', Rule::unique('coupons', 'code')->ignore($coupon?->id)],
            'discount_type' => ['required', 'in:percentage,fixed'],
            'discount_value' => [
                'required',
                'numeric',
                'gt:0',
                Rule::when(
                    $request->input('discount_type') === 'percentage',
                    ['max:100'],
                    ['max:99999999.99'],
                ),
            ],
            'minimum_order_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'max_redemptions' => ['nullable', 'integer', 'min:1', 'max:1000000000'],
            'per_customer_limit' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => [
                'nullable',
                'date',
                Rule::when($request->filled('starts_at'), ['after_or_equal:starts_at']),
            ],
        ]);
    }
}
