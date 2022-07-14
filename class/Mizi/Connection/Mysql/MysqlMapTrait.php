<?php

namespace Mizi\Connection\Mysql;

use Mizi\Datalayer\Query;

trait MysqlMapTrait
{
    /** Retorna o mapa real do banco de dados */
    protected function loadRealMap(): array
    {
        $listTable = $this->executeQuery(
            Query::select('INFORMATION_SCHEMA.TABLES')
                ->fields(['table_name' => 'name', 'table_comment' => 'comment'])
                ->order('table_name')
                ->where('table_schema', $this->data['data'])
        );

        $map = [];

        foreach ($listTable as $itemTable) {
            $table = $itemTable['name'];
            $map[$table] = ['comment' => null, 'fields' => []];
            $map[$table]['comment'] = empty($itemTable['comment']) ? null : $itemTable['comment'];

            $listFilds = $this->executeQuery("SHOW FULL COLUMNS FROM $table");

            foreach ($listFilds as $itemField) {
                if ($itemField['Field'] != 'id') {

                    $tmp = $itemField['Type'];

                    $tmp = explode(' ', $tmp);
                    $tmp = array_shift($tmp);
                    $tmp = mb_strtolower($tmp);
                    $tmp = str_replace(')', '(', $tmp);
                    $tmp = explode('(', $tmp);

                    $sqlType = array_shift($tmp);

                    $size = intval(array_shift($tmp));
                    $size = $size ? $size : null;

                    $name = $itemField['Field'];
                    $default = $itemField['Default'];

                    $default = (is_numeric($default) || is_bool($default)) ? $default : "'$default'";

                    $null = !boolval($itemField['Null'] == 'NO');

                    $comment = empty($itemField['Comment']) ? null : $itemField['Comment'];

                    $map[$table]['fields'][$name]  = [
                        'type' => $sqlType,
                        'comment' => $comment ?? null,
                        'default' => $default,
                        'size' => $size,
                        'null' => $null,
                    ];
                }
            }
        }

        return $map;
    }
}
