<?php
/**
 * Created by PhpStorm.
 * User: TheDarkSword01
 * Date: 24/02/2019
 * Time: 22:56
 */
abstract class SQL {
    protected $connection;

    private $prefix;
    private $table;

    public function __construct($prefix, $table){
        $this->prefix = $prefix;
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @param mixed $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    public function isConnected(){
        return $this->connection != null && mysqli_connect_errno();
    }

    public abstract function reconnect();

    public function close(){
        if($this->isConnected()){
            mysqli_close($this->connection);
            $this->connection = null;
        }
    }

    public function executeQuery($query){
        if(!$this->isConnected()) $this->reconnect();
        echo "Query Executed";
        return mysqli_query($this->connection, $query);
    }

    public function executeUpdate($query){
        if(!$this->isConnected()) $this->reconnect();
        return mysqli_stmt_execute(mysqli_prepare($this->connection, $query));
    }

    public function createTable(array $args){
        $query = "CREATE TABLE IF NOT EXISTS ".$this->prefix.$this->table." (";
        for($i = 0; $i < count($args); $i++){
            $query .= $args[$i];
            if($i != count($args)-1) $query .= ", ";
        }
        $query .= ");";

        echo $query;
        $this->executeQuery($query);
    }
}

final class MySQLLib extends SQL {
    private $host;
    private $port;
    private $database;
    private $username;
    private $password;

    public function __construct($table, $prefix = ""){
        parent::__construct($prefix, $table);
    }

    private function openConnection(){
        if(parent::isConnected()) return;
        $this->connection = mysqli_connect($this->host, $this->username, $this->password, $this->database, $this->port);
    }

    public function connect($host, $database, $username, $password, $port = 3306){
        $this->host = $host;
        $this->port = $port;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;

        $this->openConnection();
    }

    public function reconnect(){
        if(parent::isConnected()) mysqli_close($this->connection);
        $this->openConnection();
    }
}