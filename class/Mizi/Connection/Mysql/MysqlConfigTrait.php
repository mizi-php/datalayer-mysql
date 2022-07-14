<?php

namespace Mizi\Connection\Mysql;

use Mizi\Datalayer\Query;

trait MysqlConfigTrait
{
    /** Retorna uma configuração do banco de dados */
    protected function getConfig(string $name): string
    {
        $this->loadConfig();
        return $this->config[$name] ?? '';
    }

    /** Define uma configuração do banco de dados */
    protected function setConfig(string $name, string $value): void
    {
        $this->loadConfig();

        if (isset($this->config[$name])) {
            $query = Query::update('_cnf')->where('name', $name)->values(['value' => $value]);
        } else {
            $query = Query::insert('_cnf')->values(['name' => $name, 'value' => $value]);
        }

        $this->executeQuery($query);

        $this->config[$name] = $value;
    }

    /** Armazena as configurações do baco em um array de cache */
    protected function loadConfig(): void
    {
        if (is_null($this->config)) {
            $this->config = [];

            if (!boolval(
                $this->executeQuery(
                    Query::select('INFORMATION_SCHEMA.TABLES')
                        ->fields(['count(table_name)' => 'count'])
                        ->where('table_schema', $this->data['data'])
                        ->where('table_name', '_cnf')
                )[0]['count']
            )) {
                $this->executeQuery('CREATE TABLE _cnf (`name` VARCHAR (50), `value` TEXT);');
            }

            foreach ($this->executeQuery(Query::select('_cnf')) as $config) {
                $this->config[$config['name']] = $config['value'];
            }
        }
    }
}
