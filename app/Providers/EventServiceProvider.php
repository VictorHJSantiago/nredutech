<?php

namespace App\Providers;

use App\Models\Usuario; 
use App\Observers\UsuarioObserver; 
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     *
     * @var array
     */
    protected $observers = [
        Usuario::class => [UsuarioObserver::class], 
    ];

}