<?php

namespace Ikeraslt\Finvalda;


use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class FinvaldaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $source = realpath($raw = __DIR__.'/../config/finvalda.php') ?: $raw;

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('finvalda.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('finvalda');
        }

        $this->mergeConfigFrom($source, 'finvalda');
    }

    public function register()
    {
        $this->app->singleton('finvalda', function (Container $app) {
            $url = $app->config->get('finvalda.url', '');
            $dataUrl = $app->config->get('finvalda.data_url', '');
            $user = $app->config->get('finvalda.user', '');
            $password = $app->config->get('finvalda.password', '');

            return new Finvalda($url, $dataUrl, $user, $password);
        });

        $this->app->alias('finvalda', Finvalda::class);
    }
}