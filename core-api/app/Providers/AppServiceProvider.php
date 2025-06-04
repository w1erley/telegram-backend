<?php

namespace App\Providers;

use App\Events\Auth\Manual\UserLoggedIn;
use App\Events\Auth\Manual\UserRegistered;
use App\Events\Auth\Sessions\SessionTerminated;
use App\Listeners\Auth\Manual\NotifyUserLogin;
use App\Listeners\Auth\Manual\SendVerificationEmail;
use App\Listeners\Auth\Sessions\NotifySessionTermination;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            UserRegistered::class,
            SendVerificationEmail::class
        );

        Event::listen(
            SessionTerminated::class,
            NotifySessionTermination::class
        );

        Event::listen(
            UserLoggedIn::class,
            NotifyUserLogin::class
        );
    }
}
