<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7ab95f6abc317aac36175b7b4429a760
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Shake\\Container\\' => 16,
        ),
        'P' => 
        array (
            'Psr\\Container\\' => 14,
        ),
        'I' => 
        array (
            'Interop\\Container\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Shake\\Container\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'Interop\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/container-interop/container-interop/src/Interop/Container',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7ab95f6abc317aac36175b7b4429a760::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7ab95f6abc317aac36175b7b4429a760::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}