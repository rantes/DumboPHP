<?php
/**
 * ActiveRecord.
 *
 * @author Javier Serrano <rantes.javier@gmail.com>
 * @version 1.0
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Javier Serrano <http://www.rantes.info/>
 * @package Core
 * @subpackage ActiveRecord
 */
require 'errors.php';
require "Driver.php";

/**
 * Clase ActiveRecord.
 *
 * Contiene todos los metodos para la implementacion de Active Records
 * y Mapeo de Objetos a traves de relaciones. No tiene instanciamiento.
 *
 * @author Javier Serrano <rantes.javier@gmail.com>
 * @version 1.0
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Javier Serrano <http://www.rantes.info/>
 * @package Core
 * @subpackage ActiveRecord
 * @extends Core_General_Class
 */
 abstract class ActiveRecord extends Core_General_Class{
	/**
	* Variable protegida $ObjTable
	*
	* Es una cadena de texto que contiene el nombre de la tabla del objeto actual.
	* @var array $ObjTable
	*/
	protected $_ObjTable;
	/**
	 * Almacena el nombre en sigular de la tabla
	 * @var protected string
	 */
	protected $_singularName;
	/**
	 * Variable protegida $driver
	 *
	 * Objeto de tipo pdo para la conexion global de bases de datos.
	 * @var Object_PDO $driver
	 */
	private $driver = NULL;

	/**
	 * Variable protegida $_counter
	 *
	 * La Variable publica counter es para obtener el numero de registros existentes en el objeto
	 * cuando se ha efectuado una busqueda por el metodo {@link Find()}.
	 * @var integer $counter
	 */
	protected $_counter = 0;

	/**
	 * Variable de tipo array $has_many.
	 *
	 * ('Tiene varios') Indica cada una de las tablas con las que tiene relacion de tipo uno a muchos.
	 * Cada posicion de arreglo es una tabla.
	 * @var array $has_many
	 */
	protected $has_many = array();
	/**
	 * Variable de tipo array $has_one.
	 *
	 * ('Tiene uno') Indica cada una de las tablas con las que tiene relacion de tipo uno a uno.
	 * Cada posicion de arreglo es una tabla.
	 * @var array $has_one
	 * @access protected
	 */
	protected $has_one = array();
	/**
	 * Variable de tipo array $belongs_to.
	 *
	 * ('pertenece a') Indica cada una de las tablas con las que tiene relacion de pertenencia.
	 * Cada posicion de arreglo es una tabla.
	 * @var array $belongs_to
	 */
	protected $belongs_to = array();
	/**
	 * Variable de tipo array $has_many_and_belongs_to.
	 *
	 * ('Tiene varios y Pertenece a') Indica cada una de las tablas con las que tiene relacion de uno a muchos y de pertenencia al mismo tiempo.
	 * Se utiliza generalmente con tablas que se relacionan consigo mismas. Cada posicion de arreglo es una tabla.
	 * @var array $has_many_and_belongs_to
	 */
	protected $has_many_and_belongs_to = array();
	/**
	 * Variable de tipo array $validate.
	 *
	 * ('Validar') Indica cada uno de los tipos de validacion que se requieran y los campos a validar.
	 * Cada llave del arreglo indica el tipo de validacion y cada elemento contenido
	 * entre cada arreglo de validacion es un campo para validar.
	 * ejemplo: <pre>$this->validate = array("typo_de_validacion"=> array('campo1','campo2','campon'));</pre>
	 * @var array $validate
	 */
	protected $validate = array();
	/**
	 * Variable de tipo array $before_insert.
	 *
	 * ('antes de insertar') Indica cada una de las funciones a ejecutar antes de crear un registro nuevo.
	 * Este callback se invoca antes de ejecutar una consulta de insercion.
	 * Cada posicion de arreglo es el nombre de una funcion.
	 * Cada funcion implicada debe ser definida dentro del modelo, despues de el metodo __construct().
	 * @var array $before_insert
	 */
	protected $before_insert = array();
	/**
	 * Variable de tipo array $after_insert.
	 *
	 * ('despues de insertar') Indica cada una de las funciones a ejecutar despues de crear un registro nuevo.
	 * Este callback se invoca despues de ejecutar una consulta de insercion.
	 * Cada posicion de arreglo es el nombre de una funcion.
	 * Cada funcion implicada debe ser definida dentro del modelo, despues de el metodo __construct().
	 * @var array $before_insert
	 */
	protected $after_insert = array();
	/**
	 * Variable de tipo array $after_find.
	 *
	 * ('despues de buscar') Indica cada una de las funciones a ejecutar despues de buscar en la BD.
	 * Este callback se invoca despues de ejecutar una consulta de seleccion.
	 * Cada posicion de arreglo es el nombre de una funcion.
	 * Cada funcion implicada debe ser definida dentro del modelo, despues de el metodo __construct().
	 * @var array $after_find
	 */
	protected $after_find = array();
	/**
	 * Variable de tipo array $before_find.
	 *
	 * ('antes de buscar') Indica cada una de las funciones a ejecutar antes de buscar en la BD.
	 * Este callback se invoca antes de ejecutar una consulta de seleccion.
	 * Cada posicion de arreglo es el nombre de una funcion.
	 * Cada funcion implicada debe ser definida dentro del modelo, despues de el metodo __construct().
	 * @var array $before_find
	 */
	protected $before_find = array();
	/**
	 * Variable de tipo array $after_save.
	 *
	 * ('despues de guardar') Indica cada una de las funciones a ejecutar despues de guardar un registro en la BD.
	 * Cada posicion de arreglo es el nombre de una funcion.
	 * Cada funcion implicada debe ser definida dentro del modelo, despues de el metodo __construct().
	 * @var array $after_save
	 */
	protected $after_save = array();

	/**
	 * Variable de tipo array $before_save.
	 *
	 * ('antes de guardar') Indica cada una de las funciones a ejecutar antes de guardar un registro en la BD.
	 * Cada posicion de arreglo es el nombre de una funcion.
	 * Cada funcion implicada debe ser definida dentro del modelo, despues de el metodo __construct().
	 * @var array $before_save
	 */
	protected $before_save = array();
	/**
	 * Variable de tipo array $after_delete.
	 *
	 * ('despues de borrar') Indica cada una de las funciones a ejecutar despues de borrar un registro en la BD.
	 * Cada posicion de arreglo es el nombre de una funcion.
	 * Cada funcion implicada debe ser definida dentro del modelo, despues de el metodo __construct().
	 * @var array $after_delete
	 */
	protected $after_delete = array();
	/**
	 * Variable de tipo array $before_delete.
	 *
	 * ('antes de borrar') Indica cada una de las funciones a ejecutar antes de borrar un registro en la BD.
	 * Cada posicion de arreglo es el nombre de una funcion.
	 * Cada funcion implicada debe ser definida dentro del modelo, despues de el metodo __construct().
	 * @var array $before_delete
	 */
	protected $before_delete = array();
	/**
	 *
	 * Establece que hacer en cuanto un registro va a ser borrado, con sus registros hijos, si borrar o establecer null para eliminar el vinculo
	 * @var string destroy|nullify
	 */
	protected $dependents = '';
	/**
	 * Variable de tipo array $_data.
	 *
	 * Este arreglo contiene los datos que se recuperan de la base de datos y los valores de las propiedades.
	 * @var array
	 */
	protected $_data = array();
	/**
	 * Variable de tipo array $_attrs.
	 *
	 * Este arreglo contiene las propiedades o atributos seteados magicamente y que no son de la bd.
	 * @var array
	 */
	protected $_attrs = array();
	/**
	 * Variable de tipo array $_dataAttributes.
	 *
	 * Este arreglo contiene los datos de propiedades (atributos) de los campos.
	 * @var array
	 */
	protected $_dataAttributes = array();
	/**
	 * Variable de tipo array $_models.
	 *
	 * Este arreglo contiene los modelos, para no mesclarlos con $_data.
	 * @var array
	 */
	protected $_models = array();
	/**
	*
	* Objeto de tipo Error $_error
	*
	* Este objeto controla y muestra los errores ocurridos durante los procesos de activerecord.
	* @var Error $_error
	**/
	public $_error = NULL;
	/**
	 * Almacena la construccion del query.
	 * @var string
	 */
	public $_sqlQuery = '';
	/**
	 * Guarda los parametros pasados al modelo para busquedas
	 * @var array
	 */
	protected $_params = array('fields'=>'*','conditions'=>'');

	/**
	 * Define si puede o no realizar dumps
	 * @var boolean
	 */
	public $candump = true;
	/**
	 * Almacena temporalmente las variables para unserialize()
	 */
	public $wakedUpVars = array();
	/**
	 * recurso de conexion al servidor memcached
	 */
	private $memcached = null;
	/**
	 * Constructor
	 *
	 * Realiza conexion a la base de datos mediante el metodo connect()
	 */
	function __construct(){
		$this->_data = NULL;
		$this->_data = array();
		$this->_attrs = NULL;
		$this->_attrs = array();
		$this->checkMemcached();
		$this->Connect();
	}
	/**
	 * Destructor
	 *
	 */
	function __destruct(){
		$this->_data = NULL;
		$this->_data = array();
		$this->_attrs = NULL;
		$this->_attrs = array();
		$this->_error = NULL;
	}
	/**
	 * Se ejecuta con el llamado de serializacion.
	 * No se permite el almacenamiento en sesion de objetos ActiveRecords por seguridad y rendimiento.
	 *
	 *  @return string Objeto serializado.
	 */
	public function serialize() {
		$name = get_class($this);
		$countVars = 0;
		$conts = '';
		$backtrace = debug_backtrace();
		if(sizeof($backtrace) <= 1)	throw new Exception('Active Record objects can not be storaged into session due to security reasons.');
		$serializes = null;
		$vars = get_class_vars($name);
		foreach ($vars as $var => $type){
			if(!empty($this->{$var}) && $var !== 'driver'){
				$countVars++;
				$typeof = gettype($this->{$var});
				$conts .= 's:'.strlen($var).':"'.$var.'";';
				switch ($typeof){
					case 'integer':
						$conts .= 'i:'.$this->{$var}.';';
						break;
					case 'string':
						$conts .= 's:'.strlen($this->{$var}).':"'.$this->{$var}.'";';
						break;
					case 'boolean':
						$conts .= 'b:'.((integer)$this->{$var}).';';
						break;
					case 'array':
					case 'object':
						$conts .= serialize($this->{$var});
						break;
				}
			}
		}
		$serializes = $conts;

		if($this->_counter > 1){
			$countVars += $this->_counter;
			for($i = 0; $i < $this->_counter; $i++){
				$countVars1 = 0;
				$conts = '';
				foreach ($vars as $var => $type){
					if(!empty($this[$i]->{$var}) && $var !== 'driver'){
						$countVars1++;
						$typeof = gettype($this[$i]->{$var});
						$conts .= 's:'.strlen($var).':"'.$var.'";';
						switch ($typeof){
							case 'integer':
								$conts .= 'i:'.$this[$i]->{$var}.';';
								break;
							case 'string':
								$conts .= 's:'.strlen($this[$i]->{$var}).':"'.$this[$i]->{$var}.'";';
								break;
							case 'boolean':
								$conts .= 'b:'.((integer)$this[$i]->{$var}).';';
								break;
							case 'array':
							case 'object':
								$conts .= serialize($this[$i]->{$var});
								break;
						}
					}
				}
				$serializes1 = 'O:'.strlen($name).':"'.$name.'":'.$countVars1.':{'.$conts.'}';
				$serializes .= 'i:'.$i.';C:'.strlen($name).':"'.$name.'":'.strlen($serializes1).':{'.$serializes1.'}';
			}
		}
		$serializes = 'O:'.strlen($name).':"'.$name.'":'.$countVars.':{'.$serializes.'}';
		return $serializes;
	}
	/**
	 * Invocado por la funcion unserilize()
	 */
	public function unserialize($data) {
		$a = unserialize($data);
		if(!empty($a->wakedUpVars['main'][1]['_counter']) && $a->wakedUpVars['main'][1]['_counter'] <= 1){
			$min = sizeof($a->wakedUpVars['main']) - $a->wakedUpVars['main'][1]['_counter'];
			for($i = 0; $i < $a->wakedUpVars['main'][1]['_counter']; $i++){
				$this->offsetSet($i, NULL);
				$classToUse = get_class($this);
				$this[$i] = new $classToUse();
				foreach($a->wakedUpVars['main'][$min+$i] as $obj){
					$this[$i] = $obj;
				}
			}
		} else {
			foreach ($a->wakedUpVars['main'] as $var){
				foreach ($var as $key => $value){
					if(is_numeric($key)){
						$this->offsetSet($key, NULL);
						$classToUse = get_class($this);
						$this[$key] = $value;
					} else {
						$this->{$key} = $value;
					}
				}
			}
		}
	}
	/**
	 * Invocado por unserialize()
	 * Establece los valores para el arreglo wakedUpVars
	 */
	public function __wakeup(){
		foreach($this as $key => $value){
			$this->wakedUpVars['main'][] = array($key => $value);
		}
// 		$this->Connect();
	}

	/**
	 * metodo magico __set()
	 *
	 * Se ejecuta cuando se crea un nuevo atributo al objeto.
	 * Carga datos en la variable {@link $data}.
	 *
	 * Los valores creados, luego pueden ser accedidos directamente gracias al metodo {@link __get()}
	 * @param string $name Nombre del atributo a crear.
	 * @param mixed $value Valor del atributo.
	 */
	public function __set($name, $value) {
		if(isset($this->_data[$name])){
			$this->_data[$name] = $value;
		} else {
			$this->_attrs[$name] = $value;
		}
	}
	/**
	 * Metodo magico
	 * Elimina un campo almacenado en {@link $data}
	 * @param string $name
	 */
	public function __unset($name) {
		$this->_attrs[$name] = NULL;
		unset($this->_attrs[$name]);
	}
	/**
	 * Metodo magico
	 * Verifica si un campo existe o esta seteado en {@link $data}
	 * @param string $var
	 * @return bool
	 */
	public function __isset($var){
		return ((!empty($this->_attrs) and array_key_exists($var, $this->_attrs)) || (!empty($this->_data) and array_key_exists($var, $this->_data)));
	}
	/**
	 * (non-PHPdoc)
	 * @see ArrayObject::getIterator()
	 */
	public function getIterator() {
            return new ArrayIterator($this);
    }
	/**
	 * metodo magico __get()
	 *
	 * Este mutodo se ejecuta cuando se accede a un attributo directamente del objeto y que no existe.
	 *
	 * Retorna el valor del atributo dinamico.
	 * @param string $name Nombre del atributo que se quiere acceder.
	 */
	public function __get($name) {

			switch($name){
				case '_ObjTable':
					return $this->_TableName();
				break;
				case '_errors':
					return $this->_errors;
				break;
				default:
					if (isset($this->_data[$name])){
						return $this->_data[$name];
					} elseif(isset($this->_attrs[$name])){
						return $this->_attrs[$name];
					}else{
						$model = unCamelize($name);
						if(file_exists(INST_PATH.'app/models/'.$model.'.php')){
							if(!class_exists($name)){
								require_once INST_PATH.'app/models/'.$model.'.php';
							}
							$this->_attrs[$name] = new $name();
							return $this->_attrs[$name];
						}else{
							return null;
						}
					}
				break;
			}
		throw new Exception('Undefined variable to get: ' . $name);
		return null;
	}


	/**
	 * Metodo protegido Connect()
	 *
	 * Realiza la conexion a la base de datos
	 */
	public function Connect(){
		/**
		 * El archivo driver.php es el archivo controlador para la conexion y tratamiento de la base de datos.
		 */
		if($this->driver === NULL and !is_object($this->driver) and get_class($this->driver) != 'Driver'){
			$this->driver = new Driver(INST_PATH.'config/db_settings.ini');
		}
		$this->_error = new Errors();
	}

	private function checkMemcached(){
		$memcached = null;
		if(!defined('CAN_USE_MEMCACHED')){
			define('CAN_USE_MEMCACHED', false);
		}
		if(CAN_USE_MEMCACHED && empty($this->memcached)){
			$memcached = new Memcached();
			if(!defined('MEMCACHED_HOST')){
				define('MEMCACHED_HOST','localhost');
			}
			if(!defined('MEMCACHED_PORT')){
				define('MEMCACHED_PORT','11211');
			}
			$memcached->addServer(MEMCACHED_HOST, MEMCACHED_PORT);
		}
		return $memcached;
	}

	/**
	* Metodo privado getData()
	*
	* Este metodo se encarga de recrear los atributos del objeto.
	* Lee los nombres de los campos y sus valores de la consulta realizada y de acuerdo con eso
	* genera los nuevos atributos al objeto propio a traves de $this, siendo en el caso de un resultado de un
	* solo registro o un arreglo de objetos de este mismo tipo como atributo de este objeto.
	* @param string $query
	*/
	private function getData($query){

		$this->_data = NULL;
		$this->_data = array();

		if(sizeof($this) >0){
			for($k = 0; $k<sizeof($this); $k++){
				if(isset($this[$k])) $this->offsetUnSet($k);
			}
		}
		for($k = 0; $k<sizeof($this->_data); $k++){
			array_pop($this->_data);
		}
		foreach($this as $index => $obj){
			$obj = null;
			unset($obj);
			$this->offsetUnSet($index);
		}
		if(isset($this->_data)){
			foreach($this->_data as $key => $val){
				$this->_data[$key] = NULL;
				unset($this->_data[$key]);
			}
		}

		$result = array();
// 		$i=0;
		$j=0;
		$regs = NULL;

		$this->Connect();
		$regs = $this->driver->query($query);
		if(!is_object($regs)) die("Error in SQL Query. Please check the SQL Query: ".$query);
		$regs->setFetchMode(PDO::FETCH_ASSOC);
		$resultset = $regs->fetchAll();

		if(sizeof($resultset) > 0){
			for($j = 0; $j < sizeof($resultset); $j++){
				$classToUse = get_class($this);

				$this->offsetSet($j, NULL);
				$this[$j] = new $classToUse();
				$column = 0;
				foreach($resultset[$j] as $property => $value){
					if(!is_numeric($property)){
						$type = $regs->getColumnMeta($column);
						if(empty($type['native_type'])) $type['native_type'] = 'VAR_CHAR';
						$type['native_type'] = preg_replace('@\([0-9]+\)@', '', $type['native_type']);
						$type['native_type'] = strtoupper($type['native_type']);
						$cast = 'toString';
						$this[$j]->_counter = 1;
						switch($type['native_type']){
							case 'LONG':
							case 'INTEGER':
							case 'INT':
								$this[$j]->_data[$property] = 0 + $value;
							break;
							case 'FLOAT':
							case 'VAR_STRING':
							case 'BLOB':
							case 'TEXT':
							case 'VARCHAR':
							default:
								$this[$j]->_data[$property] = $value;
							break;
						}
						$this[$j]->_dataAttributes[$property]['native_type'] = $type['native_type'];
						$column++;
					}
				}
// 				$i++;
			}
		}
		$this->_counter = $j;
		if($this->_counter === 1){
			foreach ($this[0]->_data as $field => $value){
				$this->_data[$field] = $value;
				$this->_dataAttributes[$field]['native_type'] = $type['native_type'];
			}
		}
		if($this->_counter === 0){
			$this->offsetSet(0, NULL);
			$this[0] = NULL;
			$this->_data = NULL;
			unset($this[0]);
		}
	}

	/**
	* Metodo publico Find()
	*
	* Este metodo se encarga de construir una consulta SQL de tipo selecciun.
	* Retorna este objeto luego de ejecutar el metodo @link getData() y los
	* parametros que recibe son $conditions, que puede ser:
	* o un arreglo de forma 'nombre_campo' => 'valor'.
	*
	* o un valor entero.
	*
	* Para el condicionamiento de la consulta y $fields, que es un string
	* con un listado de campos, separados por comas, para la seleccion de campos especificos.
	*
	* o Cuando el parametro $conditions es un entero, se realiza una consulta por el campo id.
	*
	* o Cuando el parametro $conditions es nulo o no se pasa ningun parametro, se selecciona todo sin condicion.
	*
	* @param array|integer $params (opcional) Puede ser entero o array y sirve para el condicionamiento de la consulta, este arreglo es de la forma 'campo' => 'valor'.
	* @access public
	*/
	public function Find($params = NULL){
		$this->_data = null;
		$this->__destruct();
		$this->__construct();
		$memcached = $this->checkMemcached();
		if(!empty($params)) $this->_params = $params;
		if(sizeof($this->before_find) >0){
			foreach($this->before_find as $functiontoRun){
				$this->{$functiontoRun}();
			}
		}

		//if(empty($this->_ObjTable)) $this->_TableName(); //$this->ObjTable = Plurals(strtolower(unCamelize(get_class($this))));

// 		$fields = '*';
// 		if(is_array($params) and isset($params['fields'])) $fields = $params['fields'];
		$sql = '';

		if(!empty($this->_params)){
			if(is_numeric($this->_params) && strpos($this->_params,',') === FALSE) $this->_params = (integer)$this->_params;
			$type = gettype($this->_params);
			$strint = '';
			switch($type){
				case 'integer':
					$sql .= " and `id` in ($this->_params)";
				break;
				case 'string':
					if(strpos($this->_params,',')!== FALSE){
						$sql .= " and `id` in ($this->_params)";
					}
				break;
				case 'array':
					if(!empty($this->_params['conditions'])){
						if(is_array($this->_params['conditions'])){
							$NotOnlyInt = FALSE;
							while(!$NotOnlyInt and (list($key, $value) = each($this->_params['conditions']))){
								$NotOnlyInt = (!is_numeric($key))? TRUE: FALSE;
							}
							if(!$NotOnlyInt){
								$sql .= " AND `id` in (".implode(',',$this->_params['conditions']).")";
							}else{
								foreach($this->_params['conditions'] as $field => $value){
									if(is_numeric($field)) $sql .= " AND ".$value;
									else $sql .= " AND $field='$value'";
								}
							}
						}elseif(is_string($this->_params['conditions'])){
							$sql .= " AND ".$this->_params['conditions'];
						}
					}
					if(isset($this->_params['group'])){
						$sql .= " GROUP BY `".$this->_params['group']."`";
					}
					if(isset($this->_params['sort'])){
						switch (gettype($this->_params['sort'])){
							case 'string':
								$sql .= " ORDER BY ".$this->_params['sort'];
							break;
							case 'array':
								null;
							break;
						}
					}

					if(isset($this->_params['limit'])){
						$sql .= " LIMIT ".$this->_params['limit'];
					}
					if(isset($this->_params[0])){
						switch($this->_params[0]){
						case ':first':
							$sql .= " LIMIT 1";
						break;
						}
					}
				break;
			}
		}
		$fields = (!is_array($this->_params) || (is_array($this->_params) && empty($this->_params['fields'])))? '*' : $this->_params['fields'];
		$sql = "SELECT {$fields} FROM `{$this->_TableName()}` WHERE 1=1" . $sql;
// 		$this->Connect();
		$this->_sqlQuery = $sql;
		if(CAN_USE_MEMCACHED){
			$key = md5($sql);
			$res = null;
			$res = $memcached->get($key);
			if($memcached->getResultCode() == 0 && is_object($res)){
				return $res;
			}
		}
		$this->getData($sql);
		$this->_sqlQuery = $sql;

		//$this->__construct();
		if(sizeof($this->after_find)>0){
			foreach($this->after_find as $functiontoRun){
				$this->{$functiontoRun}();
			}
		}
		if(CAN_USE_MEMCACHED){
			$memcached->set($key,$this);
		}
		return clone($this);
	}


	/**
	 * Metodo publico Find_by_SQL
	 *
	 * Ejecuta una consulta de tipo seleccion con la instruccion SQL pasada por parametro.
	 * Este metodo es muy util cuando se requiere hacer una consulta muy compleja.
	 * @param string $query Cadena con la consulta SQL.
	 */
	public function Find_by_SQL($query = NULL){
	if(!$query){
		trigger_error( "The query can not be NULL", E_USER_ERROR );
		exit;
	}else{
		//$this->Connect();
		$this->getData($query);
		//$this->__construct();
		return clone $this;
	}
	}

	/**
	* Metodo publico Niu()
	*
	* Prepara un nuevo objeto para insertar a la base de datos.
	* @param array $contents Arreglo de tipo 'campo'=>'valor' para pasar al objeto, para que al crear, le adicione datos.
	*/

	public function Niu($contents = NULL){
		$this->__destruct();
		$this->__construct();
		$this->_data = NULL;
		$this->_data = array();
		$this->Connect();
		$result = $this->driver->query("SHOW COLUMNS FROM `".$this->_TableName()."`");
		$type = array();
		foreach($result as $row){
			$type['native_type'] = $row['Type'];
			$type['native_type'] = preg_replace('@\([0-9]+\)@', '', $type['native_type']);
			$type['native_type'] = strtoupper($type['native_type']);
			$cast = 'toString';
			$toCast= false;
			switch($type['native_type']){
				case 'LONG':
				case 'INTEGER':
				case 'INT':
				case 'FLOAT':
				case 'DOUBLE':
					$toCast = true;
				break;
			}
			$value = '';
			$this->_counter = 0;
			if(isset($contents) and $contents !== NULL and is_array($contents)){
				if(isset($contents[$row['Field']])){
					$value = $contents[$row['Field']];
					$this->_counter = 1;
				}
			}
			$this->_data[$row['Field']] = $toCast ?  0 + $value : $value;
			$this[0]->_data[$row['Field']] = $value;
			$this->_dataAttributes[$row['Field']]['native_type'] = $type['native_type'];
		}
		return clone($this);
	}
	/**
	* Metodo publico Save()
	*
	* Guarda los datos en la base de datos.
	* Es capaz de diferenciar cuando realizar una consulta de tipo insert y cuando una de tipo update.
	* Si todo resulta bien, devuelve true, sino devuelve falso y no termina la ejecucion.
	*
	* Realiza validaciones requeridas y definidas en el modelo y ejecuta funciones definidas en el modelo tambien.
	*
	* Cuando se realiza una actualizacion, solo verifica los campos alterados y estos son los que se incluyen
	* en la consulta.
	* @return boolean
	**/

	function Save(){
		$this->Connect();
		$className = get_class($this);
		//if(!isset($this->ObjTable)) $this->ObjTable = Plurals(strtolower(unCamelize(get_class($this))));
		if(isset($this->validate) and is_array($this->validate)){
			foreach($this->validate as $evaluation => $content){
				switch($evaluation){
					case 'presence_of':
						$empty = false;
						foreach($content as $field){
							$val = $this->{$field};
							if(!isset($val) or $val == "" or $val == " "){
								$this->_error->add(array('field'=>$field,'message'=>'This field cannot be empty or null.'));
							}
						}
						if($this->_error->isActived()) return false;
					break;

					case 'unique':
						foreach($content as $field){
							$lists = array();
							$obj1 = new $className;
							$resultset = $obj1->Find(array('fields'=>$field, 'conditions'=>"`$field`='".$this->{$field}."' AND `id`<>'".$this->id."'"));
							if($resultset->counter()>0) $this->_error->add(array('field' => $field,'message'=>'This Field cannot be duplicated', 'code'=>212));
							if($this->_error->isActived()) return false;
						}
					break;

					case 'numeric':
						$noNumber = false;
						foreach($content as $field){
							if(isset($this->{$field}) and (!is_numeric($this->{$field}))){
								$this->_error->add(array('field' => $field,'message'=>'This Field must be numeric'));
							}
						}
						if($this->_error->isActived()) return false;
					break;
					case 'is_email':

						foreach($content as $field){
							if(count(preg_match("/(@+)/",$this->{$field})) > 1){
								$trace = debug_backtrace();
										trigger_error(
											'The email provided is not a valid email address: ' . $field,
											E_USER_NOTICE);
								return false;
							}
						}
					break;
				}
			}
		}
		//$this->__construct();
		if(sizeof($this->before_save)>0){
			foreach($this->before_save as $functiontoRun){
				$this->{$functiontoRun}();
			}
		}
		if($this->_error->isActived()) return FALSE;
		if($this->id and !empty($this->id) and $this->id != ''){
			$kind = "update";
			$query = "UPDATE `".$this->_TableName()."` SET ";
			$ThisClass = get_class($this);
			$objAux = new $ThisClass();

			$existing_data = $objAux->Find($this->id)->getArray();

			$arraux = array();
			$i=0;
			foreach($existing_data as $key => $data){
				if(is_array($data)){

					foreach($data as $field => $value){
						if(!is_array($value)){
							if(isset($this->{$field}) and $value !== $this->{$field} and strcmp($field, "created_at") !== 0 and strcmp($field, "updated_at") !== 0 and strcmp($field, "id") !== 0){
								$arraux[$field] = $this->{$field};
							}
						}
					}
				}else{
					if(isset($this->_data[$key]) and $data != $this->_data[$key] and strcmp($key, "created_at") !== 0 and strcmp($key, "updated_at") !== 0 and strcmp($key, "id") !== 0){
						$arraux[$key] = $this->_data[$key];
					}
				}
			}
				$arraux['updated_at'] = time();
			foreach($arraux as $key => $value){
				$query .= "`$key` = '$value',";
			}
			$query = substr($query, 0,-1);
			$query .= " WHERE id = ".$this->id;
		}else{
			$kind = "insert";
			$query = "INSERT INTO `".$this->_TableName()."` ";
			if(isset($this->before_insert[0])){
				foreach($this->before_insert as $functiontoRun){
					$this->{$functiontoRun}();
				}
			}
			$fields = "";
			$values = "";
			$i=1;
			$this->created_at = time();
			foreach($this->_data as $field => $value){
				if(!is_array($value)){
					if($field != 'id'){
						$fields .= "`$field`, ";
						$values .= "'$value', ";
					}
					$i++;
				}
			}
			$fields = substr($fields, 0, -2);
			$values = substr($values, 0, -2);
			$query .= "($fields) VALUES ($values)";
		}
		//$this->Connect();
		$this->_sqlQuery = $query;
		if(!$this->driver->exec($query)){
		    $e = $this->driver->errorInfo();
		    $this->_error->add(array('field' => $this->_TableName(),'message'=>$e[2]."\n $query"));
		    return FALSE;
		}
		if($kind == "insert"){
			$this->id = $this->driver->lastInsertId() + 0;
			if(sizeof($this->after_insert)>0){
				foreach($this->after_insert as $functiontoRun){
					$this->{$functiontoRun}();
				}
			}
		}
		if(sizeof($this->after_save)>0){
			foreach($this->after_save as $functiontoRun){
				$this->{$functiontoRun}();
			}
		}
		return true;
	}

	/**
	*
	* Crea un nuevo registro en la tabla cuando se tiene un id, este metodo se usa cuando se cargan
	* archivos xml del dumpeo de la tabla.
	*/

	function Insert(){
		//if(!isset($this->ObjTable)) $this->ObjTable = $this->Plurals(strtolower($this->unCamelize(get_class($this))));
		$query = "INSERT INTO `".$this->_TableName()."` ";
		$fields = "";
		$values = "";
		$this->created_at = time();
		foreach($this->_data as $field => $value){
			if(!is_array($value)){
					$fields .= "`$field`,";
					$values .= "'$value',";
			}
		}

		$fields = substr($fields, 0,-1);
		$values = substr($values, 0,-1);
		//echo 'Inserting '.$values.' in '.$this->_TableName().PHP_EOL;
		$query .= "($fields) VALUES ($values)";
		$this->driver->exec($query) or die($query.'-'.print_r($this->driver->errorInfo(), true));
		return true;
	}

	/**
	* Metodo Delete()
	*
	* Elimina registros en la Base de datos, dependiendo de las condiciones.
	*
	* El parametro de condiciones $conditions puede ser un arreglo de tipo 'nombre_campo'=>'valor' o
	* puede ser un valor entero, en este caso, se condicionara la busqueda por el id del registro.
	* @param array|numeric $conditions Condiciones para la eliminacion.
	**/
	function Delete($conditions = NULL){
		//if(!isset($this->ObjTable)) $this->ObjTable = Plurals(strtolower(unCamelize(get_class($this))));

		if($conditions === NULL and isset($this->id)) $conditions = $this->id;
		if($conditions === NULL and (!isset($this->id) or $this->id == '')){
			$this->_error->add(array('field' => $this->_TableName(),'message'=>"Must specify a register to delete"));
			return FALSE;
		}else{
			$query = "DELETE FROM `".$this->_TableName()."` ";
			if(is_numeric($conditions)){
				$this->id = $conditions;
				$query .= "WHERE id='$conditions'";
				$this->_delete_or_nullify_dependents((integer)$conditions) or print($this->_error);
			}elseif(is_array($conditions)){
				foreach($conditions as $field => $value){
					$query .= " and $field='$value'";
				}
			}
			if(sizeof($this->before_delete) >0){
				foreach($this->before_delete as $functiontoRun){
					$this->{$functiontoRun}();
				}
			}
			$this->Connect();
			if(!$this->driver->exec($query)){
			    $e = $this->driver->errorInfo();
			    $this->_error->add(array('field' => $this->_TableName(),'message'=>$e[2]."\n $query"));
			    return FALSE;
			}
			if(sizeof($this->after_delete) >0){
				foreach($this->after_delete as $functiontoRun){
					$this->{$functiontoRun}();
				}
			}
			return TRUE;
		}
	}
	/**
	 *
	 * borra o rompe el enlace con las tablas dependientes
	 * @param integer $id
	 * @return boolean
	 */
	protected function _delete_or_nullify_dependents($id){
		//verifyng dependencies
		if (!empty($this->dependents) and $id != 0){
			foreach ($this->has_many as $model){
				$model1 = Camelize($model);
				//$dependentObject = new $model1();
				$children = $this->{$model1}->Find(array('conditions'=>Singulars($this->_TableName())."_id='".$id."'"));
				foreach ($children as $child){
					switch ($this->dependents){
						case 'destroy':
							if(!$child->Delete()){
							    $this->_error->add(array('field' => $this->_TableName(),'message'=>"Cannot delete dependents"));
							    return FALSE;
							}
						break;
						case 'nullify':
							$child->{$this->_TableName().'_id'}='';
							if(!$child->Save()){
							    $this->_error->add(array('field' => $this->_TableName(),'message'=>"Cannot nullify dependents"));
							    return FALSE;
							}
						break;
					}

				}
			}
		}
		return true;
	}
	/**
	* Metodo publico inspect()
	*
	* Este metodo sirve para realizar debugging del objeto. Es muy util para rastrear contenido del objeto y
	* no se puede usar print_r() o echo o var_dump(). Este metodo llama al metodo __toString() para
	* completar la impresion del contenido.
	*
	*/
	public function inspect(){
		echo "\n".get_class($this)." ".gettype($this).": ".$this;
	}
	/**
	 * Se encarga de formar la cadena para mostrar cuando se invoca {@link __toString()}
	 * @param number $i
	 * @return string
	 */
	protected function ListProperties_ToString($i=0){
		$listProperties = "";
		$l = $i+1;
		$k=0;

		for($j=0; $j<$i; $j++){
			$listProperties .= "\t";
		}
		$listProperties .= "{\n";
		if($i>0){
			$listProperties .= $this->_counter;
		}
		if($this->_counter <= 1){
			foreach ($this->_data as $var => $value){
				ob_start();
				var_dump($value);
				$buffer = ob_get_clean();
				$listProperties .= "\t{$var} => ".$buffer;
			}
		} else {
			for ($m = 0; $m < $this->_counter; $m++){
				if(is_object($this[$m]) && get_parent_class($this[$m]) == 'ActiveRecord'){
					$listProperties .= "[$m] ";
					if(is_object($this[$m])){
						$listProperties .= get_class($this[$m]).' ';
					}
					$listProperties .= gettype($this[$m]);

					$listProperties .= "{\n";
					foreach ($this[$m]->_data as $var => $value){
						ob_start();
						var_dump($value);
						$buffer = ob_get_clean();
						$listProperties .= "\t{$var} => ".$buffer;
					}
					$listProperties .= "\t}\n";
				} else {
					$listProperties .= "[$m] =>".gettype($this[$m]).": ".$this[$m].PHP_EOL;
				}

			}
		}

		for($j=0; $j<$i; $j++){
			$listProperties .= "\t";
		}
		$listProperties .= "}\n";
		return $listProperties;
	}
	/**
	 * Metodo magico __toString()
	 *
	 * Este metodo es quien se encarga de tomar los atributos y sus valores contenidos en el objeto.
	 * @return string $a Cadena de texto con el listado de atributos y sus valores.
	 */
	public function __toString(){
		$a = $this->ListProperties_ToString();
		return $a;
	}

	/**
	* Metodo publico getArray()
	*
	* Este metodo devuelve en un arreglo multidimensional el objeto tratado y cargado con los atributos de la tabla.
	* @returns array $arraux Arreglo multidimensional que contiene atributos y/o valores que trae de la BD.
	*/
	public function getArray(){
		$arraux = array();
		$arraux1 = array();

		if($this->counter() > 0){
				$n=0;
		        for($t = 0; $t < $this->counter(); $t++){
		        	if(isset($this[$t]->_data)){
				        foreach($this[$t]->_data as $property => $value){
					        $arraux[$n][$property] = (is_object($value) and get_parent_class($value) == 'ActiveRecord')? $value->getArray():$value;
				        }
				        $n++;
			        }
		        }
		}
		return $arraux;
	}
	/**
	* Metodo publico Dump($data, $path)
	*
	* Crea un archivo Xml con el contenido de $data, en la ruta $path. Ideal para bajar datos a disco antes de
	* resetear una tabla.
	* @param array $dataDump Arreglo que contiene los datos para dumpear.
	* @param string $path Cadena que contiene la ruta a guardar el archivo Xml.
	*/
	function Dump($dataDump = array(), $path){
		//if(!isset($this->ObjTable)) $this->ObjTable = Plurals(strtolower(unCamelize(get_class($this))));
		$model = $this->_TableName();
		$dom = new DOMDocument('1.0', 'utf-8');

		$sroot = $dom->appendChild(new DOMElement('table_'.$model));
		if(isset($dataDump[0])){
			foreach($dataDump as $reg){
				$root = $sroot->appendChild(new DOMElement($model));
				foreach($reg as $element => $value){
					if(preg_match("(&|<|>)", $value)){
						$value = $dom->createCDATASection($value);
						$element = $root->appendChild(new DOMElement($element, ""));
						$element->appendChild($value);
					}else{
						$element = $root->appendChild(new DOMElement($element, $value));
					}
				}
			}
		}else{
			$root = $sroot->appendChild(new DOMElement($model));
			foreach($dataDump as $element => $value){
				if(preg_match("(&|<|>)", $value)){
					$value = $dom->createCDATASection($value);
					$element = $root->appendChild(new DOMElement($element, ""));
					$element->appendChild($value);
				}else{
					$element = $root->appendChild(new DOMElement($element, $value));
				}
			}
		}

		$fp = fopen($path.$model.'.xml', 'w+b');
		fwrite($fp, $dom->saveXML());
		fclose($fp);
	}
	/**
	* Metodo publico LoadDump($docXml)
	*
	* Carga un archivo Xml que fue dumpeado anteriormente.
	* @param string $docXml Nombre del archivo a cargar.
	*/
	function LoadDump($docXml){
		$doc = new DOMDocument;
		$doc->load(INST_PATH.'migrations/dumps/'.$docXml);
		$tblName = str_replace('.xml', '', $docXml);
		$items = $doc->getElementsByTagName($tblName);
		foreach( $items as $xitem ){
			$idfield = $xitem->getElementsByTagName("id");
			$id  = $idfield->item(0)->nodeValue;
			$Obj = Camelize(Singulars($tblName));
			$Obj = new $Obj();
			$Obj->Niu();
			$arrObj = $Obj->GetFields();
			$Obj->id = $id;
			foreach($arrObj as $key => $value){
				if($key != 'table'){
					$field = $xitem->getElementsByTagName("$key");
					$Obj->{$key} = (is_object($field->item(0)))?addslashes($field->item(0)->nodeValue):'';
				}
			}
			$Obj->Insert();
		}

	}
	/**
	* Metodo publico WriteSchema($tableName)
	*
	* Este metodo se encarga de ir creando el schema de la base de datos para posterior indexacion sin necesidad de
	* consultarlo a la base de datos.
	* @param string $tableName Nombre de la tabla para registrar en el schema.
	*/
	function WriteSchema($tableName){
		$createFile = FALSE;
		$stringtoINI = '';
		$file = INST_PATH.'migrations/Schema.ini';
		if (!$schema = parse_ini_file($file, TRUE)){
			$createFile = TRUE;
		}
		if($createFile){
			$stringtoINI .= "[$tableName] \n";
			$fp = fopen($file, "w+b");
			fwrite($fp, $stringtoINI);
			fclose($fp);
		}elseif(!in_array($tableName, $schema)){
			$schema[$tableName] = "";
			$stringtoINI = "";
			foreach($schema as $table => $val){
				$stringtoINI .= "[$table] \n";
			}
			$fp = fopen($file, "w+b");
			fwrite($fp, $stringtoINI);
			fclose($fp);
		}
	}
	/**
	* Metodo publico WriteModel($table)
	*
	* Este metodo se encarga de escribir en disco el archivo preliminar de modelo.
	* @param string $table Nombre de la tabla para crearle el modelo.
	*/
	function WriteModel($table){
		$file = Singulars($table).".php";
		$modelpath = INST_PATH.'app/models/';
		if(!file_exists($modelpath.$file)){
			$newModel = "<?php\n";
			$newModel .= "class ".Camelize(str_replace('.php', '', $file))." extends ActiveRecord {\n";
			$newModel .= "\tfunction __construct(){\n";
			$newModel .= "\t}\n";
			$newModel .= "}\n";
			$newModel .= "?>";
			$fp=fopen($modelpath.$file, "w+b");
			fputs($fp, $newModel);
			fclose($fp);
		}
	}
	/**
	* Metodo publico GetFields()
	*
	* Este metodo se encarga de leer los campos de la tabla y sus atributos. Devuelve un arreglo de tipo
	* arreglo[nombre_campo] = tipo_del_campo.
	*/
	function GetFields(){
		$this->Connect();

		//if(!isset($this->ObjTable) or $this->ObjTable != '') $this->ObjTable = Plurals(strtolower(unCamelize(get_class($this))));

		$result = $this->driver->query("SHOW COLUMNS FROM `".$this->_TableName()."`") or die(print_r($this->driver->errorInfo(), true));

		$arraux = array();
		foreach($result as $row){
			$arraux[$row['Field']] = $row['Type'];
		}
		return $arraux;
	}

	/**
	 * Metodo publico BuildMigration()
	 *
	 * Construye un archivo de migracion en base del modelo.
	 *
	 * Esto se usa cuando la base de datos ya exista y no se requiera hacer los archivos de
	 * migracion para ahorrar tiempo.
	 */
	function BuildMigration(){
		//if(!isset($this->ObjTable)) $this->ObjTable = Plurals(strtolower(get_class($this)));

	}
	/**
	 * Retorna los errores ocurridos en la consulta
	 * @return Error
	 */
	public function getError(){
		return $this->_errors;
	}
	/**
	 * Retorna el numero de registros en el objeto actual
	 * @return number
	 */
	public function counter(){
		return (integer)$this->_counter;
	}
	/**
	 * Obtiene el primer registro
	 */
	public function first(){
		return $this[0];
	}
	/**
	 * Obtiene el utlimo registro
	 */
	public function last(){
		return sizeof($this) > 0? $this[sizeof($this) - 1] : FALSE;
	}
	/**
	 *
	 * Get or set the name of the table.
	 * @param string $name
	 */
	public function _TableName($name = null){
		if(!empty($name)){
			$this->_ObjTable = $name;
		}elseif(empty($this->_ObjTable) or strlen($this->_ObjTable) < 1){
			$className =  unCamelize(get_class($this));
			$words = explode("_", $className);
			$i = sizeof($words) - 1;
			$words[$i] = Plurals($words[$i]);
			$this->_ObjTable = implode("_", $words);
		}
		return $this->_ObjTable;
	}
	/**
	 * Retorna el query ejecutado
	 * @return string
	 */
	public function _sqlQuery(){
	    return $this->_sqlQuery;
	}
	/**
	 * Retorna el tipo nativo del campo especifico
	 * @param string $field
	 */
	public function _nativeType($field){
		return $this->_dataAttributes[$field]['native_type'];
	}
	/**
	 * Slices an Active Record object resulset into pieces delimited by start and length
	 * @param integer $start
	 * @param integer $length
	 */
	public function slice($start = null, $length = null){
		if(empty($length)) $length = $this->_counter;
		if($start === null) $start = 0;
		$end = $start + $length;
		if ($end > $this->_counter) $end = $this->_counter;
		$name = get_class($this);
		$arr = new $name();
		for($i=$start; $i<$end; $i++){
			$arr[] = $this[$i];
		}
		return $arr;
	}
}
?>
