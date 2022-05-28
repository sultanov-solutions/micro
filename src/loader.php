<?php
use SultanovSolutions\Micro\Base\Index;

$dirs = [getcwd(), 'vendor', 'autoload.php'];

$autoloadFile = implode(DIRECTORY_SEPARATOR, $dirs);

if (file_exists($autoloadFile))
    require $autoloadFile;

if (MICRO_LOADER === 'index')
    Index::loadIndex();

if (MICRO_LOADER === 'artisan')
    Index::loadArtisan();
