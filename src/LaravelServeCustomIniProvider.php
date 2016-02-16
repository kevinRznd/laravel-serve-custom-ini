<?php

namespace Mmieluch\LaravelServeCustomIni;

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
        $this->app->alias('command.serve', ServeCommand::class);
        $this->app->singleton('command.serve', function() {
            return new ServeCommand;
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
