<?php

use Bitrix\Main\DB\MysqliConnection;

return [
    'utf_mode'           =>
        [
            'value'    => true,
            'readonly' => true,
        ],
    'cache_flags'        =>
        [
            'value'    =>
                [
                    'config_options' => 3600,
                    'site_domain'    => 3600,
                ],
            'readonly' => false,
        ],
    'cookies'            =>
        [
            'value'    =>
                [
                    'secure'    => true,
                    'http_only' => true,
                ],
            'readonly' => false,
        ],
    'exception_handling' =>
        [
            'value'    =>
                [
                    'debug'                      => false,
                    'handled_errors_types'       => 4437,
                    'exception_errors_types'     => 4437,
                    'ignore_silence'             => false,
                    'assertion_throws_exception' => true,
                    'assertion_error_type'       => 256,
                    'log'                        => null,
                ],
            'readonly' => false,
        ],
    'connections'        =>
        [
            'value'    =>
                [
                    'default' =>
                        [
                            'className' => MysqliConnection::class,
                            'host'      => 'localhost',
                            'database'  => 'dyadyalaifu_db', // серверная версия
//                             'database'  => 'dyadyalaifu_db', // локальная версия
                            'login'     => 'dyadyalaifu_usr', // серверная версия
//                             'login'     => 'root', // локальная версия
//                            'password'  => 'HWrvVh4CsGDf2L1R', // серверная версия
                            'password'  => 'hSJp6y7XFqpa4s5y!', // серверная версия
//                             'password'  => 'ambient', // локальная версия
                            'options'   => 2,
                        ],
                ],
            'readonly' => true,
        ],
    'session'            => [
        'value' => [
            'mode'     => 'default',
        ],
    ],
    'smtp'               =>
        [
            'value' =>
                [
                    'enabled' => true,
                    'debug'   => false, // опционально
                    // 'log_file' => '/var/mailer.log', // опционально
                ],
        ],
];
