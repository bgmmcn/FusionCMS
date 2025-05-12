<?php

namespace App\Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    public string $defaultGroup = "cms";

    public array $cms = [
        "DSN" => "",
        "hostname" => "127.0.0.1",
        "username" => "root",
        "password" => "root",
        "database" => "1_cms",
        "DBDriver" => "MySQLi",
        "DBPrefix" => "",
        "pConnect" => false,
        "DBDebug" => true,
        "charset" => "utf8mb4",
        "DBCollat" => "utf8mb4_general_ci",
        "swapPre" => "",
        "encrypt" => false,
        "compress" => false,
        "strictOn" => false,
        "failover" => [],
        "port" => 3306,
    ];

    public array $account = [
        "DSN" => "",
        "hostname" => "127.0.0.1",
        "username" => "root",
        "password" => "root",
        "database" => "1_auth",
        "DBDriver" => "MySQLi",
        "DBPrefix" => "",
        "pConnect" => false,
        "DBDebug" => false,
        "charset" => "utf8",
        "DBCollat" => "utf8_general_ci",
        "swapPre" => "",
        "encrypt" => false,
        "compress" => false,
        "strictOn" => false,
        "failover" => [],
        "port" => 3306,
    ];
}
