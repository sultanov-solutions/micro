<?php

namespace SultanovSolutions\Micro\Core;

class Application extends \Illuminate\Foundation\Application
{

    /**
     * Get the application namespace.
     *
     * @return string
     */
    public function getNamespace(): string
    {
        if (!is_null($this->namespace)) {
            return $this->namespace;
        }

        $composer = json_decode(file_get_contents(MICRO_ROOT_DIR . '/composer.json'), true);

        return $this->namespace = array_key_first($composer['autoload']['psr-4']);
    }
}
