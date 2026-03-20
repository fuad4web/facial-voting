<?php

namespace App\Providers;

use App\Repositories\Contracts\{CategoryRepositoryInterface, UserRepositoryInterface, VoteRepositoryInterface, };
use App\Repositories\{CategoryRepository, UserRepository, VoteRepository, };
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(VoteRepositoryInterface::class, VoteRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
