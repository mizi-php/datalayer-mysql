<?php

use Mizi\Connection\Mysql;
use Mizi\Datalayer;

Datalayer::registerType('mysql', Mysql::class);