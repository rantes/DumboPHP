<?php
defined('_IN_SHELL_') || define('_IN_SHELL_', php_sapi_name() === 'cli' && empty($_SERVER['REMOTE_ADDR']));
/**
 * This function is to handle transition to php7.4 since this will change the way you call implode
 * Will change on php7.4 official release
 */
function imploder($glue = '', array $pieces ) {
    return (defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 70400) ? implode($glue, $pieces) : implode($pieces, $glue);
}

if (_IN_SHELL_ && !empty($argv)) parse_str(imploder('&', array_slice($argv, 1)), $_GET);

for ($i = 1; $i <= 5; $i++) {
    for ($j = 0; $j < 100; $j++) {
        $code = ($i*100)+$j;
        defined('HTTP_'.$code) || define('HTTP_'.$code, $code);
    }
}

if (!function_exists('getallheaders')) {
    /**
     * Get all HTTP header key/values as an associative array for the current request.
     *
     * @return string[string] The HTTP header key/value pairs.
     */
    function getallheaders() {
        $headers = [];
        $copy_server = array(
            'CONTENT_TYPE'   => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5'    => 'Content-Md5',
        );
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);
                if (!isset($copy_server[$key]) || !isset($_SERVER[$key])) {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                    $headers[$key] = $value;
                }
            } elseif (isset($copy_server[$key])) {
                $headers[$copy_server[$key]] = $value;
            }
        }
        if (!isset($headers['Authorization'])) {
            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
                $basic_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
                $headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }
        return $headers;
    }
}
/**
 * Generates an Universal Unique ID v4
 *
 * @return string
 */
function uuidV4() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}
/**
 * Implements the functionalities for translating
 * @author rantes
 * @package Core
 *
 */
final class IrregularNouns {
    public $singular = [];
    public $plural   = [];
    /**
     * Fills the singular and plural arrays with the irregular nouns
     */
    public function __construct() {
        $this->singular[] = 'abyss';
        $this->singular[] = 'alumnus';
        $this->singular[] = 'analysis';
        $this->singular[] = 'aquarium';
        $this->singular[] = 'arch';
        $this->singular[] = 'atlas';
        $this->singular[] = 'axe';
        $this->singular[] = 'baby';
        $this->singular[] = 'bacterium';
        $this->singular[] = 'batch';
        $this->singular[] = 'beach';
        $this->singular[] = 'browse';
        $this->singular[] = 'brush';
        $this->singular[] = 'bus';
        $this->singular[] = 'calf';
        $this->singular[] = 'chateau';
        $this->singular[] = 'cherry';
        $this->singular[] = 'child';
        $this->singular[] = 'church';
        $this->singular[] = 'circus';
        $this->singular[] = 'city';
        $this->singular[] = 'cod';
        $this->singular[] = 'copy';
        $this->singular[] = 'course';
        $this->singular[] = 'crisis';
        $this->singular[] = 'curriculum';
        $this->singular[] = 'datum';
        $this->singular[] = 'deer';
        $this->singular[] = 'dictionary';
        $this->singular[] = 'diagnosis';
        $this->singular[] = 'domino';
        $this->singular[] = 'dwarf';
        $this->singular[] = 'echo';
        $this->singular[] = 'elf';
        $this->singular[] = 'emphasis';
        $this->singular[] = 'family';
        $this->singular[] = 'fax';
        $this->singular[] = 'fish';
        $this->singular[] = 'flush';
        $this->singular[] = 'fly';
        $this->singular[] = 'foot';
        $this->singular[] = 'fungus';
        $this->singular[] = 'half';
        $this->singular[] = 'hero';
        $this->singular[] = 'hippopotamus';
        $this->singular[] = 'hoax';
        $this->singular[] = 'hoof';
        $this->singular[] = 'index';
        $this->singular[] = 'iris';
        $this->singular[] = 'kiss';
        $this->singular[] = 'knife';
        $this->singular[] = 'lady';
        $this->singular[] = 'leaf';
        $this->singular[] = 'life';
        $this->singular[] = 'loaf';
        $this->singular[] = 'man';
        $this->singular[] = 'mango';
        $this->singular[] = 'memorandum';
        $this->singular[] = 'mess';
        $this->singular[] = 'moose';
        $this->singular[] = 'motto';
        $this->singular[] = 'mouse';
        $this->singular[] = 'nanny';
        $this->singular[] = 'neurosis';
        $this->singular[] = 'nucleus';
        $this->singular[] = 'oasis';
        $this->singular[] = 'octopus';
        $this->singular[] = 'page';
        $this->singular[] = 'party';
        $this->singular[] = 'pass';
        $this->singular[] = 'penny';
        $this->singular[] = 'person';
        $this->singular[] = 'plateau';
        $this->singular[] = 'poppy';
        $this->singular[] = 'potato';
        $this->singular[] = 'purchase';
        $this->singular[] = 'quiz';
        $this->singular[] = 'reflex';
        $this->singular[] = 'runner-up';
        $this->singular[] = 'scarf';
        $this->singular[] = 'scratch';
        $this->singular[] = 'series';
        $this->singular[] = 'sheaf';
        $this->singular[] = 'sheep';
        $this->singular[] = 'shelf';
        $this->singular[] = 'son-in-law';
        $this->singular[] = 'species';
        $this->singular[] = 'splash';
        $this->singular[] = 'spy';
        $this->singular[] = 'status';
        $this->singular[] = 'stitch';
        $this->singular[] = 'story';
        $this->singular[] = 'syllabus';
        $this->singular[] = 'tax';
        $this->singular[] = 'thesis';
        $this->singular[] = 'thief';
        $this->singular[] = 'tomato';
        $this->singular[] = 'tooth';
        $this->singular[] = 'tornado';
        $this->singular[] = 'try';
        $this->singular[] = 'volcano';
        $this->singular[] = 'waltz';
        $this->singular[] = 'wash';
        $this->singular[] = 'watch';
        $this->singular[] = 'wharf';
        $this->singular[] = 'wife';
        $this->singular[] = 'woman';
        $this->plural[]   = 'abysses';
        $this->plural[]   = 'alumni';
        $this->plural[]   = 'analyses';
        $this->plural[]   = 'aquaria';
        $this->plural[]   = 'arches';
        $this->plural[]   = 'atlases';
        $this->plural[]   = 'axes';
        $this->plural[]   = 'babies';
        $this->plural[]   = 'bacteria';
        $this->plural[]   = 'batches';
        $this->plural[]   = 'beaches';
        $this->plural[]   = 'browses';
        $this->plural[]   = 'brushes';
        $this->plural[]   = 'buses';
        $this->plural[]   = 'calves';
        $this->plural[]   = 'chateaux';
        $this->plural[]   = 'cherries';
        $this->plural[]   = 'children';
        $this->plural[]   = 'churches';
        $this->plural[]   = 'circuses';
        $this->plural[]   = 'cities';
        $this->plural[]   = 'cod';
        $this->plural[]   = 'copies';
        $this->plural[]   = 'courses';
        $this->plural[]   = 'crises';
        $this->plural[]   = 'curricula';
        $this->plural[]   = 'data';
        $this->plural[]   = 'deer';
        $this->plural[]   = 'dictionaries';
        $this->plural[]   = 'diagnoses';
        $this->plural[]   = 'dominoes';
        $this->plural[]   = 'dwarves';
        $this->plural[]   = 'echoes';
        $this->plural[]   = 'elves';
        $this->plural[]   = 'emphases';
        $this->plural[]   = 'families';
        $this->plural[]   = 'faxes';
        $this->plural[]   = 'fish';
        $this->plural[]   = 'flushes';
        $this->plural[]   = 'flies';
        $this->plural[]   = 'feet';
        $this->plural[]   = 'fungi';
        $this->plural[]   = 'halves';
        $this->plural[]   = 'heroes';
        $this->plural[]   = 'hippopotami';
        $this->plural[]   = 'hoaxes';
        $this->plural[]   = 'hooves';
        $this->plural[]   = 'indexes';
        $this->plural[]   = 'irises';
        $this->plural[]   = 'kisses';
        $this->plural[]   = 'knives';
        $this->plural[]   = 'ladies';
        $this->plural[]   = 'leaves';
        $this->plural[]   = 'lives';
        $this->plural[]   = 'loaves';
        $this->plural[]   = 'men';
        $this->plural[]   = 'mangoes';
        $this->plural[]   = 'memoranda';
        $this->plural[]   = 'messes';
        $this->plural[]   = 'moose';
        $this->plural[]   = 'mottoes';
        $this->plural[]   = 'mice';
        $this->plural[]   = 'nannies';
        $this->plural[]   = 'neuroses';
        $this->plural[]   = 'nuclei';
        $this->plural[]   = 'oases';
        $this->plural[]   = 'octopi';
        $this->plural[]   = 'pages';
        $this->plural[]   = 'parties';
        $this->plural[]   = 'passes';
        $this->plural[]   = 'pennies';
        $this->plural[]   = 'people';
        $this->plural[]   = 'plateaux';
        $this->plural[]   = 'poppies';
        $this->plural[]   = 'potatoes';
        $this->plural[]   = 'shopping';
        $this->plural[]   = 'quizzes';
        $this->plural[]   = 'reflexes';
        $this->plural[]   = 'runners-up';
        $this->plural[]   = 'scarves';
        $this->plural[]   = 'scratches';
        $this->plural[]   = 'series';
        $this->plural[]   = 'sheaves';
        $this->plural[]   = 'sheep';
        $this->plural[]   = 'shelves';
        $this->plural[]   = 'sons-in-law';
        $this->plural[]   = 'species';
        $this->plural[]   = 'splashes';
        $this->plural[]   = 'spies';
        $this->plural[]   = 'statuses';
        $this->plural[]   = 'stitches';
        $this->plural[]   = 'stories';
        $this->plural[]   = 'syllabi';
        $this->plural[]   = 'taxes';
        $this->plural[]   = 'theses';
        $this->plural[]   = 'thieves';
        $this->plural[]   = 'tomatoes';
        $this->plural[]   = 'teeth';
        $this->plural[]   = 'tornadoes';
        $this->plural[]   = 'tries';
        $this->plural[]   = 'volcanoes';
        $this->plural[]   = 'waltzes';
        $this->plural[]   = 'washes';
        $this->plural[]   = 'watches';
        $this->plural[]   = 'wharves';
        $this->plural[]   = 'wives';
        $this->plural[]   = 'women';
    }
}
$GLOBALS['IN'] = new IrregularNouns();
$GLOBALS['PDOCASTS'] = [
    'BLOB' => false,
    'MEDIUM_BLOB' => false,
    'LONG_BLOB' => false,
    'DATETIME' => false,
    'DATE' => false,
    'DOUBLE' => false,
    'FLOAT' => true,
    'BIGINT' => true,
    'INT' => true,
    'INTEGER' => true,
    'LONGLONG' => true,
    'LONG' => true,
    'NEWDECIMAL' => false,
    'SHORT' => true,
    'STRING' => false,
    'TIME' => false,
    'TIMESTAMP' => true,
    'TINY' => true,
    'VAR_CHAR' => false,
    'VAR_STRING' => false,
    'blob' => false,
    'medium_blob' => false,
    'long_blob' => false,
    'datetime' => false,
    'date' => false,
    'double' => false,
    'float' => true,
    'bigint' => true,
    'int' => true,
    'integer' => true,
    'longlong' => true,
    'long' => true,
    'newdecimal' => false,
    'short' => true,
    'string' => false,
    'time' => false,
    'timestamp' => true,
    'tiny' => true,
    'var_char' => false,
    'var_string' => false,
];
/**
 * Turns a singular word into its plural
 * @param array|string $params
 * @param object $obj
 * @return string
 */
function Plurals($params, &$obj = NULL) {
    if ($obj === NULL) {
        $string = $params;
    } else {
        $string = $params[0];
    }

    if (in_array($string, $GLOBALS['IN']->singular)) {
        $key     = array_search($string, $GLOBALS['IN']->singular);
        $strconv = $GLOBALS['IN']->plural[$key];
    } elseif (in_array($string, $GLOBALS['IN']->plural)) {
        $strconv = $string;
    } else {
        $vowels = array('a', 'e', 'i', 'o', 'u');
        if (substr($string, -1, 1) == 'y') {
            $prec = substr($string, -2, 1);
            if (in_array($prec, $vowels)) {
                $strconv = $string.'s';
            } else {
                $strconv = str_replace('y', 'ies', $string);
            }
        } elseif (substr($string, -1, 1) == 'x' or substr($string, -1, 1) == 's' or substr($string, -2, 2) == 'ch' or substr($string, -2, 2) == 'sh' or substr($string, -2, 2) == 'ss') {
            $strconv = $string.'es';
        } else {
            $strconv = $string.'s';
        }
    }

    return $strconv;
}
/**
 * Turns a plural word into its singular
 * @param array|string $params
 * @param object $obj
 * @return string
 */
function Singulars($params, &$obj = NULL) {
    if ($obj == NULL) {
        $string = $params;
    } else {
        $string = $params[0];
    }

    $strconv = '';
    if (in_array($string, $GLOBALS['IN']->plural)) {
        $key     = array_search($string, $GLOBALS['IN']->plural);
        $strconv = $GLOBALS['IN']->singular[$key];
    } elseif (substr($string, -3, 3) == 'ies') {
        $strconv = str_replace('ies', 'y', $string);
    } elseif (substr($string, -2, 2) == 'es') {
        $test = substr($string, 0, -2);
        if (substr($test, -1, 1) == 'x' or substr($test, -1, 1) == 's' or substr($test, -2, 2) == 'ch' or substr($test, -2, 2) == 'sh' or substr($test, -2, 2) == 'ss') {
            $strconv = $test;
        } else {
            $strconv = substr($string, 0, -1);
        }
    } elseif (substr($string, -1, 1) == 's') {
        $strconv = substr($string, 0, -1);
    } else {
        $strconv = $string;
    }
    return $strconv;
}
/**
 * Turns an uncamelized_word into its CamelizedEquivalent
 * @param array|string $params
 * @param object $obj
 * @return string
 */
function Camelize($params, &$obj = NULL) {
    if ($obj === NULL) {$string = $params;
    } else {
        $string = $params[0];
    }

    $newName = "";
    if (preg_match("[_]", $string)) {
        $names = preg_split("[_]", $string);
        $i     = 1;
        foreach ($names as $single) {
            $newName .= ucfirst($single);
            $i++;
        }
    } else {
        $newName .= ucfirst($string);
    }
    return $newName;
}
/**
 * Set a CamelizedString back to uncamelized_string
 * @param array|string $params
 * @param object $obj
 * @return string
 */
function unCamelize($params, &$obj = NULL) {
    if ($obj === NULL) {
        $string = $params;
    } else {
        $string = $params[0];
    }

    $newstring = '';
    if (isset($string) and is_string($string)) {
        $string[0] = strtolower($string[0]);
        for ($i = 0; $i < strlen($string); $i++) {
            if (preg_match('`[A-Z]`', $string[$i])) {
                $newstring .= '_';
            }
            $newstring .= strtolower($string[$i]);
        }
    }
    return $newstring;
}
/**
 * Changes accent chars to its ASCII approximate and changes any non alpha-numeric char into a dash
 * @param array|string $params
 * @param object $obj
 * @return string
 * @todo Checks sometimes does not match the unicode char to change
 */
function cleanToSEO($params, &$obj = NULL) {
    if ($obj === NULL) {$string = $params;
    } else {
        $string = $params[0];
    }

    $string = preg_replace('/[áàãâä]/ui', 'a', $string);
    $string = preg_replace('/[éèêë]/ui', 'e', $string);
    $string = preg_replace('/[íìîï]/ui', 'i', $string);
    $string = preg_replace('/[óòõôö]/ui', 'o', $string);
    $string = preg_replace('/[úùûü]/ui', 'u', $string);
    $string = preg_replace('/[ç]/ui', 'c', $string);
    $string = preg_replace('/[ÿý]/ui', 'y', $string);
    $string = preg_replace('/[þ]/ui', 'b', $string);
    $string = preg_replace('/[ð]/ui', 'd', $string);
    $string = strtolower($string);
    $string = preg_replace('/[\s]+/', '-', $string);
    $string = preg_replace('/[^a-zA-Z0-9-]/', '', $string);
    return $string;
}
/**
 * Generates random alpha-numeric string
 * @param array $params
 * @return string
 */
function strGenerate($params = null) {
    $length         = 8;
    $case           = 'both';
    $includeNumbers = true;
    $lowerChars     = 'abcdefghijklmnopqrstuvwxyz';
    $upperChars     = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numberChars    = '0123456789';
    $primaryString  = '';
    $result         = '';
    if (!empty($params) && is_array($params)) {
        if (!empty($params['case'])) {
            $case = $params['case'];
        }
        if (isset($params['includeNumbers'])) {
            $includeNumbers = $params['includeNumbers'];
        }
        if (!empty($params['length'])) {
            $length = $params['length'];
        }
    }
    switch ($case) {
        case 'lower':
            $primaryString = $lowerChars;
            break;
        case 'upper':
            $primaryString = $upperChars;
            break;
        case 'both':
            $primaryString = $lowerChars.$upperChars;
            break;
    }
    if ($includeNumbers) {
        $primaryString .= $numberChars;
    }
    $max = strlen($primaryString)-1;
    do {
        $pos = mt_rand(0, $max);
        $result .= $primaryString[$pos];
    } while (strlen($result) < $length);
    return $result;
}
/**
 * Determines the type of input for the given field
 * @param array|string $type
 * @param ActiveRecord $obj
 * @return string
 */
function GetInput($type, &$obj = NULL) {
    if ($obj != NULL) {
        $type = $type[0];
    }

    $type = strtolower($type);
    if (strpos($type, 'text') !== FALSE) {
        return 'textarea';
    }
    return 'text';
}
/**
 * Will handle the system config values
 */
trait DumboSysConfig {
    /** stores the env config values */
    private $__sys_conf_values__ = [];
    /**
     * Retrieves a config value if not exists, returns a default value given
     *
     * @param string $key
     * @param string $default
     * @return string
     */
    public function _sysConfig(string $key, $default = null) {
        empty($this->__sys_conf_values__) && ($this->__sys_conf_values__ = parse_ini_file(INST_PATH.'.env'));
        $rval = $this->__sys_conf_values__[$key] ?? $default;

        return $rval;
    }
}
/**
 * Handles the database connet
 * @author rantes
 * @package Core
 *
 */
class Connection extends PDO {
    use DumboSysConfig;
    public $_settings = null;
    public $engine = null;

    function __construct() {
        empty($GLOBALS['env']) && ($GLOBALS['env'] = 'production');
        $databases = [];

        require INST_PATH.'config/db_settings.php';
        if(empty($databases[$GLOBALS['env']])) throw new Exception('There is no DB settings for the choosen env.');
        $this->_settings = $databases[$GLOBALS['env']];
        $this->engine = $this->_settings['driver'];

        switch ($this->engine) {
            case 'firebird':
                $dsn = "'firebird:dbname={$this->_settings['host']}/{$this->_settings['port']}:{$this->_settings['schema']}";
            break;
            case 'sqlite':
            case 'sqlite2':
            case 'sqlite3':
                $dsn = "{$this->engine}:{$this->_settings['schema']}";
                $this->_settings['schema'] === 'memory' and ($dsn = "{$this->engine}::memory:");
            break;
            default:
                empty($this->_settings['unix_socket']) or ($host = ':unix_socket=' . $this->_settings['unix_socket']);
                empty($this->_settings['port']) or ($host = ':host=' . $this->_settings['host'].';port=' . $this->_settings['port']);

                $charset = $dialect = '';

                empty($this->_settings['dialect']) or ($dialect = ";dialect={$this->_settings['dialect']}");
                empty($this->_settings['charset']) or ($charset = ";charset={$this->_settings['charset']}");

                $dsn = "{$this->engine}{$host};dbname={$this->_settings['schema']}{$dialect}{$charset}";
            break;
        }
        empty($this->_settings['username']) and $this->_settings['username'] = null;
        empty($this->_settings['password']) and $this->_settings['password'] = null;
        parent::__construct($dsn, $this->_settings['username'], $this->_settings['password'], [PDO::ATTR_PERSISTENT => true]);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $this->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
        $this->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_TO_STRING);
    }
    /**
     * Regarding the espcific dirver query, get the fields info from a table
     *
     * @param [string] $query
     * @return array
     */
    public function getColumnFields($query) {
        $numerics = ['INT', 'FLOAT', 'BIGINT', 'TINY', 'LONG', 'INTEGER'];
        $norm = [
            'INT' => 'INTEGER'
        ];
        try {
            $result1 = $this->query($query);
            $result1->setFetchMode(PDO::FETCH_ASSOC);
            $resultset1 = $result1->fetchAll();
            $ret = [];

            while(null !== ($res = array_shift($resultset1))) {
                $rtype = $res['type'] ?? $res['Type'];
                $rname = $res['name'] ?? $res['Field'];
                $type = strtoupper(preg_replace('@\([0-9]+\)@', '', $rtype));
                $ret[] = [
                    'Cast' => in_array($type, $numerics),
                    'Field' => $rname,
                    'Type' => $norm[$type] ?? $type,
                    'Value' => null
                ];
            }
            return $ret;
        } catch (PDOException $e) {
            die("Error to run query: -- {$query} -- due to: ".$e->getMessage());
        } catch (Exception $e) {
            die('Internal error: '.$e->getMessage());
        }
    }
    /**
     * gets the number fields validated by the query
     *
     * @param [string] $query
     * @return integer
     */
    public function validateField($query, $field = '') {
        $count = 0;
        $res = $this->query($query);
        $res->setFetchMode(PDO::FETCH_ASSOC);
        $result = $res->fetchAll();

        switch ($this->engine) {
            case 'sqlite':
            case 'sqlite2':
            case 'sqlite3':
                while(null !== ($reg = array_shift($result))) {
                    if ($reg['name'] === $field) {
                        $count = 1;
                        break;
                    }
                }
            break;
            default:
                $count = (integer)$result[0]['counter'];
            break;
        }

        return $count;
    }
}
/**
 * Handles the ActiveRecord errors
 * @author rantes
 * @package Core
 */
class Errors {
    private $actived  = FALSE;
    private $messages = [];
    private $counter  = 0;
    public function add($params) {
        if ($params === NULL or !is_array($params)):
            throw new Exception("Must to give an array with the params to add.");
        else :
            if (isset($params['field']) and isset($params['message'])):
                $this->messages[$params['field']][] = array('message' => $params['message'], 'code' => isset($params['code'])?$params['code']:'');
                $this->counter++;
                $this->actived = TRUE;
            else :
                throw new Exception("Must to give an array with the params to add.");
            endif;
        endif;
    }
    public function __toString() {
        $msgs = [];

        foreach ($this->messages as $field => $messages) {
            foreach ($messages as $message) {
                $msgs[] = $message['message'];
            }
        }
        return imploder(',', $msgs);
    }
    public function isActived() {
        return $this->actived;
    }
    public function errCodes() {
        $errorsCodes = [];
        foreach ($this->messages as $field => $messages) {
            foreach ($messages as $message) {
                $errorCodes[] = $message['code'];
            }
        }
        return $errorCodes;
    }
    public function errFields() {
        $errorFields = [];
        foreach ($this->messages as $field => $messages) {
            $errorFields[] = $field;
        }
        return $errorFields;
    }
    public function hasErrorCode($code = NULL) {
        return in_array($code, $this->errCodes());
    }
}
/**
 * Dumbo Core
 */
abstract class Core_General_Class extends ArrayObject {
    use DumboSysConfig;
    /**
     * Magic method to handle the ORM
     *
     * @param string $ClassName
     * @param [string] $val
     * @return void
     */
    public function __call($ClassName, $val = NULL) {
        $field         = Singulars(strtolower($ClassName));
        $classFromCall = Camelize($ClassName);
        $conditions    = '';
        $params        = [];
        if (file_exists(INST_PATH.'app/models/'.$field.'.php')) {
            $way = 'down';
            if (!empty($val[0])) {
                switch ($val[0]) {
                    case 'up':
                    case 'down':
                        $way = $val[0];
                        break;
                    case ':first':
                        $params = array(':first');
                        break;
                    default:
                        $params = $val[0];
                        break;
                }
            }
            $foreign = strtolower($field)."_id";
            $prefix  = unCamelize(get_class($this));
            if (!class_exists($classFromCall)) {
                require_once INST_PATH.'app/models/'.$field.'.php';
            }
            $obj1       = new $classFromCall();
            $conditions = "1=1";
            if (method_exists($obj1, 'Find')) {
                if ($classFromCall == get_class($this) && in_array($ClassName, $this->has_many_and_belongs_to)  && !empty($this->{$foreign})) {
                    $conditions = ($way == 'up')?"`{$this->pk}`='".$this->{$foreign} ."'":$conditions;
                } elseif (in_array($ClassName, $this->belongs_to) && !empty($this->{$foreign})) {
                    $conditions = "`{$this->pk}`='".$this->{$foreign} ."'";
                } elseif (in_array($ClassName, $this->has_many) ) {
                    $conditions = "`{$prefix}_id`='{$this->{$this->pk}}'";
                }
                $params['conditions'] = empty($params['conditions'])?$conditions:' AND '.$conditions;
                return ($conditions !== NULL)?$obj1->Find($params):$obj1->Niu();
            }
            return NULL;
        } elseif (preg_match('/Find_by_/', $ClassName)) {
            $nustring = str_replace('Find_by_', '', $ClassName);
            return $this->Find([
                    'conditions' => [
                        [$nustring, $val[0]]
                    ]
                ]);
        } else {
            return $ClassName($val, $this);
        }
    }
}
$GLOBALS['models'] = [];
/**
 * Class for Active Record design
 * @version 2.0
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Javier Serrano <http://www.rantes.info/>
 * @package Core
 * @subpackage ActiveRecord
 * @extends Core_General_Class
 *
 */
abstract class ActiveRecord extends Core_General_Class implements JsonSerializable {
    public $PaginatePageVarName = 'page';
    public $PaginateTotalItems  = 0;
    public $PaginateTotalPages  = 0;
    public $PaginatePageNumber  = 1;
    public $paginateURL         = '/';
    public $driver              = NULL;
    public $_error              = NULL;
    public $_sqlQuery           = '';
    public $candump             = true;
    public $id = null;
    protected $_ObjTable;
    protected $_singularName;
    protected $_counter                = 0;
    public $has_many                = [];
    public $has_one                 = [];
    public $belongs_to              = [];
    public $has_many_and_belongs_to = [];
    public $validate                = [];
    public $disableCast = true;
    protected $before_insert           = [];
    protected $after_insert            = [];
    protected $before_update           = [];
    protected $after_update            = [];
    protected $before_find             = [];
    protected $after_find              = [];
    protected $before_save             = [];
    protected $after_save              = [];
    protected $before_delete           = [];
    protected $after_delete            = [];
    protected $dependents              = '';
    protected $_dataAttributes         = [];
    protected $_params                 = array('fields' => '*', 'conditions' => '');
    protected $pk                      = 'id';
    protected $escapeField             = [];
    protected $_fields                 = [];
    protected $_preparedQuery = [];
    private $_paginatePrevChar  = '&lt;';
    private $_paginateNextChar  = '&gt;';
    private $_paginateFirstChar  = '|&lt;&lt;';
    private $_paginateLastChar  = '&gt;&gt;|';
    private $_validate = true;

    public function _init_() {}

    public final function __construct($fields = null) {
        $name = get_class($this);

        if (empty($GLOBALS['models'][$name])) {
            $className       = unCamelize($name);
            $words           = explode("_", $className);
            $i               = sizeof($words)-1;
            $words[$i]       = Plurals($words[$i]);
            $GLOBALS['models'][$name]['tableName'] = imploder('_', $words);
        }

        if (empty($this->_ObjTable)) {
            $this->_ObjTable = $GLOBALS['models'][$name]['tableName'];
        }
        defined('AUTO_AUDITS') or define('AUTO_AUDITS', true);

        if (empty($GLOBALS['Connection'])):
            $GLOBALS['Connection'] = new Connection();
        endif;

        if(preg_match('@sqlite@', $GLOBALS['Connection']->engine)):
            $this->pk = 'rowid';
            $this->rowid = null;
        endif;

        if (empty($GLOBALS['driver'])) {
            require_once dirname(__FILE__).'/../lib/db_drivers/'.$GLOBALS['Connection']->engine.'.php';
            $driver = $GLOBALS['Connection']->engine.'Driver';
            $GLOBALS['driver'] = new $driver();
        }

        if(empty($fields)) {
            $this->_setInitialCols();
        } else {
            $this->_fields = $fields;
        }
        $this->_error = new Errors;
        $this->_init_();
        $this->_counter = 0;
    }
    /**
     * Sets the name for the linked table. If the param comes empty, turns into a getter.
     * @param string $name
     */
    public function _TableName($name = '') {
        empty($name) or ($this->_ObjTable = $name);
        return $this->_ObjTable;
    }
    /**
     * Sets the initial col names (attrs)
     */
    private function _setInitialCols() {
        if (empty($GLOBALS['models'][$this->_ObjTable]['fields'])) {
            $fields = $GLOBALS['Connection']->getColumnFields($GLOBALS['driver']->getColumns($this->_ObjTable));
            while(null !== ($field = array_shift($fields))) {
                $this->_fields[$field['Field']] = $field['Cast'];
            }

            $GLOBALS['models'][$this->_ObjTable]['fields'] = $this->_fields;
        } else {
            $this->_fields = $GLOBALS['models'][$this->_ObjTable]['fields'];
        }

        return true;
    }
    public function jsonSerialize() {
        return $this->getArray();
    }
    /**
     * Gets an array with the field names of this model
     * @return array
     */
    public function getRawFields() {
        empty($this->_fields) && $this->_setInitialCols();
        return array_keys($this->_fields);
    }
    /**
     * Getter for the fields taken from the query or table
     * @return array Fields of the current Active Record Object
     */
    public function getFields() {
        empty($this->_fields) && $this->_setInitialCols();
        return $this->_fields;
    }
    /**
     * Unset the params for the queries
     */
    public function __destruct() {
        $this->_params = null;
    }
    /**
     *
     * {@inheritDoc}
     * @see ArrayObject::getIterator()
     */
    public function getIterator() {
        return new ArrayIterator($this);
    }
    /**
     * Fetch the data with the providen query
     * Sets the active record.
     * @param string $query SQL query to fetch the data
     */
    protected function getData($prepared, $data) {
        (empty($GLOBALS['connection']) || empty($GLOBALS['driver'])) && $this->__construct();
        $obj = clone $this;
        $obj->_fields = [];
        $obj->_dataAttributes = [];

        try {
            $sh = $GLOBALS['Connection']->prepare($prepared);
            $sh->execute($data);

            $cols = $sh->columnCount();
            // $rows = $sh->rowCount();

            $sh->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, get_class($obj));
            $resultset = $sh->fetchAll();

            if (preg_match('@sqlite@', $GLOBALS['Connection']->engine)):
                // $rows = sizeof($resultset);
                foreach($this->_fields as $field => $cast) {
                    $obj->_set_columns([
                        'native_type' => 'VAR_STRING',
                        'name' => $field
                    ]);
                }
            else:
                for ($i = 0; $i < $cols; $i++) {
                    $meta = $sh->getColumnMeta($i);
                    $obj->_set_columns($meta);
                }
            endif;

            $obj->_counter = sizeof($resultset);;

            $sh->closeCursor();
            $obj->exchangeArray($resultset);
        } catch (PDOException $e) {
            foreach($this->_fields as $field => $cast) {
                $obj->_set_columns([
                    'native_type' => 'VAR_STRING',
                    'name' => $field
                ]);
            }
            throw new Exception("Failed to run {$this->_sqlQuery} due to: {$e->getMessage()}");
        }

        if ($obj->_counter === 0) {
            $obj->offsetSet(0, NULL);
            $obj[0] = NULL;
            unset($obj[0]);
            foreach ($obj->_fields as $field => $cast):
                $obj->{$field} = '';
            endforeach;
        } elseif ($obj->_counter === 1) {
            foreach ($obj->_fields as $field => $cast) {
                if (isset($obj[0]->{$field})) {
                    $obj->{$field} = $obj[0]->{$field};
                }
            }
            if(preg_match('@sqlite@', $GLOBALS['Connection']->engine) and isset($obj[0]->rowid)) {
                $obj->rowid = $obj[0]->rowid;
            }
        }
        if (!$this->disableCast) {
            for ($i=0; $i<$obj->_counter; $i++) {
                foreach ($obj->_fields as $field => $cast) {
                    $obj[$i]->{$field} = $cast ? 0 + $obj[$i]->{$field} : $obj[$i]->{$field};
                }
            }
        }
        return $obj;
    }
    /**
     * Performs the select queries to the database according to the given params
     * @param array|integer $params
     * @return ActiveRecord
     */
    public function Find($params = NULL) {
        (empty($GLOBALS['connection']) || empty($GLOBALS['driver'])) && $this->__construct();

        if (sizeof($this->before_find) > 0) {
            foreach ($this->before_find as $functiontoRun) {
                $this->{$functiontoRun}();
            }
        }
        $prepared = $GLOBALS['driver']->Select($params, $this->_ObjTable, $this->pk);
        $this->_sqlQuery = $prepared['query'];
        $x = $this->getData($prepared['prepared'], $prepared['data']);

        if (sizeof($x->after_find) > 0) {
            foreach ($x->after_find as $functiontoRun) {
                $x->{$functiontoRun}();
            }
        }

        return $x;
    }
    /**
     * Performs a select query from a given string
     * @param string $query
     * @return ActiveRecord
     */
    public function Find_by_SQL($query) {
        if (!is_string($query)) {
            throw new Exception('The query must be an string');
            exit;
        } else {
            $this->_sqlQuery = $query;
            $x = $this->getData($query, []);
            return $x;
        }
    }
    private function _set_attributes($resultset) {
        if (!empty($resultset)) {
            foreach ($resultset[0] as $key => $value) {
                if (!array_key_exists($key, $this->_fields)) {
                    $this->_fields[$key] = is_numeric($value);
                    $this->{$key} = '';
                    $this->_dataAttributes[$key]['native_type'] = is_numeric($value)?'NUMERIC':'STRING';
                    $this->_dataAttributes[$key]['cast'] = $this->_fields[$key];
                }
            }
        }
    }
    private function _set_columns($meta) {
        (empty($meta['native_type']) || ($meta['native_type'] === 'null')) && ($meta['native_type'] = 'VAR_STRING');
        if(!empty($meta['name'])) {
            $this->_fields[$meta['name']] = $GLOBALS['PDOCASTS'][$meta['native_type']];
            $this->{$meta['name']} = '';
            $this->_dataAttributes[$meta['name']]['native_type'] = $meta['native_type'];
            $this->_dataAttributes[$meta['name']]['cast'] = $this->_fields[$meta['name']];
        }
    }
    private function _setValues(array $values) {
        if (empty($values)) {
            foreach ($this->_fields as $field => $cast){
                $values[$field] = null;
            }
        }
        foreach ($values as $field => $value) {
            if (array_key_exists($field, $this->_fields)) {
                $this->{$field} = $value;
            }
        }
    }
    /**
     * Set a flat set of the current dataset
     * Will take the first value as key and the second as value
     *
     * @return array
     */
    public function flatten() {
        $result = [];
        $fields = $this->getRawFields();
        foreach($this as $row) {
            $result[$row->{$fields[0]}] = $row->{$fields[1]};
        }

        return $result;
    }
    /**
     * Creates a new Active Record instance
     * @param array $contents
     * @return ActiveRecord
     */
    public function Niu(array $contents = []) {
        foreach($this->_fields as $field => $val) {
            $this->{$field} = null;
        };
        for ($i = 0; $i < $this->_counter; $i++) {
            $this[$i] = null;
            $this->offsetUnset($i);
        }

        $this->__construct();
        $this->_setInitialCols();
        $this->_setValues($contents);

        return clone $this;
    }
    /**
     * Performs an update data into the table
     * @param array $params
     * @throws Exception
     * @return boolean
     */
    public function Update(array $params) {
        if (empty($params['conditions']) || !is_string($params['conditions']))
            throw new Exception('The param conditions should not be empty and must be string.');

        if (empty($params['data']) || !is_array($params['data']))
            throw new Exception('The param data should not be empty and must be array.');

        defined('AUTO_AUDITS') or define('AUTO_AUDITS', true);
        $prepared = $GLOBALS['driver']->Update($params, $this->_ObjTable);
        $this->_sqlQuery = $prepared['query'];
        $sh = $GLOBALS['Connection']->prepare($this->_sqlQuery);
        if (!$sh->execute($prepared['prepared'])) {
            $this->_error->add(array('field' => $this->_ObjTable, 'message' => $GLOBALS['Connection']->errorInfo()[2]."\n {$this->_sqlQuery}"));
            return false;
        }
        return true;
    }
    /**
     * Deactivate validations
     */
    public function validationsOff() {
        $this->_validate = false;
    }
    /**
     * Turn on validations
     */
    public function validationsOn() {
        $this->_validate = true;
    }
    /**
     * Performs validations regarding array definitions
     * @param string $action [insert/save]
     * @throws Exception
     */
    private function _ValidateOnSave($action = 'insert') {
        if ($this->_validate && !empty($this->validate)) {
            if (!empty($this->validate['email'])) {
                foreach ($this->validate['email'] as $field) {
                    $message = 'The email provided is not a valid email address.';
                    $matches = [];
                    if (is_array($field)) {
                        if (empty($field['field'])) throw new Exception('Field key must be defined in array.');

                        empty($field['message']) || ($message = $field['message']);
                        $field = $field['field'];
                    }
                    if (!empty($this->{$field})) {
                        preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/", $this->{$field}, $matches);
                        empty($matches) && $this->_error->add(['field' => $field, 'message' => $message]);
                    }
                }
            }
            if (!empty($this->validate['numeric'])) {
                foreach ($this->validate['numeric'] as $field) {
                    $message = 'This Field must be numeric.';
                    if (is_array($field)) {
                        if (empty($field['field'])) throw new Exception('Field key must be defined in array.');

                        empty($field['message']) || ($message = $field['message']);
                        $field = $field['field'];
                    }
                    isset($this->{$field}) && (!is_numeric($this->{$field})) && $this->_error->add(['field' => $field, 'message' => $message]);
                }
            }
            if (!empty($this->validate['unique'])) {
                foreach ($this->validate['unique'] as $field) {
                    $message = 'This field can not be duplicated.';
                    if (is_array($field)) {
                        if (empty($field['field'])) {throw new Exception('Field key must be defined in array.');}

                        empty($field['message']) || ($message = $field['message']);
                        $field['field'];
                    }
                    if (!empty($this->{$field['field']})) {
                        $thisclass = get_class($this);
                        $obj1 = new $thisclass();
                        $resultset = $obj1->Find([
                            'fields' => $field['field'],
                            'conditions' => "{$field['field']}='" .$this->{$field['field']} ."' AND {$this->pk}<>'" .$this->{$this->pk} ."'"
                        ]);
                        $resultset->counter() > 0 && $this->_error->add(['field' => $field['field'], 'message' => $message]);
                    }
                }
            }

            if (!empty($this->validate['presence_of'])) {
                foreach ($this->validate['presence_of'] as $field) {
                    $message = 'This field can not be empty or null.';
                    if (is_array($field)) {
                        if (empty($field['field'])) throw new Exception('Field key must be defined in array.');

                        empty($field['message']) or ($message = $field['message']);
                        $field = $field['field'];
                    }
                    (($action === 'insert'
                        && !isset($this->{$field}))
                        || (empty($this->{$field})
                        && isset($this->{$field})
                        && !is_numeric($this->{$field})))
                    && $this->_error->add([
                        'field' => $field,
                        'message' => $message
                    ]);
                }
            }
        }
    }

    /**
     * Performs a save action of an object into the database, it could be insert or update, depending on the id
     * @return boolean
     */
    public function Save() {
        defined('AUTO_AUDITS') or define('AUTO_AUDITS', true);
        AUTO_AUDITS && !($this->updated_at = 0) && !($this->created_at = 0);
        $fields = array_keys($this->_fields);

        foreach($this->before_save as $functiontoRun) {
            $this->{$functiontoRun}();
            if ($this->_error->isActived()) return false;
        }

        if (!empty($this->{$this->pk})) {
            foreach($this->before_update as $functiontoRun) {
                $this->{$functiontoRun}();
                if ($this->_error->isActived()) return false;
            }
            $this->_ValidateOnSave('update');
            if ($this->_error->isActived()) return false;

            AUTO_AUDITS && ($this->updated_at = time());
            $data = [];

            while (null !== ($field = array_shift($fields))) {
                $field !== $this->pk && isset($this->{$field}) && $field !== 'created_at' && ($data[$field] = $this->{$field});
            }

            $prepared = $GLOBALS['driver']->Update(['data' => $data, 'conditions' => "{$this->_ObjTable}.{$this->pk} = '" .$this->{$this->pk}."'"], $this->_ObjTable);
        } else {
            foreach ($this->before_insert as $functiontoRun) {
                $this->{$functiontoRun}();
                if ($this->_error->isActived()) return false;
            }

            $this->_ValidateOnSave();
            if ($this->_error->isActived()) return false;

            AUTO_AUDITS && ($this->created_at = time());
            $data = [];

            while (null !== ($field = array_shift($fields))) {
                $field != $this->pk && isset($this->{$field}) && ($data[$field] = $this->{$field});
            }
            if (preg_match('@sqlite@', $GLOBALS['Connection']->engine) and isset($data['id'])) unset($data['id']);

            $prepared = $GLOBALS['driver']->Insert($data, $this->_ObjTable);
        }

        $this->_sqlQuery = $prepared['query'];
        $sh = $GLOBALS['Connection']->prepare($this->_sqlQuery);

        try {
            if (!$sh->execute($prepared['prepared'])) {
                $e = $GLOBALS['Connection']->errorInfo();
                $this->_error->add(['field' => $this->_ObjTable, 'message' => $e[2]."\n {$this->_sqlQuery}"]);
                return FALSE;
            }

            if (empty($this->{$this->pk})) {
                $name = preg_match('@sqlite@', $GLOBALS['Connection']->engine) ? 'rowid' : null;
                $this->{$this->pk} = (int)$GLOBALS['Connection']->lastInsertId($name);
                if (sizeof($this->after_insert) > 0) {
                    foreach ($this->after_insert as $functiontoRun) {
                        $this->{$functiontoRun}();
                        if ($this->_error->isActived()) return false;
                    }
                }
            } elseif (sizeof($this->after_update) > 0) {
                foreach ($this->after_update as $functiontoRun) {
                    $this->{$functiontoRun}();
                    if ($this->_error->isActived()) return false;
                }
            }
        } catch (PDOException $e) {
            echo 'Failed to run ', $this->_sqlQuery, ' due to: ', $e->getMessage();
            return FALSE;
        }

        $this->_counter === 0 && ($this->_counter = 1) && $this->offsetSet(0, $this);

        if (sizeof($this->after_save) > 0) {
            foreach ($this->after_save as $functiontoRun) {
                $this->{$functiontoRun}();
                if ($this->_error->isActived()) return false;
            }
        }

        return true;
    }
    /**
     * Attempts to insert directly to the table, one register at once
     *
     * @return boolean
     */
    public function Insert() {
        defined('AUTO_AUDITS') or define('AUTO_AUDITS', true);
        AUTO_AUDITS && !($this->updated_at = 0) && !($this->created_at = 0);
        $fields = array_keys($this->_fields);

        foreach($this->before_save as $functiontoRun) {
            $this->{$functiontoRun}();
            if ($this->_error->isActived()) return false;
        }

        foreach ($this->before_insert as $functiontoRun) {
            $this->{$functiontoRun}();
            if ($this->_error->isActived()) return false;
        }

        $this->_ValidateOnSave();
        if ($this->_error->isActived()) return false;

        AUTO_AUDITS && ($this->created_at = time());
        $data = [];

        while (null !== ($field = array_shift($fields))) {
            isset($this->{$field}) && ($data[$field] = $this->{$field});
        }
        $prepared = $GLOBALS['driver']->Insert($data, $this->_ObjTable);

        $this->_sqlQuery = $prepared['query'];

        try {
            $sh = $GLOBALS['Connection']->prepare($prepared['query']);
            if (!$sh->execute($prepared['prepared'])) {
                $e = $GLOBALS['Connection']->errorInfo();
                $this->_error->add(['field' => $this->_ObjTable, 'message' => $e[2]."\n {$this->_sqlQuery}"]);
                return FALSE;
            }

            $this->{$this->pk} = (integer)$GLOBALS['Connection']->lastInsertId();
            if (sizeof($this->after_insert) > 0) {
                foreach ($this->after_insert as $functiontoRun) {
                    $this->{$functiontoRun}();
                    if ($this->_error->isActived()) return false;
                }
            }
        } catch (PDOException $e) {
            echo 'Failed to run ', $this->_sqlQuery, ' due to: ', $e->getMessage();
            return FALSE;
        }

        $this->_counter === 0 && ($this->_counter = 1) && $this->offsetSet(0, $this);

        if (sizeof($this->after_save) > 0) {
            foreach ($this->after_save as $functiontoRun) {
                $this->{$functiontoRun}();
                if ($this->_error->isActived()) return false;
            }
        }

        return true;
    }
    /**
     * Handles the delete register in database
     * @param array|integer $conditions can be an array of IDs or just a single ID
     * @return boolean
     */
    public function Delete($conditions = NULL) {
        if ($this->_counter > 1) {
            $conditions = [];
            foreach ($this as $ele) {
                $conditions[] = $ele->{$this->pk};
            }
        }
        if ($conditions === NULL and !empty($this->{$this->pk})) {
            $conditions = (int)$this->{$this->pk};
        }

        if ($conditions === NULL and empty($this->{$this->pk})) {
            $this->_error->add(array('field' => $this->_ObjTable, 'message' => "Must specify a register to delete"));
            return FALSE;
        }
        if (sizeof($this->before_delete) > 0) {
            foreach ($this->before_delete as $functiontoRun) {
                $this->{$functiontoRun}();
                if (!empty($this->_error) && $this->_error->isActived()) {
                    return false;
                }
            }
        }
        if (!$this->_delete_or_nullify_dependents($conditions)) {
            return false;
        }
        $this->_sqlQuery = $GLOBALS['driver']->Delete($conditions, $this->_ObjTable);

        if ($GLOBALS['Connection']->exec($this->_sqlQuery) === false) {
            $e = $GLOBALS['Connection']->errorInfo();
            $this->_error->add(array('field' => $this->_ObjTable, 'message' => $e[2]."\n {$this->_sqlQuery}"));
            return FALSE;
        }
        if (sizeof($this->after_delete) > 0) {
            foreach ($this->after_delete as $functiontoRun) {
                $this->{$functiontoRun}();
                if (!empty($this->_error) && $this->_error->isActived()) {
                    return false;
                }
            }
        }
        return TRUE;
    }
    /**
     * Handles the dependants tasks, delete or set to null the relational data in other models
     * @param integer $id
     * @return boolean
     */
    protected function _delete_or_nullify_dependents($id) {
        if (!empty($this->dependents) && !empty($id)) {
            foreach ($this->has_many as $model) {
                $s = Singulars($model);
                $m = Camelize($s);
                class_exists($m) or require_once INST_PATH.'app/models/'.strtolower($s).'.php';
                $model1   = new $m();
                $condition = is_numeric($id)? " = '{$id}'" : " IN (".imploder(',', $id).")";
                $children = $model1->Find([
                    'conditions' => Singulars($this->_ObjTable)."_id{$condition}"
                ]);
                if ($children->counter() > 0) {
                    foreach ($children as $child) {
                        switch ($this->dependents) {
                            case 'destroy':
                                if (!$child->Delete()) {
                                    $this->_error->add([
                                        'field' => $this->_ObjTable,
                                        'message' => 'Cannot delete dependents'
                                    ]);
                                    return FALSE;
                                }
                            break;
                            case 'nullify':
                                $child->{$this->_ObjTable.'_id'} = '';
                                if (!$child->Save()) {
                                    $this->_error->add(array('field' => $this->_ObjTable, 'message' => "Cannot nullify dependents"));
                                    return FALSE;
                                }
                            break;
                        }
                    }
                }
            }
        }
        return true;
    }
    public function __debugInfo() {
        $this->inspect();
    }
    /**
     * Dumps out the data contained in the model.
     */
    public function inspect($tabs = 0) {
        if (_IN_SHELL_):
            fwrite(STDOUT, get_class($this) . " ActiveRecord ({$this->_counter}): " . $this->ListProperties_ToString($tabs));
        else:
            echo get_class($this), " ActiveRecord ({$this->_counter}): ", $this->ListProperties_ToString($tabs);
        endif;
    }
    protected function ListProperties_ToString($i = 0) {
        $listProperties = "{\n";
        $fields = array_keys($this->_fields);

        if ($this->_counter <= 1) {
            foreach ($fields as $field) {
                $buffer = 'NULL'.PHP_EOL;
                if (isset($this->{$field})) {
                    $buffer = print_r($this->{$field}, true);
                }
                for ($j = 0; $j < $i+1; $j++) {
                    $listProperties .= "\t";
                }
                $listProperties .= "{$field} => {$buffer}";
            }
        } else {
            ob_start(null, 0, PHP_OUTPUT_HANDLER_STDFLAGS);
            for ($j = 0; $j < $this->_counter; $j++) {
                $this[$j]->inspect($i+1);
            }
            $buffer = ob_get_clean();
            $listProperties .= $buffer;
        }

        for ($j = 0; $j < $i; $j++) {
            $listProperties .= "\t";
        }

        $listProperties .= "}\n";
        return $listProperties;
    }
    public function __toString() {
        $a = $this->ListProperties_ToString();
        return $a;
    }
    /**
     * Returns an array from ActiveRecord ArrayObject
     * @return array
     */
    public function getArray() {
        $arraux = [];
        $fields = array_keys($this->_fields);

        if ($this->_counter > 0) {
            for ($j = 0; $j < $this->_counter; $j++) {
                foreach ($fields as $field) {
                    if (isset($this[$j]->{$field})) {
                        $arraux[$j][$field] = (is_object($this[$j]->{$field}) && get_parent_class($this[$j]->{$field}) == 'ActiveRecord')?$this[$j]->{$field}->getArray():$this[$j]->{$field};
                    }
                }
            }
        } else {
            foreach ($fields as $field) {
                if (isset($this->{$field})) {
                    $arraux[0][$field] = (is_object($this->{$field}) && get_parent_class($this->{$field}) == 'ActiveRecord')?$this->{$field}->getArray():$this->{$field};
                }
            }
        }

        return $arraux;
    }
    /**
     * Dumps the table data into a xml file
     */
    public function Dump() {
        $model    = $this->_ObjTable;
        $dom      = new DOMDocument('1.0', 'utf-8');
        $path     = defined('DUMPS_PATH')?DUMPS_PATH:INST_PATH.'migrations/dumps/';
        file_exists($path) || mkdir($path);
        $sroot    = $dom->appendChild(new DOMElement('table_'.$model));
        foreach ($this as $reg) {
            $root = $sroot->appendChild(new DOMElement($model));
            foreach (array_keys($this->_fields) as $field) {
                if (preg_match("(&|<|>)", $reg->{$field})) {
                    $value   = $dom->createCDATASection($reg->{$field});
                    $element = $root->appendChild(new DOMElement($field, ""));
                    $element->appendChild($value);
                } else {
                    $element = $root->appendChild(new DOMElement($field, $reg->{$field}));
                }
            }
        }
        file_put_contents($path.$model.'.xml', $dom->saveXML());
    }
    /**
     * Prepares the field to build the query before a mass insert from an xml dump data
     * @param DOMDocument $items
     * @return Generator
     */
    private function _prepareFromXML($items) {
        $row = [];
        $regenerate = false;
        empty($this->_insertionFields) && ($regenerate = true) && ($this->_insertionFields = []);
        for ($i = 0; $i < $items->length; $i++) {
            $xitem = $items->item($i);

            foreach (array_keys($this->_fields) as $field) {
                $item = $xitem->getElementsByTagName($field);
                if (is_object($item->item(0))) {
                    $regenerate && ($this->_insertionFields[] = "`{$field}`");
                    $row[$field] = "'{$item->item(0)->nodeValue}'";
                }
            }

            $regenerate = false;

            yield $row;
        }
    }
    /**
     * Loads a dump file (xml) into the database
     */
    public function LoadDump() {
        $doc  = new DOMDocument;
        $path = defined('DUMPS_PATH')?DUMPS_PATH:INST_PATH.'migrations/dumps/';
        if (file_exists($path.$this->_ObjTable.'.xml')) {
            $doc->load($path.$this->_ObjTable.'.xml');
            $items = $doc->getElementsByTagName($this->_ObjTable);
            $query = '';
            empty($this->_fields) && $this->_setInitialCols();
            foreach ($this->_prepareFromXML($items) as $row) {
                $query .= "(".imploder(',', $row)."),";
            }
            if (strlen($query) > 1) {
                $this->_sqlQuery = "INSERT INTO `{$this->_ObjTable}` (" . imploder(',', $this->_insertionFields) . ") VALUES ";
                $query = substr($query, 0, -1);
                $this->_sqlQuery .= $query;

                try {
                    $GLOBALS['Connection']->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
                    $GLOBALS['Connection']->beginTransaction();
                    $sh = $GLOBALS['Connection']->exec($this->_sqlQuery);
                    $GLOBALS['Connection']->commit();
                    $GLOBALS['Connection']->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
                } catch (PDOException $e) {
                    fwrite(STDERR,  $e->getMessage(). '. On : '.$this->_sqlQuery . PHP_EOL);
                    $GLOBALS['Connection']->rollback();
                    return false;
                } catch (Exception $e) {
                    fwrite(STDERR,  $e->getMessage(). '. On : '.$this->_sqlQuery . PHP_EOL);
                    $GLOBALS['Connection']->rollback();
                    return false;
                }
                fwrite(STDOUT, "Inserted {$sh} Regs.". PHP_EOL);
            } else {
                fwrite(STDOUT, "Inserted 0 Regs. Empty dump.". PHP_EOL);
            }
        }

        return true;
    }
    public function WriteSchema($tableName) {
        $createFile  = FALSE;
        $stringtoINI = '';
        $file        = INST_PATH.'migrations/Schema.ini';
        file_exists($file) or file_put_contents($file, '');
        if (!$schema = parse_ini_file($file, TRUE)) {
            $createFile = TRUE;
        }
        if ($createFile) {
            $stringtoINI .= "[$tableName] \n";
            $fp = fopen($file, "w+b");
            fwrite($fp, $stringtoINI);
            fclose($fp);
        } elseif (!in_array($tableName, $schema)) {
            $schema[$tableName] = "";
            $stringtoINI        = "";
            foreach ($schema as $table => $val) {
                $stringtoINI .= "[$table] \n";
            }
            $fp = fopen($file, "w+b");
            fwrite($fp, $stringtoINI);
            fclose($fp);
        }
    }
    public function getError() {
        return $this->_error;
    }
    public function counter() {
        return (integer) $this->_counter;
    }
    public function first() {
        return $this->_counter > 0?$this[0]:false;
    }
    public function last() {
        return $this->_counter > 0?$this[$this->counter()-1]:false;
    }
    public function _sqlQuery() {
        return $this->_sqlQuery;
    }
    public function _nativeType($field) {
        if (empty($this->_dataAttributes[$field]['native_type'])) {
            return null;
        }
        return $this->_dataAttributes[$field]['native_type'];
    }
    public function slice($start = 0, $length = 0) {
        empty($length) && ($length = $this->_counter);

        $end = $start+$length;

        $end > $this->_counter && ($end = $this->_counter);

        $name = get_class($this);
        $arr  = new $name();
        for ($i = $start; $i < $end; $i++) {
            $arr[] = $this[$i];
        }
        return $arr;
    }
    /**
     * Sets pagination for an Active Record Model
     * @param array $params
     * @return ActiveRecord
     */
    public function Paginate($params = NULL) {
        if (is_array($params) && sizeof($params) === 1 && !empty($params[0])) {
            $params = $params[0];
        }

        $fullquery = $GLOBALS['driver']->Select($params, $this->_ObjTable);
        $queryCount = $GLOBALS['driver']->RowCountOnQuery($fullquery['query']);
        $regs = $this->Find_by_SQL($queryCount);

        $request = parse_url(_FULL_URL);

        $per_page = empty($params['per_page'])? 10 : (int)$params['per_page'];
        isset($params['prevChar']) && ($this->_paginatePrevChar = $params['prevChar']);
        isset($params['nextChar']) && ($this->_paginateNextChar = $params['nextChar']);
        isset($params['forwardChar']) && ($this->_paginateLastChar = $params['forwardChar']);
        isset($params['backwardChar']) && ($this->_paginateFirstChar = $params['backwardChar']);

        $this->paginateURL = empty($params['url'])? "{$request['scheme']}://{$request['host']}{$request['path']}" : $params['url'];

        empty($params['varPageName']) or $this->PaginatePageVarName = $params['varPageName'];
        if (!empty($_GET[$this->PaginatePageVarName])) {
            $this->PaginatePageNumber = $params[$this->PaginatePageVarName] = $_GET[$this->PaginatePageVarName];
        }
        if (!empty($params[$this->PaginatePageVarName])) {
            $this->PaginatePageNumber = $params[$this->PaginatePageVarName];
        }
        $this->PaginatePageNumber = (int)$this->PaginatePageNumber;
        $start = ($this->PaginatePageNumber - 1) * $per_page;

        $params['limit'] = $start.",".$per_page;
        $data = $this->Find($params);

        $data->PaginateTotalItems = $regs->rows;
        $data->PaginateTotalPages = ceil($data->PaginateTotalItems/$per_page);
        return $data;
    }
    /**
     * Displays the proper html for the paginated model
     * @param array $params
     * @return string
     */
    public function WillPaginate($params = NULL) {
        if (is_array($params) && sizeof($params) === 1 && !empty($params[0])) {
            $params = $params[0];
        }

        $vars = [];
        $request = parse_url(_FULL_URL);
        empty($request['query']) || parse_str($request['query'], $vars);

        $str  = '';
        $tail = '';
        $i    = 1;

        if ($this->PaginatePageNumber > 1) {
            $vars[$this->PaginatePageVarName] = 1;
            $str .= "<a class=\"paginate paginate-page paginate-page-first\" href=\"{$this->paginateURL}?".http_build_query($vars)."\">{$this->_paginateFirstChar}</a>";
            $vars[$this->PaginatePageVarName] = $this->PaginatePageNumber - 1;
            $str .= "<a class=\"paginate paginate-page paginate-page-prev\" href=\"{$this->paginateURL}?".http_build_query($vars)."\">{$this->_paginatePrevChar}</a>";
        }
        $top = $this->PaginateTotalPages;
        if ($this->PaginateTotalPages > 10) {
            $top  = ($this->PaginatePageNumber-1)+10;
            if ($top > $this->PaginateTotalPages) {
                $top = $this->PaginateTotalPages;
            }

            $i = $top-10;
            if ($i < 1) {
                $i = 1;
            }
        }
        if ($this->PaginatePageNumber < $this->PaginateTotalPages) {
            $vars[$this->PaginatePageVarName] = $this->PaginatePageNumber + 1;
            $tail .= "<a class=\"paginate paginate-page paginate-page-next\" href=\"{$this->paginateURL}?".http_build_query($vars)."\">{$this->_paginateNextChar}</a>";
            $vars[$this->PaginatePageVarName] = $this->PaginateTotalPages;
            $tail .= "<a class=\"paginate paginate-page paginate-page-last\" href=\"{$this->paginateURL}?".http_build_query($vars)."\">{$this->_paginateLastChar}</a>";
        }
        for (; $i <= $top; $i++) {
            $vars[$this->PaginatePageVarName] = $i;
            $str .= "<a class=\"paginate paginate-page".($this->PaginatePageNumber == $i?" paginate-page-active":"")."\" href=\"{$this->paginateURL}?".http_build_query($vars)."\">{$i}</a>";
        }
        $str .= $tail;
        return $str;
    }

    /**
     * Creates an input depending on the type of field, will use a set of html elemnts defined
     * Example: $model->input_for('id', ['type'=>'hidden', 'name'=>'model_id', 'class'=>'class']);
     * @param array $attributes
     * @throws Exception
     * @return NULL|string
     */
    public function input_for($field, array $attributes = [], array $list = [], array $params = []) {
        $types = new stdClass();
        $types->input = 'input';
        $types->textarea = 'textarea';
        $types->select = 'select';
        $types->label = 'label';

        $_scaffoldFolder = INST_PATH.'scaffold/';

        if (empty($attributes['name'])) $attributes['name'] = Singulars(strtolower($this->_ObjTable)).'['.$field.']';
        if (empty($attributes['value'])) $attributes['value'] = "<?=\$this->data->{$field};?>";

        file_exists("{$_scaffoldFolder}/tags.json") and $types = json_decode(file_get_contents("{$_scaffoldFolder}/tags.json"));

        if ($field === 'id' and empty($attributes['type'])) $attributes['type'] = 'hidden';
        if (empty($attributes['type'])):
            $type = strtoupper($this->_nativeType($field));
            switch ($type):
                case 'INTEGER':
                case 'LONG':
                case 'STRING':
                case 'INT':
                case 'BIGINT':
                case 'VAR_CHAR':
                case 'VARCHAR':
                case 'FLOAT':
                case 'VAR_STRING';
                    $attributes['type'] = 'text';
                    break;
                case 'BLOB':
                case 'TEXT':
                    $attributes['type'] = 'textarea';
                    break;
            endswitch;
        endif;

        $doc = new DOMDocument('1.0');
        $input = null;
        $label = null;

        switch ($attributes['type']):
            case 'checkbox':
                if (!empty($this->{$field}) and empty($attributes['checked'])) $attributes['checked'] = 'checked';
            case 'text':
            case 'hidden':
                $input = $doc->createElement($types->input);
                break;
            case 'textarea':
                $input = $doc->createElement($types->textarea);
                $input->appendChild($doc->createTextnode($this->{$field}));
                break;
            case 'select':
                $input = $doc->createElement($types->textarea);
                $optionTag = null;

                foreach ($params['list'] as $value => $option):
                    $optionTag = $doc->createElement('option');
                    if ($this->{$field} == $value) $optionTag->setAttribute('selected', 'selected');
                    $optionTag->setAttribute('value', $value);
                    $optionTag->appendChild($doc->createTextNode($option));
                    $input->appendChild($optionTag);
                    $optionTag = null;
                endforeach;
            break;
        endswitch;
        if (!empty($types->label)):
            $label = $doc->createElement($types->label);
            $label->appendChild($doc->createTextNode($field));
            $doc->appendChild($label);
        endif;
        foreach($attributes as $attr => $value):
            $input->setAttribute($attr, $value);
        endforeach;

        $doc->appendChild($input);
        return $doc->saveHtml();
    }
    /**
     * @deprecated
     * @param string $params
     * @return string
     */
    public function form_for($params) {
        $string = '<form';
        $method = 'post';
        $action = '#';
        $name   = '';
        $id     = '';
        $html   = '';
        $name   = singulars(strtolower($this->_ObjTable));
        $action = !empty($params['action'])?$params['action']:INST_URI.strtolower($this->_ObjTable);
        if (!empty($params['html']) and is_array($params['html'])) {
            foreach ($params['html'] as $element => $value) {
                $html .= $element.'="'.$value.'" ';
            }
        }
        $html = trim($html);
        if (strlen($html) > 0) {
            $html = " {$html}";
        }

        $string .= " method=\"{$method}\" id=\"{$id}\" action=\"{$action}\" name=\"$name\"{$html}>";
        return $string;
    }
    public function getPK() {
        return $this->pk;
    }
}
class Vendor {
    private $_path = '';
    private $_vendors = null;

    public function __construct() {
        $this->_path = INST_PATH.'vendor/dumbophp/';
        $this->_vendors = new stdClass();
    }

    public function __get($var) {
        if (empty($this->_vendors->{$var})) {
            $lowerName = strtolower($var);
            $className = "DumboPHP{$var}";
            $file = "{$this->_path}dumbophp_{$lowerName}.php";

            require_once $file;
            $this->_vendors->{$var} = new $className();
        }

        return $this->_vendors->{$var};
    }

    public function __set($var, $value) {
        return null;
    }
}
/**
 *
 * @author Javier Serrano <rantes.javier@gmail.com>
 *
 */
abstract class Page extends Core_General_Class {
    public $exceptsBeforeFilter = [];
    public $Vendor = null;
    public $_outputContent = '';
    public $metaDescription = '';
    public $pageTitle = '';
    protected $layout = '';
    protected $render = NULL;
    protected $flash = '';
    protected $yield = '';
    protected $params = [];
    protected $controller = '';
    protected $action = '';
    protected $excepts_after_filter  = [];
    protected $excepts_after_render  = [];
    protected $excepts_before_render = [];
    protected $_exposeContent = true;
    protected $_data_ = [];
    protected $outputHtml = true;
    private $_respondToAJAX   = '';
    private $_canrespondtoajax= false;
    private $_preventLoad = false;

    public function __construct() {
        $this->Vendor = new Vendor();
    }

    public function __get($var) {
        $model = unCamelize($var);
        if (file_exists(INST_PATH.'app/models/'.$model.'.php')) {
            if (!class_exists($var)) {
                require INST_PATH.'app/models/'.$model.'.php';
            }
            $this->{$var} = new $var();
            return $this->{$var};
        }
    }

    public function _getController_() {
        return $this->controller;
    }

    public function _getAction_() {
        return $this->action;
    }

    public function _setController_($controller) {
        $this->controller = $controller;
    }

    public function _setAction_($action) {
        $this->action = $action;
    }

    public function display() {
        $renderPage  = TRUE;
        empty($this->action) and ($this->action = _ACTION);
        empty($this->controller) and ($this->controller = _CONTROLLER);
        if (property_exists($this, 'noTemplate') and in_array($this->action, $this->noTemplate)) $renderPage = FALSE;
        if ($this->canRespondToAJAX()) {
            if (!headers_sent()) {
                header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
                header('Content-type: "application/json; charset=utf-8"');
                header('ETag: 123');
                header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
                header('Pragma: no-cache');
                header('X-Powered-By: "DUMBO PHP - LA TUTECA"');
            }

            $this->_outputContent = $this->respondToAJAX();
            if (!is_string($this->_outputContent)) {
                throw new Exception('The output content for JSON should be an string.', HTTP_500);
            }

            if (!empty($this->params['callback'])) $this->_outputContent = "({$this->_outputContent}));";

            $renderPage   = false;
            $this->layout = '';
        } else {
            if (isset($this->render) and is_array($this->render)) {
                if (isset($this->render['action']) && $this->render['action'] === false) {
                    $this->yield = '';
                    $renderPage  = false;
                } elseif (!empty($this->render['file'])) {
                    $view = $this->render['file'];
                } elseif (!empty($this->render['partial'])) {
                    $view = $this->controller.'/_'.$this->render['partial'].'.phtml';
                } elseif (!empty($this->render['text'])) {
                    $this->yield = $this->render['text'];
                    $renderPage  = false;
                    $view = null;
                } elseif (!empty($this->render['action'])) {
                    $view = $this->controller.'/'.$this->render['action'].'.phtml';
                } else {
                    $view = $this->controller.'/'.$this->action.'.phtml';
                }
            } else {
                $view = $this->controller.'/'.$this->action.'.phtml';
            }
            if (isset($this->render['layout']) && $this->render['layout'] !== false) $this->layout = $this->render['layout'];

            if (isset($this->render['layout']) && $this->render['layout'] === false) $this->layout = '';

            $viewsFolder = INST_PATH.'app/views/';
            $this->_outputContent = $this->yield;

            if ($renderPage) {
                if (strlen($this->layout) > 0) {
                    ob_start(null, 0, PHP_OUTPUT_HANDLER_STDFLAGS);
                    include_once ($viewsFolder.$view);
                    $this->yield = ob_get_clean();
                    $this->_outputContent = $this->yield;
                } elseif (!empty($view)) {
                    include_once ($viewsFolder.$view);
                }
            }

            if (strlen($this->layout) > 0) {
                ob_start(null, 0, PHP_OUTPUT_HANDLER_STDFLAGS);
                include_once ($viewsFolder.$this->layout.".phtml");
                $this->_outputContent = ob_get_clean();
            }
        }
        if ($this->_exposeContent) echo $this->_outputContent;
    }
    /**
     * Sets or retrieve if should or not to load the view/action
     * @param boolean $prevent
     * @return boolean
     */
    public function PreventLoad($prevent = null) {
        $prevent !== null && ($this->_preventLoad = !!$prevent);

        return $this->_preventLoad;
    }
    public function LoadHelper($helper = NULL) {
        if (isset($helper) and is_array($helper)) {
            foreach ($helper as $file) {
                require_once (INST_PATH."app/helpers/{$file}_Helper.php");
            }
        } elseif (isset($helper) and is_string($helper)) {
            require_once (INST_PATH."app/helpers/{$helper}_Helper.php");
        }
    }
    public function params($params = NULL) {
        $params !== null && ($this->params = $params);
        return $this->params;
    }
    public function respondToAJAX($val = null) {
        if ($val === null):
            return $this->_respondToAJAX;
        else :
            $this->_respondToAJAX    = $val;
            $this->_canrespondtoajax = true;
        endif;
    }
    public function canRespondToAJAX() {
        return $this->_canrespondtoajax;
    }
}
/**
 * Implements whole Migrations actions
 * @author rantes
 *
 */
abstract class Migrations extends Core_General_Class {
    private $_table = '';
    public $_fields;

    private final function connect() {
        if (empty($GLOBALS['Connection'])) {
            $GLOBALS['Connection'] = new Connection();
            require_once imploder(DIRECTORY_SEPARATOR, [dirname(__FILE__),'../lib', 'db_drivers', $GLOBALS['Connection']->engine.'.php']);
        }

        if (empty($GLOBALS['driver'])) {
            $driver = $GLOBALS['Connection']->engine.'Driver';
            $GLOBALS['driver']= new $driver();
        }
    }

    private final function _runQuery($query) {
        echo 'Running query: ', $query, PHP_EOL;
        if ($GLOBALS['Connection']->exec($query) === false) {
            fwrite(STDERR, $GLOBALS['Connection']->errorInfo() . PHP_EOL);
        }
    }

    public final function __construct() {
        $this->_table = Plurals(unCamelize(substr(get_class($this), 6)));

        $this->_init_();
    }

    public function __destruct() {}

    public function _init_() {}

    public function up() {
        echo 'Nothing to do.';
    }

    public function down() {
        echo 'Nothing to do.';
    }

    public function alter() {
        echo 'Nothing to do.';
    }

    public function Reset() {
        $this->down();
        $this->up();
    }

    public function Run() {
        $this->up();
        $this->alter();
    }

    public function getDefinitions() {
        return $this->_fields;
    }

    public function getFields() {
        $fields = [];

        for ($i = 0; $i < sizeof($this->_fields); $i++) {
            $fields[] = $this->_fields[$i]['field'];
        }

        return $fields;
    }
    /**
     *
     * @param array $table
     */
    protected function Create_Table() {
        $this->connect();
        $query = $GLOBALS['driver']->CreateTable($this->_table, $this->_fields);
        empty($query) || $this->_runQuery($query);
    }

    protected function Drop_Table() {
        $this->connect();
        $query = $GLOBALS['driver']->DropTable($this->_table);

        empty($query) || $this->_runQuery($query);
    }
    /**
     * Adds a column to the table
     * @param array $params Array with the field and attributes
     * @example
     * $this->AddColumn(['field' => 'additional', 'type'=>'INT', 'null'=>'false']);
     */
    protected function Add_Column(array $params) {
        $this->connect();
        $query = $GLOBALS['driver']->validateField($this->_table, $params['field']);
        $res = $GLOBALS['Connection']->validateField($query, $params['field']);

        if ($res < 1) {
            $query = $GLOBALS['driver']->AddColumn($this->_table, $params);
            $this->_runQuery($query);
        }

    }
    /**
     * Add index to the table
     * @param array $params Array with the index attributes
     * @throws Exception Each attribute is mandatory
     */
    protected function Add_Index(array $params) {
        $this->connect();

        if (empty($params['name'])) {
            throw new Exception("name param can not be empty", 1);
        }

        if (empty($params['fields'])) {
            throw new Exception("fields param can not be empty", 1);
        }

        if (!is_array($params['fields'])) {
            throw new Exception("fields param must be an array", 1);
        }

        $query = $GLOBALS['driver']->AddIndex($this->_table, $params['name'], imploder(',', $params['fields']));

        empty($query) || $this->_runQuery($query);
    }
    /**
     * Sets a single index into the table
     * @param string $field
     */
    protected function Add_Single_Index($field) {
        $this->connect();
        $query = $GLOBALS['driver']->AddSingleIndex($this->_table, $field);

        empty($query) || $this->_runQuery($query);
    }
    /**
     * Attempts to remove a single index in the table
     * @param string $index
     */
    protected function Remove_Index($index) {
        $this->connect();
        $query = $GLOBALS['driver']->RemoveIndex($this->_table, $index);

        empty($query) || $this->_runQuery($query);
    }
    /**
     * Attempts to remove all indexes in table
     *
     * @return void
     */
    protected function Remove_All_indexes() {
        $this->connect();
        $query = $GLOBALS['driver']->GetAllIndexes($this->_table);
        $indexes = $this->_runQuery($query);
        foreach ($indexes as $index) {
            if ($index['INDEX_NAME'] !== 'PRIMARY') {
                $query = $GLOBALS['driver']->RemoveIndex($this->_table, $index['INDEX_NAME']);
                empty($query) || $this->_runQuery($query);
            }
        }

    }
    /**
     * Set primary key in the table
     * @param string $field
     * @param boolean $autoIncrement
     * @throws Exception
     */
    protected function Add_Primary($field, $autoIncrement = false) {
        $this->connect();

        if (empty($field)) throw new Exception("fields param can not be empty", 1);

        if (!is_string($field)) throw new Exception("fields param must be a string", 1);

        $query = $GLOBALS['driver']->AddPrimaryKey($this->_table, $field);

        if (!empty($query)) {
            $this->_runQuery($query);
            $autoIncrement && $this->Alter_Column(['field'=>$field, 'type'=>'AUTO_INCREMENT']);
        }
    }
    /**
     * Change column definitions
     * @param array $params Array with the column attributes
     */
    protected function Alter_Column(array $params) {
        $this->connect();
        $query = $GLOBALS['driver']->validateField($this->_table, $params['field']);
        if ($GLOBALS['Connection']->validateField($query) > 0) {
            $query = $GLOBALS['driver']->AlterColumn($this->_table, $params);
            $this->_runQuery($query);
        }
    }
    /**
     * Deletes a column in the table
     * @param string $field
     * @throws Exception If the param is not a string
     */
    protected function Remove_Column($field) {
        if (!is_string($field)) {
            throw new Exception('fields param must be a string', 1);
        }

        $this->connect();
        $query = $GLOBALS['driver']->RemoveColumn($this->_table, $field);

        empty($query) || $this->_runQuery($query);
    }
}

class index {
    use DumboSysConfig;
    public $page = null;
    public function __construct() {
        http_response_code(HTTP_200);
        if (!empty($_GET['url'])) {
            $_GET['url'][0] === '/' && ($_GET['url'] = substr($_GET['url'], 1));
            $request = explode('/', $_GET['url']);
            unset($_GET['url']);
        }

        $path = INST_PATH.'app/controllers/';

        empty($request[0]) && ($request[0] = DEF_CONTROLLER);
        empty($request[1]) && ($request[1] = DEF_ACTION);

        $controllerFile = $request[0]."_controller.php";
        $controller = array_shift($request);
        $action = array_shift($request);

        foreach ($request as $key => $value) {
            if (empty($value)) unset($request[$key]);
        }

        $params = [];

        if (sizeof($request) === 1 and !strstr($request[0], "=") and is_numeric($request[0])) {
            $params['id'] = $request[0];
        } elseif (sizeof($request) > 0 and strstr($request[0], "=")) {
            while (null !== ($r = array_shift($request))) {
                $p = explode('=', $r);
                if (isset($p[1])) {
                    $params[$p[0]] = $p[1];
                } else {
                    $params[] = $p[0];
                }
            }
        } elseif (sizeof($request) > 0) {
            while (null !== ($varParam = array_shift($request))) {
                $params[] = $varParam;
            }
        }

        $params = is_array($params) ? array_merge($params, $_GET) : $_GET;

        if (defined('SITE_STATUS') and SITE_STATUS == 'MAINTENANCE') {
            $urlToLand = explode('/', LANDING_PAGE);
            $replace   = false;
            if (LANDING_REPLACE == 'ALL') {
                $replace = true;
            } else {
                $locations = explode(',', LANDING_REPLACE);
                if (in_array($controller.'/'.$action, $locations)) {
                    $replace = true;
                }
            }
            if ($replace) {
                $controller     = $urlToLand[0];
                $action         = $urlToLand[1];
                $controllerFile = $controller.'_controller.php';
            }
        }
        $canGo = true;
        if (!file_exists($path.$controllerFile) && defined('USE_ALTER_URL') && USE_ALTER_URL) {
            $params['alter_controller'] = $controller;
            $params['alter_action']     = $action;
            $parts                      = explode('/', ALTER_URL_CONTROLLER_ACTION);
            $controller                 = $parts[0];
            $action                     = $parts[1];
            $controllerFile             = $controller.'_controller.php';
        } elseif (!file_exists($path.$controllerFile)) {
            $canGo = false;
            http_response_code(HTTP_404);
            echo 'Missing Controller';
        }
        defined('_CONTROLLER') || define('_CONTROLLER', $controller);
        defined('_ACTION') || define('_ACTION', $action);
        $queryparams = http_build_query($params);
        empty($queryparams) || ($queryparams = "?{$queryparams}");
        defined('_FULL_URL') || define('_FULL_URL', INST_URI."{$controller}/{$action}/{$queryparams}");

        if ($canGo) {
            $classPage = Camelize($controller)."Controller";
            class_exists($classPage) || require_once ($path.$controllerFile);
            $this->page = new $classPage();
            $this->page->params($params);
            $this->page->_setAction_($action);
            $this->page->_setController_($controller);
            //loads of helpers
            if (isset($this->page->helper) and sizeof($this->page->helper) > 0) {
                $this->page->LoadHelper($this->page->helper);
            }
            //before filter, executed before the action execution
            if (method_exists($this->page, "before_filter")) {
                $actionsToExclude = $controllersToExclude = [];
                if (!empty($this->page->exceptsBeforeFilter) && is_array($this->page->exceptsBeforeFilter)) {
                    if (!empty($this->page->exceptsBeforeFilter['actions']) && is_string($this->page->exceptsBeforeFilter['actions'])) {
                        $actionsToExclude = explode(',', $this->page->exceptsBeforeFilter['actions']);
                        foreach ($actionsToExclude as $index => $act) {
                            $actionsToExclude[$index] = trim($act);
                        }
                    }
                    if (!empty($this->page->exceptsBeforeFilter['controllers']) && is_string($this->page->exceptsBeforeFilter['controllers'])) {
                        $controllersToExclude = explode(',', $this->page->exceptsBeforeFilter['controllers']);
                        foreach ($controllersToExclude as $index => $cont) {
                            $controllersToExclude[$index] = trim($cont);
                        }
                    }
                }
                if (!in_array($controller, $controllersToExclude) && !in_array($action, $actionsToExclude)) {
                    $this->page->before_filter();
                }
            }
            $action = $this->page->_getAction_();
            if (method_exists($this->page, $action."Action")) {
                if(!$this->page->PreventLoad()){
                    $actionToRun = "{$action}Action";
                    $this->page->{$actionToRun}();
                    //before render, executed after the action execution and before the data renderize
                    if (method_exists($this->page, "before_render")) {
                        $actionsToExclude = $controllersToExclude = [];
                        if (!empty($this->page->excepts_before_render) && is_array($this->page->excepts_before_render)) {
                            if (!empty($this->page->excepts_before_render['actions']) && is_string($this->page->excepts_before_render['actions'])) {
                                $actionsToExclude = explode(',', $this->page->excepts_before_render['actions']);
                                foreach ($actionsToExclude as $index => $act) {
                                    $actionsToExclude[$index] = trim($act);
                                }
                            }
                            if (!empty($this->page->excepts_before_render['controllers']) && is_string($this->page->excepts_before_render['controllers'])) {
                                $controllersToExclude = explode(',', $this->page->excepts_before_render['controllers']);
                                foreach ($controllersToExclude as $index => $cont) {
                                    $controllersToExclude[$index] = trim($cont);
                                }
                            }
                        }
                        if (!in_array($controller, $controllersToExclude) && !in_array($action, $actionsToExclude)) {
                            $this->page->before_render();
                        }
                    }

                    $this->page->display();
                    if (method_exists($this->page, "after_render")) {
                        $actionsToExclude = $controllersToExclude = [];
                        if (!empty($this->page->excepts_after_render) && is_array($this->page->excepts_after_render)) {
                            if (!empty($this->page->excepts_after_render['actions']) && is_string($this->page->excepts_after_render['actions'])) {
                                $actionsToExclude = explode(',', $this->page->excepts_after_render['actions']);
                                foreach ($actionsToExclude as $index => $act) {
                                    $actionsToExclude[$index] = trim($act);
                                }
                            }
                            if (!empty($this->page->excepts_after_render['controllers']) && is_string($this->page->excepts_after_render['controllers'])) {
                                $controllersToExclude = explode(',', $this->page->excepts_after_render['controllers']);
                                foreach ($controllersToExclude as $index => $cont) {
                                    $controllersToExclude[$index] = trim($cont);
                                }
                            }
                        }
                        if (!in_array($controller, $controllersToExclude) && !in_array($action, $actionsToExclude)) {
                            $this->page->after_render();
                        }
                    }
                }
            } else {
                http_response_code(HTTP_404);
                echo 'Missing Action';
            }
        }
    }

    public function __destruct() {
        $GLOBALS['Connection'] = null;
    }
}
?>
