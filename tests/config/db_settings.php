<?php
/**
 * DB settings for the self-test environment.
 *
 * Por defecto: SQLite en memoria (rápido y aislado). Para verificar contra MySQL (necesario para todo lo que
 * depende del escape con barra invertida, que SQLite no trata como escape) exporta DUMBO_TEST_MYSQL=1 y apunta a
 * una BASE DESECHABLE (las suites hacen DROP/CREATE de sus tablas):
 *
 *   DUMBO_TEST_MYSQL=1 DUMBO_TEST_DB_SCHEMA=dumbo_test DUMBO_TEST_DB_USER=… DUMBO_TEST_DB_PASS=… php tests/run.php
 *
 * Opcionales: DUMBO_TEST_DB_HOST (127.0.0.1), DUMBO_TEST_DB_PORT (3306), DUMBO_TEST_DB_SOCKET, DUMBO_TEST_DB_CHARSET (utf8mb4).
 */
$databases = [
    'test' => getenv('DUMBO_TEST_MYSQL')
        ? [
            'driver'      => 'mysql',
            'host'        => getenv('DUMBO_TEST_DB_HOST') ?: '127.0.0.1',
            'port'        => getenv('DUMBO_TEST_DB_PORT') ?: '3306',
            'schema'      => getenv('DUMBO_TEST_DB_SCHEMA') ?: 'dumbo_test',
            'username'    => getenv('DUMBO_TEST_DB_USER') ?: '',
            'password'    => getenv('DUMBO_TEST_DB_PASS') ?: '',
            'unix_socket' => getenv('DUMBO_TEST_DB_SOCKET') ?: '',
            'charset'     => getenv('DUMBO_TEST_DB_CHARSET') ?: 'utf8mb4',
        ]
        : [
            'driver'   => 'sqlite',
            'schema'   => 'memory',
            'username' => '',
            'password' => '',
        ],
];
