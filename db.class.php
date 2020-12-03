<?php

class DB {

    private $connection = null;

    public function __construct($dbhost = "", $dbname = "", $username = "", $password = "")
    {
        try {
            // create the connection
            // MYSQL_ATTR_FOUND_ROWS will make the rowCount() to return the number of rows found on update even if nothing was updated (oldvalue = newvalue)
            $this->connection = new PDO("mysql:host={$dbhost};dbname={$dbname};", $username, $password, array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch( Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function insert($sql = "" , $parameters = [])
    {
        try {
            $this->executeQuery($sql , $parameters);

            return $this->connection->lastInsertId();

        } catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function select($sql = "" , $parameters = [])
    {
        try {
            $query = $this->executeQuery($sql , $parameters);
            return $query->fetchAll();

        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }

    }

    public function update($sql = "" , $parameters = [])
    {
        try {
            $query = $this->executeQuery($sql , $parameters);
            return $query->rowCount();
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function delete($sql = "" , $parameters = [])
    {
        try {
            $this->executeQuery($sql , $parameters);

        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function executeQuery($sql = "" , $parameters = [])
    {
        try {
            $query = $this->connection->prepare($sql);
            $query->execute($parameters);

            return $query;

        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}