<?php
namespace DumboPHP\lib\db_drivers;
use DumboPHP\lib\ShellCommands\Interfaces\DBDriver;

/**
 *
 */
class postgresqlDriver implements DBDriver {
    private ?array $_params   = null;
    public ?string $tableName = null;
    public ?string $schema    = null;
    public string $pk         = 'id';

    public function AddColumn(string $table, array $params): string {
        $query = '';
        if ($this->validateField($table, $params['field'], $GLOBALS['Connection']->_settings['schema']) < 1) {
            $params['type'] == 'VARCHAR' && empty($params['limit']) && ($params['limit'] = '255');

            $query  = "ALTER TABLE `" . $table . "` ADD COLUMN `" . $params['field'] . "` " . strtoupper($params['type']);
            $query .= (isset($params['limit']) && $params['limit'] != '') ? "(" . $params['limit'] . ")" : null;
            $query .= (isset($params['null']) && $params['null'] != '') ? " NOT NULL" : null;
            $query .= (isset($params['default']) && $params['default'] != '') ? " DEFAULT '" . $params['default'] . "'" : null;
            $query .= (! empty($params['comments'])) ? " COMMENT '" . $params['comment'] . "'" : null;
        }

        return $query;
    }

    public function AddIndex(string $table, string $name, string $fields): string {
        $query = '';

        if (! $this->ValidateIndex($table, $name, $GLOBALS['Connection']->_settings['schema'])) {
            $query = "ALTER TABLE `{$table}` ADD INDEX `{$name}` ({$fields})";
        }

        return $query;
    }

    public function AddPrimaryKey(string $table, string $fields): string {
        return "ALTER TABLE `{$table}` ADD PRIMARY KEY ({$fields})";
    }

    public function AddSingleIndex(string $table, string $field): string {
        $query = '';
        $x     = $this->ValidateIndex($table, $field, $GLOBALS['Connection']->_settings['schema']);

        if (! $x) {
            $query = "ALTER TABLE `{$table}` ADD INDEX (`{$field}`)";
        }

        return $query;
    }

    public function AlterColumn(string $table, array $params): string {
        $query = '';
        if ($this->validateField($table, $params['field'], $GLOBALS['Connection']->_settings['schema']) > 0) {
            $params['type'] == 'VARCHAR' && empty($params['limit']) && ($params['limit'] = '255');
            $params['type'] == 'INTEGER' && empty($params['limit']) && ($params['limit'] = 11);

            $query  = "ALTER TABLE `" . $table . "` MODIFY `" . $params['field'] . "` " . strtoupper($params['type']);
            $query .= (isset($params['limit']) && $params['limit'] != '') ? "(" . $params['limit'] . ")" : null;
            $query .= (isset($params['null']) && $params['null'] != '') ? " NOT NULL" : null;
            $query .= (isset($params['default']) && $params['default'] != '') ? " DEFAULT '" . $params['default'] . "'" : null;
            $query .= (! empty($params['comments'])) ? " COMMENT '" . $params['comment'] . "'" : null;
        }

        return $query;
    }

    public function CreateTable(string $table, array $fields): string {
        $query       = "CREATE TABLE IF NOT EXISTS `{$table}` (";
        $queryFields = [];

        $parsed = [
            'INT' => 'INTEGER',
        ];
        while (null !== ($field = array_shift($fields))) {
            if (empty($field['field']) || empty($field['type'])) {
                throw new \Exception('Field and type values are mandatory.', 1);
            }

            array_key_exists($field['type'], $parsed) and ($field['type'] = $parsed[$field['type']]);

            $field['type'] === 'VARCHAR' && empty($field['limit']) && ($field['limit'] = 250);
            $field['type'] == 'INTEGER' && empty($field['limit']) && ($field['limit'] = 11);
            empty($field['autoincrement']) || ($field['type'] = "{$field['type']} AUTO_INCREMENT");
            empty($field['primary']) || ($field['type'] = "{$field['type']} PRIMARY KEY");
            $limit   = empty($field['limit']) ? '' : "({$field['limit']})";
            $notNull = (empty($field['null']) || $field['null'] === 'false') ? ' NOT NULL' : '';
            $default = isset($field['default']) ? " DEFAULT '{$field['default']}'" : '';
            $comment = isset($field['comment']) ? " COMMENT '{$field['comment']}'" : '';

            $queryFields[] = "`{$field['field']}` {$field['type']}{$limit}{$notNull}{$default}{$comment}";
        }

        $query .= implode(',', $queryFields);
        $query = "{$query});";

        return $query;
    }

    public function Delete(array | string | int $conditions, string $table, string $pk = 'id'): string {
        $query = "DELETE FROM `{$table}` ";
        if (is_numeric($conditions)) {
            $this->{$pk}  = $conditions;
            $query       .= "WHERE " . $pk . "='$conditions'";
        } elseif (is_array($conditions) && empty($conditions['conditions'])) {
            $query .= 'WHERE `' . $pk . '` IN (' . implode(',', $conditions) . ')';
        } elseif (! empty($conditions['conditions']) && is_string($conditions['conditions'])) {
            $query .= 'WHERE ' . $conditions['conditions'];
        } else {
            throw new \Exception("Invalid conditions for delete.", 1);
        }

        return $query;
    }

    public function DropTable(string $table): string {
        return "DROP TABLE IF EXISTS `{$table}`";
    }

    public function GetAllIndexes(string $table, string $schema): string {
        $query = <<<DUMBO
SELECT INDEX_NAME FROM information_schema.statistics WHERE table_schema = '{$schema}' AND table_name = '{$table}'
DUMBO;

        return $query;
    }

    public function getColumns(string $table): string {
        return "SHOW COLUMNS FROM {$table}";
    }

    public function Insert(array $params, string $table, bool $replace = false): array {
        $prepared = [];
        $fields   = '';
        $values   = '';
        $action   = 'INSERT';

        $replace && ($action = 'REPLACE');

        $query = "{$action} INTO `{$table}` ";
        foreach ($params as $field => $value) {
            if (is_string($value) || is_numeric($value)) {
                $fields                 .= "`$field`,";
                $values                 .= ":" . $field . ",";
                $prepared[':' . $field]  = $value;
            }
        }

        $fields = substr($fields, 0, -1);
        $values = substr($values, 0, -1);

        $query .= "({$fields}) VALUES ({$values})";

        return ['query' => $query, 'prepared' => $prepared];
    }

    public function RemoveColumn(string $table, string $field): string {
        $query = '';
        if ($this->validateField($table, $field, $GLOBALS['Connection']->_settings['schema']) > 0) {
            $query = "ALTER TABLE `" . $table . "` DROP `" . $field . "`";
        }

        return $query;
    }

    public function RemoveIndex(string $table, string $index): string {
        $query = '';
        if ($this->ValidateIndex($table, $index, $GLOBALS['Connection']->_settings['schema']) > 0) {
            $query = "ALTER TABLE `{$table}` DROP INDEX `{$index}`";
        }

        return $query;
    }

    public function RowCountOnQuery(string $query): string {
        return "SELECT COUNT(*) AS `rows` FROM ($query) AS countedTable";
    }

    public function Select(array | string | int $params, string $table, string $pk = 'id'): array {
        $this->_params = $params;

        $tail     = '';
        $prepared = '';
        $values   = [];
        $head     = 'SELECT ';
        $body     = " FROM {$table} ";

        if (! empty($this->_params)) {
            is_numeric($this->_params) && ($this->_params = (integer) $this->_params);
            $type = gettype($this->_params);

            switch ($type) {
            case 'integer':
                $tail     = "{$tail} WHERE `{$pk}` = {$this->_params}";
                $prepared = "{$prepared} WHERE `{$pk}` = ?";
                $values   = [$this->_params];
                break;
            case 'string':
                $ids  = explode(',', $this->_params);
                $tail = "{$tail} WHERE `{$pk}` in ( ";
                while (null !== ($id = array_shift($ids))) {
                    $id        = (integer) $id;
                    $values[]  = $id;
                    $tail     .= '?,';
                }
                $tail = substr($tail, -1);
                $tail = "{$tail})";
                break;
            case 'array':
                $tail       = ' WHERE 1=1';
                $operator   = '=';
                $conditions = ' ';
                $connector  = 'AND';
                if (! empty($this->_params['conditions'])) {
                    if (is_string($this->_params['conditions'])) {
                        $prepared = $tail = "{$tail} AND {$this->_params['conditions']}";
                    } elseif (is_array($this->_params['conditions'])) {
                        foreach ($this->_params['conditions'] as $conn => $condition) {
                            is_numeric($conn) or ($connector = strtoupper($conn));
                            if (sizeof($condition) > 2) {
                                $operator = $condition[1];
                                unset($condition[1]);
                            }
                            $field       = array_shift($condition);
                            $prepared   .= " {$connector} {$field} {$operator} ";
                            $conditions .= " {$connector} {$field} {$operator} ";

                            if (preg_match('@BETWEEN@i', $connector) === 1) {
                                $conditions .= "{$condition[0]} AND {$condition[1]}";
                                $prepared   .= '? AND ?';
                                $values[]    = $condition[0];
                                $values[]    = $condition[1];
                            } elseif (preg_match('@IN@i', $connector) === 1) {
                                $conditions .= '( ';
                                while (null !== ($item = array_shift($condition))) {
                                    $conditions .= "{$item},";
                                    $prepared   .= '?,';
                                    $values[]    = $item;
                                }
                                $conditions  = substr($conditions, 0, -1);
                                $conditions .= ')';
                            } else {
                                $value       = array_shift($condition);
                                $conditions .= "'{$value}'";
                                $prepared   .= '?';
                                $values[]    = $value;
                            }
                        }

                        $prepared = "{$tail}{$prepared}";
                        $tail     = "{$tail}{$conditions}";
                    }
                }
                if (! empty($this->_params['join'])) {
                    $body .= $this->_params['join'];
                }
                if (isset($this->_params['group'])) {
                    $tail     .= " GROUP BY {$this->_params['group']}";
                    $prepared .= " GROUP BY {$this->_params['group']}";
                }
                if (isset($this->_params['sort'])) {
                    switch (gettype($this->_params['sort'])) {
                    case 'string':
                        $tail     .= " ORDER BY {$this->_params['sort']}";
                        $prepared .= " ORDER BY {$this->_params['sort']}";
                        break;
                    }
                }

                if (isset($this->_params['limit'])) {
                    $tail     .= " LIMIT {$this->_params['limit']}";
                    $prepared .= " LIMIT {$this->_params['limit']}";
                }
                if (isset($this->_params[0])) {
                    switch ($this->_params[0]) {
                    case ':first':
                        $tail     .= " LIMIT 1";
                        $prepared .= " LIMIT 1";
                        break;
                    }
                }
                break;
            }
        }
        $fields   = (! is_array($this->_params) || (is_array($this->_params) && empty($this->_params['fields']))) ? '*' : $this->_params['fields'];
        $sql      = "{$head}{$fields}{$body}{$tail}";
        $prepared = "{$head}{$fields}{$body}{$prepared}";

        return ['query' => $sql, 'prepared' => $prepared, 'data' => $values];
    }

    public function Update(array $params, string $table, string $pk = 'id'): array {
        $prepared = [];
        $query    = 'UPDATE `' . $table . '` SET ';
        foreach ($params['data'] as $field => $value) {
            if ($field != $pk && $value !== null) {
                $query                  .= "`$field`=:$field,";
                $prepared[':' . $field]  = $value;
            }
        }

        $query  = substr($query, 0, -1);

        $query .= ' WHERE ' . $params['conditions'];

        return ['query' => $query, 'prepared' => $prepared];
    }

    public function validateField(string $table, string $field, string $schema): string {
        $query = <<<DUMBO
SELECT COUNT(COLUMN_NAME) AS counter
FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_name = '{$table}'
    AND table_schema = '{$schema}'
    AND column_name = '{$field}';
DUMBO;
        return $query;
    }

    public function ValidateIndex(string $table, string $index, string $schema): string {
        $query = <<<DUMBO
SELECT COUNT(INDEX_NAME) AS indexes FROM information_schema.statistics WHERE table_schema = '{$schema}' AND table_name = '{$table}' AND index_name = '{$index}'
DUMBO;

        return $query;
    }
}

?>
