<?php
namespace DumboPHP\lib\db_drivers;

use Exception;
use DumboPHP\lib\ShellCommands\Interfaces\DBDriver;
/**
 *
 */
class sqlite implements DBDriver {
    private ?array $_params   = null;
    public ?string $tableName = null;
    public ?string $schema    = null;
    public string $pk         = 'rowid';

    public function AddColumn(string $table, array $params): string {
        $query = '';
        $params['type'] == 'VARCHAR' && empty($params['limit']) && ($params['limit'] = '255');

        $query  = "ALTER TABLE `" . $table . "` ADD COLUMN `" . $params['field'] . "` " . strtoupper($params['type']);
        $query .= (isset($params['limit']) && $params['limit'] != '') ? "(" . $params['limit'] . ")" : null;
        $query .= (isset($params['null']) && $params['null'] != '') ? " NOT NULL" : null;
        $query .= (isset($params['default']) && $params['default'] != '') ? " DEFAULT '" . $params['default'] . "'" : null;
        $query .= (! empty($params['comments'])) ? " COMMENT '" . $params['comment'] . "'" : null;

        return $query;
    }

    public function AddIndex(string $table, string $name, string $fields): string {
        $query = '';

        if (! $this->ValidateIndex($table, $name, '')) {
            $query = "CREATE INDEX {$name} ON {$table} ({$fields})";
        }

        return $query;
    }

    public function AddPrimaryKey(string $table, string $fields): string {
        return "ALTER TABLE `{$table}` ADD PRIMARY KEY ({$fields})";
    }

    public function AddSingleIndex(string $table, string $field): string {
        $query     = '';
        $indexName = "idx_{$table}_{$field}";

        $x = $this->ValidateIndex($table, $indexName, '');

        if ($x === 0) {
            $query = "CREATE INDEX {$indexName} ON {$table} ({$field})";
        }

        return $query;
    }

    public function AlterColumn(string $table, array $params): string {
        $query = '';
        $params['type'] == 'VARCHAR' && empty($params['limit']) && ($params['limit'] = '255');

        $query  = "ALTER TABLE `" . $table . "` MODIFY `" . $params['field'] . "` " . strtoupper($params['type']);
        $query .= (isset($params['limit']) && $params['limit'] != '') ? "(" . $params['limit'] . ")" : null;
        $query .= (isset($params['null']) && $params['null'] != '') ? " NOT NULL" : null;
        $query .= (isset($params['default']) && $params['default'] != '') ? " DEFAULT '" . $params['default'] . "'" : null;
        $query .= (! empty($params['comments'])) ? " COMMENT '" . $params['comment'] . "'" : null;

        return $query;
    }

    public function CreateTable(string $table, array $fields): string {
        $query       = "CREATE TABLE IF NOT EXISTS `{$table}` (";
        $queryFields = [];
        $parsed      = [
            'INT'    => 'INTEGER',
            'BIGINT' => 'INTEGER',
        ];
        while (null !== ($field = array_shift($fields))) {
            if (empty($field['field']) || empty($field['type'])) {
                throw new Exception('Field and type values are mandatory.', 1);
            }

            array_key_exists($field['type'], $parsed) and ($field['type'] = $parsed[$field['type']]);
            $extra = ' ';
            $field['type'] == 'VARCHAR' && empty($field['limit']) && ($field['limit'] = 250);

            empty($field['primary']) || ($extra = "{$extra} PRIMARY KEY");
            empty($field['autoincrement']) || ($extra = "{$extra} AUTOINCREMENT");

            $limit   = empty($field['limit']) ? '' : "({$field['limit']})";
            $notNull = (isset($field['null']) && ($field['null'] === false || $field['null'] === 'false')) ? ' NOT NULL' : '';
            $default = isset($field['default']) ? " DEFAULT '{$field['default']}'" : '';
            $comment = isset($field['comment']) ? " COMMENT '{$field['comment']}'" : '';

            $queryFields[] = "`{$field['field']}` {$field['type']}{$limit}{$extra}{$notNull}{$default}{$comment}";
        }

        $query .= implode(',', $queryFields);
        $query = "{$query})";

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
            throw new \Exception('Invalid conditions for delete.', 1);
        }

        return $query;
    }

    public function DropTable(string $table): string {
        return "DROP TABLE IF EXISTS `{$table}`";
    }

    public function GetAllIndexes(string $table, string $schema): string {
        $query = <<<DUMBO
PRAGMA index_list('{$table}')
DUMBO;

        return $query;
    }

    public function getColumns(string $table): string {
        return "PRAGMA table_info({$table})";
    }

    public function Insert(array $params, string $table, bool $replace = false): array {
        $prepared = [];
        $fields   = '';
        $values   = '';
        $action   = 'INSERT';

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
        if ($this->validateField($table, $field, '') > 0) {
            $query = "ALTER TABLE `" . $table . "` DROP `" . $field . "`";
        }

        return $query;
    }

    public function RemoveIndex(string $table, string $index): string {
        $query = '';
        if ($this->ValidateIndex($table, $index, '') > 0) {
            $query = "DROP INDEX IF EXISTS `{$index}`";
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
                if (! empty($this->_params['conditions']) and is_string($this->_params['conditions']) and strlen(trim($this->_params['conditions'])) > 0) {
                    $prepared = $tail = "{$tail} {$this->_params['conditions']}";
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
        $fields   = (! is_array($this->_params) || (is_array($this->_params) && empty($this->_params['fields']))) ? "{$this->pk}, *" : $this->_params['fields'];
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
PRAGMA table_info({$table})
DUMBO;
        return $query;
    }

    public function ValidateIndex(string $table, string $index, string $schema): string {
        $query = <<<DUMBO
SELECT COUNT(name) AS indexes FROM sqlite_master WHERE type = 'index' AND tbl_name = '{$table}' AND name = '{$index}';
DUMBO;

        return $query;
    }
}

class sqlite2 extends sqlite {}
class sqlite3 extends sqlite {}
?>
