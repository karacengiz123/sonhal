<?php
/**
 * Created by PhpStorm.
 * User: aydn33
 * Date: 27.02.2019
 * Time: 10:44
 */

namespace App\Rethink;

use r;

class Service
{
    private $host;
    private $port;
    private $api;
    private $conn;
    private $db;
//  private $all;

    public function __construct($host,$port=28015,$db='test')
    {
        $this->host = $host;
        $this->port = $port;
        $this->db   = $db;
//      $this->all  = $all;
        $this->connect();
    }

    public function connect()
    {
        $this->conn = r\connect($this->host,$this->port,$this->db);
        return $this;
    }

    public function getConnection()
    {
        return $this->conn;

    }
}