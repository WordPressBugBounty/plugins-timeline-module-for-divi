<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5711792194962e783b4c690f28b11505
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'TMDIVI\\Modules\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'TMDIVI\\Modules\\' => 
        array (
            0 => __DIR__ . '/../..' . '/server/Modules',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5711792194962e783b4c690f28b11505::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5711792194962e783b4c690f28b11505::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5711792194962e783b4c690f28b11505::$classMap;

        }, null, ClassLoader::class);
    }
}
