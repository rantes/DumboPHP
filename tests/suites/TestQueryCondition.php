<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;
use DumboPHP\QueryCondition;
use DumboPHP\QueryConditionException;

/**
 * Verifies DumboPHP\QueryCondition — connector validation, string casting and
 * the deterministic md5 key used to de-duplicate conditions.
 */
class TestQueryCondition extends dumboTests {

    public function connectorAndValidTest(): void {
        $c = new QueryCondition('a = 1', 'AND');
        $this->assertEquals('AND a = 1', (string) $c);
    }

    public function connectorOrValidTest(): void {
        $c = new QueryCondition('a = 1', 'OR');
        $this->assertEquals('OR a = 1', (string) $c);
    }

    public function connectorInvalidTest(): void {
        $threw = false;
        try {
            new QueryCondition('a = 1', 'XOR');
        } catch (QueryConditionException $e) {
            $threw = true;
        }
        $this->assertTrue($threw);
    }

    public function connectorCaseInsensitiveTest(): void {
        $c = new QueryCondition('a = 1', 'and');
        $this->assertEquals('AND a = 1', (string) $c);
    }

    public function toStringTest(): void {
        $c = new QueryCondition('name = "Ana"');
        $this->assertEquals('AND name = "Ana"', (string) $c);
    }

    public function getKeyTest(): void {
        $c   = new QueryCondition('a = 1', 'AND');
        $key = $c->getKey();
        $this->assertEquals(md5('AND a = 1'), $key);
    }

    public function duplicateKeyTest(): void {
        $a = new QueryCondition('a = 1', 'AND');
        $b = new QueryCondition('a = 1', 'AND');
        $this->assertEquals($a->getKey(), $b->getKey());
    }

    public function whitespaceNormalizationTest(): void {
        $c = new QueryCondition("a    =     1");
        $this->assertEquals('AND a = 1', (string) $c);
    }
}
