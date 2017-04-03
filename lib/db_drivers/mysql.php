<?php
/**
*
*/
class mysqlDriver {
    private $_params = null;
    public $tableName = null;
    public $pk = null;

    public function getColumns() {
        $numerics = ['INT', 'FLOAT', 'BIGINT'];
        $fields = array();
        $result1 = $GLOBALS['Connection']->query("SHOW COLUMNS FROM {$this->tableName}");
        $result1->setFetchMode(PDO::FETCH_ASSOC);
        $resultset1 = $result1->fetchAll();
        foreach ($resultset1 as $res) {
            $type = strtoupper(preg_replace('@\([0-9]+\)@', '', $res['Type']));
            $fields[] = array(
                        'Cast'=>in_array($type, $numerics),
                        'Field'=>$res['Field'],
                        'Type'=>$type,
                        'Value' => null
                        );
        }

        return $fields;
    }

    public function Select($params = null) {
        $this->_params = $params;

        $tail = '';
        $head = 'SELECT ';
        $body = " FROM {$this->tableName} ";

        if(!empty($this->_params)){
            if(is_numeric($this->_params) && strpos($this->_params,',') === FALSE) $this->_params = 0 + $this->_params;
            $type = gettype($this->_params);

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

    public function Update($params = null) {
        $prepared = array();
        $query = 'UPDATE `'.$this->tableName.'` SET ';
        foreach ($params['data'] as $field => $value) {
            if($field != $this->pk &&  $value !== null){
                $query .= "`$field`=:$field,";
                $prepared[':'.$field] = $value;
            }
        }

        $query = substr($query, 0, -1);
        AUTO_AUDITS && ($query .= ',`updated_at`='.time());

        $query .= ' WHERE '.$params['conditions'];

        return array('query'=>$query, 'prepared'=>$prepared);
    }

    public function Insert($params = null) {
        if(empty($params)){
            throw new Exception("Empty params for insertion.", 1);
        }

        $prepared = array();
        $fields = '';
        $values = '';

        $query = "INSERT INTO `{$this->tableName}` ";
        foreach($params as $field => $value){
            if(is_string($value) || is_numeric($value)){
                $fields .= "`$field`,";
                $values .= ":".$field.",";
                $prepared[':'.$field] = $value;
            }
        }

        $fields = substr($fields, 0, -1);
        $values = substr($values, 0, -1);

        $query .= "({$fields}) VALUES ({$values})";

        return array('query' => $query, 'prepared' => $prepared);
    }

    public function Delete($conditions) {
        $query = "DELETE FROM `{$this->tableName}` ";
        if(is_numeric($conditions)){
            $this->{$this->pk} = $conditions;
            $query .= "WHERE ".$this->pk."='$conditions'";
        }elseif(is_array($conditions) && empty($conditions['conditions'])){
            $query .= 'WHERE `'.$this->pk.'` IN ('.implode(',', $conditions).')';
        }elseif(!empty($conditions['conditions']) && is_string($conditions['conditions'])){
            $query .= 'WHERE '.$conditions['conditions'];
        } else {
            throw new Exception("Invalid conditions for delete.", 1);
        }

        return $query;
    }

    public function CreateTable($table, $fields) {
        $query    = "CREATE TABLE IF NOT EXISTS `{$table}` (";
//         $query .= "`id` INT AUTO_INCREMENT PRIMARY KEY ,";
        foreach ($fields as $field) {
            if ($field['type'] == 'VARCHAR' && empty($field['limit'])) {
                $field['limit'] = 250;
            }

            $query .= (!empty($field['field']) && !empty($field['type']))?"`".$field['field']."` ".$field['type']:NULL;
            $query .= (!empty($field['limit']))?" (".$field['limit'].")":NULL;
            $query .= (empty($field['null']) || $field['null'] === 'false')?" NOT NULL":NULL;
            $query .= (isset($field['default']))?" DEFAULT '".$field['default']."'":NULL;
            $query .= (!empty($field['comments']))?" COMMENT '".$field['comment']."'":NULL;
            $query .= " ,";
        }

//         if (AUTO_AUDITS) {
//             $query .= "`created_at` INT NOT NULL ,";
//             $query .= "`updated_at` INT NOT NULL ,";
//         }

        $query = substr($query, 0, -2);
        $query .= ");";

        return $query;
    }

    public function DropTable($table) {
        return "DROP TABLE IF EXISTS `{$table}`";
    }
}

?>
