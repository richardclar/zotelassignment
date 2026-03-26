<?php

declare(strict_types=1);

namespace App\Providers;

use App\Interfaces\DiscountServiceInterface;
use App\Interfaces\PricingServiceInterface;
use App\Interfaces\SearchServiceInterface;
use App\Services\DiscountService;
use App\Services\PricingService;
use App\Services\SearchService;
use Illuminate\Support\ServiceProvider;

class HotelSearchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DiscountServiceInterface::class, DiscountService::class);
        $this->app->singleton(PricingServiceInterface::class, PricingService::class);
        $this->app->singleton(SearchServiceInterface::class, SearchService::class);
    }

    public function boot(): void
    {
    }
}
