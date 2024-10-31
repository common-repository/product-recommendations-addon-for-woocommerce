<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit81e8061278fbec0a8ddbd89f02cc886f
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit81e8061278fbec0a8ddbd89f02cc886f', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit81e8061278fbec0a8ddbd89f02cc886f', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit81e8061278fbec0a8ddbd89f02cc886f::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
