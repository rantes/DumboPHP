<?php
require 'errors.php';
require "Driver.php";
/**
 * Clase ActiveRecord.
 *
 * @author Javier Serrano <rantes.javier@gmail.com>
 * @version 1.0
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Javier Serrano <http://www.rantes.info/>
 * @package Core
 * @extends Core_General_Class
 */
/**
 * Clase ActiveRecord.
 *
 * Contiene todos los m?todos para la implementaci?n de Active Records
 * y Mapeo de Objetos a trav?s de relaciones. No tiene instanciamiento.
 *
 * @author Javier Serrano <rantes.javier@gmail.com>
 * @version 1.0
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Javier Serrano <http://www.rantes.info/>
 * @package Core
 * @extends Core_General_Class
 * @access abstract
 */
 abstract class ActiveRecord extends Core_General_Class{
	/**
	* Variable protegida $ObjTable
	*
	* Es una cadena de texto que contiene el nombre de la tabla del objeto actual.
	* @var array $ObjTable
	* @access protected
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
	 * Objeto de tipo pdo para la conexi?n global de bases de datos.
	 * @var Object_PDO $driver
	 */
	public $driver = NULL;

	/**
	 * Variable protegida $_counter
	 *
	 * La Variable p?blica counter es para obtener el n?mero de registros existentes en el objeto
	 * cuando se ha efectuado una b?squeda por el m?todo {@link Find()}.
	 * @var integer $counter
	 * @default 0
	 * @access protected
	 */
	protected $_counter = 0;

	/**
	 * Variable de tipo array $has_many.
	 *
	 * ('Tiene varios') Indica cada una de las tablas con las que tiene relaci?n de tipo uno a muchos.
	 * Cada posici?n de arreglo es una tabla.
	 * @var array $has_many
	 * @access protected
	 */
	protected $has_many = array();
	/**
	 * Variable de tipo array $has_one.
	 *
	 * ('Tiene uno') Indica cada una de las tablas con las que tiene relaci?n de tipo uno a uno.
	 * Cada posicion de arreglo es una tabla.
	 * @var array $has_one
	 * @access protected
	 */
	protected $has_one = array();
	/**
	 * Variable de tipo array $belongs_to.
	 *
	 * ('pertenece a') Indica cada una de las tablas con las que tiene relaci?n de pertenencia.
	 * Cada posici?n de arreglo es una tabla.
	 * @var array $belongs_to
	 * @access protected
	 */
	protected $belongs_to = array();
	/**
	 * Variable de tipo array $has_many_and_belongs_to.
	 *
	 * ('Tiene varios y Pertenece a') Indica cada una de las tablas con las que tiene relaci?n de uno a muchos y de pertenencia al mismo tiempo.
	 * Se utiliza generalmente con tablas que se relacionan consigo mismas. Cada posici?n de arreglo es una tabla.
	 * @var array $has_many_and_belongs_to
	 * @access protected
	 */
	protected $has_many_and_belongs_to = array();
	/**
	 * Variable de tipo array $validate.
	 *
	 * ('Validar') Indica cada uno de los tipos de validaci?n que se requieran y los campos a validar.
	 * Cada llave del arreglo indica el tipo de validaci?n y cada elemento contenido
	 * entre cada arreglo de validaci?n es un campo para validar.
	 * ejemplo: <pre>$this->validate = array("typo_de_validacion"=> array('campo1','campo2','campon'));</pre>
	 * @var array $validate
	 * @access protected
	 */
	protected $validate = array();
	/**
	 * Variable de tipo array $before_insert.
	 *
	 * ('antes de insertar') Indica cada una de las funciones a ejecutar antes de crear un registro nuevo.
	 * Este callback se invoca antes de ejecutar una consulta de inserci?n.
	 * Cada posici?n de arreglo es el nombre de una funci?n.
	 * Cada funci?n implicada debe ser definida dentro del modelo, desp?es de el metodo __construct().
	 * @var array $before_insert
	 */
	protected $before_insert = array();
	protected $after_insert = array();
	/**
	 * Variable de tipo array $after_find.
	 *
	 * ('despues de buscar') Indica cada una de las funciones a ejecutar despues de buscar en la BD.
	 * Este callback se invoca despues de ejecutar una consulta de selecci?n.
	 * Cada posici?n de arreglo es el nombre de una funci?n.
	 * Cada funci?n implicada debe ser definida dentro del modelo, desp?es de el metodo __construct().
	 * @var array $after_find
	 */
	protected $after_find = array();
	/**
	 * Variable de tipo array $before_find.
	 *
	 * ('antes de buscar') Indica cada una de las funciones a ejecutar antes de buscar en la BD.
	 * Este callback se invoca antes de ejecutar una consulta de selecci?n.
	 * Cada posici?n de arreglo es el nombre de una funci?n.
	 * Cada funci?n implicada debe ser definida dentro del modelo, desp?es de el metodo __construct().
	 * @var array $before_find
	 */
	protected $before_find = array();
	/**
	 * Variable de tipo array $after_save.
	 *
	 * ('despues de guardar') Indica cada una de las funciones a ejecutar despues de guardar un registro en la BD.
	 * Cada posici?n de arreglo es el nombre de una funci?n.
	 * Cada funci?n implicada debe ser definida dentro del modelo, desp?es de el metodo __construct().
	 * @var array $after_save
	 */
	protected $after_save = array();

	/**
	 * Variable de tipo array $before_save.
	 *
	 * ('antes de guardar') Indica cada una de las funciones a ejecutar antes de guardar un registro en la BD.
	 * Cada posici?n de arreglo es el nombre de una funci?n.
	 * Cada funci?n implicada debe ser definida dentro del modelo, desp?es de el metodo __construct().
	 * @var array $before_save
	 */
	protected $before_save = array();
	/**
	 * Variable de tipo array $after_delete.
	 *
	 * ('despues de borrar') Indica cada una de las funciones a ejecutar despues de borrar un registro en la BD.
	 * Cada posici?n de arreglo es el nombre de una funci?n.
	 * Cada funci?n implicada debe ser definida dentro del modelo, desp?es de el metodo __construct().
	 * @var array $after_delete
	 */
	protected $after_delete = array();
	/**
	 * Variable de tipo array $before_delete.
	 *
	 * ('antes de borrar') Indica cada una de las funciones a ejecutar antes de borrar un registro en la BD.
	 * Cada posici?n de arreglo es el nombre de una funci?n.
	 * Cada funci?n implicada debe ser definida dentro del modelo, desp?es de el metodo __construct().
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

	protected $_dataAttributes = array();

	protected $_models = array();

	/**
	*
	* Objeto de tipo Error $_error
	*
	* Este objeto controla y muestra los errores ocurridos durante los procesos de activerecord.
	* @var Error $_error
	**/
	public $_error = NULL;

	public $_sqlQuery = '';

	/**
	 * Constructor
	 *
	 * Realiza conecci?n a la base de datos mediante el m?todo {@link connect()}
	 */
	function __construct(){
		//parent::__construct(array(), ArrayObject::ARRAY_AS_PROPS);
		$this->_data = NULL;
		$this->_data = array();
		//$this->_TableName();
		$this->Connect();

	}
	/**
	 * Destructor
	 *
	 */
	function __destruct(){
		$this->_data = NULL;
		$this->_data = array();
		$this->_error = NULL;
	}
	/**
	 * m?todo m?gico __set()
	 *
	 * Este m?todo se ejecuta cuando se crea un nuevo atributo al objeto.
	 * Carga datos en la variable {@link $data}.
	 *
	 * Los valores creados, luego pueden ser accedidos directamente gracias al m?todo {@link __get()}
	 * @param string $name Nombre del atributo a crear.
	 * @param mixed $value Valor del atributo.
	 */
	public function __set($name, $value) {
		try{
//		if(is_object($value) and strcmp(get_parent_class($value),'ActiveRecord')===0):
//			$this->_models[$name] = $value;
//		else:
			$this->_data[$name] = $value;
//		endif;

		}
		catch(Exception $e){
			$trace = debug_backtrace();
			trigger_error(
				'error:'.$e,
				E_USER_NOTICE);
		}
		//$this->ksort();
	}
	public function __unset($name) {
		$this->_data[$name] = NULL;
		unset($this->_data[$name]);
	}
	public function __isset($var){
		if(!empty($this->_data) and array_key_exists($var, $this->_data)) return TRUE;
		return FALSE;
	}
	public function getIterator() {
            return new ArrayIterator($this);
    }
	/**
	 * m?todo m?gico __get()
	 *
	 * Este m?todo se ejecuta cuando se accede a un attributo directamente del objeto y que no existe.
	 *
	 * Retorna el valor del atributo din?mico.
	 * @param string $name Nombre del atributo que se quiere acceder.
	 */
	public function __get($name) {
//		if (isset($this->_models[$name])):
//			return $this->_models[$name];
//		else:
			switch($name):
				case '_ObjTable':
					return $this->_TableName();
				break;
				case '_errors':
					return $this->_errors;
				break;
				default:

					if (isset($this->_data[$name])):
						return $this->_data[$name];
					else:
						$model = unCamelize($name);
						if(file_exists(INST_PATH.'app/models/'.$model.'.php')):
							if(!class_exists($name)):
								require INST_PATH.'app/models/'.$model.'.php';
							endif;
							$this->_data[$name] = new $name();
							return $this->_data[$name];
						else:
							return null;
						endif;
					endif;
				break;
			endswitch;
//		endif;
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
		 * El archivo driver.php es el archivo controlador para la conexi?n y tratamiento de la base de datos.
		 */

		if($this->driver === NULL and !is_object($this->driver) and get_class($this->driver) != 'Driver'):
			$this->driver = new Driver(INST_PATH.'config/db_settings.ini');
		endif;
		//$this->ObjTable = Plurals(strtolower(unCamelize(get_class($this))));
		$this->_error = new Errors();
//		$pathModels=INST_PATH.'app/models/';
//		$directorio=dir($pathModels);
//		$objects = array();
//		$classes = get_declared_classes();
//		while (($archivo = $directorio->read()) != FALSE):
//			if($archivo !="." and $archivo != ".." and $archivo != "_notes" and preg_match('/.php/', $archivo)):

//				$nameTable = str_replace('.php', '', $archivo);
//				$nameCons = strtoupper($nameTable);
//				$class = Camelize($nameTable);
//				if(!in_array($class, $classes)) include($pathModels.$archivo);
//				global $$class;
//				$this->{$class} = new $class();
				//$objects[$nameCons] = $nameTable;
//			endif;
//		endwhile;

//		$directorio->close();
	}

	/**
	* Metodo privado getData()
	*
	* Este metodo se encarga de recrear los atributos del objeto.
	* Lee los nombres de los campos y sus valores de la consulta realizada y de acuerdo con eso
	* genera los nuevos atributos al objeto propio a trav?s de $this, siendo en el caso de un resultado de un
	* solo registro o un arreglo de objetos de este mismo tipo como atributo de este objeto.
	*/
	private function getData($query){

		$this->_data = NULL;
		$this->_data = array();

		if(sizeof($this) >0):
			for($k = 0; $k<sizeof($this); $k++):
				if(isset($this[$k])) $this->offsetUnSet($k);
			endfor;
		endif;
		for($k = 0; $k<sizeof($this->_data); $k++):
			array_pop($this->_data);
		endfor;
		foreach($this as $index => $obj):
			$obj = null;
			unset($obj);
			$this->offsetUnSet($index);
		endforeach;
		if(isset($this->_data)):
			foreach($this->_data as $key => $val):
				$this->_data[$key] = NULL;
				unset($this->_data[$key]);
			endforeach;
		endif;

		$result = array();
		$i=0;
		$regs = NULL;

		$this->Connect();
		$regs = $this->driver->query($query);
		if(!is_object($regs)) die("Error in SQL Query. Please check the SQL Query: ".$query);
		$regs->setFetchMode(PDO::FETCH_ASSOC);
		$resultset = $regs->fetchAll();

		if(sizeof($resultset) > 0):
			for($j = 0; $j < sizeof($resultset); $j++):
				$classToUse = get_class($this);

				$this->offsetSet($i, NULL);
				$this[$j] = new $classToUse();
				$column = 0;
				foreach($resultset[$j] as $property => $value):
					if(!is_numeric($property)):
						$type = $regs->getColumnMeta($column);
// 						$this[$j]->{$property} = new ActiveRegister($value, $type);//$value;
						$deftype = 'VAR_CHAR';
						$type['native_type'] = $deftype;
						$type['native_type'] = preg_replace('@\([0-9]+\)@', '', $type['native_type']);
						$type['native_type'] = strtoupper($type['native_type']);
						$cast = 'toString';
						switch($type['native_type']):
							case 'LONG':
							case 'INTEGER':
							case 'INT':
								$cast = 'toInteger';
							break;
							case 'FLOAT':
							case 'VAR_STRING':
							case 'BLOB':
							case 'TEXT':
							case 'VARCHAR':
								$cast = 'toString';
							break;
						endswitch;
						$this[$j]->_data[$property] = $cast($value);//$value;
						$this[$j]->_dataAttributes[$property]['native_type'] = $type['native_type'];
						$column++;
					endif;
				endforeach;
				$i++;
			endfor;
		endif;

		$this->_counter = $i;
		if($this->_counter === 1):
			foreach ($this[0]->_data as $field => $value):
				$this->{$field} = $value;
				$this->_dataAttributes[$field]['native_type'] = $type['native_type'];
			endforeach;
		endif;
		if($this->_counter === 0):
			$this[0] = NULL;
			$this->_data = NULL;
			unset($this[0]);
		endif;
	}

	/**
	* M?todo p?blico Find()
	*
	* Este m?todo se encarga de construir una consulta SQL de tipo selecci?n.
	* Retorna este objeto luego de ejecutar el m?todo {@link getData()} y los
	* par?metros que recibe son $conditions, que puede ser:
	* o un arreglo de forma 'nombre_campo' => 'valor'.
	*
	* o un valor entero.
	*
	* Para el condicionamiento de la consulta y $fields, que es un string
	* con un listado de campos, separados por comas, para la selecci?n de campos espec?ficos.
	*
	* o Cuando el par?metro $conditions es un entero, se realiza una consulta por el campo id.
	*
	* o Cuando el par?metro $conditions es nulo o no se pasa ning?n par?metro, se selecciona todo sin condici?n.
	*
	* @todo completar la implementacion de la opcion sort.
	* @param array|integer $conditions (opcional) Puede ser entero o array y sirve para el condicionamiento de la consulta, este arreglo es de la forma 'campo' => 'valor'.
	* @param string $fields (opcional) Cadena que contiene los campos separados por comas.
	* @access public
	*/
	public function Find($params = NULL){
		$this->_data = null;
		$this->__destruct();
		$this->__construct();
		if(sizeof($this->before_find) >0):
			foreach($this->before_find as $functiontoRun):
				$this->{$functiontoRun}();
			endforeach;
		endif;

		//if(empty($this->_ObjTable)) $this->_TableName(); //$this->ObjTable = Plurals(strtolower(unCamelize(get_class($this))));

		$fields = '*';
		if(is_array($params) and isset($params['fields'])) $fields = $params['fields'];
		$sql = "SELECT $fields FROM `".$this->_TableName()."` WHERE 1=1";

		if(isset($params) and !empty($params)):
			if(is_numeric($params)) $params = (integer)$params;
			$type = gettype($params);
			$strint = '';
			switch($type):
				case 'integer':
					$sql .= " and id in ($params)";
				break;
				case 'string':
					if(strpos($params,',')!== FALSE):
						$sql .= " and id in ($params)";
					endif;
				break;
				case 'array':
					if(isset($params['conditions'])):
						if(is_array($params['conditions'])):
							$NotOnlyInt = FALSE;
							while(!$NotOnlyInt and (list($key, $value) = each($params['conditions']))):
								$NotOnlyInt = (!is_numeric($key))? TRUE: FALSE;
							endwhile;
							if(!$NotOnlyInt):
								$sql .= " and id in (".$this->ToList($params['conditions']).")";
							else:
								foreach($params['conditions'] as $field => $value){
									if(is_numeric($field)) $sql .= " and ".$value;
									else $sql .= " and $field='$value'";
								}
							endif;
						elseif(is_string($params['conditions'])):
							$sql .= " and ".$params['conditions'];
						endif;
					endif;
					if(isset($params['group'])):
						$sql .= " GROUP BY `".$params['group']."`";
					endif;
					if(isset($params['sort'])):
						switch (gettype($params['sort'])):
							case 'string':
								$sql .= " ORDER BY ".$params['sort'];
							break;
							case 'array':
								null;
							break;
						endswitch;
					endif;

					if(isset($params['limit'])):
						$sql .= " LIMIT ".$params['limit'];
					endif;
					if(isset($params[0])):
						switch($params[0]):
						case ':first':
							$sql .= " LIMIT 1";
						break;
						endswitch;
					endif;
				break;
			endswitch;
		endif;
		//$this->Connect();
		$this->getData($sql);
		$this->_sqlQuery = $sql;

		//$this->__construct();
		if(sizeof($this->after_find)>0):
			foreach($this->after_find as $functiontoRun):
				$this->{$functiontoRun}();
			endforeach;
		endif;
		return clone($this);
	}


	/**
	 * M?todo p?blico Find_by_SQL
	 *
	 * Ejecuta una consulta de tipo selecci?n con la instrucci?n SQL pasada por par?metro.
	 * Este m?todo es muy ?til cuando se requiere hacer una consulta muy compleja.
	 * @param string $query Cadena con la consulta SQL.
	 */
	public function Find_by_SQL($query = NULL){
	if(!$query):
		trigger_error( "The query can not be NULL", E_USER_ERROR );
		exit;
	else:
		//$this->Connect();
		$this->getData($query);
		//$this->__construct();
		return clone $this;
	endif;
	}

	/**
	* M?todo p?blico Niu()
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
		foreach($result as $row):
			$type['native_type'] = $row['Type'];
			$type['native_type'] = preg_replace('@\([0-9]+\)@', '', $type['native_type']);
			$type['native_type'] = strtoupper($type['native_type']);
			$cast = 'toString';
			$toCast= false;
			switch($type['native_type']):
				case 'LONG':
				case 'INTEGER':
				case 'INT':
					$toCast = true;
// 					$cast = 'toInteger';
				break;
// 				case 'FLOAT':
// 				case 'VAR_STRING':
// 				case 'BLOB':
// 				case 'TEXT':
// 				case 'VARCHAR':
// 					$cast = 'toString';
// 				break;
			endswitch;
			$value = '';
			$this->_counter = 0;
			if(isset($contents) and $contents !== NULL and is_array($contents)):
				if(isset($contents[$row['Field']])):
// 					$value = $cast($contents[$row['Field']]);
					$value = $toCast ? (integer)$contents[$row['Field']] : $contents[$row['Field']];
					$this->_counter = 1;
				else:
					continue;
				endif;
			endif;
			$this->{$row['Field']} = $value;
			$this[0]->{$row['Field']} = $value;
			$this->_dataAttributes[$row['Field']]['native_type'] = $type['native_type'];
		endforeach;
		return clone($this);
	}
	/**
	* M?todo p?blico Save()
	*
	* Guarda los datos en la base de datos.
	* Es capaz de diferenciar cuando realizar una consulta de tipo insert y cuando una de tipo update.
	* Si todo resulta bien, devuelve true, sino devuelve falso y no termina la ejecuci?n.
	*
	* Realiza validaciones requeridas y definidas en el modelo y ejecuta funciones definidas en el modelo tambi?n.
	*
	* Cuando se realiza una actualizaci?n, solo verifica los campos alterados y estos son los que se incluyen
	* en la consulta.
	* @return boolean
	**/

	function Save(){
		$this->Connect();
		$className = get_class($this);
		//if(!isset($this->ObjTable)) $this->ObjTable = Plurals(strtolower(unCamelize(get_class($this))));
		if(isset($this->validate) and is_array($this->validate)):
			foreach($this->validate as $evaluation => $content){
				switch($evaluation):
					case 'presence_of':
						$empty = false;
						foreach($content as $field){
							$val = $this->{$field};
							if(!isset($val) or $val == "" or $val == " "):
								$this->_error->add(array('field'=>$field,'message'=>'This field cannot be empty or null.'));
//                                $trace = debug_backtrace();
//								trigger_error(
//									'This Field cannot be empty or null: ' . $field,
//									E_USER_NOTICE);
//								$empty = true;
							endif;
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
							if(isset($this->{$field}) and (!is_numeric($this->{$field}))):
								$this->_error->add(array('field' => $field,'message'=>'This Field must be numeric'));
							endif;
						}
						if($this->_error->isActived()) return false;
					break;
					case 'is_email':

						foreach($content as $field){
							if(count(preg_match("/(@+)/",$this->{$field})) > 1):
								$trace = debug_backtrace();
										trigger_error(
											'The email provided is not a valid email address: ' . $field,
											E_USER_NOTICE);
								return false;
							endif;
						}
					break;
				endswitch;
			}
		endif;
		$this->__construct();
		if(sizeof($this->before_save)>0):
			foreach($this->before_save as $functiontoRun){
				$this->{$functiontoRun}();
			}
		endif;
		if($this->_error->isActived()) return FALSE;
		if($this->id and !empty($this->id) and $this->id != ''):
			$kind = "update";
			$query = "UPDATE `".$this->_TableName()."` SET ";
			$ThisClass = get_class($this);
			$objAux = new $ThisClass();

			$existing_data = $objAux->Find($this->id)->getArray();

			$arraux = array();
			$i=0;
			foreach($existing_data as $key => $data):
				if(is_array($data)):

					foreach($data as $field => $value):
						if(!is_array($value)):
							if(isset($this->{$field}) and $value !== $this->{$field} and strcmp($field, "created_at") !== 0 and strcmp($field, "updated_at") !== 0 and strcmp($field, "id") !== 0):
								$arraux[$field] = $this->{$field};
							endif;
						endif;
					endforeach;
				else:
					if(isset($this->_data[$key]) and $data != $this->_data[$key] and strcmp($key, "created_at") !== 0 and strcmp($key, "updated_at") !== 0 and strcmp($key, "id") !== 0):
						$arraux[$key] = $this->_data[$key];
					endif;
				endif;
			endforeach;
				$arraux['updated_at'] = time();
			foreach($arraux as $key => $value):
				$query .= "`$key` = '$value',";
			endforeach;
			$query = substr($query, 0,-1);
			$query .= " WHERE id = ".$this->id;
		else:
			$kind = "insert";
			$query = "INSERT INTO `".$this->_TableName()."` ";
			if(isset($this->before_insert[0])):
				foreach($this->before_insert as $functiontoRun){
					$this->{$functiontoRun}();
				}
			endif;
			$fields = "";
			$values = "";
			$i=1;
			$this->created_at = time();
			$this->updated_at = 0;
			foreach($this->_data as $field => $value){
				if(!is_array($value)):
					if($field != 'id'):
						$fields .= "`$field`, ";
						$values .= "'$value', ";
					endif;
					$i++;
				endif;
			}
			$fields = substr($fields, 0, -2);
			$values = substr($values, 0, -2);
			$query .= "($fields) VALUES ($values)";
		endif;
		//$this->Connect();
		$this->_sqlQuery = $query;
		if(!$this->driver->exec($query)):
		    $e = $this->driver->errorInfo();
		    $this->_error->add(array('field' => $this->_TableName(),'message'=>$e[2]."\n $query"));
		    return FALSE;
		endif;
		if($kind == "insert"):
			$this->id = $this->driver->lastInsertId();
			if(sizeof($this->after_insert)>0):
				foreach($this->after_insert as $functiontoRun):
					$this->{$functiontoRun}();
				endforeach;
			endif;
		endif;
		if(sizeof($this->after_save)>0):
			foreach($this->after_save as $functiontoRun):
				$this->{$functiontoRun}();
			endforeach;
		endif;
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
			if(!is_array($value)):
					$fields .= "`$field`,";
					$values .= "'$value',";
			endif;
		}

		$fields = substr($fields, 0,-1);
		$values = substr($values, 0,-1);
		$query .= "($fields) VALUES ($values)";
		$this->driver->exec($query) or die(print_r($this->driver->errorInfo(), true));
		return true;
	}

	/**
	* M?todo Delete()
	*
	* Elimina registros en la Base de datos, dependiendo de las condiciones.
	*
	* El par?metro de condiciones $conditions puede ser un arreglo de tipo 'nombre_campo'=>'valor' o
	* puede ser un valor entero, en este caso, se condicionar? la b?squeda por el id del registro.
	* @param array|numeric $conditions Condiciones para la eliminacion.
	**/
	function Delete($conditions = NULL){
		//if(!isset($this->ObjTable)) $this->ObjTable = Plurals(strtolower(unCamelize(get_class($this))));

		if($conditions === NULL and isset($this->id)) $conditions = $this->id;
		if($conditions === NULL and (!isset($this->id) or $this->id == '')):
			$this->_error->add(array('field' => $this->_TableName(),'message'=>"Must specify a register to delete"));
			return FALSE;
		else:
			$query = "DELETE FROM `".$this->_TableName()."` ";
			if(is_numeric($conditions)):
				$this->id = $conditions;
				$query .= "WHERE id='$conditions'";
				$this->_delete_or_nullify_dependents((integer)$conditions) or print($this->_error);
			elseif(is_array($conditions)):
				foreach($conditions as $field => $value){
					$query .= " and $field='$value'";
				}
			endif;
			if(sizeof($this->before_delete) >0):
				foreach($this->before_delete as $functiontoRun){
					$this->{$functiontoRun}();
				}
			endif;
			$this->Connect();
			if(!$this->driver->exec($query)):
			    $e = $this->driver->errorInfo();
			    $this->_error->add(array('field' => $this->_TableName(),'message'=>$e[2]."\n $query"));
			    return FALSE;
			endif;
			if(sizeof($this->after_delete) >0):
				foreach($this->after_delete as $functiontoRun){
					$this->{$functiontoRun}();
				}
			endif;
			return TRUE;
		endif;
	}
	/**
	 *
	 * borra o rompe el enlace con las tablas dependientes
	 * @param integer $id
	 * @return boolean
	 */
	protected function _delete_or_nullify_dependents($id){
		//verifyng dependencies
		if (!empty($this->dependents) and $id != 0):
			foreach ($this->has_many as $model):
				$model1 = Camelize($model);
				//$dependentObject = new $model1();
				$children = $this->{$model1}->Find(array('conditions'=>Singulars($this->_TableName())."_id='".$id."'"));

				foreach ($children as $child):
					switch ($this->dependents):
						case 'destroy':
							if(!$child->Delete()):
							    $this->_error->add(array('field' => $this->_TableName(),'message'=>"Cannot delete dependents"));
							    return FALSE;
							endif;
						break;
						case 'nullify':
							$child->{$this->_TableName().'_id'}='';
							if(!$child->Save()):
							    $this->_error->add(array('field' => $this->_TableName(),'message'=>"Cannot nullify dependents"));
							    return FALSE;
							endif;
						break;
					endswitch;

				endforeach;
			endforeach;
		endif;
		return true;
	}
	/**
	* M?todo p?blico inspect()
	*
	* Este m?todo sirve para realizar debugging del objeto. Es muy ?til para rastrear contenido del objeto y
	* no se puede usar print_r() o echo o var_dump(). Este m?todo llama al m?todo {@link __toString()} para
	* completar la impresi?n del contenido.
	*
	*/
	public function inspect(){
		echo "\n".get_class($this)." ".gettype($this).": ".$this;
	}
	/**
	* M?todo p?blico m?gico __toString()
	*
	* Este m?todo es quien se encarga de tomar los atributos y sus valores contenidos en el objeto.
	* @return string $listProperties Cadena de texto con el listado de atributos y sus valores.
	*/
	//protected $tabs;
	public function ListProperties_ToString($i=0){
		$listProperties = "";
		$l = $i+1;
		$k=0;

		for($j=0; $j<$i; $j++){
			$listProperties .= "\t";
		}
		$listProperties .= "{\n";
		foreach($this as $obj):
			$listProperties .= "\t".get_class($obj)."{\n";
			$l = $i+2;
			if(sizeof($obj->_data)>0):
				foreach($obj->_data as $prop => $value):
						for($j=0; $j<$l; $j++):
							$listProperties .= "\t";
						endfor;
						$listProperties .= "[$prop] => ".((is_object($value) and get_parent_class($value) == 'ActiveRecord')?get_class($value)." ".gettype($value).": ".$value->ListProperties_ToString(($i+1)):$value)."\n";
					$k++;
				endforeach;
			endif;
			$listProperties .= "\t}\n";
		endforeach;

		for($j=0; $j<$i; $j++):
			$listProperties .= "\t";
		endfor;
		$listProperties .= "}";
		return $listProperties;
	}
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

		if($this->counter() > 0):
				$n=0;
		        for($t = 0; $t < $this->counter(); $t++):
		        	if(isset($this[$t]->_data)):
				        foreach($this[$t]->_data as $property => $value):
					        $arraux[$n][$property] = (is_object($value) and get_parent_class($value) == 'ActiveRecord')? $value->getArray():$value;
				        endforeach;
				        $n++;
			        endif;
		        endfor;
		endif;
		return $arraux;
	}
	/**
	* Metodo publico Dump($data, $path)
	*
	* Crea un archivo Xml con el contenido de $data, en la ruta $path. Ideal para bajar datos a disco antes de
	* resetear una tabla.
	* @param array $data Arreglo que contiene los datos para dumpear.
	* @param string $path Cadena que contiene la ruta a guardar el archivo Xml.
	*/
	function Dump($dataDump = array(), $path){
		//if(!isset($this->ObjTable)) $this->ObjTable = Plurals(strtolower(unCamelize(get_class($this))));
		$model = $this->_TableName();
		$dom = new DOMDocument('1.0', 'utf-8');

		$sroot = $dom->appendChild(new DOMElement('table_'.$model));
		if(isset($dataDump[0])):
			foreach($dataDump as $reg):
				$root = $sroot->appendChild(new DOMElement($model));
				foreach($reg as $element => $value):
					if(preg_match("(&|<|>)", $value)):
						$value = $dom->createCDATASection($value);
						$element = $root->appendChild(new DOMElement($element, ""));
						$element->appendChild($value);
					else:
						$element = $root->appendChild(new DOMElement($element, $value));
					endif;
				endforeach;
			endforeach;
		else:
			$root = $sroot->appendChild(new DOMElement($model));
			foreach($dataDump as $element => $value){
				if(preg_match("(&|<|>)", $value)):
					$value = $dom->createCDATASection($value);
					$element = $root->appendChild(new DOMElement($element, ""));
					$element->appendChild($value);
				else:
					$element = $root->appendChild(new DOMElement($element, $value));
				endif;
			}
		endif;

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
		foreach( $items as $xitem ):
			$idfield = $xitem->getElementsByTagName("id");
			$id  = $idfield->item(0)->nodeValue;
			$Obj = Camelize(Singulars($tblName));
			$Obj = new $Obj();
			$Obj->Niu();
			$arrObj = $Obj->GetFields();
			$Obj->id = $id;
			foreach($arrObj as $key => $value):
				if($key != 'table'):
					$field = $xitem->getElementsByTagName("$key");
					$Obj->{$key} = (is_object($field->item(0)))?addslashes($field->item(0)->nodeValue):'';
				endif;
			endforeach;
			$Obj->Insert();
		endforeach;

	}
	/**
	* Metodo publico WriteSchema($tableName)
	*
	* Este metodo se encarga de ir creando el schema de la base de datos para posterior indexaci?n sin necesidad de
	* consultarlo a la base de datos.
	* @param string $tableName Nombre de la tabla para registrar en el schema.
	*/
	function WriteSchema($tableName){
		$createFile = FALSE;
		$stringtoINI = '';
		$file = INST_PATH.'migrations/Schema.ini';
		if (!$schema = parse_ini_file($file, TRUE)):
			$createFile = TRUE;
		endif;
		if($createFile):
			$stringtoINI .= "[$tableName] \n";
			$fp = fopen($file, "w+b");
			fwrite($fp, $stringtoINI);
			fclose($fp);
		elseif(!in_array($tableName, $schema)):
			$schema[$tableName] = "";
			$stringtoINI = "";
			foreach($schema as $table => $val){
				$stringtoINI .= "[$table] \n";
			}
			$fp = fopen($file, "w+b");
			fwrite($fp, $stringtoINI);
			fclose($fp);
		endif;
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
		if(!file_exists($modelpath.$file)):
			$newModel = "<?php \n";
			$newModel .= "class ".Camelize(str_replace('.php', '', $file))." extends ActiveRecord {\n";
			$newModel .= "\tfunction __construct(){ \n";
			$newModel .= "\t} \n";
			$newModel .= "} \n";
			$newModel .= "?>";
			$fp=fopen($modelpath.$file, "w+b");
			fputs($fp, $newModel);
			fclose($fp);
		endif;
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
	 * Construye un archivo de migraci?n en base del modelo.
	 *
	 * Esto se usa cuando la base de datos ya exista y no se requiera hacer los archivos de
	 * migraci?n para ahorrar tiempo.
	 */
	function BuildMigration(){
		//if(!isset($this->ObjTable)) $this->ObjTable = Plurals(strtolower(get_class($this)));

	}

	public function getError(){
		return $this->_errors;
	}
	public function counter(){
		return (integer)$this->_counter;
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
		if(!empty($name)):
			$this->_ObjTable = $name;
		elseif(empty($this->_ObjTable) or strlen($this->_ObjTable) < 1):
			$className =  unCamelize(get_class($this));
			$words = explode("_", $className);
			$i = sizeof($words) - 1;
			$words[$i] = Plurals($words[$i]);
			$this->_ObjTable = implode("_", $words);
		endif;
		return $this->_ObjTable;
	}
	/**
	 *
	 * Gets the executed query.
	 */
	public function _sqlQuery(){
	    return $this->_sqlQuery;
	}
	/**
	 * Gets the native type of an element or attribute of the recordset
	 * @param mixed $field
	 */
	public function _nativeType($field){
		return $this->_dataAttributes[$field]['native_type'];
	}
	/**
	 * Gets the JSON of an ActiveRecord set
	 * @return string JSON
	 */
	function toJSON(){
		$jsonString = "";
		if($this->_counter > 1):
			$jsonString .= "[";
			foreach($this as $record):
				$jsonString .= $record->toJSON().",";
			endforeach;
			$jsonString = substr($jsonString, 0, -1);
			$jsonString .= "]";
		else:
			$jsonString .= "{";
			foreach($this->_data as $index => $element):
				if(is_object($element) and get_parent_class($element) == 'ActiveRecord'):
					$jsonString .= "'".$index."':".$element->toJSON();
				elseif(is_array($element)):
					$jsonString .= "'".$index."':".json_encode($element);
				else:
					$jsonString .= "'".$index."':'".$element."'";
				endif;
				$jsonString .= ",";
			endforeach;
			$jsonString = substr($jsonString, 0, -1);
// 			$jsonString = substr($jsonString, 0, (substr($jsonString, -1,1) == ',')?-1:-3);
			$jsonString .= "}";
		endif;
		return $jsonString;
	}
}
?>