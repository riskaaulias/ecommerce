<?php
namespace App\Providers;

use Illuminate\Auth\Events\Login;
use App\Listeners\MergeCartListener;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
protected $listen = [
    Login::class => [
        MergeCartListener::class,
    ],
];
}