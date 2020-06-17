<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Repository\CommentRepositoryInterface', 'App\Repository\CommentRepositoryEloquent'
        );
        $this->app->bind(
            'App\Repository\PostRepositoryInterface', 'App\Repository\PostRepositoryEloquent'
        );
        $this->app->bind(
            'App\Repository\UserRepositoryInterface', 'App\Repository\UserRepositoryEloquent'
        );
        $this->app->bind(
            'App\Repository\ApiSettingsRepositoryInterface', 'App\Repository\ApiSettingsRepositoryEloquent'
        );
        $this->app->bind(
            'App\Repository\HighlightCommentRepositoryInterface', 'App\Repository\HighlightCommentRepositoryEloquent'
        );
        $this->app->bind(
            'App\Repository\TransactionRepositoryInterface', 'App\Repository\TransactionRepositoryEloquent'
        );
        $this->app->bind(
            'App\Repository\NotificationRepositoryInterface', 'App\Repository\NotificationRepositoryEloquent'
        );
    }
}
