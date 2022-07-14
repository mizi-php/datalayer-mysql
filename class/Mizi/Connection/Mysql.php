<?php

namespace Mizi\Connection;

use Mizi\Cif;
use Mizi\Connection\Mysql\MysqlMapTrait;
use Mizi\Connection\Mysql\MysqlConfigTrait;
use Mizi\Connection\Mysql\MysqlExecuteQueryTrait;
use Mizi\Connection\Mysql\MysqlExecuteSchemeQueryTrait;
use Mizi\Datalayer\Connection;

class Mysql extends Connection
{
    use MysqlMapTrait;
    use MysqlConfigTrait;
    use MysqlExecuteQueryTrait;
    use MysqlExecuteSchemeQueryTrait;

    /** Inicializa a conexão */
    protected function load()
    {
        $this->data['host'] = $this->data['host'] ?? env(strtoupper("DB_{$this->datalayer}_HOST"));
        $this->data['data'] = $this->data['data'] ?? env(strtoupper("DB_{$this->datalayer}_DATA"));
        $this->data['user'] = $this->data['user'] ?? env(strtoupper("DB_{$this->datalayer}_USER"));
        $this->data['pass'] = $this->data['pass'] ?? env(strtoupper("DB_{$this->datalayer}_PASS"));

        foreach ($this->data as $name => $value)
            $this->data[$name] = Cif::off($value);

        $this->instancePDO = [
            "mysql:host={$this->data['host']};dbname={$this->data['data']};charset=utf8",
            $this->data['user'],
            $this->data['pass']
        ];
    }
}