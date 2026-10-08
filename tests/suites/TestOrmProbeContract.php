<?php
namespace tests;

use DumboPHP\ActiveRecord;
use DumboPHP\lib\Timothy\dumboTests;

/**
 * CONTRATO CON LAS APPS — miembros privados de ActiveRecord de los que depende el sondeo de arranque.
 *
 * Komodo (`App\Services\OrmProtectionGuard`) comprueba por REFLEXIÓN, al arrancar, que el ORM instalado enlaza los valores
 * de las condiciones: construye condiciones con `_buildConditions()` y lee `_queryConditions` y `_queryBindings`. Si una
 * de estas piezas se renombra, cambia de visibilidad o de forma, el sondeo de las apps deja de poder verificar la
 * protección y — por diseño fail-closed — cierra TODAS las apps (503) tras el despliegue del framework.
 *
 * Esta prueba falla antes de que eso ocurra. Si el cambio es intencional:
 *   1. actualiza este contrato y el sondeo de cada app (Komodo: OrmProtectionGuard::PROBE_VERSION + ormClass()),
 *   2. despliega primero la versión de las apps que entiende el nuevo contrato,
 *   3. avisa en el checklist de despliegue.
 *
 * Las pruebas de comportamiento solo aplican con DUMBO_QUOTE_CONDITIONS activa (`php tests/run.php --protected`).
 */
class TestOrmProbeContract extends dumboTests {

    public function beforeEach(): void {
        $this->_migrateTables(['users']);
    }

    private function flagOn(): bool {
        return defined('DUMBO_QUOTE_CONDITIONS') && DUMBO_QUOTE_CONDITIONS;
    }

    public function buildConditionsIsPrivateAndTakesOneArrayTest(): void {
        $m = new \ReflectionMethod(ActiveRecord::class, '_buildConditions');

        $this->assertTrue($m->isPrivate(), '_buildConditions debe seguir siendo privado (el sondeo lo invoca por reflexión).');
        $this->assertFalse($m->isStatic());
        $this->assertEquals(1, $m->getNumberOfParameters(), 'Un solo parámetro: la lista de condiciones.');
        $type = $m->getParameters()[0]->getType();
        $this->assertEquals('array', $type === null ? '' : (string) $type, 'El parámetro es array.');
    }

    public function queryConditionsIsAPrivateArrayDefaultingToEmptyTest(): void {
        $p = new \ReflectionProperty(ActiveRecord::class, '_queryConditions');

        $this->assertTrue($p->isPrivate());
        $this->assertFalse($p->isStatic());
        $this->assertEquals([], $p->getDefaultValue(), 'Arranca como array vacío.');
    }

    public function queryBindingsIsAPrivateArrayDefaultingToEmptyTest(): void {
        $p = new \ReflectionProperty(ActiveRecord::class, '_queryBindings');

        $this->assertTrue($p->isPrivate());
        $this->assertFalse($p->isStatic());
        $this->assertEquals([], $p->getDefaultValue(), 'Arranca como array vacío.');
    }

    public function probeReadsTheSameWayItDoesInTheAppsTest(): void {
        $this->describe('El mecanismo exacto del sondeo: _buildConditions → _queryConditions (SQL sin el valor) + _queryBindings (con el valor)');
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }

        $model = $this->User->Niu();
        $build = new \ReflectionMethod(ActiveRecord::class, '_buildConditions');
        $conds = new \ReflectionProperty(ActiveRecord::class, '_queryConditions');
        $binds = new \ReflectionProperty(ActiveRecord::class, '_queryBindings');

        foreach (["a'b", 'a\\b', "x' OR '1'='1", 'x\\\' OR 1=1 -- -'] as $value) {
            // 'rowid' es siempre un campo válido (pk/rowid), sin depender de las columnas de ninguna tabla.
            $build->invoke($model, [['rowid', $value]]);
            $sql = implode(' ', array_map('strval', $conds->getValue($model)));

            $this->assertFalse(str_contains($sql, $value), 'El valor no está en el SQL: ' . $sql);
            $this->assertTrue(in_array($value, $binds->getValue($model), true), 'El valor está enlazado: ' . $value);

            $conds->setValue($model, []);
            $binds->setValue($model, []);
        }
    }

    public function probeFieldRowidIsAlwaysAcceptedTest(): void {
        $this->describe("El campo 'rowid' que usa el sondeo no depende de las columnas del modelo");
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }

        $model = $this->User->Niu();
        $build = new \ReflectionMethod(ActiveRecord::class, '_buildConditions');
        $build->invoke($model, [['rowid', 'x']]);

        $this->assertTrue(true, 'No lanza QueryConditionException.');
    }
}
