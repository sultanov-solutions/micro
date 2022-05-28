<?php
namespace SultanovSolutions\Micro\Base;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use SultanovSolutions\Micro\Micro;

class Index
{
    public static function loadIndex(){
        /*
        |--------------------------------------------------------------------------
        | Check If The Application Is Under Maintenance
        |--------------------------------------------------------------------------
        |
        | If the application is in maintenance / demo mode via the "down" command
        | we will load this file so that any pre-rendered content can be shown
        | instead of starting the framework, which could cause an exception.
        |
        */
        if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
            require $maintenance;
        }

        /*
        |--------------------------------------------------------------------------
        | Run The Application
        |--------------------------------------------------------------------------
        |
        | Once we have the application, we can handle the incoming request using
        | the application's HTTP kernel. Then, we will send the response back
        | to this client's browser, allowing them to enjoy our application.
        |
        */
        $kernel = Micro::app()->make(Kernel::class);

        $response = $kernel->handle(
            $request = Request::capture()
        )->send();

        $kernel->terminate($request, $response);
    }

    public static function loadArtisan(){
        /*
        |--------------------------------------------------------------------------
        | Run The Artisan Application
        |--------------------------------------------------------------------------
        |
        | When we run the console application, the current CLI command will be
        | executed in this console and the response sent back to a terminal
        | or another output device for the developers. Here goes nothing!
        |
        */
        $kernel = Micro::app()->make(\Illuminate\Contracts\Console\Kernel::class);

        $status = $kernel->handle(
            $input = new \Symfony\Component\Console\Input\ArgvInput,
            new \Symfony\Component\Console\Output\ConsoleOutput
        );

        /*
        |--------------------------------------------------------------------------
        | Shutdown The Application
        |--------------------------------------------------------------------------
        |
        | Once Artisan has finished running, we will fire off the shutdown events
        | so that any final work may be done by the application before we shut
        | down the process. This is the last thing to happen to the request.
        |
        */

        $kernel->terminate($input, $status);

        exit($status);
    }
}
