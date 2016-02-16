# laravel-serve-custom-ini

Replaces Laravel's "serve" command with an equivalent allowing the developer to specify his custom `php.ini` file. Comes in handy when serving locally and in need of quickly toggling Xdebug.

[![Latest Stable Version](https://poser.pugx.org/mmieluch/laravel-serve-custom-ini/v/stable)](https://packagist.org/packages/mmieluch/laravel-serve-custom-ini)
[![License](https://poser.pugx.org/mmieluch/laravel-serve-custom-ini/license)](https://packagist.org/packages/mmieluch/laravel-serve-custom-ini)

## Installation

1. Install via Composer:

  ```bash
  composer require --dev mmieluch/laravel-serve-custom-ini
  ```

2. Update your project configuration and add new service provider to the list:

    ```php
    'providers' => [
        ...
        
        /*
         * 3rd Party Service Providers
         */
        Mmieluch\LaravelServeCustomIni\LaravelServeCustomIniProvider::class,
    ],
    ```

3. Create a new `php.ini` file in your project base directory.

4. You can start serving! If you placed your `php.ini` file in a project root, all you need to do is to issue this command:

    ```bash
    php artisan serve --ini
    ```
    
    However, if you specified your `php.ini` file elsewhere, you can specify that path too:
    
    ```bash
    php artisan serve --ini --ini-path=/path/to/your/php.ini
    ```
    
    If you don't pass the `--ini` parameter, the command will fall back to the original behaviour.

## What is it for?

I personally use this command to enable Xdebug support. I set up my local environment, so that `php` is an alias for for `/usr/local/bin/php -dzend_extension=/usr/local/opt/php56-xdebug/xdebug.so`. I keep all of my Xdebug configuration in its own INI file, but I commented out the inclusion of the extension itself:

```ini
[xdebug]
;zend_extension="/usr/local/opt/php56-xdebug/xdebug.so"

xdebug.default_enable=1
xdebug.idekey=PHPSTORM
xdebug.remote_autostart=1
xdebug.remote_enable=1
xdebug.remote_connect_back=1
xdebug.remote_port=9000
xdebug.remote_handler=dbgp
```

Instead of having it on all the time, I can toggle Xdebug as I please.

But the original `artisan serve` command was ignoring my environment PHP alias and was going straight for the `PHP_BINARY`. That's why I created this package, so the command would have to respect my settings no matter what.

## You did the above and Laravel freezes on requests?

Yea, I've been there too :) Make sure you don't have Xdebug enabled when you run initial `php artisan serve --ini`. If you do have it enabled then your first Xdebug process will block your default port (usually 9000) and connections from served application will never reach your debugging client.

## Bug? Issues?

Feel free to file a bug report or submit a PR.
