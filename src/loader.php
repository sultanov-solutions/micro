<?php
use SultanovSolutions\Micro\Base\Index;

$dirs = [];

$dirs[] = getcwd();

if (in_array('public', explode(DIRECTORY_SEPARATOR, getcwd())))
    $dirs[] = '..';

$dirs[] = 'vendor';
$dirs[] = 'autoload.php';


$autoloadFile = implode(DIRECTORY_SEPARATOR, $dirs);

if (file_exists($autoloadFile)){
    require $autoloadFile;

    if (MICRO_LOADER === 'index')
        Index::loadIndex();

    if (MICRO_LOADER === 'artisan')
        Index::loadArtisan();
}
else
{
    echo $autoloadFile . ' file not found';
    exit($autoloadFile . ' file not found');
}
