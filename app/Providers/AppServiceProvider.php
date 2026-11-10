<?php

namespace App\Providers;

use App\Models\StoreSetting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer([
            'storefront.layout',
            'storefront.contact',
            'storefront.home',
            'storefront.product',
            'storefront.shop',
            'storefront.cart',
        ], function ($view): void {
            $settings = StoreSetting::query()->find(StoreSetting::SINGLETON_ID);
            $view->with(
                'whatsappNumber',
                $settings ? $settings->whatsapp_number : config('store.whatsapp')
            );
        });
    }
}
