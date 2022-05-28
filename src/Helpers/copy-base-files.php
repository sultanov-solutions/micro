<?php

if (!defined('ROOT_DIR'))
    define('ROOT_DIR', getcwd());

if (!defined('MICRO_VENDOR_DIR'))
    define('MICRO_VENDOR_DIR', __DIR__);

$STUBS_DIR = false;

if ( is_dir(implode(DIRECTORY_SEPARATOR, [MICRO_VENDOR_DIR,'..','Stubs']) ) )
{
    $STUBS_DIR = implode(DIRECTORY_SEPARATOR, [MICRO_VENDOR_DIR,'..','Stubs']);
}


if ( !file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . 'index.php') )
{
    if ($STUBS_DIR){
        copy($STUBS_DIR . DIRECTORY_SEPARATOR . 'index.php', ROOT_DIR . DIRECTORY_SEPARATOR . 'index.php');
        print_r("Index file copied \n");
    }
}
else
{
    print_r("Index file is exist \n");
}


if ( !file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . 'artisan') ){
    if ($STUBS_DIR){
        copy($STUBS_DIR . DIRECTORY_SEPARATOR . 'artisan', ROOT_DIR . DIRECTORY_SEPARATOR . 'artisan');
        print_r("Artisan file copied \n");
    }
}
else
{
    print_r("Artisan file is exist \n");
}

exit();
