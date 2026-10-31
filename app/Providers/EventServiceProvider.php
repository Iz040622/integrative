<?php
// Chin Jia Jun

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\PaymentSuccessful;
use App\Listeners\CreateBookingAfterPayment;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    // Event listener mappings for the application
    protected $listen = [
        PaymentSuccessful::class => [
            CreateBookingAfterPayment::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
