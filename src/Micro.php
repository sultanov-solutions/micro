<?php

namespace SultanovSolutions\Micro;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Http\Kernel as ContractsHttpKernel;
use App\Http\Kernel as HttpKernel;
use Illuminate\Contracts\Console\Kernel as ContractsConsoleKernel;
use App\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Debug\ExceptionHandler as ContractsDebugExceptionHandler;
use App\Exceptions\Handler;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\Output;

require __DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'constants.php';

class Micro
{
    private static ?ConsoleOutput $console;

    public static function app($base_path = null): Application
    {
        if (!$base_path)
            $base_path = LARAVEL_ROOT_DIR;

        $app = new Application($base_path);

        /*
        |--------------------------------------------------------------------------
        | Bind Important Interfaces
        |--------------------------------------------------------------------------
        |
        | Next, we need to bind some important interfaces into the container so
        | we will be able to resolve them when needed. The kernels serve the
        | incoming requests to this application from both the web and CLI.
        |
        */
        $app->singleton(
            ContractsHttpKernel::class,
            HttpKernel::class
        );

        $app->singleton(
            ContractsConsoleKernel::class,
            ConsoleKernel::class
        );

        $app->singleton(
            ContractsDebugExceptionHandler::class,
            Handler::class
        );

        /*
        |--------------------------------------------------------------------------
        | Return The Application
        |--------------------------------------------------------------------------
        |
        | This script returns the application instance. The instance is given to
        | the calling script so we can separate the building of the instances
        | from the actual running of the application and sending responses.
        |
        */

        $app->setBasePath(LARAVEL_ROOT_DIR);
        $app->useLangPath(LARAVEL_ROOT_DIR . DIRECTORY_SEPARATOR . 'lang');
        $app->useEnvironmentPath(MICRO_ROOT_DIR);
        $app->loadEnvironmentFrom('.env');
        $app->useAppPath(LARAVEL_ROOT_DIR . DIRECTORY_SEPARATOR . 'app');
        $app->useDatabasePath(LARAVEL_ROOT_DIR . DIRECTORY_SEPARATOR . 'database');
        $app->useStoragePath(LARAVEL_ROOT_DIR . DIRECTORY_SEPARATOR . 'storage');

        $app->instance('path', LARAVEL_ROOT_DIR . DIRECTORY_SEPARATOR . 'app');
        $app->instance('path.base', MICRO_ROOT_DIR);
        $app->instance('path.config', LARAVEL_ROOT_DIR . DIRECTORY_SEPARATOR . 'config');
        $app->instance('path.database', LARAVEL_ROOT_DIR . DIRECTORY_SEPARATOR . 'database');
        $app->instance('path.resources', LARAVEL_ROOT_DIR . DIRECTORY_SEPARATOR . 'resources');
        $app->instance('path.bootstrap', LARAVEL_ROOT_DIR . DIRECTORY_SEPARATOR . 'bootstrap');
        $app->instance('path.storage', LARAVEL_ROOT_DIR . DIRECTORY_SEPARATOR . 'storage');
        $app->instance('path.public', MICRO_ROOT_DIR . DIRECTORY_SEPARATOR . 'public');


        if (is_dir(MICRO_SRC_DIR . DIRECTORY_SEPARATOR . 'app')) {
            $app->useAppPath(LARAVEL_ROOT_DIR . DIRECTORY_SEPARATOR . 'app');
            $app->instance('path', MICRO_SRC_DIR . DIRECTORY_SEPARATOR . 'app');
        }

        if (is_dir(MICRO_SRC_DIR . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'lang'))
            $app->useLangPath(MICRO_SRC_DIR . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'lang');

        if (file_exists(MICRO_ROOT_DIR . DIRECTORY_SEPARATOR . '.env')) {
            $app->useEnvironmentPath(MICRO_ROOT_DIR);
            $app->loadEnvironmentFrom('.env');
        }

        if (is_dir(MICRO_SRC_DIR . DIRECTORY_SEPARATOR . 'Database')) {
            $app->useDatabasePath(MICRO_SRC_DIR . DIRECTORY_SEPARATOR . 'Database');
            $app->instance('path.database', MICRO_SRC_DIR . DIRECTORY_SEPARATOR . 'Database');
        }

        if (is_dir(MICRO_SRC_DIR . DIRECTORY_SEPARATOR . 'Resources'))
            $app->instance('path.resources', MICRO_SRC_DIR . DIRECTORY_SEPARATOR . 'Resources');

        if (is_dir(MICRO_SRC_DIR . DIRECTORY_SEPARATOR . 'Bootstrap'))
            $app->instance('path.bootstrap', MICRO_SRC_DIR . DIRECTORY_SEPARATOR . 'Bootstrap');

        if (is_dir(MICRO_SRC_DIR . DIRECTORY_SEPARATOR . 'storage')) {
            $app->useStoragePath(MICRO_SRC_DIR . DIRECTORY_SEPARATOR . 'Storage');
            $app->instance('path.storage', MICRO_SRC_DIR . DIRECTORY_SEPARATOR . 'Storage');
        }

        if (is_dir(MICRO_SRC_DIR . DIRECTORY_SEPARATOR . 'Public'))
            $app->instance('path.storage', MICRO_SRC_DIR . DIRECTORY_SEPARATOR . 'Public');


        $app->make('path');
        $app->make('path.base');
        $app->make('path.config');
        $app->make('path.database');
        $app->make('path.resources');
        $app->make('path.bootstrap');
        $app->make('path.storage');

        $app->booted(function () use ($app) {
            self::unbindRoute();
            self::loadProviders($app);
        });

        return $app;
    }

    public static function loadProviders(Application $app)
    {
        $manifest = $app->make(PackageManifest::class);
        $manifest->vendorPath = MICRO_ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor';

        $composerFile = json_decode(file_get_contents(MICRO_ROOT_DIR.DIRECTORY_SEPARATOR.'composer.json'), 1);
        if (isset($composerFile['extra'],$composerFile['extra']['laravel'],$composerFile['extra']['laravel']['providers'])){
            $providers = $composerFile['extra']['laravel']['providers'];
            foreach ($providers as $provider)
                $app->register($provider);
        }
        $manifest->build();
    }

    public static function unbindRoute()
    {
        Route::any('/', fn() => abort(404));
        Route::any('/api/user', fn() => abort(404));
        Route::any('/sanctum/csrf-cookie', fn() => abort(404));
    }

    public static function copyStubs()
    {
        $STUBS_DIR = false;

        self::$console = new ConsoleOutput();

        if ( is_dir(implode(DIRECTORY_SEPARATOR, [MICRO_VENDOR_DIR,'Stubs']) ) )
        {
            $STUBS_DIR = implode(DIRECTORY_SEPARATOR, [MICRO_VENDOR_DIR,'Stubs']);
        }


        if ( !file_exists(MICRO_ROOT_DIR . DIRECTORY_SEPARATOR . 'index.php') )
        {
            if ($STUBS_DIR){
                copy($STUBS_DIR . DIRECTORY_SEPARATOR . 'index.php', MICRO_ROOT_DIR . DIRECTORY_SEPARATOR . 'index.php');
                self::$console->writeln('<info>Index file copied</info>');
            }
        }
        else
        {
            self::$console->writeln('<error>Index file is exist</error>');
        }


        if ( !file_exists(MICRO_ROOT_DIR . DIRECTORY_SEPARATOR . 'artisan') ){
            if ($STUBS_DIR){
                copy($STUBS_DIR . DIRECTORY_SEPARATOR . 'artisan', MICRO_ROOT_DIR . DIRECTORY_SEPARATOR . 'artisan');
                self::$console->writeln('<info>Artisan file copied</info>');
            }
        }
        else
        {
            self::$console->writeln('<error>Artisan file is exist</error>');
        }

        if ( !file_exists(MICRO_ROOT_DIR . DIRECTORY_SEPARATOR . '.env') ){
            if ($STUBS_DIR){
                copy(MICRO_ROOT_DIR . DIRECTORY_SEPARATOR . '.env.example', MICRO_ROOT_DIR . DIRECTORY_SEPARATOR . '.env');
                self::$console->writeln('<info>.env file copied</info>');
            }
        }
        else
        {
            self::$console->writeln('<error>.env file is exist</error>');
        }

        exit();
    }

    public static function setPackageName(){

        self::$console = new ConsoleOutput();

        if (!isset($_SERVER['argv'][2]) || empty(trim($_SERVER['argv'][2]))){
            self::$console->writeln('<error>Package name not set</error>');
            exit();
        }

        $package_name = trim($_SERVER['argv'][2]);

        self::updateComposerNames($package_name);
        self::updateFiles($package_name);
    }

    /**
     * Update package name & namespace helpers
     */

    private static function updateComposerNames($package_name){
        if (!$package_name){
            self::$console->writeln('<error>Package name not set</error>');
            exit();
        }

        $composer = file_get_contents(MICRO_ROOT_DIR . DS . 'composer.json');

        $composer = Str::of($composer)
            ->replace('package_name', Str::of($package_name)->slug()->lower()->toString())
            ->replace('%SERVICE_NAME%', Str::of($package_name)->camel()->ucfirst()->toString())
            ->toString();

        file_put_contents(MICRO_ROOT_DIR . DS . 'composer.json', $composer);

        self::$console->writeln('<info>composer.json has updated</info>');
    }

    private static function updateFiles($package_name){
        if (!$package_name){
            self::$console->writeln('<error>Package name not set</error>');
            exit();
        }
        self::scanDir(MICRO_SRC_DIR, $package_name);
    }

    private static function scanDir($startDir, $package_name){
        $dirs = Collection::make(scandir($startDir))->filter(fn($d) => !in_array($d, ['.', '..']));

        foreach ($dirs as $dir)
        {
            if ( is_file($startDir . DS . $dir) )
            {
                self::updateFileNameSpace($startDir . DS . $dir, $package_name);
            }
            elseif(is_dir($startDir . DS . $dir))
            {
                self::scanDir($startDir . DS . $dir, $package_name);
            }
        }
    }

    private static function updateFileNameSpace($filename, $package_name){
        $file = file_get_contents($filename);

        $file = Str::of($file)->replace('%SERVICE_NAME%',Str::of($package_name)->camel()->ucfirst()->toString())->toString();

        file_put_contents($filename, $file);

        self::$console->writeln('<info>'.$filename.' has updated </info>');
    }

}
