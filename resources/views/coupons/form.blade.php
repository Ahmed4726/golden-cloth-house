<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="coupon-code">Coupon code</label>
        <input class="form-control text-uppercase" id="coupon-code" name="code" maxlength="40" pattern="[A-Za-z0-9_-]+" value="{{ old('code', $coupon->code ?? '') }}" required>
        @error('code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold" for="discount-type">Discount type</label>
        <select class="form-select" id="discount-type" name="discount_type" required>
            <option value="percentage" @selected(old('discount_type', $coupon->discount_type ?? 'percentage') === 'percentage')>Percentage</option>
            <option value="fixed" @selected(old('discount_type', $coupon->discount_type ?? '') === 'fixed')>Fixed amount</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold" for="discount-value">Discount value</label>
        <input class="form-control" id="discount-value" name="discount_value" type="number" min="0.01" step="0.01" value="{{ old('discount_value', $coupon->discount_value ?? '') }}" required>
        @error('discount_value')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold" for="minimum-order">Minimum order amount</label>
        <input class="form-control" id="minimum-order" name="minimum_order_amount" type="number" min="0" step="0.01" value="{{ old('minimum_order_amount', $coupon->minimum_order_amount ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold" for="max-redemptions">Total redemption limit</label>
        <input class="form-control" id="max-redemptions" name="max_redemptions" type="number" min="1" value="{{ old('max_redemptions', $coupon->max_redemptions ?? '') }}" placeholder="Unlimited">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold" for="per-customer-limit">Limit per customer</label>
        <input class="form-control" id="per-customer-limit" name="per_customer_limit" type="number" min="1" value="{{ old('per_customer_limit', $coupon->per_customer_limit ?? '') }}" placeholder="Unlimited">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="starts-at">Starts at</label>
        <input class="form-control" id="starts-at" name="starts_at" type="datetime-local" value="{{ old('starts_at', isset($coupon) ? $coupon->starts_at?->format('Y-m-d\TH:i') : '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="expires-at">Expires at</label>
        <input class="form-control" id="expires-at" name="expires_at" type="datetime-local" value="{{ old('expires_at', isset($coupon) ? $coupon->expires_at?->format('Y-m-d\TH:i') : '') }}">
        @error('expires_at')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <div class="form-check">
            <input type="hidden" name="active" value="0">
            <input class="form-check-input" id="coupon-active" name="active" type="checkbox" value="1" @checked(old('active', $coupon->active ?? true))>
            <label class="form-check-label" for="coupon-active">Active</label>
        </div>
    </div>
</div>
