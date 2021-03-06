<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8b0d3eb4385ba044ca3813b6f539571b
{
    public static $files = array(
        '3f8bdd3b35094c73a26f0106e3c0f8b2' => __DIR__ . '/../..' . '/lib/SendGrid.php',
    );

    public static $prefixLengthsPsr4 = array(
        'S' =>
            array(
                'SendGrid\\Stats\\' => 15,
                'SendGrid\\Mail\\' => 14,
                'SendGrid\\Contacts\\' => 18,
                'SendGrid\\' => 9,
            ),
    );

    public static $prefixDirsPsr4 = array(
        'SendGrid\\Stats\\' =>
            array(
                0 => __DIR__ . '/../..' . '/lib/stats',
            ),
        'SendGrid\\Mail\\' =>
            array(
                0 => __DIR__ . '/../..' . '/lib/mail',
            ),
        'SendGrid\\Contacts\\' =>
            array(
                0 => __DIR__ . '/../..' . '/lib/contacts',
            ),
        'SendGrid\\' =>
            array(
                0 => __DIR__ . '/../..' . '/lib',
                1 => __DIR__ . '/..' . '/sendgrid/php-http-client/lib',
            ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit8b0d3eb4385ba044ca3813b6f539571b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8b0d3eb4385ba044ca3813b6f539571b::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
