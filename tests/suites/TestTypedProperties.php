<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;
use TypeError;

/**
 * Properties typed after their columns (?int, ?float, ?string): bad data becomes a validation
 * error in _error (never an exception) when it arrives through the constructor / Niu(),
 * "" is 0 / 0.0 in numeric fields, a non integral number is rejected for an integer field,
 * and DECIMAL columns are read back as numbers.
 */
class TestTypedProperties extends dumboTests {

    public function beforeEach(): void {
        $this->_migrateTables(['typed_things']);
    }

    public function invalidIntegerIsAValidationErrorTest(): void {
        foreach (['abc', '5.7', '3,5', [1], 5.7] as $bad):
            $o = $this->TypedThing->Niu(['qty' => $bad, 'name' => 'ok']);
            $this->assertTrue($o->_error->isActived(), 'qty=' . json_encode($bad) . ' registers an error');
            $this->assertTrue(in_array('qty', $o->_error->errFields()), 'qty=' . json_encode($bad) . ' error is on the qty field');
            $this->assertEquals(null, $o->qty, 'qty=' . json_encode($bad) . ' is not assigned');
            $this->assertEquals('ok', $o->name, 'the other fields are still assigned');
            $this->assertFalse($o->Save(), 'qty=' . json_encode($bad) . ' Save() fails');
        endforeach;
    }

    public function invalidFloatIsAValidationErrorTest(): void {
        foreach (['3,5', 'abc', [1]] as $bad):
            $o = $this->TypedThing->Niu(['price' => $bad]);
            $this->assertTrue($o->_error->isActived(), 'price=' . json_encode($bad) . ' registers an error');
            $this->assertTrue(in_array('price', $o->_error->errFields()), 'error is on the price field');
            $this->assertEquals(null, $o->price, 'price is not assigned');
            $this->assertFalse($o->Save(), 'Save() fails');
        endforeach;
    }

    public function invalidStringIsAValidationErrorTest(): void {
        $o = $this->TypedThing->Niu(['name' => ['a']]);
        $this->assertTrue($o->_error->isActived(), 'an array in a string field registers an error');
        $this->assertTrue(in_array('name', $o->_error->errFields()), 'error is on the name field');
        $this->assertEquals(null, $o->name, 'name is not assigned');
    }

    public function errorMessageIsLiteralTest(): void {
        $o = $this->TypedThing->Niu(['qty' => 'abc', 'price' => 'x']);
        $this->assertEquals('This Field must be an integer number.,This Field must be numeric.', (string) $o->_error);
    }

    public function emptyStringIsZeroTest(): void {
        $o = $this->TypedThing->Niu(['qty' => '', 'price' => '', 'amount' => '']);
        $this->assertFalse($o->_error->isActived(), 'no error');
        $this->assertEquals(0, $o->qty, 'qty is 0');
        $this->assertEquals(0.0, $o->price, 'price is 0.0');
        $this->assertEquals(0.0, $o->amount, 'amount is 0.0');
    }

    public function validNumbersAreAcceptedTest(): void {
        $o = $this->TypedThing->Niu(['qty' => '1e3', 'price' => '3.5', 'amount' => '12.50']);
        $this->assertFalse($o->_error->isActived(), 'no error');
        $this->assertEquals(1000, $o->qty, '1e3 is the integer 1000');
        $this->assertEquals(3.5, $o->price, '"3.5" is 3.5');
        $this->assertEquals(12.5, $o->amount, '"12.50" is 12.5');
        $this->assertEquals(5, $this->TypedThing->Niu(['qty' => '5'])->qty, '"5" is 5');
        $this->assertEquals(5, $this->TypedThing->Niu(['qty' => '5.0'])->qty, '"5.0" is 5');
        $this->assertEquals(5, $this->TypedThing->Niu(['qty' => 5.0])->qty, '5.0 is 5');
    }

    public function nullInNiuStaysZeroTest(): void {
        $o = $this->TypedThing->Niu(['qty' => null]);
        $this->assertEquals(0, $o->qty, 'null in Niu() is still 0');
    }

    public function validObjectSavesTest(): void {
        $o = $this->TypedThing->Niu(['qty' => '7', 'price' => '3.5', 'name' => 'x', 'amount' => '12.50']);
        $this->assertTrue($o->Save(), 'Save() works');
        $f = $this->TypedThing->Find((int) $o->id);
        $this->assertEquals(7, $f->qty, 'qty is read back as int');
        $this->assertEquals(3.5, $f->price, 'price is read back as float');
    }

    public function decimalIsReadAsNumberTest(): void {
        $this->TypedThing->Niu(['amount' => '12.50', 'name' => 'a'])->Save();
        $this->TypedThing->Niu(['amount' => '1.10', 'name' => 'b'])->Save();
        $one = $this->TypedThing->Find(1);
        $this->assertEquals(12.5, $one->amount, 'single record: DECIMAL is a float');
        $all = $this->TypedThing->Find();
        $this->assertEquals(12.5, $all[0]->amount, 'dataset row 0: DECIMAL is a float');
        $this->assertEquals(1.1, $all[1]->amount, 'dataset row 1: DECIMAL is a float');
    }

    public function directAssignmentIsNotInterceptedTest(): void {
        $o      = $this->TypedThing->Niu();
        $thrown = false;
        try {
            $o->qty = 'abc';
        } catch (TypeError $e) {
            $thrown = true;
        }
        $this->assertTrue($thrown, 'a direct assignment to a declared typed property still throws TypeError (documented limit)');
    }

    public function generatorEmitsTheTypeOfTheColumnTest(): void {
        $dir = sys_get_temp_dir() . '/dumbo_gen_' . uniqid();
        mkdir($dir . '/config', 0775, true);
        mkdir($dir . '/app/models', 0775, true);
        file_put_contents($dir . '/config/host.php', "<?php\n");
        $repo = dirname(__DIR__, 2);
        file_put_contents($dir . '/run.php', "<?php\n"
            . "set_include_path('{$repo}' . PATH_SEPARATOR . '{$repo}/bin' . PATH_SEPARATOR . '{$repo}/lib');\n"
            . "define('INST_PATH', '{$dir}/');\n"
            . "require_once 'dumbophp.php';\n"
            . "require_once '{$repo}/lib/DumboShellColors.php';\n"
            . "require_once '{$repo}/lib/DumboGeneratorClass.php';\n"
            . "\$g = new DumboPHP\\lib\\DumboGeneratorClass();\n"
            . "\$g->model(['things', 'no-migration', 'pk:primary', 'qty:integer', 'big:biginteger', 'price:float', 'amount:decimal', 'name:string', 'body:text']);\n");
        shell_exec('cd ' . escapeshellarg($dir) . ' && php run.php 2>&1');
        $model = (string) @file_get_contents($dir . '/app/models/thing.php');
        shell_exec('rm -r ' . escapeshellarg($dir));

        foreach (['pk' => '?int', 'qty' => '?int', 'big' => '?int', 'price' => '?float', 'amount' => '?float', 'name' => '?string', 'body' => '?string'] as $field => $type):
            $this->assertTrue(str_contains($model, "public {$type} \${$field} = null;"), "{$field} is generated as {$type}");
        endforeach;
    }
}
