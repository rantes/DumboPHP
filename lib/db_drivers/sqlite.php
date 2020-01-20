<?php
/**
*
*/
class sqliteDriver {
    private $_params = null;
    public $tableName = null;
    public $pk = 'id';

    public function getColumns($table) {
        return "SELECT sql FROM sqlite_master WHERE name = '{$table}'";
    }

    public function Select($params = null, $table, $pk = 'id') {
        $this->_params = $params;

        $tail = '';
        $head = 'SELECT ';
        $body = " FROM {$table} ";

        if(!empty($this->_params)){
            if(is_numeric($this->_params) && strpos($this->_params,',') === FALSE) $this->_params = 0 + $this->_params;
            $type = gettype($this->_params);

            switch($type){
                case 'integer':
                    $tail .= " WHERE {$pk} in ($this->_params)";
                break;
                case 'string':
                    if(strpos($this->_params,',')!== FALSE){
                        $tail .= " WHERE {$pk} in ({$this->_params})";
                    }
                break;
                case 'array':
                    $tail = ' WHERE 1=1';
                    if(!empty($this->_params['conditions'])){
                        if(is_array($this->_params['conditions'])){
                            $NotOnlyInt = FALSE;
                            while(!$NotOnlyInt and (list($key, $value) = each($this->_params['conditions']))){
                                $NotOnlyInt = (!is_numeric($key))? TRUE: FALSE;
                            }
                            if(!$NotOnlyInt){
                                $tail .= " AND {$pk} in (".implode(',',$this->_params['conditions']).")";
                            }else{
                                foreach($this->_params['conditions'] as $field => $value){
                                    if(is_numeric($field)) {
                                        $tail .= " AND {$value}";
                                    } else {
                                        $tail .= " AND $field='{$value}'";
                                    }
                                }
                                $tail = substr($tail, 4);
                            }
                        } elseif (is_string($this->_params['conditions'])) {
                            $tail .= ' AND '. $this->_params['conditions'];
                        }
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

    public function Update($params = null, $table, $pk = 'id') {
        $prepared = array();
        $query = 'UPDATE `'.$table.'` SET ';
        foreach ($params['data'] as $field => $value) {
            if($field != $pk &&  $value !== null){
                $query .= "`$field`=:$field,";
                $prepared[':'.$field] = $value;
            }
        }

        $query = substr($query, 0, -1);

        $query .= ' WHERE '.$params['conditions'];

        return array('query'=>$query, 'prepared'=>$prepared);
    }

    public function Insert($params, $table, $replace = false) {
        $prepared = array();
        $fields = '';
        $values = '';
        $action = 'INSERT';

        $replace && ($action = 'REPLACE');

        $query = "{$action} INTO `{$table}` ";
        foreach($params as $field => $value){
            if(is_string($value) || is_numeric($value)) {
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

    public function Delete($conditions, $table, $pk ='id') {
        $query = "DELETE FROM `{$table}` ";
        if(is_numeric($conditions)){
            $this->{$pk} = $conditions;
            $query .= "WHERE ".$pk."='$conditions'";
        }elseif(is_array($conditions) && empty($conditions['conditions'])){
            $query .= 'WHERE `'.$pk.'` IN ('.implode(',', $conditions).')';
        }elseif(!empty($conditions['conditions']) && is_string($conditions['conditions'])){
            $query .= 'WHERE '.$conditions['conditions'];
        } else {
            throw new Exception("Invalid conditions for delete.", 1);
        }

        return $query;
    }

    public function CreateTable($table, $fields) {
        $query = "CREATE TABLE IF NOT EXISTS `{$table}` (";
        foreach ($fields as $field) {
            if ($field['type'] == 'VARCHAR' && empty($field['limit'])) {
                $field['limit'] = 250;
            }

            $query .= (!empty($field['field']) && !empty($field['type']))?"`".$field['field']."` ".$field['type']:NULL;
            $query .= (!empty($field['limit']))?" (".$field['limit'].")":NULL;
            $query .= (empty($field['null']) || $field['null'] === 'false')?" NOT NULL":NULL;
            $query .= (isset($field['default']))?" DEFAULT '".$field['default']."'":NULL;
            $query .= (!empty($field['comment']))?" COMMENT '".$field['comment']."'":NULL;
            $query .= " ,";
        }

        $query = substr($query, 0, -2);
        $query .= ");";

        return $query;
    }

    public function DropTable($table) {
        return "DROP TABLE IF EXISTS `{$table}`";
    }
    /**
     * Query for assertion of presence of a field in a table
     *
     * @param [string] $table
     * @param [string] $field
     * @return string query
     */
    public function validateField($table, $field) {
        $query =<<<DUMBO
SELECT COUNT(COLUMN_NAME) AS counter
FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_name = '{$table}'
    AND table_schema = '{$GLOBALS['Connection']->_settings['schema']}'
    AND column_name = '{$field}';
DUMBO;
        return $query;
    }

    public function AddColumn($table, $params) {
        $query = '';
        $params['type'] == 'VARCHAR' && empty($params['limit']) && ($params['limit'] = '255');

        $query = "ALTER TABLE `".$table."` ADD COLUMN `".$params['field']."` ".strtoupper($params['type']);
        $query .= (isset($params['limit']) && $params['limit'] != '')?"(".$params['limit'].")":NULL;
        $query .= (isset($params['null']) && $params['null'] != '')?" NOT NULL":NULL;
        $query .= (isset($params['default']) && $params['default'] != '')?" DEFAULT '".$params['default']."'":NULL;
        $query .= (!empty($params['comments']))?" COMMENT '".$params['comment']."'":NULL;

        return $query;
    }
    /**
     * Alters a specific column on the table.
     * @param string $table
     * @param array $params
     * @return string|NULL
     */
    public function AlterColumn($table, array $params) {
        $query = '';
        $params['type'] == 'VARCHAR' && empty($params['limit']) && ($params['limit'] = '255');

        $query = "ALTER TABLE `".$table."` MODIFY `".$params['field']."` ".strtoupper($params['type']);
        $query .= (isset($params['limit']) && $params['limit'] != '')?"(".$params['limit'].")":NULL;
        $query .= (isset($params['null']) && $params['null'] != '')?" NOT NULL":NULL;
        $query .= (isset($params['default']) && $params['default'] != '')?" DEFAULT '".$params['default']."'":NULL;
        $query .= (!empty($params['comments']))?" COMMENT '".$params['comment']."'":NULL;

        return $query;
    }
    /**
     * Alters the table and drops a column
     * @param string $table
     * @param string $field
     * @return string The query to run
     */
    public function RemoveColumn($table, $field) {
        $query = '';
        if ($this->validateField($table, $field) > 0) {
            $query = "ALTER TABLE `".$table."` DROP `".$field."`";
        }

        return $query;
    }
    /**
     * Sets an index with a particular name
     * @param string $table
     * @param string $name
     * @param string $fields
     * @return string The query to run
     */
    public function AddIndex($table, $name, $fields) {
        $query = '';

        if (!$this->ValidateIndex($table, $name)) {
            "ALTER TABLE `{$table}` ADD INDEX `{$name}` ({$fields})";
        }

        return $query;
    }
    /**
     * Validates if an index exists on a given table
     * @param string $table
     * @param string $index
     * @return number how many indexes exists
     */
    public function ValidateIndex($table, $index) {
        $query = <<<DUMBO
SELECT COUNT(INDEX_NAME) AS indexes FROM information_schema.statistics WHERE table_schema = '{$GLOBALS['Connection']->_settings['schema']}' AND table_name = '{$table}' AND index_name = '{$index}'
DUMBO;

        $res = $GLOBALS['Connection']->query($query);
        $res->setFetchMode(PDO::FETCH_ASSOC);
        $c = $res->fetchAll();
        return 0 + $c[0]['indexes'];
    }
    /**
     * Adds single index or index which is nothing but the field name
     * @param string $table
     * @param string $field
     * @return string The query to run
     */
    public function AddSingleIndex($table, $field) {
        $query = '';
        $x = $this->ValidateIndex($table, $field);

        if (!$x) {
            $query = "ALTER TABLE `{$table}` ADD INDEX (`{$field}`)";
        }

        return $query;
    }
    /**
     * Adds primary key to the table
     * @param string $table
     * @param string $fields
     * @return string The query to run
     */
    public function AddPrimaryKey($table, $fields) {
        return "ALTER TABLE `{$table}` ADD PRIMARY KEY ({$fields})";
    }
    /**
     * Retrieves all indexes from a single table
     * @param string $table
     * @return array The indexes names
     */
    public function GetAllIndexes($table) {
        $query = <<<DUMBO
SELECT INDEX_NAME FROM information_schema.statistics WHERE table_schema = '{$GLOBALS['Connection']->_settings['schema']}' AND table_name = '{$table}'
DUMBO;

        return $query;
    }
    /**
     * Gets the query for dropping an index in a table
     * @param string $table
     * @param string $index
     * @return string The query to run
     */
    public function RemoveIndex($table, $index) {
        $query = '';
        if ($this->validateIndex($table, $index) > 0) {
            $query = "ALTER TABLE `{$table}` DROP INDEX `{$index}`";
        }

        return $query;
    }
}

?>