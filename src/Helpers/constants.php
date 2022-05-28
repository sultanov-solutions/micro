<?php
define('LARAVEL_START', microtime(true));

if (!defined('MICRO_ROOT_DIR')) {
    define('MICRO_ROOT_DIR', getcwd());
}

if (!defined('MICRO_SRC_DIR'))
    define('MICRO_SRC_DIR', MICRO_ROOT_DIR . DIRECTORY_SEPARATOR . 'src');

if (!defined('MICRO_VENDOR_DIR'))
    define('MICRO_VENDOR_DIR', implode(DIRECTORY_SEPARATOR, [
        MICRO_ROOT_DIR,
        'vendor',
        'sultanov-solutions',
        'micro',
        'src'
    ]));

if (!defined('LARAVEL_ROOT_DIR'))
    define('LARAVEL_ROOT_DIR', implode(DIRECTORY_SEPARATOR, [MICRO_ROOT_DIR, 'vendor', 'laravel', 'laravel']));
