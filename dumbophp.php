<?php
$in_shell = false;
if (php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR']) && !empty($argv)) {
    parse_str(implode('&', array_slice($argv, 1)), $_GET);
    $in_shell = true;
}
defined('_IN_SHELL_') || define('_IN_SHELL_', $in_shell);

for ($i = 1; $i <= 5; $i++) {
    for ($j = 0; $j <= 10; $j++) {
        $code = ($i*100)+$j;
        defined('HTTP_'.$code) || define('HTTP_'.$code, $code);
    }
}
/**
 * Implements the functionalities for translating
 * @author rantes
 *
 */
final class IrregularNouns {
    public $singular = array();
    public $plural   = array();
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

    $IN = new IrregularNouns();
    if (in_array($string, $IN->singular)) {
        $key     = array_search($string, $IN->singular);
        $strconv = $IN->plural[$key];
    } elseif (in_array($string, $IN->plural)) {
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

    $IN      = new IrregularNouns();
    $strconv = '';
    if (in_array($string, $IN->plural)) {
        $key     = array_search($string, $IN->plural);
        $strconv = $IN->singular[$key];
    } elseif (substr($string, -3, 3) == 'ies') {
        $strconv = str_replace('ies', 'y', $string);
    } elseif (substr($string, -2, 2) == 'es') {
        $test = substr($string, 0, -2);
        if (substr($test, -1, 1) == 'x' or substr($test, -1, 1) == 's' or substr($test, -2, 2) == 'ch' or substr($test, -2, 2) == 'sh' or substr($test, -2, 2) == 'ss') {
            $strconv = substr($string, 0, -2);
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
 * Turns an Active Record resulset object into a comma separated list
 * @param array $arr
 * @param object $obj
 * @return string
 */
function ToList(&$arr, &$obj = NULL) {
    if (isset($obj) and is_object($obj) and get_parent_class($obj) == 'ActiveRecord') {
        $arr = $obj->getArray();
    }

    return implode(',', $arr);
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

    $specialChars = array("/\xc0/", "/\xc1/", "/\xc2/", "/\xc3/", "/\xc4/", "/\xc5/", "/\xc6/", "/\xc7/", "/\xc8/", "/\xc9/", "/\xca/", "/\xcb/", "/\xcc/", "/\xcd/", "/\xce/", "/\xcf/", "/\xd0/", "/\xd1/",
        "/\xd2/", "/\xd3/", "/\xd4/", "/\xd5/", "/\xd6/", "/\xd7/", "/\xd8/", "/\xd9/", "/\xda/", "/\xdb/", "/\xdc/", "/\xdd/", "/\xde/", "/\xdf/", "/\xe0/", "/\xe1/", "/\xe2/", "/\xe3/", "/\xe4/", "/\xe5/", "/\xe6/",
        "/\xe7/", "/\xe8/", "/\xe9/", "/\xea/", "/\xeb/", "/\xec/", "/\xed/", "/\xee/", "/\xef/", "/\xf0/", "/\xf1/", "/\xf2/", "/\xf3/", "/\xf4/", "/\xf5/", "/\xf6/", "/\xf7/", "/\xf8/", "/\xf9/", "/\xfa/", "/\xfb/", "/\xfc/", "/\xfd/", "/\xfe/", "/\xff/");
    $normalChars = array('A', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N',
        'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'B', 'Ss', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'd', 'n',
        'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'b', 'y');
    $string = preg_replace($specialChars, $normalChars, $string);
    $string = strtolower($string);
    $string = preg_replace('/[\s]+/', '-', $string);
    $string = preg_replace('/[^a-zA-Z0-9-]/', '', $string);
    return $string;
}
/**
 * Equivalent to htmlentities
 * @deprecated
 * @param string $string
 * @return string
 */
function cleanASCII($string) {
    $specialChars = array("/\xc0/", "/\xc1/", "/\xc2/", "/\xc3/", "/\xc4/", "/\xc5/", "/\xc6/", "/\xc7/", "/\xc8/", "/\xc9/", "/\xca/", "/\xcb/", "/\xcc/", "/\xcd/", "/\xce/", "/\xcf/", "/\xd0/", "/\xd1/",
        "/\xd2/", "/\xd3/", "/\xd4/", "/\xd5/", "/\xd6/", "/\xd7/", "/\xd8/", "/\xd9/", "/\xda/", "/\xdb/", "/\xdc/", "/\xdd/", "/\xde/", "/\xdf/", "/\xe0/", "/\xe1/", "/\xe2/", "/\xe3/", "/\xe4/", "/\xe5/", "/\xe6/",
        "/\xe7/", "/\xe8/", "/\xe9/", "/\xea/", "/\xeb/", "/\xec/", "/\xed/", "/\xee/", "/\xef/", "/\xf0/", "/\xf1/", "/\xf2/", "/\xf3/", "/\xf4/", "/\xf5/", "/\xf6/", "/\xf7/", "/\xf8/", "/\xf9/", "/\xfa/", "/\xfb/", "/\xfc/", "/\xfd/", "/\xfe/", "/\xff/");
    $normalChars = array('&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring', '&AElig;', '&Ccedil;', '&Egrave;', '&Eacute;', '&Ecirc;', '&Euml;', '&Igrave;', '&Iacute;', '&Icirc;', '&Iuml;', '&ETH;', '&Ntilde;',
        '&Ograve;', '&Oacute;', '&Ocirc;', '&Otilde;', '&Ouml;', '&Oslash;', 'U', 'U', 'U', 'U', 'Y', 'B', 'Ss', '&agrave;', '&aacute;', '&acirc;', '&atilde;', '&auml;', '&aring', '&aElig;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;', '&iuml;', '&eth;', '&ntilde;',
        '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&oslash;', 'u', 'u', 'u', 'u', 'y', 'b', 'y');
    return preg_replace($specialChars, $normalChars, $string);
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
 * Returns an array with the attributes of the current active record
 * @deprecated
 * @param unknown $arr
 * @param unknown $obj
 * @return string[]
 */
function toOptions(&$arr, &$obj = NULL) {
    $arr1   = array();
    $arraux = array();
    if (isset($obj) and is_object($obj)) {
        $arr = $obj->getArray();
    }
    foreach ($arr as $element) {
        $arraux = array();
        foreach ($element as $value) {
            $arraux[] = (string) $value;
        }
        $arr1[$arraux[0]] = $arraux[1];
    }
    return $arr1;
}
/**
 * @deprecated
 * @param string $arr
 * @param unknown $obj
 * @return number
 */
function checkBoxToInt(&$arr, &$obj = NULL) {
    if ($arr !== NULL and $arr == 'on') {
        return 1;
    }

    return 0;
}
function end_form_for() {
    return '</form>';
}
function image_tag($params, &$obj = NULL) {
    $rute   = 'images/';
    $params = ($obj === NULL)?$params:$params[0];
    if (is_array($params)):
    if (isset($params['image'])):
    if (isset($params['rute'])):
    if ($params['rute'] == 'absolute'):
    $rute = INST_URI.$rute;
     else :
    $rute = '/'.$rute;
    endif;
     else :
    $rute = '/'.$rute;
    endif;
    $params['image'] = $params['image'];
    $html_options    = '';
    if (isset($params['html'])):
    foreach ($params['html'] as $attr => $value):
    $html_options .= " $attr=\"$value\"";
    endforeach;
    endif;
    if (isset($params['alt'])) {$html_options .= ' alt="'.$params['alt'].'"';
    }

    if (isset($params['border'])) {$html_options .= ' border="'.$params['border'].'"';
    }

    return '<img src="'.INST_URI.'images/'.$params['image'].'" '.$html_options.' />';
    endif;
     elseif (is_string($params)):
    $image = $params;
    return '<img src="'.INST_URI.'images/'.$image.'" />';
    endif;
}
function stylesheet_link_tag($params, &$obj = NULL) {
    $css                                                         = NULL;
    if (!is_array($params) and is_string($params)) {$css         = $params;
    } elseif (isset($params[0]) and sizeof($params) === 1) {$css = $params[0];
    } elseif (isset($params['css'])) {$css                       = $params['css'];
    }

    if ($css === NULL):
    throw new Exception('Must specify a css file');
     elseif (!file_exists(INST_PATH.'app/webroot/css/'.$css)):
    throw new Exception('The file specified do not exists: '.INST_PATH.'app/webroot/css/'.$css);
     else :
    $media = 'all';
    $type  = 'text/css';
    $rel   = 'stylesheet';
    if (is_array($params)):
    if (isset($params['type'])) {$type = $params['type'];
    }

    if (isset($params['rel'])) {$rel = $params['rel'];
    }

    if (isset($params['media'])) {$media = $params['media'];
    }

    endif;
    $css .= '?'.time();
    return "<link href=\"".INST_URI."css/$css\" type=\"$type\" rel=\"$rel\" media=\"$media\"  />";
    endif;
}
function link_to($params = array(), &$obj = NULL) {
    $params       = ($obj === NULL)?$params:$params[0];
    $link         = '';
    $content      = '';
    $html_options = '';
    $action       = _ACTION;
    $controller   = _CONTROLLER;
    if (isset($params)):
    if (is_string($params) and strlen($params) > 0):
    $content = $params;
     elseif (is_array($params)):
    if (isset($params['controller'])):
    $controller = $params['controller'];
    unset($params['controller']);
    $link = INST_URI.$controller.'/';
    endif;
    if (isset($params['action'])):
    $action = $params['action'];
    unset($params['action']);
    $link .= $action;
    endif;
    if (isset($params['url'])):
    if (is_string($params['url'])):
    $link = $params['url'];
    endif;
    unset($params['url']);
    endif;
    if (isset($params['params'])):
    $link .= '/'.$params['params'];
    endif;
    if (isset($params[0])):
    $content = $params[0];
    unset($params[0]);
    endif;
    if (isset($params['html'])):
    foreach ($params['html'] as $attr => $value) {
        $html_options .= " $attr=\"$value\"";
    }
    unset($params['html']);
    endif;
    if (sizeof($params) > 0 and !is_array($params)):
    if (sizeof($params) === 1):
    list($var) = $params;
    if (key($params) == 'id'):
    $link .= "/$var";
     else :
    $link .= "/?".key($params)."=".$var;
    endif;
     else :
    $link .= "/?";
    foreach ($params as $var => $val) {
        $link .= "$var=$val&";
    }
    $link = substr($link, 0, -1);
    endif;
    endif;
    endif;
    if (strlen($link) > 0) {$link = 'href="'.$link.'"';
    }

    return "<a ".$link." $html_options>$content</a>";
    endif;
}
function javascript_include_tag($params, &$obj = NULL) {
    $js     = '';
    $params = ($obj === NULL)?$params:$params[0];
    if (isset($params) or $params != NULL):
    if (is_string($params) and strlen($params) > 0):
    preg_match("@plugins/[.]*@U", $params, $arr);
    if (!empty($arr[0]) and $arr[0] == 'plugins/'):
    $js = INST_URI.$params.'.js';
     else :
    $js = INST_URI."js/".$params.'.js';
    endif;
    return "<script type=\"text/javascript\" language=\"javascript\" src=\"$js\"></script>";
     elseif (is_array($params) and sizeof($params) > 0):
    $string = '';
    foreach ($params as $file) {
        preg_match("@plugins/[.]*@U", $file, $arr);
        if (!empty($arr[0]) and $arr[0] == 'plugins/'):
        $js = INST_URI.$file.'.js';
         else :
        $js = INST_URI."js/".$file.'.js';
        endif;
        $string .= "<script type=\"text/javascript\"  src=\"$js\"></script>";
    }
    return $string;
     else :
    throw new Exception("Must give a valid string for file name.");
    return NULL;
    endif;
     else :
    throw new Exception("Must to give a file name.");
    return NULL;
    endif;
}
class Connection extends PDO {
    public $_settings = null;
    public $engine = null;

    function __construct($file = 'config/db_settings.ini') {
        empty($GLOBALS['env']) && ($GLOBALS['env'] = 'production');

        if (!$this->_settings = parse_ini_file($file, TRUE)) throw new exception('Unable to open ' . $file . '.');

        $this->_settings = $this->_settings[$GLOBALS['env']];
        $this->engine = $this->_settings['driver'];

        switch ($this->engine) {
            case 'firebird':
                $dsn = 'firebird:dbname='.$this->_settings['host'].'/'.$this->_settings['port'].':'.$this->_settings['schema'];
            break;
            case 'sqlite':
            case 'sqlite2':
                if($this->_settings['schema'] === 'memory'){
                    $dsn = $this->engine.'::memory:';
                } else {
                    $dsn = $this->engine.':'.$this->_settings['schema'];
                }
            break;
            default:
                $host = ':host=' . $this->_settings['host'].
                        ';port=' . $this->_settings['port'];

                if (!empty($this->_settings['unix_socket'])) {
                    $host = ':unix_socket=' . $this->_settings['unix_socket'];
                }

                $dsn = $this->engine . $host .
                ';dbname=' . $this->_settings['schema'] .
                ((!empty($this->_settings['dialect'])) ? (';dialect=' . $this->_settings['dialect']) : '') .
                ((!empty($this->_settings['charset'])) ? (';charset=' . $this->_settings['charset']) : '');
            break;
        }
        empty($this->_settings['username']) and $this->_settings['username'] = null;
        empty($this->_settings['password']) and $this->_settings['password'] = null;
        parent::__construct($dsn, $this->_settings['username'], $this->_settings['password'],array(PDO::MYSQL_ATTR_LOCAL_INFILE => true,PDO::ATTR_PERSISTENT => true));
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}

class Errors {
    private $actived  = FALSE;
    private $messages = array();
    private $counter  = 0;
    public function add($params = NULL) {
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
        $strmes = '';
        foreach ($this->messages as $field => $messages) {
            foreach ($messages as $message) {
                $strmes .= "\t".$message['message']."\n";
            }
        }
        return $strmes;
    }
    public function isActived() {
        return $this->actived;
    }
    public function errCodes() {
        $errorsCodes = array();
        foreach ($this->messages as $field => $messages) {
            foreach ($messages as $message) {
                $errorCodes[] = $message['code'];
            }
        }
        return $errorCodes;
    }
    public function errFields() {
        $errorsFields = array();
        foreach ($this->messages as $field => $messages) {
            $errorFields[] = $field;
        }
        return $errorFields;
    }
    public function hasErrorCode($code = NULL) {
        return in_array($code, $this->errCodes());
    }
}
abstract class Core_General_Class extends ArrayObject {
    public function __call($ClassName, $val = NULL) {
        $field         = Singulars(strtolower($ClassName));
        $classFromCall = Camelize($ClassName);
        $conditions    = '';
        $params        = array();
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
            $conditions = "`".$prefix."_id`='".$this->id."'";
            if (method_exists($obj1, 'Find')) {
                if ($classFromCall == get_class($this) and in_array($ClassName, $this->has_many_and_belongs_to)) {
                    $conditions = ($way == 'up')?"`id`='".$this->{$foreign} ."'":$conditions;
                } elseif (in_array($ClassName, $this->belongs_to)) {
                    $conditions = "`id`='".$this->{$foreign} ."'";
                }
                $params['conditions'] = empty($params['conditions'])?$conditions:' AND '.$conditions;
                return ($conditions !== NULL)?$obj1->Find($params):$obj1->Niu();
            }
            return NULL;
        } elseif (preg_match('/Find_by_/', $ClassName)) {
            $nustring = str_replace("Find_by_", '', $ClassName);
            return $this->Find(array('conditions' => $nustring."='".$val[0]."'"));
        } else {
            return $ClassName($val, $this);
        }
    }
}
defined('CAN_USE_MEMCACHED') or define('CAN_USE_MEMCACHED', false);
if (CAN_USE_MEMCACHED) {
    $GLOBALS['memcached'] = new Memcached();
    defined('MEMCACHED_HOST') or define('MEMCACHED_HOST', 'localhost');
    defined('MEMCACHED_PORT') or define('MEMCACHED_PORT', '11211');
    $GLOBALS['memcached']->addServer(MEMCACHED_HOST, MEMCACHED_PORT);
}
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
abstract class ActiveRecord extends Core_General_Class {
    public $PaginatePageVarName = 'page';
    public $PaginateTotalItems  = 0;
    public $PaginateTotalPages  = 0;
    public $PaginatePageNumber  = 1;
    public $paginateURL         = '/';
    public $driver              = NULL;
    public $_error              = NULL;
    public $_sqlQuery           = '';
    public $candump             = true;
    public $id                  = null;
    public $created_at          = 0;
    public $updated_at          = 0;
    protected $_ObjTable;
    protected $_singularName;
    protected $_counter                = 0;
    protected $has_many                = array();
    protected $has_one                 = array();
    protected $belongs_to              = array();
    protected $has_many_and_belongs_to = array();
    protected $validate                = array();
    protected $before_insert           = array();
    protected $after_insert            = array();
    protected $after_find              = array();
    protected $before_find             = array();
    protected $after_save              = array();
    protected $before_save             = array();
    protected $after_delete            = array();
    protected $before_delete           = array();
    protected $dependents              = '';
    protected $_dataAttributes         = array();
    protected $_params                 = array('fields' => '*', 'conditions' => '');
    protected $pk                      = 'id';
    protected $escapeField             = array();
    private $engine                    = 'mysql';
    protected $_fields                 = array();

    public function _init_() {}

    final public function __construct() {
        if (empty($this->_ObjTable)) {
            $className       = unCamelize(get_class($this));
            $words           = explode("_", $className);
            $i               = sizeof($words)-1;
            $words[$i]       = Plurals($words[$i]);
            $this->_ObjTable = implode("_", $words);
        }
        defined('AUTO_AUDITS') or define('AUTO_AUDITS', true);

        if (empty($GLOBALS['Connection'])) {
            $GLOBALS['Connection'] = new Connection(INST_PATH.'config/db_settings.ini');
            require_once dirname(__FILE__).'/lib/db_drivers/'.$GLOBALS['Connection']->engine.'.php';
        }

        if ($this->driver === null) {
            $driver = $GLOBALS['Connection']->engine.'Driver';
            $this->driver = new $driver();
        }

        $this->driver->tableName = $this->_ObjTable;
        $this->driver->pk        = $this->pk;
        $this->_error            = new Errors;
        $this->_init_();
    }
    private function _setMemcacheKey($key) {
        $res = $GLOBALS['memcached']->get($this->_ObjTable);
        ($GLOBALS['memcached']->getResultCode() === 0 && is_array($res)) || ($res = array());
        in_array($key, $res) || array_push($res, $key);
        $GLOBALS['memcached']->set($this->_ObjTable, $res);
    }
    private function _refreshCache() {
        $res = $GLOBALS['memcached']->get($this->_ObjTable);
        ($GLOBALS['memcached']->getResultCode() === 0 && is_array($res)) || ($res = array());
        foreach ($res as $key) {
            $GLOBALS['memcached']->delete($key);
        }
    }
    /**
     * Getter for the fields taken from the query or table
     * @return array Fields of the current Active Record Object
     */
    public function GetFields() {
        return $this->_fields;
    }
    /**
     * Getter of the current
     */
    public function getValues() {
        $data = array();
        foreach ($this->_fields as $field => $cast) {
            $data[$field] = $this->{$field};
        }
        return $data;
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
    protected function getData($query) {
        $result = array();
        foreach ($this as $i => $val) {
            $this[$i] = null;
            $this->offsetUnset($i);
        }
        $j    = 0;
        $regs = $GLOBALS['Connection']->query($query);
        is_object($regs) or die("Error in SQL Query. Please check the SQL Query: ".$query);

        $regs->setFetchMode(PDO::FETCH_ASSOC);
        $resultset = $regs->fetchAll();
        $count     = sizeof($resultset);
        $this->_set_attributes($resultset);
        if ($count > 0) {
            for ($j = 0; $j < $count; $j++) {
                $this->offsetSet($j, new $this);
                $this[$j]->_counter = 1;
                $this[$j]->_fields  = $this->_fields;
            }
            for ($j = 0; $j < $count; $j++) {
                foreach ($resultset[$j] as $property => $value) {
                    if (!is_numeric($property)) {
                        $this[$j]->{$property} = $this->_fields[$property]?0+$value:$value;
                    }
                }
            }
        }
        $this->_counter = $j;
        if ($this->_counter === 0) {
            $this->offsetSet(0, NULL);
            $this[0] = NULL;
            unset($this[0]);
            $fields = $this->driver->getColumns();
            foreach ($fields as $row) {
                $this->_fields[$row['Field']] = false;
                $this->{$row['Field']} = null;
                $this->_dataAttributes[$row['Field']]['native_type'] = $row['Type'];
                $this->_dataAttributes[$row['Field']]['cast'] = $this->_fields[$row['Field']];
            }
        } elseif ($this->_counter === 1) {
            foreach ($this->_fields as $field => $cast) {
                if (isset($this[0]->{$field})) {
                    $this->{$field} =& $this[0]->{$field};
                }
            }
        }
    }
    /**
     * Performs the select queries to the database according to the given params
     * @param array|integer $params
     * @return ActiveRecord
     */
    public function Find($params = NULL) {
        if (sizeof($this->before_find) > 0) {
            foreach ($this->before_find as $functiontoRun) {
                $this->{$functiontoRun}();
            }
        }
        if (CAN_USE_MEMCACHED) {
            $key = md5($this->_ObjTable.':'.serialize($params));
            $res = $GLOBALS['memcached']->get($key);
            if ($GLOBALS['memcached']->getResultCode() == 0 && is_object($res)) {
                return $res;
            }
        }
        $this->_sqlQuery = $this->driver->Select($params);
        $this->getData($this->_sqlQuery);
        if (sizeof($this->after_find) > 0) {
            foreach ($this->after_find as $functiontoRun) {
                $this->{$functiontoRun}();
            }
        }
        CAN_USE_MEMCACHED && $GLOBALS['memcached']->set($key, $this) && $this->_setMemcacheKey($key);
        return clone($this);
    }
    /**
     * Performs a select query from a given string
     * @param string $query
     * @return unknown|ActiveRecord
     */
    public function Find_by_SQL($query = NULL) {
        if (!$query) {
            trigger_error("The query can not be NULL", E_USER_ERROR);
            exit;
        } else {
            $this->_sqlQuery = $query;
            if (CAN_USE_MEMCACHED) {
                $key = md5($query);
                $res = null;
                $res = $GLOBALS['memcached']->get($key);
                if ($GLOBALS['memcached']->getResultCode() == 0 && is_object($res)) {
                    return $res;
                }
            }
            $this->getData($query);
            CAN_USE_MEMCACHED && $GLOBALS['memcached']->set($key, $this);
            return clone($this);
        }
    }
    private function _set_attributes($resultset) {
        if (!empty($resultset)) {
            foreach ($resultset[0] as $key => $value) {
                if (!array_key_exists($key, $this->_fields)) {
                    $this->_fields[$key]                        = is_numeric($value);
                    $this->{$key}                              = '';
                    $this->_dataAttributes[$key]['native_type'] = is_numeric($value)?'NUMERIC':'STRING';
                    $this->_dataAttributes[$key]['cast']        = $this->_fields[$key];
                }
            }
        }
    }
    public function Niu($contents = NULL) {
        foreach ($this as $i => $val) {
            $this[$i] = null;
            $this->offsetUnset($i);
        }
        $this->__construct();
        $fields = $this->driver->getColumns();
        foreach ($fields as $row) {
            $this->_fields[$row['Field']] = false;
            switch ($row['Type']) {
                case 'NUMERIC':
                case 'INTEGER':
                case 'INT':
                case 'FLOAT':
                case 'DOUBLE':
                    $this->_fields[$row['Field']] = true;
                    break;
            }
            $this->_dataAttributes[$row['Field']]['native_type'] = $row['Type'];
            $this->_dataAttributes[$row['Field']]['cast']        = $this->_fields[$row['Field']];
        }
        $this->_counter = 0;
        if (!empty($contents)) {
            $this->_counter = 1;
            $this->offsetSet(0, new $this);
            foreach ($contents as $field => $content) {
                if (array_key_exists($field, $this->_fields)) {
                    $this[0]->{$field} = $this->{$field} = $this->_fields[$field]?0+$content:$content;
                }
            }
        } else {
            foreach ($this->_fields as $field => $cast) {
                $this->{$field} = null;
            }
        }
        return clone($this);
    }
    public function Update($params) {
        if (!is_array($params)) {
            throw new Exception('The params for the Update() method must be an array');
        }
        if (empty($params['conditions']) || !is_string($params['conditions'])) {
            throw new Exception('The param conditions should not be empty and must be string.');
        }
        if (empty($params['data']) || !is_array($params['data'])) {
            throw new Exception('The param data should not be empty and must be array.');
        }
        defined('AUTO_AUDITS') or define('AUTO_AUDITS', true);
        $prepared        = $this->driver->Update($params);
        $this->_sqlQuery = $prepared['query'];
        $sh              = $GLOBALS['Connection']->prepare($this->_sqlQuery);
        if (!$sh->execute($prepared['prepared'])) {
            $e = $GLOBALS['Connection']->errorInfo();
            $this->_error->add(array('field' => $this->_ObjTable, 'message' => $e[2]."\n $query"));
            return false;
        }
        CAN_USE_MEMCACHED && $this->_refreshCache();
        return true;
    }
    public function load($params = null) {
        defined('AUTO_AUDITS') or define('AUTO_AUDITS', true);
        if (empty($params)) {
            foreach ($this->_fields as $field => $cast) {
                if (isset($this->{$field})) {
                    $params[$field] = $this->{$field};
                }
            }
        }
        $prepared        = $this->driver->Insert($params);
        $this->_sqlQuery = $prepared['query'];
        $sh              = $GLOBALS['Connection']->prepare($this->_sqlQuery);
        if (!$sh->execute($prepared['prepared'])) {
            $e = $GLOBALS['Connection']->errorInfo();
            $this->_error->add(array('field' => $this->_ObjTable, 'message' => $e[2]."\n $query"));
            return false;
        }
        return true;
    }
    private function _ValidateOnSave($action = 'insert') {
        if (!empty($this->validate)) {
            if (!empty($this->validate['email'])) {
                foreach ($this->validate['email'] as $field) {
                    $message = 'The email provided is not a valid email address.';
                    if (is_array($field)) {
                        if (empty($field['field'])) {throw new Exception('Field key must be defined in array.');
                        }

                        empty($field['message']) or ($message = $field['message']);
                        $field = $field['field'];
                    }
                    preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $this->{$field}, $matches);
                    isset($this->{$field}) and empty($matches) and $this->_error->add(array('field' => $field, 'message' => $message));
                }
            }
            if (!empty($this->validate['numeric'])) {
                foreach ($this->validate['numeric'] as $field) {
                    $message = 'This Field must be numeric.';
                    if (is_array($field)) {
                        if (empty($field['field'])) {throw new Exception('Field key must be defined in array.');
                        }

                        empty($field['message']) || ($message = $field['message']);
                        $field = $field['field'];
                    }
                    isset($this->{$field}) && (!is_numeric($this->{$field})) && $this->_error->add(array('field' => $field, 'message' => $message));
                }
            }
            if (!empty($this->validate['unique'])) {
                foreach ($this->validate['unique'] as $field) {
                    $message = 'This field can not be duplicated.';
                    if (is_array($field)) {
                        if (empty($field['field'])) {throw new Exception('Field key must be defined in array.');
                        }

                        empty($field['message']) || ($message = $field['message']);
                        $field = $field['field'];
                    }
                    if (!empty($this->{$field})) {
                        $obj1      = new $this;
                        $resultset = $obj1->Find(array('fields'                          => $field, 'conditions'                          => "`{$field}`='" .$this->{$field} ."' AND `{$this->pk}`<>'" .$this->{$this->pk} ."'"));
                        if ($resultset->counter() > 0) {$this->_error->add(array('field' => $field, 'message' => $message));
                        }
                    }
                }
            }

            if (!empty($this->validate['presence_of'])) {
                foreach ($this->validate['presence_of'] as $field) {
                    $message = 'This field can not be empty or null.';
                    if (is_array($field)) {
                        if (empty($field['field'])) {throw new Exception('Field key must be defined in array.');
                        }

                        empty($field['message']) or ($message = $field['message']);
                        $field = $field['field'];
                    }
                    empty($this->{$field}) and $this->_error->add(array('field' => $field, 'message' => $message));
                }
            }
        }
    }
    public function Save() {
        defined('AUTO_AUDITS') or define('AUTO_AUDITS', true);
        if (sizeof($this->before_save) > 0) {
            foreach ($this->before_save as $functiontoRun) {
                $this->{$functiontoRun}();
            }
        }
        if ($this->_error->isActived()) {
            return FALSE;
        }

        if (!empty($this->{$this->pk})) {
            $this->_ValidateOnSave('update');
            if ($this->_error->isActived()) {
                return false;
            }

            $this->updated_at = time();
            $data             = array();
            foreach ($this->_fields as $field => $cast) {
                if ($field !== $this->pk && isset($this->{$field})) {
                    $data[$field] = $this->{$field};
                }
            }
            $prepared = $this->driver->Update(array('data' => $data, 'conditions' => "{$this->_ObjTable}.{$this->pk} = " .$this->{$this->pk}));
        } else {
            if (!empty($this->before_insert)) {
                foreach ($this->before_insert as $functiontoRun) {
                    $this->{$functiontoRun}();
                }
            }
            if ($this->_error->isActived()) {
                return false;
            }

            $this->_ValidateOnSave();
            if ($this->_error->isActived()) {
                return false;
            }

            $this->created_at = time();
            $this->updated_at = 0;
            $data             = array();
            foreach ($this->_fields as $field => $cast) {
                if ($field != $this->pk && isset($this->{$field})) {
                    $data[$field] = $this->{$field};
                }
            }
            $prepared = $this->driver->Insert($data);
        }

        $this->_sqlQuery = $prepared['query'];
        $sh              = $GLOBALS['Connection']->prepare($this->_sqlQuery);
        if (!$sh->execute($prepared['prepared'])) {
            $e = $GLOBALS['Connection']->errorInfo();
            $this->_error->add(array('field' => $this->_ObjTable, 'message' => $e[2]."\n {$this->_sqlQuery}"));
            return FALSE;
        }

        if (empty($this->{$this->pk})) {
            $this->{$this->pk} = $GLOBALS['Connection']->lastInsertId()+0;
            isset($this[0]) && ($this[0]->{$this->pk} = $this->{$this->pk});
            if (sizeof($this->after_insert) > 0) {
                foreach ($this->after_insert as $functiontoRun) {
                    $this->{$functiontoRun}();
                }
            }
        }

        if (sizeof($this->after_save) > 0) {
            foreach ($this->after_save as $functiontoRun) {
                $this->{$functiontoRun}();
            }
        }
        CAN_USE_MEMCACHED && $this->_refreshCache();
        return true;
    }

    public function Delete($conditions = NULL) {
        if ($this->_counter > 1) {
            $conditions = array();
            foreach ($this as $ele) {
                $conditions[] = $ele->{$this->pk};
            }
        }
        if ($conditions === NULL and !empty($this->{$this->pk})) {$conditions = $this->{$this->pk};
        }

        if ($conditions === NULL and empty($this->{$this->pk})) {
            $this->_error->add(array('field' => $this->_ObjTable, 'message' => "Must specify a register to delete"));
            return FALSE;
        }
        $this->_sqlQuery = $this->driver->Delete($conditions);
        if (sizeof($this->before_delete) > 0) {
            foreach ($this->before_delete as $functiontoRun) {
                $this->{$functiontoRun}();
            }
            if (!empty($this->_error) && $this->_error->isActived()) {
                return false;
            }
        }
        $this->_delete_or_nullify_dependents((integer) $conditions) or print($this->_error);
        if (!$GLOBALS['Connection']->exec($this->_sqlQuery)) {
            $e = $GLOBALS['Connection']->errorInfo();
            $this->_error->add(array('field' => $this->_ObjTable, 'message' => $e[2]."\n {$this->_sqlQuery}"));
            return FALSE;
        }
        if (sizeof($this->after_delete) > 0) {
            foreach ($this->after_delete as $functiontoRun) {
                $this->{$functiontoRun}();
            }
        }
        CAN_USE_MEMCACHED && $this->_refreshCache();
        return TRUE;
    }
    protected function _delete_or_nullify_dependents($id) {
        if (!empty($this->dependents) && !empty($id)) {
            foreach ($this->has_many as $model) {
                $s = Singulars($model);
                $m = Camelize($s);
                class_exists($m) or require_once INST_PATH.'app/models/'.strtolower($s).'.php';
                $model1   = new $m();
                $children = $model1->Find(array('conditions' => Singulars($this->_ObjTable)."_id='".$id."'"));
                if ($children->counter() > 0) {
                    foreach ($children as $child) {
                        switch ($this->dependents) {
                            case 'destroy':
                                if (!$child->Delete()) {
                                    $this->_error->add(array('field' => $this->_ObjTable, 'message' => "Cannot delete dependents"));
                                    return FALSE;
                                }
                                break;
                            case 'nullify':
                                $child->{$this->_ObjTable.'_id'    } = '';
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
    public function inspect($tabs = 0) {
        echo get_class($this), " ActiveRecord ({$this->_counter}): ", $this->ListProperties_ToString($tabs);
    }
    protected function ListProperties_ToString($i = 0) {
        $listProperties = "{\n";
        if ($this->_counter <= 1) {
            foreach ($this->_fields as $field => $cast) {
                $buffer = 'NULL'.PHP_EOL;
                if (isset($this->{$field})) {
                    ob_start();
                    var_dump($this->{$field});
                    $buffer = ob_get_clean();
                }
                for ($j = 0; $j < $i+1; $j++) {
                    $listProperties .= "\t";
                }
                $listProperties .= "{$field} => {$buffer}";
            }
        } else {
            for ($j = 0; $j < $this->_counter; $j++) {
                $this[$j]->inspect($i+1);
            }
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
        $arraux = array();
//         if ($this->_counter > 1) {
            for ($j = 0; $j < $this->_counter; $j++) {
                foreach ($this->_fields as $field => $cast) {
                    if (isset($this[$j]->{$field})) {
                        $arraux[$j][$field] = (is_object($this[$j]->{$field}) && get_parent_class($this[$j]->{$field}) == 'ActiveRecord')?$this[$j]->{$field}->getArray():$this[$j]->{$field};
                    }
                }
            }
//         } else {
//             foreach ($this->_fields as $field => $cast) {
//                 if (isset($this->{$field})) {
//                     $arraux[0][$field] = (is_object($this->{$field}) && get_parent_class($this->{$field}) == 'ActiveRecord')?$this->{$field}->getArray():$this->{$field};
//                 }
//             }
//         }
        return $arraux;
    }
    public function Dump() {
        $model    = $this->_ObjTable;
        $dom      = new DOMDocument('1.0', 'utf-8');
        $dataDump = $this->getArray();
        $path     = defined('DUMPS_PATH')?DUMPS_PATH:INST_PATH.'migrations/dumps/';
        $sroot    = $dom->appendChild(new DOMElement('table_'.$model));
        foreach ($dataDump as $reg) {
            $root = $sroot->appendChild(new DOMElement($model));
            foreach ($reg as $element => $value) {
                if (preg_match("(&|<|>)", $value)) {
                    $value   = $dom->createCDATASection($value);
                    $element = $root->appendChild(new DOMElement($element, ""));
                    $element->appendChild($value);
                } else {
                    $element = $root->appendChild(new DOMElement($element, $value));
                }
            }
        }
        file_put_contents($path.$model.'.xml', $dom->saveXML());
    }
    public function LoadDump() {
        $doc  = new DOMDocument;
        $path = defined('DUMPS_PATH')?DUMPS_PATH:INST_PATH.'migrations/dumps/';
        if (file_exists($path.$this->_ObjTable.'.xml')) {
            $doc->load($path.$this->_ObjTable.'.xml');
            $items = $doc->getElementsByTagName($this->_ObjTable);
            for ($i = 0; $i < $items->length; $i++) {
                $xitem = $items->item($i);
                $data  = array();
                if (empty($this->_fields)) {
                    $fields = $this->driver->getColumns();
                    foreach ($fields as $row) {
                        $this->_fields[$row['Field']] = false;
                    }
                }
                foreach ($this->_fields as $field => $cast) {
                    $item = $xitem->getElementsByTagName($field);
                    $data[$field] = (is_object($item->item(0)))?$item->item(0)->nodeValue:'';
                }
                $prepared = $this->driver->Insert($data);
                $this->_sqlQuery = $prepared['query'];
                $sh = $GLOBALS['Connection']->prepare($this->_sqlQuery);
                if (!$sh->execute($prepared['prepared'])) {
                    $e = $GLOBALS['Connection']->errorInfo();
                    die("{$e[2]} {$this->_sqlQuery}");
                }
            }
        }
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
        return $Errors;
    }
    public function counter() {
        return (integer) $this->_counter;
    }
    public function first() {
        return $this->_counter > 0?$this[0]:FALSE;
    }
    public function last() {
        return $this->_counter > 0?$this[$this->counter()-1]:FALSE;
    }
    public function _sqlQuery() {
        return $this->_sqlQuery;
    }
    public function _nativeType($field) {
        if (empty($this->_dataAttributes[$field]['native_type'])) {
            return false;
        }
        return $this->_dataAttributes[$field]['native_type'];
    }
    public function slice($start = null, $length = null) {
        if (empty($length)) {$length = $this->_counter;
        }

        if ($start === null) {$start = 0;
        }

        $end                              = $start+$length;
        if ($end > $this->_counter) {$end = $this->_counter;
        }

        $name = get_class($this);
        $arr  = new $name();
        for ($i = $start; $i < $end; $i++) {
            $arr[] = $this[$i];
        }
        return $arr;
    }
    public function toJSON() {
        return json_encode($this->getArray());
    }
    public function Paginate($params = NULL) {
        $resultset = array();
        if (is_array($params) && sizeof($params) === 1 && !empty($params[0])) {
            $params = $params[0];
        }

        $url = explode('&', $_SERVER['REQUEST_URI']);
        $url = explode('?', $url[0]);

        $params2                                                    = $params;
        $per_page                                                   = (isset($params['per_page']))?$params['per_page']:10;
        $this->paginateURL                                          = empty($params['url'])?$url[0]:$params['url'];
        empty($params['varPageName']) or $this->PaginatePageVarName = $params['varPageName'];
        if (!empty($_GET[$this->PaginatePageVarName])) {
            $this->PaginatePageNumber = $params[$this->PaginatePageVarName] = $_GET[$this->PaginatePageVarName];
        }
        if (!empty($params[$this->PaginatePageVarName])) {
            $this->PaginatePageNumber = $params[$this->PaginatePageVarName];
        }
        $start                                                      = ($this->PaginatePageNumber-1)*$per_page;
        $params['limit']                                            = $start.",".$per_page;
        $params2['fields']                                          = "COUNT({$this->_ObjTable}.{$this->pk}) AS PaginateTotalRegs";
        $queryCounter                                               = $this->driver->Select($params2);
        if (CAN_USE_MEMCACHED) {
            $key       = md5($queryCounter);
            $resultset = $GLOBALS['memcached']->get($key);
        }
        if (empty($resultset) || !is_array($resultset)) {
            $regs = $GLOBALS['Connection']->query($queryCounter);
            $regs->setFetchMode(PDO::FETCH_ASSOC);
            $resultset = $regs->fetchAll();
            if (CAN_USE_MEMCACHED) {
                $key = md5($queryCounter);
                $GLOBALS['memcached']->set($key, $resultset);
                $this->_setMemcacheKey($key);
            }
        }
        $this->PaginateTotalItems = 0+$resultset[0]['PaginateTotalRegs'];
        $this->PaginateTotalPages = ceil($this->PaginateTotalItems/$per_page);
        return $this->Find($params);
    }
    public function WillPaginate($params = NULL) {
        if (is_array($params) && sizeof($params) === 1 && !empty($params[0])) {$params = $params[0];
        }

        $str  = '';
        $tail = '';
        $i    = 1;
        if ($this->PaginatePageNumber > 1):
        $str .= '<a class="paginate paginate-first-page" href="'.$this->paginateURL.'?'.$this->PaginatePageVarName.'=1">|&lt;&lt;</a>&nbsp;';
        $str .= '<a class="paginate paginate-prev-page" href="'.$this->paginateURL.'?'.$this->PaginatePageVarName.'='.($this->PaginatePageNumber-1).'">&lt;</a>&nbsp;';
        endif;
        $top = $this->PaginateTotalPages;
        if ($this->PaginateTotalPages > 10):
        $top                                        = ($this->PaginatePageNumber-1)+10;
        if ($top > $this->PaginateTotalPages) {$top = $this->PaginateTotalPages;
        }

        $i              = $top-10;
        if ($i < 1) {$i = 1;
        }

        endif;
        if ($this->PaginatePageNumber < $this->PaginateTotalPages):
        $tail .= '<a class="paginate paginate-next-page" href="'.$this->paginateURL.'?'.$this->PaginatePageVarName.'='.($this->PaginatePageNumber+1).'">&gt;</a>&nbsp;';
        $tail .= '<a class="paginate paginate-last-page" href="'.$this->paginateURL.'?'.$this->PaginatePageVarName.'='.($this->PaginateTotalPages).'">&gt;&gt;|</a>&nbsp;';
        endif;
        for (; $i <= $top; $i++) {
            $str .= '<a class="paginate paginate-page'.($this->PaginatePageNumber == $i?" paginate-active-page":"").'" href="'.$this->paginateURL.'?'.$this->PaginatePageVarName.'='.$i.'">'.$i.'</a>&nbsp;';
        }
        $str .= $tail;
        return $str;
    }
    public function input_for($params) {
        $stringi = '<input';
        $stringt = '<textarea';
        $strings = '<select';
        $name    = '';
        $html    = '';
        $type    = '';
        $input   = '';
        if (!empty($params) or !empty($params[0]) or isset($params['field'])) {
            if (is_array($params)) {
                $field = isset($params['field'])?$params['field']:$params[0];
            } else {
                $field = $params;
            }
            if (empty($params['name']) or !is_array($params)) {
                $name = Singulars(strtolower($this->_ObjTable)).'['.$field.']';
            } else {
                $name = $params['name'];
            }
            if (is_array($params) and !empty($params['html']) and is_array($params['html'])) {
                foreach ($params['html'] as $element => $value) {
                    $html .= $element.'="'.$value.'" ';
                }
            }
            $html = trim($html);
            if (strlen($html) > 0):
            $html = ' '.$html;
            endif;
            if (is_array($params) and !empty($params['type']) and is_string($params['type'])) {
                $type = $params['type'];
            } else {
                $nattype = $this->_nativeType($field);
                switch ($nattype) {
                    case 'INTEGER':
                    case 'LONG':
                    case 'STRING':
                    case 'INT':
                    case 'BIGINT':
                    case 'VAR_CHAR':
                    case 'VARCHAR':
                    case 'FLOAT':
                    case 'VAR_STRING';
                        $type = 'text';
                        break;
                    case 'BLOB':
                    case 'TEXT':
                        $type = 'textarea';
                        break;
                }
            }
            if ($field === 'id') {
                $type = 'hidden';
            }

            switch ($type) {
                case 'text':
                case 'hidden':
                    $input = "{$stringi} type=\"{$type}\" name=\"{$name}\"{$html} value=\"{$this->{$field}}\" />";
                    break;
                case 'checkbox':
                    $checked = $this->{$field} ? ' checked="checked"' : '';
                    $input = "{$stringi} type=\"{$type}\" name=\"{$name}\"{$html} value=\"\"$checked />";
                break;
                case 'textarea':
                    $input = $stringt.' type="'.$type.'" name="'.$name.'"'.$html.'>'.$this->{$field}.'</textarea>';
                    break;
                case 'select':
                    $cont = !empty($params['first'])?'<option value="">'.$params['first'].'</option>':'';
                    foreach ($params['list'] as $value => $option) {
                        $default = '';
                        if ($this->{$field } == $value) {
                            $default = 'selected="selected"';
                        }

                        $cont .= '<option value="'.$value.'"'.$default.'>'.$option.'</option>'.PHP_EOL;
                    }
                    $input = $strings.' name="'.$name.'"'.$html.'>'.$cont.'</select>';
                break;
            }
        } else {
            throw new Exception("Must to give the field to input.");
            return null;
        }
        return $input;
    }
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
        $html                         = trim($html);
        if (strlen($html) > 0) {$html = ' '.$html;
        }

        $string .= ' method="'.$method.'" action="'.$action.'" name="'.$name.'"'.$html.'>';
        return $string;
    }
}
abstract class Page extends Core_General_Class {
    public $excepts_before_filter    = array();
    protected $layout                = "";
    protected $render                = NULL;
    protected $flash                 = "";
    protected $yield                 = "";
    protected $params                = array();
    protected $metaDescription       = '';
    protected $pageTitle             = '';
    protected $controller            = '';
    protected $action                = '';
    protected $_outputContent        = '';
    protected $excepts_after_filter  = array();
    protected $excepts_after_render  = array();
    protected $excepts_before_render = array();
    protected $_exposeContent        = true;
    protected $_data_                = array();
    private $_respondToAJAX          = '';
    private $_canrespondtoajax       = false;
    private $models                  = array();
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
    public function display() {
        $renderPage  = TRUE;
        $this->action = _ACTION;
        $this->controller = _CONTROLLER;
        if (property_exists($this, 'noTemplate') and in_array(_ACTION, $this->noTemplate)) $renderPage = FALSE;
        if ($this->canRespondToAJAX()) {
            if (!headers_sent()) {
                header('Cache-Control: no-cache, must-revalidate');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Content-type: application/json');
            }

            $this->_outputContent = $this->respondToAJAX();
            if (!empty($this->params['callback'])) $this->_outputContent = "({$this->_outputContent}));";

            $renderPage   = false;
            $this->layout = '';
        } else {

            if (isset($this->render) and is_array($this->render)) {
                if (isset($this->render['action']) && $this->render['action'] === false) {
                    $this->yield = '';
                    $renderPage  = FALSE;
                } elseif (!empty($this->render['file'])) {
                    $view = $this->render['file'];
                } elseif (!empty($this->render['partial'])) {
                    $view = _CONTROLLER.'/_'.$this->render['partial'].'.phtml';
                } elseif (!empty($this->render['text'])) {
                    $this->yield = $this->render['text'];
                    $renderPage  = FALSE;
                } elseif (!empty($this->render['action'])) {
                    $view = _CONTROLLER.'/'.$this->render['action'].'.phtml';
                } else {
                    $view = _CONTROLLER.'/'._ACTION.'.phtml';
                }
            } else {
                $view = _CONTROLLER.'/'._ACTION.'.phtml';
            }

            $viewsFolder = INST_PATH.'app/views/';

            if ($renderPage) {
                ob_start();
                include_once ($viewsFolder.$view);
                $this->yield = ob_get_clean();
            }

            if (isset($this->render['layout']) && $this->render['layout'] !== false) $this->layout = $this->render['layout'];

            if (isset($this->render['layout']) && $this->render['layout'] === false) $this->layout = '';

            $this->_outputContent = $this->yield;

            if (strlen($this->layout) > 0) {
                ob_start();
                include_once ($viewsFolder.$this->layout.".phtml");
                $this->_outputContent = ob_get_clean();
            }
        }

        $this->_exposeContent && print $this->_outputContent;
    }
    public function LoadHelper($helper = NULL) {
        if (isset($helper) and is_array($helper)):
        foreach ($helper as $file) {
            require_once (INST_PATH."app/helpers/".$file."_Helper.php");
        } elseif (isset($helper) and is_string($helper)):
        require_once (INST_PATH."app/helpers/".$helper."_Helper.php");
        endif;
    }
    public function params($params = NULL) {
        $this->params = $params;
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
    private $driver = null;
    public function __construct() {
        if (empty($GLOBALS['Connection'])) {
            $GLOBALS['Connection'] = new Connection(INST_PATH.'config/db_settings.ini');
            require_once dirname(__FILE__).'/lib/db_drivers/'.$GLOBALS['Connection']->engine.'.php';
        }
        $driver = $GLOBALS['Connection']->engine.'Driver';
        $this->driver = new $driver();
    }
    public function __destruct() {}
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
    /**
     *
     * @param array $table
     */
    protected function Create_Table(array $table) {
        defined('AUTO_AUDITS') || define('AUTO_AUDITS', true);
        if (empty($table['Table'])) throw new Exception('Table field must be present.');
        $tablName = $table['Table'];
        $query    = "CREATE TABLE IF NOT EXISTS `".$tablName."` (";
        $query .= "`id` INT PRIMARY KEY ,";
        foreach ($table as $key => $Field) {
            if (strcmp($key, 'Table') != 0) {
                if ($Field['type'] == 'VARCHAR' && empty($Field['limit'])) {
                    $Field['limit'] = 250;
                }

                $query .= (!empty($Field['field']) && !empty($Field['type']))?"`".$Field['field']."` ".$Field['type']:NULL;
                $query .= (!empty($Field['limit']))?" (".$Field['limit'].")":NULL;
                $query .= (empty($Field['null']) || $Field['null'] === 'false')?" NOT NULL":NULL;
                $query .= (isset($Field['default']))?" DEFAULT '".$Field['default']."'":NULL;
                $query .= (!empty($Field['comments']))?" COMMENT '".$Field['comment']."'":NULL;
                $query .= " ,";
            }
        }
        if (AUTO_AUDITS) {
            $query .= "`created_at` INT NOT NULL ,";
            $query .= "`updated_at` INT NOT NULL ,";
        }
        $query = substr($query, 0, -2);
        $query .= ");";
        syslog(LOG_DEBUG,'Running query: '.$query.PHP_EOL);
        if ($GLOBALS['Connection']->exec($query) === false) {
            syslog(LOG_ERR,$GLOBALS['Connection']->errorInfo());
        }

        $query = "ALTER TABLE `$tablName` MODIFY COLUMN `id` INT AUTO_INCREMENT";
        syslog(LOG_DEBUG,'Running query: '.$query.PHP_EOL);
        if ($GLOBALS['Connection']->exec($query) === false) {
            syslog(LOG_ERR,$GLOBALS['Connection']->errorInfo());
        }
    }

    protected function Drop_Table($table) {
        $query = "DROP TABLE IF EXISTS `".$table."`";
        syslog(LOG_DEBUG,'Running query: '.$query.PHP_EOL);
        if ($GLOBALS['Connection']->exec($query) === false) {
            syslog(LOG_ERR,$GLOBALS['Connection']->errorInfo());
        }
    }

    protected function Add_Column(array $params) {
        $getinfo =<<<DUMBO
SELECT COUNT(COLUMN_NAME) AS counter
FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_name = '{$params['Table']}'
    AND table_schema = '{$GLOBALS['Connection']->_settings['schema']}'
    AND column_name = '{$params['field']}';
DUMBO;
        $res = $GLOBALS['Connection']->query($getinfo);
        $res->setFetchMode(PDO::FETCH_ASSOC);
        $result = 0 + $res->fetchAll()[0]['counter'];

        if ($result < 1) {
            $params['type'] == 'VARCHAR' && empty($params['limit']) && ($params['limit'] = '255');

            $query = "ALTER TABLE `".$params['Table']."` ADD COLUMN `".$params['field']."` ".strtoupper($params['type']);
            $query .= (isset($params['limit']) && $params['limit'] != '')?"(".$params['limit'].")":NULL;
            $query .= (isset($params['null']) && $params['null'] != '')?" NOT NULL":NULL;
            $query .= (isset($params['default']) && $params['default'] != '')?" DEFAULT '".$params['default']."'":NULL;
            $query .= (!empty($params['comments']))?" COMMENT '".$params['comment']."'":NULL;

            syslog(LOG_DEBUG,'Running query: '.$query.PHP_EOL);
            $db = $GLOBALS['Connection']->prepare($query);
            if ($db->execute() === false) {
                syslog(LOG_ERR,$GLOBALS['Connection']->errorInfo());
            }
        }
    }

    protected function Add_Index(array $params) {
        if (empty($params['Table'])) {
            throw new Exception("Table param can not be empty", 1);
        }

        if (empty($params['name'])) {
            throw new Exception("name param can not be empty", 1);
        }

        if (empty($params['fields'])) {
            throw new Exception("fields param can not be empty", 1);
        }

        if (!is_array($params['fields'])) {
            throw new Exception("fields param must be an array", 1);
        }

        $fields = implode(',', $params['fields']);
        $query  = "ALTER TABLE `{$params['Table']}` ADD INDEX `{$params['name']}` ({$fields})";
        $GLOBALS['Connection']->exec($query) !== false || print_r($GLOBALS['Connection']->errorInfo());
    }
    protected function Alter_Column(array $params) {
        $getinfo =<<<DUMBO
SELECT COUNT(COLUMN_NAME) AS counter
FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_name = '{$params['Table']}'
    AND table_schema = '{$GLOBALS['Connection']->_settings['schema']}'
    AND column_name = '{$params['field']}';
DUMBO;
        $res = $GLOBALS['Connection']->query($getinfo);
        $res->setFetchMode(PDO::FETCH_ASSOC);
        $result = 0 + $res->fetchAll()[0]['counter'];

        if ($result > 0) {
            $params['type'] == 'VARCHAR' && empty($params['limit']) && ($params['limit'] = '255');

            $query = "ALTER TABLE `".$params['Table']."` MODIFY `".$params['field']."` ".strtoupper($params['type']);
            $query .= (isset($params['limit']) && $params['limit'] != '')?"(".$params['limit'].")":NULL;
            $query .= (isset($params['null']) && $params['null'] != '')?" NOT NULL":NULL;
            $query .= (isset($params['default']) && $params['default'] != '')?" DEFAULT '".$params['default']."'":NULL;
            $query .= (!empty($params['comments']))?" COMMENT '".$params['comment']."'":NULL;

            fwrite(STDOUT, 'Running query: '.$query . "\n");
            $db = $GLOBALS['Connection']->prepare($query);
            if ($db->execute() === false) {
                fwrite(STDOUT, $GLOBALS['Connection']->errorInfo() . "\n");
            }
        }
    }
    protected function Remove_Column(array $params) {
        $getinfo =<<<DUMBO
SELECT COUNT(COLUMN_NAME) AS counter
FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_name = '{$params['Table']}'
    AND table_schema = '{$GLOBALS['Connection']->_settings['schema']}'
    AND column_name = '{$params['field']}';
DUMBO;
        $res = $GLOBALS['Connection']->query($getinfo);
        $res->setFetchMode(PDO::FETCH_ASSOC);
        $result = 0 + $res->fetchAll()[0]['counter'];

        if ($result > 0) {
            $query = "ALTER TABLE `".$params['Table']."` DROP `".$params['field']."`";
            syslog(LOG_DEBUG,'Running query: '.$query.PHP_EOL);
            if ($GLOBALS['Connection']->exec($query) === false) {
                syslog(LOG_ERR,$GLOBALS['Connection']->errorInfo());
            }
        }
    }
}

class index {
    public function __construct() {
        if (isset($_GET['url'])) {
            $request = explode("/", $_GET['url']);
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

        $params = array();

        if (sizeof($request) === 1 and !strstr($request[0], "=") and is_numeric($request[0])) {
            $params['id'] = $request[0];
        } elseif (sizeof($request) > 0 and strstr($request[0], "=")) {
            for ($i = 0; $i < sizeof($request); $i++) {
                $p = explode('=', $request[$i]);
                if (isset($p[1])) {
                    $params[$p[0]] = $p[1];
                } else {
                    $params[] = $p[0];
                }
                unset($request[$i]);
            }
        } elseif (sizeof($request) > 0) {
            foreach ($request as $index => $varParam) {
                $params[] = $varParam;
                unset($request[$index]);
            }
        }
        if (is_array($params)) {
            $params = array_merge($params, $_GET);
        } else {
            $params = $_GET;
        }
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
            echo "Missing Controller";
        }
        defined('_CONTROLLER') || define('_CONTROLLER', $controller);
        defined('_ACTION') || define('_ACTION', $action);
        defined('_FULL_URL') || define('_FULL_URL', INST_URI._CONTROLLER.'/'._ACTION.'/?'.http_build_query($params));
        if ($canGo) {
            $classPage = Camelize($controller)."Controller";
            class_exists($classPage) || require_once ($path.$controllerFile);
            $page      = new $classPage();
            $page->params($params);
            //loads of helpers
            if (isset($page->helper) and sizeof($page->helper) > 0) {
                $page->LoadHelper($page->helper);
            }
            //before filter, executed before the action execution
            if (method_exists($page, "before_filter")) {
                $actionsToExclude = $controllersToExclude = array();
                if (!empty($page->excepts_before_filter) && is_array($page->excepts_before_filter)) {
                    if (!empty($page->excepts_before_filter['actions']) && is_string($page->excepts_before_filter['actions'])) {
                        $actionsToExclude = explode(',', $page->excepts_before_filter['actions']);
                        foreach ($actionsToExclude as $index => $act) {
                            $actionsToExclude[$index] = trim($act);
                        }
                    }
                    if (!empty($page->excepts_before_filter['controllers']) && is_string($page->excepts_before_filter['controllers'])) {
                        $controllersToExclude = explode(',', $page->excepts_before_filter['controllers']);
                        foreach ($controllersToExclude as $index => $cont) {
                            $controllersToExclude[$index] = trim($cont);
                        }
                    }
                }
                if (!in_array($controller, $controllersToExclude) && !in_array($action, $actionsToExclude)) {
                    $page->before_filter();
                }
            }
            if (method_exists($page, $action."Action")) {
                $page->{$action."Action"}();
                //before render, executed after the action execution and before the data renderize
                if (method_exists($page, "before_render")) {
                    $actionsToExclude = $controllersToExclude = array();
                    if (!empty($page->excepts_before_render) && is_array($page->excepts_before_render)) {
                        if (!empty($page->excepts_before_render['actions']) && is_string($page->excepts_before_render['actions'])) {
                            $actionsToExclude = explode(',', $page->excepts_before_render['actions']);
                            foreach ($actionsToExclude as $index => $act) {
                                $actionsToExclude[$index] = trim($act);
                            }
                        }
                        if (!empty($page->excepts_before_render['controllers']) && is_string($page->excepts_before_render['controllers'])) {
                            $controllersToExclude = explode(',', $page->excepts_before_render['controllers']);
                            foreach ($controllersToExclude as $index => $cont) {
                                $controllersToExclude[$index] = trim($cont);
                            }
                        }
                    }
                    if (!in_array($controller, $controllersToExclude) && !in_array($action, $actionsToExclude)) {
                        $page->before_render();
                    }
                }
                $page->display();
                if (method_exists($page, "after_render")) {
                    $actionsToExclude = $controllersToExclude = array();
                    if (!empty($page->excepts_after_render) && is_array($page->excepts_after_render)) {
                        if (!empty($page->excepts_after_render['actions']) && is_string($page->excepts_after_render['actions'])) {
                            $actionsToExclude = explode(',', $page->excepts_after_render['actions']);
                            foreach ($actionsToExclude as $index => $act) {
                                $actionsToExclude[$index] = trim($act);
                            }
                        }
                        if (!empty($page->excepts_after_render['controllers']) && is_string($page->excepts_after_render['controllers'])) {
                            $controllersToExclude = explode(',', $page->excepts_after_render['controllers']);
                            foreach ($controllersToExclude as $index => $cont) {
                                $controllersToExclude[$index] = trim($cont);
                            }
                        }
                    }
                    if (!in_array($controller, $controllersToExclude) && !in_array($action, $actionsToExclude)) {
                        $page->after_render();
                    }
                }
            } else {
                echo "Missing Action";
            }
        }
    }
}
?>