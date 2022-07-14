<?php

namespace Mizi\Connection\Mysql;

trait MysqlExecuteSchemeQueryTrait
{
    /** Executa uma lista de querys de esquema */
    function executeSchemeQuery(array $schemeQueryList): void
    {
        $queryList = [];

        foreach ($schemeQueryList as $schemeQuery) {
            list($action, $data) = $schemeQuery;
            array_push($queryList, ...match ($action) {
                'create' => $this->getQueryCreateTable(...$data),
                'alter' => $this->getQueryAlterTable(...$data),
                'drop' => $this->getQueryDropTable(...$data),
                default => []
            });
        }

        $this->executeQueryList($queryList);
    }

    /** Returna um array de query de criação de tabela */
    protected function getQueryCreateTable(string $table, ?string $comment, array $fields): array
    {
        $queryFields = [
            '`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY'
        ];

        foreach ($fields['add'] ?? [] as $fielName => $field)
            if ($field)
                $queryFields[] = $this->getQueryFieldTemplate($fielName, $field);

        return [
            prepare(
                "CREATE TABLE `[#name]` ([#fields]) DEFAULT CHARSET=utf8[#comment] ENGINE=InnoDB;",
                [
                    'name' => $table,
                    'fields' => implode(', ', $queryFields),
                    'comment' => $comment ? " COMMENT='$comment'" : ''
                ]
            )
        ];
    }

    /** Retorna um array de query para alteração de tabela */
    protected function getQueryAlterTable(string $table, ?string $comment, array $fields): array
    {
        $query = [];

        if (!is_null($comment)) {
            $query[] = prepare(
                "ALTER TABLE `[#table]` COMMENT='[#comment]'",
                ['table' => $table, 'comment' => $comment]
            );
        }

        foreach ($fields['add'] as $name => $field) {
            $query[] = prepare(
                'ALTER TABLE `[#table]` ADD COLUMN [#fieldQuery]',
                ['table' => $table, 'fieldQuery' => $this->getQueryFieldTemplate($name, $field)]
            );
        }

        foreach ($fields['drop'] as $name => $field) {
            $query[] = prepare(
                'ALTER TABLE `[#table]` DROP COLUMN `[#fieldName]`',
                ['table' => $table, 'fieldName' => $name]
            );
        }

        foreach ($fields['alter'] as $name => $field) {
            $query[] = prepare(
                'ALTER TABLE `[#table]` MODIFY COLUMN [#fieldQuery]',
                ['table' => $table, 'fieldQuery' => $this->getQueryFieldTemplate($name, $field)]
            );
        }

        return $query;
    }

    /** Retorna um array de query para remoçao de tabela */
    protected function getQueryDropTable(string $table): array
    {
        return [
            prepare(
                "DROP TABLE `[#]`",
                $table
            )
        ];
    }

    /** Retorna o template do campo para composição de querys */
    protected static function getQueryFieldTemplate(string $fieldName, array $field): string
    {
        $prepare = '';
        $field['name'] = $fieldName;
        $field['null'] = $field['null'] ? '' : ' NOT NULL';
        switch ($field['type']) {
            case 'idx':
            case 'time':
                $field['default'] = is_null($field['default']) ? '' : ' DEFAULT ' . $field['default'];
                $prepare = "`[#name]` int([#size]) UNSIGNED[#default][#null] COMMENT '[#comment]'";
                break;

            case 'int':
                $field['default'] = is_null($field['default']) ? '' : ' DEFAULT ' . $field['default'];
                $prepare = "`[#name]` int([#size])[#default][#null] COMMENT '[#comment]'";
                break;

            case 'tinyint':
            case 'boolean':
            case 'status':
                $field['default'] = is_null($field['default']) ? '' : ' DEFAULT ' . $field['default'];
                $prepare = "`[#name]` tinyint([#size])[#default][#null] COMMENT '[#comment]'";
                break;

            case 'float':
                $field['default'] = is_null($field['default']) ? '' : ' DEFAULT ' . $field['default'];
                $prepare = "`[#name]` float([#size])[#default][#null] COMMENT '[#comment]'";
                break;

            case 'ids':
            case 'log':
            case 'tag':
            case 'text':
            case 'list':
            case 'json':
            case 'meta':
            case 'config':
                $field['default'] = is_null($field['default']) ? '' : " DEFAULT '" . $field['default'] . "'";
                $prepare = "`[#name]` text[#null] COMMENT '[#comment]'";
                break;

            case 'varchar':
            case 'string':
            case 'email':
            case 'md5':
            case 'code':
                $field['default'] = is_null($field['default']) ? '' : " DEFAULT '" . $field['default'] . "'";
                $prepare = "`[#name]` varchar([#size])[#default][#null] COMMENT '[#comment]'";
                break;
        }
        return prepare($prepare, $field);
    }
}
