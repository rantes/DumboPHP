<?php
/**
*
*/
class mysqlDriver {
	private $_params = null;
	public $tableName = null;
	public $pk = null;

	public function getColumns() {
		$fields = array();
		$result1 = $GLOBALS['Driver']->query("SHOW COLUMNS FROM {$this->tableName}");

		$result1->setFetchMode(PDO::FETCH_ASSOC);
		$resultset1 = $result1->fetchAll();
		foreach ($resultset1 as $res){
			$type = strtoupper(preg_replace('@\([0-9]+\)@', '', $res['Type']));
			$fields[] = array(
						'Field'=>$res['Field'],
						'Type'=>$type,
						'Value' => null
						);
		}

		return $fields;
	}

	public function select($params = null) {
		$this->_params = $params;

		$tail = '';
		$head = 'SELECT ';
		$body = " FROM {$this->tableName} ";

		if(!empty($this->_params)){
			if(is_numeric($this->_params) && strpos($this->_params,',') === FALSE) $this->_params = 0 + $this->_params;
			$type = gettype($this->_params);
			$strint = '';
			switch($type){
				case 'integer':
					$tail .= " WHERE ".$this->pk." in ($this->_params)";
				break;
				case 'string':
					if(strpos($this->_params,',')!== FALSE){
						$tail .= " WHERE ".$this->pk." in ({$this->_params})";
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
								$tail .= $this->pk." in (".implode(',',$this->_params['conditions']).")";
							}else{
								foreach($this->_params['conditions'] as $field => $value){
									if(is_numeric($field)) $tail .= " AND ".$value;
									else $tail .= " AND $field='$value'";
								}
								$tail = substr($tail, 4);
							}
						}elseif(is_string($this->_params['conditions'])){
							$tail .= $this->_params['conditions'];
						}
						$tail = ' WHERE '.$tail;
					}
					if(!empty($this->_params['join'])){
						$body .= $this->_params['join'];
					}
					if(isset($this->_params['group'])){
						$tail .= " GROUP BY ".$this->_params['group'];
					}
					if(isset($this->_params['sort'])){
						switch (gettype($this->_params['sort'])){
							case 'string':
								$tail .= " ORDER BY ".$this->_params['sort'];
							break;
							case 'array':
								null;
							break;
						}
					}

					if(isset($this->_params['limit'])){
						$tail .= " LIMIT ".$this->_params['limit'];
					}
					if(isset($this->_params[0])){
						switch($this->_params[0]){
						case ':first':
							$tail .= " LIMIT 1";
						break;
						}
					}
				break;
			}
		}
		$fields = (!is_array($this->_params) || (is_array($this->_params) && empty($this->_params['fields'])))? '*' : $this->_params['fields'];
		$sql = $head.$fields.$body.$tail;

		return $sql;

	}
}

?>
