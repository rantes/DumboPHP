<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;
use DumboPHP\SchemaCache;
use Migrations\CreateGadgets;

/**
 * PERF-CACHE-ESQUEMA — Caché del esquema entre peticiones: el resultado de Connection::getColumnFields() se lee de un archivo
 * (por tabla, conexión y sello) en vez de consultar los metadatos en cada petición.
 *
 * Cada prueba usa un directorio temporal propio y cambia DB->schemaCache por una caché apuntando ahí (la de DB es de SQLite en
 * memoria y está desactivada por diseño); el original se restaura siempre. Las consultas de metadatos se cuentan sin extensiones:
 * en SQLite con un autorizador (Pdo\Sqlite::setAuthorizer, PHP 8.5) sobre una segunda conexión persistente que comparte el manejador
 * de la base en memoria; en MySQL con el contador de sesión Com_show_fields (esa rama no se ha ejecutado contra un servidor).
 */
class TestSchemaCache extends dumboTests {
    private string $_dir      = '';
    private ?SchemaCache $_original = null;

    public function beforeEach(): void {
        $this->_migrateTables(['gadgets', 'posts']);
        $this->_dir      = sys_get_temp_dir() . '/dumbo_schema_' . bin2hex(random_bytes(6));
        $this->_original = DB->swapSchemaCache(new SchemaCache($this->_dir, 'test|identity', true));
        DB->resetModelRegistry();
    }

    public function afterEach(): void {
        DB->swapSchemaCache($this->_original);
        $this->_removeDir($this->_dir);
        DB->resetModelRegistry();
    }

    private function _removeDir(string $dir): void {
        is_dir($dir) && @chmod($dir, 0770);
        foreach (glob($dir . '/*') ?: [] as $file):
            @unlink($file);
        endforeach;
        @rmdir($dir);
    }

    /** Archivos de caché de tablas (no el sello) que hay hoy en el directorio temporal. */
    private function _cacheFiles(): array {
        return array_map('basename', glob($this->_dir . '/*-*.php') ?: []);
    }

    /** Consultas de metadatos de tablas que ejecuta $work (PRAGMA table_info en SQLite, SHOW COLUMNS en MySQL). */
    private function _metadataQueries(callable $work): int {
        $count = 0;
        if (DB->engine === 'mysql'):
            $status = fn(): int => (int) DB->query("SHOW SESSION STATUS LIKE 'Com_show_fields'")->fetch(\PDO::FETCH_NUM)[1];
            $before = $status();
            $work();
            $count = $status() - $before;
        else:
            $spy = \PDO::connect('sqlite::memory:', null, null, [\PDO::ATTR_PERSISTENT => true]);
            $spy->setAuthorizer(function (int $action, ?string $name) use (&$count): int {
                // 19 = SQLITE_PRAGMA (nombre de la pragma, argumento)
                ($action === 19 && $name === 'table_info') and $count++;

                return \Pdo\Sqlite::OK;
            });
            try {
                $work();
            } finally {
                $spy->setAuthorizer(null);
            }
        endif;

        return $count;
    }

    private function _plainFields(string $table): array {
        return DB->getColumnFields(DB->driver->getColumns($table));
    }

    public function firstReadWritesFileAndSecondReadAvoidsTheQueryTest(): void {
        DB->schemaCache->renew();

        $first = $this->_metadataQueries(fn() => DB->columnFieldsFor('gadgets'));
        $files = $this->_cacheFiles();
        $second = $this->_metadataQueries(fn() => DB->columnFieldsFor('gadgets'));

        $this->assertEquals(1, $first, 'La primera lectura consulta una vez los metadatos.');
        $this->assertEquals(1, count($files), 'Y deja un archivo de caché.');
        $this->assertEquals(0, $second, 'La segunda lectura no ejecuta ninguna consulta de metadatos.');
    }

    public function newRegistryReadsFromTheFileTest(): void {
        DB->schemaCache->renew();
        $this->Post->Niu();
        $this->assertEquals(1, count($this->_cacheFiles()), 'Instanciar el modelo dejó el archivo.');

        DB->resetModelRegistry();
        $queries = $this->_metadataQueries(function (): void {
            $this->Post->Niu();
        });

        $this->assertEquals(0, $queries, 'Con el registro en memoria vacío, un modelo nuevo se arma sin consultar metadatos.');
        $this->assertTrue(property_exists($this->Post->Niu(), 'title') || isset($GLOBALS['models']['posts']['fields']), 'El registro volvió a poblarse.');
    }

    public function fileResultIsIdenticalToTheQueryTest(): void {
        DB->schemaCache->renew();
        DB->columnFieldsFor('gadgets');

        $fromFile = DB->columnFieldsFor('gadgets');

        $this->assertTrue($fromFile === $this->_plainFields('gadgets'), 'Idéntico (===) al que devuelve la consulta.');
    }

    public function changingTheStampInvalidatesTest(): void {
        DB->schemaCache->renew();
        DB->columnFieldsFor('gadgets');
        $old = $this->_cacheFiles();

        DB->schemaCache->renew();
        $queries = $this->_metadataQueries(fn() => DB->columnFieldsFor('gadgets'));
        $new     = $this->_cacheFiles();

        $this->assertEquals(1, $queries, 'Con sello nuevo vuelve a consultar.');
        $this->assertEquals(1, count($new), 'Hay un archivo nuevo y el anterior se borró.');
        $this->assertTrue($new !== $old, 'El archivo nuevo tiene otro nombre.');
    }

    public function withoutStampNothingIsCachedTest(): void {
        $queries = $this->_metadataQueries(function (): void {
            DB->columnFieldsFor('gadgets');
            DB->columnFieldsFor('gadgets');
        });

        $this->assertEquals(2, $queries, 'Sin sello cada lectura consulta, como hoy.');
        $this->assertFalse(is_dir($this->_dir), 'Y no se crea ningún directorio ni archivo.');
        $this->assertTrue(DB->columnFieldsFor('gadgets') === $this->_plainFields('gadgets'), 'El resultado es el de siempre.');
    }

    public function unwritableDirectoryWorksWithoutCacheTest(): void {
        if (function_exists('posix_geteuid') && posix_geteuid() === 0):
            $this->assertTrue(true, 'OMITIDA: como root un directorio 0555 sigue siendo escribible.');
        else:
            DB->schemaCache->renew();
            chmod($this->_dir, 0555);

            $queries = $this->_metadataQueries(function (): void {
                DB->columnFieldsFor('gadgets');
                DB->columnFieldsFor('gadgets');
            });

            $this->assertEquals(2, $queries, 'Sin poder escribir, consulta en cada lectura.');
            $this->assertEquals([], $this->_cacheFiles(), 'No quedan archivos.');
            $this->assertTrue(DB->columnFieldsFor('gadgets') === $this->_plainFields('gadgets'), 'Y sin error ni resultado distinto.');
        endif;
    }

    public function corruptOrUnexpectedFilesAreIgnoredAndRegeneratedTest(): void {
        DB->schemaCache->renew();
        DB->columnFieldsFor('gadgets');
        $file = $this->_dir . '/' . $this->_cacheFiles()[0];
        $good = file_get_contents($file);
        $bad  = [
            'sintaxis rota'       => '<?php return [',
            'vacío'               => '',
            'devuelve una cadena' => "<?php return 'x';",
            'arreglo vacío'       => '<?php return [];',
            'forma inesperada'    => "<?php return [['Field' => 'id']];",
            'tipos erróneos'      => "<?php return [['Cast' => 'si', 'Field' => 'id', 'Type' => 'INT', 'Value' => null]];",
        ];
        foreach ($bad as $label => $content):
            file_put_contents($file, $content);
            function_exists('opcache_invalidate') && opcache_invalidate($file, true);

            $queries = $this->_metadataQueries(fn() => DB->columnFieldsFor('gadgets'));

            $this->assertEquals(1, $queries, "{$label}: se ignora y se consulta.");
            $this->assertTrue(DB->columnFieldsFor('gadgets') === $this->_plainFields('gadgets'), "{$label}: nunca un esquema parcial.");
            $this->assertEquals($good, file_get_contents($file), "{$label}: el archivo se regenera.");
        endforeach;
    }

    public function connectionsWithDifferentDatabasesDoNotShareTest(): void {
        $a = new SchemaCache($this->_dir, 'mysql|localhost|komodo_a', true);
        $b = new SchemaCache($this->_dir, 'mysql|localhost|komodo_b', true);
        $a->renew();
        $b->renew();
        $fields = $this->_plainFields('gadgets');
        $a->store('gadgets', $fields);

        $this->assertTrue($a->fetch('gadgets') === $fields, 'La conexión A ve su caché.');
        $this->assertEquals(null, $b->fetch('gadgets'), 'La conexión B, con otra base, no la ve.');
    }

    public function inMemoryDatabaseNeverUsesPersistentCacheTest(): void {
        DB->swapSchemaCache($this->_original);

        $this->assertEquals(null, $this->_original->fetch('gadgets'), 'La conexión de pruebas (SQLite en memoria) no lee caché persistente.');
        $this->_original->store('gadgets', $this->_plainFields('gadgets'));
        $this->assertFalse(is_dir($this->_dir), 'Ni la escribe.');
    }

    public function schemaChangeThroughMigrationIsSeenByTheNextReadTest(): void {
        DB->schemaCache->renew();
        $before = array_column(DB->columnFieldsFor('gadgets'), 'Field');

        ob_start();
        (new CreateGadgets())->addColumn(['field' => 'qty', 'type' => 'INTEGER', 'default' => '3']);
        ob_end_clean();
        $after = array_column(DB->columnFieldsFor('gadgets'), 'Field');

        $this->assertEquals(['id', 'name'], $before, 'Antes de migrar no está la columna.');
        $this->assertEquals(['id', 'name', 'qty'], $after, 'Tras la migración la siguiente lectura ve la columna nueva.');
        $this->assertEquals(1, count($this->_cacheFiles()), 'Y solo queda el archivo del sello nuevo.');
    }

    public function migrationWithoutStampDoesNotCreateOneTest(): void {
        ob_start();
        (new CreateGadgets())->addColumn(['field' => 'qty', 'type' => 'INTEGER']);
        ob_end_clean();

        $this->assertFalse(is_dir($this->_dir), 'Un cambio de esquema sin sello previo no crea sello ni caché.');
    }

    public function stampsAreUnpredictableAndFilesHaveRestrictedPermissionsTest(): void {
        $cache = new SchemaCache($this->_dir, 'x', true);
        $cache->renew();
        $first = $cache->stamp();
        $cache->renew();
        $cache->store('gadgets', $this->_plainFields('gadgets'));
        $mode = fileperms($this->_dir . '/stamp') & 0777;

        $this->assertTrue($first !== $cache->stamp(), 'Dos sellos seguidos son distintos.');
        $this->assertEquals(0660, $mode, 'El sello no es legible por todos.');
        $this->assertEquals(0660, fileperms($this->_dir . '/' . $this->_cacheFiles()[0]) & 0777, 'Ni los archivos de caché.');
        $this->assertEquals(0770, fileperms($this->_dir) & 0777, 'Ni el directorio.');
    }
}
