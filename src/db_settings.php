<?php
$databases = [
    'dev' => [
        'driver' => $this->_sysConfig('DB_DRIVER'),
        'host' =>  $this->_sysConfig('DB_HOST'),
        'charset' => $this->_sysConfig('DB_CHARSET'),
        'dialect' => $this->_sysConfig('DB_DIALECT'),
        'port' => $this->_sysConfig('DB_PORT'),
        'schema' => $this->_sysConfig('DB_SCHEMA'),
        'username' => $this->_sysConfig('DB_USERNAME'),
        'password' => $this->_sysConfig('DB_PASSWORD'),
        'unix_socket' => $this->_sysConfig('DB_UNIX_SOCKET')
    ],
    'test' => [
        'driver' => 'sqlite',
        'schema' => 'memory'
    ]
];
?>