<?php
/**
*
*/
class sqliteDriver {
    private $_params = null;
    public $tableName = null;
    public $pk = 'id';

    public function getColumns($table) {
        return "PRAGMA table_info({$table})";
    }

    public function Select($params = null, $table, $pk = 'id') {
        $this->_params = $params;

        $tail = '';
        $prepared = '';
        $values = [];
        $head = 'SELECT ';
        $body = " FROM {$table} ";


        if(!empty($this->_params)){
            is_numeric($this->_params) && ($this->_params = (integer) $this->_params);
            $type = gettype($this->_params);

            switch($type){
                case 'integer':
                    $tail = "{$tail} WHERE `{$pk}` = {$this->_params}";
                    $prepared = "{$prepared} WHERE `{$pk}` = ?";
                    $values = [$this->_params];
                break;
                case 'string':
                    $ids = explode(',', $this->_params);
                    $tail = "{$tail} WHERE `{$pk}` in ( ";
                    while(null !== ($id = array_shift($ids))) {
                        $id = (integer) $id;
                        $values[] = $id;
                        $tail .= '?,';
                    }
                    $tail = substr($tail, -1);
                    $tail = "{$tail})";
                break;
                case 'array':
                    $tail = ' WHERE 1=1';
                    $operator = '=';
                    $conditions = ' ';
                    $connector = 'AND';
                    if(!empty($this->_params['conditions'])){
                        if (is_string($this->_params['conditions'])) {
                            $prepared = $tail = "{$tail} AND {$this->_params['conditions']}";
                        } elseif(is_array($this->_params['conditions'])){
                            foreach($this->_params['conditions'] as $conn => $condition) {
                                $operator = '=';
                                is_numeric($conn) or ($connector = strtoupper($conn));
                                if(sizeof($condition) > 2) {
                                    $operator = strtoupper($condition[1]);
                                    unset($condition[1]);
                                }
                                $field = array_shift($condition);
                                $prepared .= " {$connector} {$field} {$operator} ";
                                $conditions .= " {$connector} {$field} {$operator} ";

                                if(preg_match('@BETWEEN@i', $operator) === 1) {
                                    $conditions .= "{$condition[0]} AND {$condition[1]}";
                                    $prepared .= '? AND ?';
                                    $values[] = $condition[0];
                                    $values[] = $condition[1];
                                } elseif(preg_match('@IN@i', $operator) === 1) {
                                    $conditions .= '( ';
                                    $prepared .= '( ';
                                    $condition = $condition[0];
                                    while(null !== ($item = array_shift($condition))) {
                                        $conditions .= "{$item},";
                                        $prepared .= '?,';
                                        $values[] = $item;
                                    }
                                    $conditions = substr($conditions, 0, -1);
                                    $prepared = substr($prepared, 0, -1);
                                    $conditions .= ')';
                                    $prepared .= ')';
                                } else {
                                    $value = array_shift($condition);
                                    $conditions .= "'{$value}'";
                                    $prepared .= '?';
                                    $values[] = $value;
                                }
                            }

                            $prepared = "{$tail}{$prepared}";
                            $tail = "{$tail}{$conditions}";
                        }
                    }
                    if(!empty($this->_params['join'])){
                        $body .= $this->_params['join'];
                    }
                    if(isset($this->_params['group'])){
                        $tail .= " GROUP BY {$this->_params['group']}";
                        $prepared .= " GROUP BY {$this->_params['group']}";
                    }
                    if(isset($this->_params['sort'])){
                        switch (gettype($this->_params['sort'])){
                            case 'string':
                                $tail .= " ORDER BY {$this->_params['sort']}";
                                $prepared .= " ORDER BY {$this->_params['sort']}";
                            break;
                        }
                    }

                    if(isset($this->_params['limit'])){
                        $tail .= " LIMIT {$this->_params['limit']}";
                        $prepared .= " LIMIT {$this->_params['limit']}";
                    }
                    if(isset($this->_params[0])){
                        switch($this->_params[0]){
                            case ':first':
                                $tail .= " LIMIT 1";
                                $prepared .= " LIMIT 1";
                            break;
                        }
                    }
                break;
            }
        }
        $fields = (!is_array($this->_params) || (is_array($this->_params) && empty($this->_params['fields'])))? '*' : $this->_params['fields'];
        $sql = "{$head}{$fields}{$body}{$tail}";
        $prepared = "{$head}{$fields}{$body}{$prepared}";

        return ['query' => $sql, 'prepared' => $prepared, 'data' => $values];
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

    public function Insert($params, $table) {
        $prepared = array();
        $fields = '';
        $values = '';
        $action = 'INSERT';

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
            throw new Exception('Invalid conditions for delete.', 1);
        }

        return $query;
    }

    public function CreateTable($table, $fields) {
        $query = "CREATE TABLE IF NOT EXISTS `{$table}` (";
        $queryFields = [];
        while (null !== ($field = array_shift($fields))) {
            if (empty($field['field']) || empty($field['type'])) throw new Exception('Field and type values are mandatory.', 1);
            $field['type'] == 'VARCHAR' && empty($field['limit']) && ($field['limit'] = 250);
            
            empty($field['primary']) || ($field['type'] = "{$field['type']} PRIMARY KEY");
            empty($field['autoincrement']) || ($field['type'] = "{$field['type']} AUTOINCREMENT");
            $limit = empty($field['limit']) ? '' : "({$field['limit']})";
            $notNull = (empty($field['null']) || $field['null'] === 'false') ? ' NOT NULL' : '';
            $default = isset($field['default']) ? " DEFAULT '{$field['default']}'" : '';
            $comment = isset($field['comment']) ? " COMMENT '{$field['comment']}'" : '';

            $queryFields[] = "`{$field['field']}` {$field['type']}{$limit}{$notNull}{$default}{$comment}";
        }

        $query .= implode(',', $queryFields);
        $query = "{$query});";

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
PRAGMA table_info({$table})
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
            $query = "CREATE INDEX {$name} ON {$table} ({$fields})";
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
SELECT COUNT(name) AS indexes FROM sqlite_master WHERE type = 'index' AND tbl_name = '{$table}' AND name = '{$index}';
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
        $query = "CREATE INDEX {$field} ON {$table} ({$field})";
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
        $query = "DROP INDEX IF EXISTS `{$index}`";

        return $query;
    }
    /**
     * Sets the proper query for count the rows result from a query
     * @param string query The query to count the results
     * @return string query Builded query to run on DB
     */
    public function RowCountOnQuery($query) {
        return "SELECT COUNT(*) AS `rows` FROM ($query) AS countedTable";
    }
}

class sqlite2Driver extends sqliteDriver {}
class sqlite3Driver extends sqliteDriver {}
?>
