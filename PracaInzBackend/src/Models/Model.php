<?php
namespace Models;
use \PDO;

abstract class Model
{
    protected $pdo;

    public function __construct()
    {
        {
            try {
                $this->pdo = \Config\Database\DBConnection::getHandle();
            } catch (\PDOException $e) {
                $this->pdo = null;
            }
        }
    }
}
