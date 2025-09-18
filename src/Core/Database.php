<?php

namespace Src\Core;

use PDO;
use PDOException;
use Exception;

class Database
{
    private $conn = null;
    private $query;
    private $params;
    private $stmt;
    private $inTransaction = false;
    private $logFile = "database_errors.log";

    public function __construct($config)
    {
        $user = getenv("LOCAL_DB_ADMISSION_USERNAME");
        $pass = getenv("LOCAL_DB_ADMISSION_PASSWORD");

        $dsn = "mysql:" . http_build_query($config, "", ";");
        try {
            $this->conn = new PDO(
                $dsn,
                $user,
                $pass,
                [
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]
            );
        } catch (PDOException $e) {
            throw $e;
        }
    }

    private function logError(PDOException $e)
    {
        $logFilePath = dirname(__FILE__) . '/' . $this->logFile;
        if (!file_exists($logFilePath)) {
            touch($logFilePath);
        }
        error_log("Warning: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString() . "\n", 3, $logFilePath);
    }

    // New transaction methods
    public function beginTransaction()
    {
        if (!$this->inTransaction) {
            try {
                $this->inTransaction = $this->conn->beginTransaction();
                return $this->inTransaction;
            } catch (PDOException $e) {
                $this->logError($e);
                return json_encode(array("error" => "Transaction start failed: " . $e->getMessage()));
            }
        }
        return false;
    }

    public function commit()
    {
        if ($this->inTransaction) {
            try {
                $this->conn->commit();
                $this->inTransaction = false;
                return true;
            } catch (PDOException $e) {
                $this->logError($e);
                return json_encode(array("error" => "Transaction commit failed: " . $e->getMessage()));
            }
        }
        return false;
    }

    public function rollback()
    {
        if ($this->inTransaction) {
            try {
                $this->conn->rollBack();
                $this->inTransaction = false;
                return true;
            } catch (PDOException $e) {
                $this->logError($e);
                return json_encode(array("error" => "Transaction rollback failed: " . $e->getMessage()));
            }
        }
        return false;
    }


    public function run($query, $params = array()): mixed
    {
        $this->query = $query;
        $this->params = $params;
        try {
            $this->stmt = $this->conn->prepare($this->query);
            $this->stmt->execute($this->params);
            return $this;
        } catch (Exception $e) {
            $this->logError($e);
            throw $e;
        }
    }

    private function type(): mixed
    {
        return explode(' ', $this->query)[0];
    }

    public function all()
    {
        if ($this->type() == 'SELECT') return $this->stmt->fetchAll();
    }

    public function one()
    {
        if ($this->type() == 'SELECT') return $this->stmt->fetch();
    }

    public function add($autoIncrementColumn = null, $primaryKeyValue = null)
    {
        if ($this->type() == 'INSERT') {
            if ($autoIncrementColumn) return $this->conn->lastInsertId($autoIncrementColumn);
            else if ($primaryKeyValue) return $primaryKeyValue;
            else return true;
        }
        return false;
    }

    public function del()
    {
        if ($this->type() == 'DELETE') return $this->stmt->rowCount();
        return false;
    }

    public function edit()
    {
        if ($this->type() == 'UPDATE') return $this->stmt->rowCount();
        return false;
    }
}
