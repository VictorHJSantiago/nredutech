<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Faker\Generator as FakerGenerator;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\View; 
use Illuminate\Support\Facades\Auth;  
use App\Models\Notificacao;           

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FakerGenerator::class, function () {
            $faker = FakerFactory::create();
            $faker->addProvider(new \Faker\Provider\pt_BR\Person($faker));
            $faker->addProvider(new \Faker\Provider\pt_BR\Address($faker));
            $faker->addProvider(new \Faker\Provider\pt_BR\PhoneNumber($faker));
            $faker->addProvider(new \Faker\Provider\pt_BR\Company($faker));
            return $faker;
        });
    }

    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $unreadCount = 0;
            if (Auth::check()) {
                $unreadCount = Notificacao::where('id_usuario', Auth::id())
                                          ->where('status_mensagem', 'enviada')
                                          ->count();
            }
            $view->with('unreadNotificationsCount', $unreadCount);
        });
    }
}