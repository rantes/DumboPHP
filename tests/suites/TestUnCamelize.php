<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;
use Error;
use function DumboPHP\unCamelize;

/**
 * Characterization of DumboPHP\unCamelize: fixed cases, framework names and reproducible random strings compared against a private
 * copy of the original implementation (the oracle). Any output difference with the oracle is a behavior change.
 */
class TestUnCamelize extends dumboTests {
    /** Copy of the original implementation, byte for byte in behavior (loop char by char). */
    private function _oracle(string $toUncamelize): string {
        $newstring = '';
        if (isset($toUncamelize) and is_string($toUncamelize)) {
            $toUncamelize[0] = strtolower($toUncamelize[0]);
            for ($i = 0; $i < strlen($toUncamelize); $i++) {
                if (preg_match('`[A-Z]`', $toUncamelize[$i])) {
                    $newstring .= '_';
                }
                $newstring .= strtolower($toUncamelize[$i]);
            }
        }
        return $newstring;
    }

    /** Runs a callable and returns its result, or the class and message of the Error it throws (warnings are swallowed). */
    private function _outcome(callable $fn, string $input): string {
        set_error_handler(fn (): bool => true);
        try {
            $outcome = 'ok:' . $fn($input);
        } catch (Error $e) {
            $outcome = get_class($e) . ':' . $e->getMessage();
        } finally {
            restore_error_handler();
        }
        return $outcome;
    }

    public function fixedCasesTest(): void {
        $cases = [
            'a' => 'a', 'X' => 'x', 'abc' => 'abc', 'CamelCase' => 'camel_case', 'camelCase' => 'camel_case',
            'HTMLParser' => 'h_t_m_l_parser', 'XMLHttpRequest' => 'x_m_l_http_request', 'ABC' => 'a_b_c',
            'Model2Name' => 'model2_name', 'V2' => 'v2', 'a1B2' => 'a1_b2', '9Lives' => '9_lives',
            'Already_Snake' => 'already__snake', '_Leading' => '__leading',
            'App\\Models\\User' => 'app\\_models\\_user',
            "Tab\tA" => "tab\t_a", 'Sp aceB' => 'sp ace_b',
            'Ñandú' => 'Ñandú', 'ÉCole' => 'É_cole', 'NÃO' => 'nÃ_o', 'Ünï' => 'Ünï',
            'AppUser' => 'app_user', 'UserProperty' => 'user_property', 'UserPropertyHistory' => 'user_property_history',
            'CreateUserProperties' => 'create_user_properties', 'AdminController' => 'admin_controller',
        ];
        foreach ($cases as $input => $expected) {
            $this->assertEquals($expected, unCamelize((string) $input), "unCamelize('{$input}')");
        }
    }

    public function emptyStringBehaviorTest(): void {
        $this->describe('La cadena vacía lanza el mismo Error que la implementación original');
        $old = $this->_outcome(fn (string $s): string => $this->_oracle($s), '');
        $new = $this->_outcome(fn (string $s): string => unCamelize($s), '');
        $this->assertEquals('Error:Cannot assign an empty string to a string offset', $old, 'El oráculo lanza Error');
        $this->assertEquals($old, $new, 'La función actual se comporta igual');
    }

    public function frameworkNamesMatchOracleTest(): void {
        $names = [
            'Connection', 'Config', 'Secrets', 'Core_General_Class', 'ActiveRecord', 'Controller', 'Migrations', 'QueryCondition',
            'IrregularNouns', 'SchemaCache', 'index', 'dumboTests', 'testDispatcher', 'Shell', 'BaseShell', 'MigrationCommand',
            'GenerateCommand', 'DestroyCommand', 'CreateCommand', 'DbCommand', 'RunCommand', 'HelpCommand', 'AutocompleteCommand',
            'DumboGeneratorClass', 'DBDriver', 'DumboCommand', 'mysql', 'postgresql', 'sqlite', 'CreateUsers', 'CreateUserProfiles',
            'UserProfile', 'App\\Models\\UserProfile', 'App\\Controllers\\ProductController', 'Migrations\\CreateGadgets',
            'DumboPHP\\lib\\db_drivers\\mysql', 'DumboPHP\\lib\\ShellCommands\\MigrationCommand', 'ControllerException',
            'HTTPServer', 'URLParser', 'XMLHttpRequest', 'Model2Name', 'APIv2Client', 'iOSApp', 'eBook', 'ID', 'PDO', 'ABc', 'aBC',
        ];
        foreach ($names as $name) {
            $this->assertEquals($this->_outcome(fn (string $s): string => $this->_oracle($s), $name), $this->_outcome(fn (string $s): string => unCamelize($s), $name), "Equivale al oráculo: {$name}");
        }
    }

    public function randomStringsMatchOracleTest(): void {
        $this->describe('2.000 cadenas aleatorias reproducibles (semilla fija, longitud 0 a 40) dan lo mismo que el oráculo');
        $alphabet = array_merge(
            str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_\\'),
            ['á', 'É', 'ñ', 'Ü', 'ß', '€', '日', "\xC3", "\x80", ' ', '-', "\n"]
        );
        $last = count($alphabet) - 1;
        mt_srand(20261106);
        $mismatches = [];
        for ($n = 0; $n < 2000; $n++) {
            $length = mt_rand(0, 40);
            $input  = '';
            for ($i = 0; $i < $length; $i++) {
                $input .= $alphabet[mt_rand(0, $last)];
            }
            $old = $this->_outcome(fn (string $s): string => $this->_oracle($s), $input);
            $new = $this->_outcome(fn (string $s): string => unCamelize($s), $input);
            $old === $new or $mismatches[] = json_encode($input, JSON_INVALID_UTF8_SUBSTITUTE) . " => {$old} / {$new}";
        }
        $this->assertEquals([], array_slice($mismatches, 0, 5), 'Sin diferencias con el oráculo');
    }
}
