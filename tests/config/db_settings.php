<?php
/**
 * DB settings for the self-test environment.
 * Only the `test` environment exists and it uses SQLite in memory so the
 * suites are fast and fully isolated.
 */
$databases = [
    'test' => [
        'driver'   => 'sqlite',
        'schema'   => 'memory',
        'username' => '',
        'password' => '',
    ],
];
