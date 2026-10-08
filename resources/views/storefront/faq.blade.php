@extends('storefront.layout')

@php
    $title = 'FAQs | '.config('store.name');
    $description = 'Frequently asked questions about ordering, Cash on Delivery, delivery and unstitched wear from '.config('store.name').'.';
@endphp

@section('content')
    <section class="py-5 text-center">
        <div class="container">
            <div class="small text-uppercase text-secondary fw-bold">Customer care</div>
            <h1 class="display-5 fw-normal">Frequently Asked Questions</h1>
            <p class="text-secondary mb-0">Everything you need to know before placing your order.</p>
        </div>
    </section>

    <div class="container pb-5">
        <div class="accordion mx-auto col-lg-9" id="faqAccordion">
            @php
                $faqs = [
                    ['Do you offer Cash on Delivery?', 'Yes. Cash on Delivery is the primary payment method for the store and is available across Pakistan, subject to the checkout eligibility shown for your order.'],
                    ['Do you sell stitched or unstitched clothing?', 'Our store is focused on women and men unstitched wear. Product titles and descriptions clearly identify the pieces included in each fabric set.'],
                    ['What is included in a 2-piece or 3-piece suit?', 'The exact pieces and fabric lengths are listed on each product detail page. Please review the Product Details section before ordering.'],
                    ['How can I track my order?', 'Use the Track Order page and enter your order number and phone number used during checkout.'],
                    ['How much is delivery?', 'Delivery charges are calculated at checkout. Orders above the configured free-delivery threshold qualify for free delivery.'],
                    ['Can I change or cancel my order?', 'Contact us as soon as possible after placing the order. Once an order has entered dispatch, changes may no longer be possible.'],
                    ['Why can product colours look slightly different?', 'Photography, lighting and individual screen settings can affect how colours appear. Small variations are therefore possible.'],
                ];
            @endphp

            @foreach ($faqs as $index => [$question, $answer])
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqHeading{{ $index }}">
                        <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="faqCollapse{{ $index }}">{{ $question }}</button>
                    </h2>
                    <div id="faqCollapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary">{{ $answer }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
