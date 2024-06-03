<?php

class Database
{

    private $connection;
    private $host = 'localhost';
    private $user = 'root';
    private $password = '';
    private $dbName = 'pokemon';

    private $queryResult;

    public function __construct()
    {
        $this->connect();
    }

    public function __destruct()
    {
        mysqli_close($this->connection);
    }

    // Conexión a la base de datos
    private function connect()
    {
        $this->connection = new mysqli($this->host, $this->user, $this->password, $this->dbName);
        if ($this->connection->connect_errno) {
            die("Conexión Fallida: {$this->connection->connect_errno}");
        }
    }

    public function escape($value)
    {
        return $this->connection->real_escape_string($value);
    }

    // Guardar un registro
    public function insert($table, $fields, $values)
    {
        $sqlQuery = "INSERT INTO $table ($fields) VALUES $values";

        if ($this->connection->query($sqlQuery)) {
            return $this->connection->insert_id;
        } else {
            return "Error al guardar el registro: {$this->connection->error}";
        }
    }

    // Obtener un registro
    public function select($table, $fields, $condition)
    {
        $sqlQuery = "SELECT $fields FROM $table WHERE $condition";
        $this->queryResult = $this->connection->query($sqlQuery);
        return $this;
    }

    // Consulta personalizada múltiple
    public function customMultiQuery($query)
    {
        $resQuery = $this->connection->multi_query($query);
        return $resQuery;
    }

    // Consulta personalizada
    public function customSingleQuery($sqlQuery)
    {
        $this->queryResult = $this->connection->query($sqlQuery);
        return $this;
    }

    // Obtener múltiples registros
    public function getRows()
    {
        $rows = [];
        while ($row = $this->queryResult->fetch_object()) {
            $rows[] = $row;
        }

        return $rows;
    }

    // Obtener un registro
    public function getRow()
    {
        return $this->queryResult->fetch_object();
    }
}
