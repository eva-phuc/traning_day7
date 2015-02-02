<?php
/**
 * Use this file to override global defaults.
 *
 * See the individual environment DB configs for specific config information.
 */

return array(
    'default' => array(
        'type'        => 'pdo',
        'connection'  => array(
            'dsn'        => 'mysql:host=192.168.101.147;dbname=haq_drive;charset=utf8;',
            'username'   => 'haq_admin',
            'password'   => 'Nj22Gxfx',
            'persistent' => false,
            'compress'   => false,
        ),
        'identifier'   => '`',
        'table_prefix' => '',
        'charset'      => 'utf8',
        'enable_cache' => true,
        'profiling'    => false,
    ),
);
