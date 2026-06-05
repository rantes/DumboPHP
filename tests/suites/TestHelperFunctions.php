<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;
use function DumboPHP\uuidV4;
use function DumboPHP\getallheaders;
use function DumboPHP\cleanToSEO;
use function DumboPHP\strGenerate;

/**
 * Verifies the standalone helper functions exported by the framework core.
 */
class TestHelperFunctions extends dumboTests {

    public function uuidV4FormatTest(): void {
        $uuid = uuidV4();
        $re   = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
        $this->assertEquals(1, preg_match($re, $uuid));
    }

    public function uuidV4UniqueTest(): void {
        $this->assertTrue(uuidV4() !== uuidV4());
    }

    public function getallheadersTest(): void {
        $this->assertTrue(is_array(getallheaders()));
    }

    public function cleanToSeoTest(): void {
        $this->assertEquals('cafe-con-leche', cleanToSEO('Café con Leche'));
    }

    /**
     * strGenerate() is typed `?string $params` but its body reads $params as an
     * array, so only the no-arg/default path is callable. Default length is 8.
     */
    public function strGenerateDefaultLengthTest(): void {
        $this->assertEquals(8, strlen(strGenerate()));
    }
}
