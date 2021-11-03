<?php

namespace App\Providers;

use App\Models\ContactForm;
use App\Models\StreamUserAction;
use App\Models\UserFriendRequest;
use App\Models\UserStream;
use App\Observers\StreamUserActionObserver;
use App\Observers\UserFriendRequestObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Observers\BlameableObserver;
use App\Observers\UserStreamObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        //ContactForm::observe(BlameableObserver::class);
        UserStream::observe(UserStreamObserver::class);
        UserFriendRequest::observe(UserFriendRequestObserver::class);
        StreamUserAction::observe(StreamUserActionObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Way\Generators\GeneratorsServiceProvider::class);
            $this->app->register(\Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider::class);
        }
    }
}
