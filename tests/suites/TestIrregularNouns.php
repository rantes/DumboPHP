<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;
use function DumboPHP\Plurals;
use function DumboPHP\Singulars;
use function DumboPHP\Camelize;
use function DumboPHP\unCamelize;

/**
 * Verifies the inflection helpers that drive DumboPHP's convention-based
 * table/class name mapping.
 */
class TestIrregularNouns extends dumboTests {

    public function pluralizeRegularTest(): void {
        $this->assertEquals('users', Plurals('user'));
    }

    public function pluralizeIrregularTest(): void {
        $this->assertEquals('people', Plurals('person'));
    }

    public function singularizeRegularTest(): void {
        $this->assertEquals('user', Singulars('users'));
    }

    public function singularizeIrregularTest(): void {
        $this->assertEquals('person', Singulars('people'));
    }

    public function camelizeTest(): void {
        $this->assertEquals('UserProfile', Camelize('user_profile'));
    }

    public function unCamelizeTest(): void {
        $this->assertEquals('user_profile', unCamelize('UserProfile'));
    }

    /**
     * An irregular plural that is already plural is idempotent.
     */
    public function pluralizeAlreadyPluralTest(): void {
        $this->assertEquals('people', Plurals('people'));
    }

    /**
     * Characterization test: regular words are NOT detected as already-plural,
     * so Plurals naively re-pluralizes them. This documents current behavior;
     * if the inflector gains "already plural" detection, update this assertion.
     */
    public function regularRePluralizeIsNaiveTest(): void {
        $this->assertEquals('userses', Plurals('users'));
    }
}
