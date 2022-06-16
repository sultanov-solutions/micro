<?php

if (!function_exists('get_root_namespace')) {
    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    function get_root_namespace(): string
    {
        $content = file_get_contents(MICRO_ROOT_DIR . '/composer.json');
        $content = json_decode($content, true);

        return array_key_first($content['autoload']['psr-4']);
    }

}

if (!function_exists('get_src_class')) {
    /**
     * Returns the className with root namespace.
     *
     * @param string $className
     * @return string
     */
    function get_src_class(string $className): string
    {
        $firstElement = str($className)->substr(0, 1)->toString();
        if ($firstElement === "\\") {
            $className = str($className)->substr(1)->toString();
        }

        return get_root_namespace() . $className;
    }
}

if (!function_exists('if_class_exists')) {

    /**
     * Checks if the class exists in installed package.
     *
     * @param string $className
     * @return bool
     */
    function if_class_exists(string $className): bool
    {
        $className = get_src_class($className);

        return class_exists($className);
    }
}
