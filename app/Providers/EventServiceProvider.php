<?php

namespace App\Providers;

// Make sure these 'use' statements are correct for your project structure
use App\Models\Event;          // Default location for models
use App\Observers\EventObserver;  // Default location for observers

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider; // Make sure it extends the base class
use Illuminate\Support\Facades\Event as EventFacade; // Often not needed when using $observers array

class EventServiceProvider extends ServiceProvider // Ensure it extends ServiceProvider
{
    /**
     * The model observers for your application.
     *
     * ADD THIS PROPERTY:
     *
     * @var array
     */
    protected $observers = [
        Event::class => [EventObserver::class],
        // If you have other models and observers, add them here too:
        // \App\Models\AnotherModel::class => [\App\Observers\AnotherObserver::class],
    ];

    /**
     * Register any events for your application.
     * (This method often comes with the default provider, you might not need 'register')
     * If using the $observers array, you usually don't need code in boot() for observer registration.
     *
     * @return void
     */
    public function boot(): void
    {
         parent::boot(); // Call parent boot method if it exists in your default file. Usually good practice.
         // You generally DON'T need to manually register observers here
         // if you are using the $observers array above.
         // EventFacade::observe(Event::class, EventObserver::class); // <-- Don't do this if using $observers array
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     * (This method also often comes with the default provider)
     *
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return false; // Or true, depending on your preference/setup
    }


    /**
     * Register services. (This might not be in the default EventServiceProvider)
     */
    // public function register(): void // If you don't need register(), you can remove it.
    // {
    //     //
    // }
}