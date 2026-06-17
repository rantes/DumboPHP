<?php
$databases = [
    'dev' => [
        'driver' => APP_CONFIGS->get('DB_DRIVER'),
        'host' =>  APP_CONFIGS->get('DB_HOST'),
        'charset' => APP_CONFIGS->get('DB_CHARSET'),
        'dialect' => APP_CONFIGS->get('DB_DIALECT'),
        'port' => APP_CONFIGS->get('DB_PORT'),
        'schema' => APP_CONFIGS->get('DB_SCHEMA'),
        'username' => $this->_secrets->get('DB_USERNAME'),
        'password' => $this->_secrets->get('DB_PASSWORD'),
        'unix_socket' => APP_CONFIGS->get('DB_UNIX_SOCKET'),
        'protocol' => APP_CONFIGS->get('DB_PROTOCOL'),
    ],
    'test' => [
        'driver' => APP_CONFIGS->get('DB_DRIVER_TEST'),
        'host' =>  APP_CONFIGS->get('DB_HOST_TEST'),
        'charset' => APP_CONFIGS->get('DB_CHARSET_TEST'),
        'dialect' => APP_CONFIGS->get('DB_DIALECT_TEST'),
        'port' => APP_CONFIGS->get('DB_PORT_TEST'),
        'schema' => APP_CONFIGS->get('DB_SCHEMA_TEST'),
        'username' => $this->_secrets->get('DB_USERNAME_TEST'),
        'password' => $this->_secrets->get('DB_PASSWORD_TEST'),
        'unix_socket' => APP_CONFIGS->get('DB_UNIX_SOCKET_TEST')
    ]
];
