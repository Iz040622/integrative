<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Observers\BookingObserver;
use App\Observers\BookingRoomObserver;
use App\Services\User\UserActionFactory;
use App\Services\User\UserActionFactoryImpl;
use App\Services\User\AdminActionFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserActionFactory::class, UserActionFactoryImpl::class);

        $this->app->singleton(AdminActionFactory::class, function ($app) {
            return new AdminActionFactory($app);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        Booking::observe(BookingObserver::class);
        BookingRoom::observe(BookingRoomObserver::class);

        // View composers
        View::composer('*', function ($view) {
            if ($view->getName() === 'payment') {
                Session::put('cart_expires_at', now()->addMinutes(15));
            }

            if (Session::has('cart_expires_at')) {
                $view->with('cartExpiresAt', Session::get('cart_expires_at'));
            }
        });
    }
}
