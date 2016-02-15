<?php

namespace Mmieluch\LaravelServeCustomIni;

use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Support\ServiceProvider;

class LaravelServeCustomIniProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the "serve" command.
     *
     * @return void
     */
    public function register()
    {
        $this->app['events']->listen(ArtisanStarting::class, function ($event) {
            $this->app->singleton('command.serve', function ($app) {
                return new ServeCommand();
            });

            $this->commands('command.serve');
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
          'command.serve',
        ];
    }

}
