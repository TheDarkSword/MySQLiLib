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

    public function executeStatement($query){
        if(!$this->isConnected()) $this->reconnect();
        $stm = mysqli_prepare($this->connection, $query);
        return mysqli_stmt_execute($stm);
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

    public function addLineArray(array $columns, array $values, $secure = false){
        if(count($columns) != count($values)) return;

        if($secure){
            for($i = 0; $i < count($values); $i++){
                $values[$i] = mysqli_real_escape_string($this->connection, $values[$i]);
            }
        }

        $query = "INSERT INTO `".$this->prefix.$this->table."` (";
        for($i = 0; $i < count($columns); $i++){
            $query .= "`$columns[$i]`";
            if($i != count($columns)-1) $query .= ", ";
        }
        $query .= ") VALUES (";
        for($i = 0; $i < count($values); $i++){
            $query .= "'$values[$i]'";
            if($i != count($values)-1) $query .= ", ";
        }
        $query .= ");";

        $this->executeQuery($query);
    }

    public function addLine($column, $value, $secure = false){
        $this->addLineArray(array($column), array($value), $secure);
    }

    public function removeLineArray(array $columns, array $values){
        if(count($columns) != count($values)) return;

        $query = "DELETE FROM `$this->prefix$this->table` WHERE (";
        for($i = 0; $i < count($columns); $i++){
            if($values[$i] == null) $query .= "`$columns[$i]` IS NULL";
            else $query .= "`$columns[$i]` = '$values[$i]'";
            if($i != count($columns)-1) $query .= " AND ";
        }
        $query .= ");";

        $this->executeQuery($query);
    }

    public function removeLine($column, $value){
        $this->removeLineArray(array($column), array($value));
    }

    public function lineExistsArray(array $columns, array $values){
        if(count($columns) != count($values)) return false;

        $query = "SELECT * FROM `$this->prefix$this->table` WHERE (";
        for($i = 0; $i < count($columns); $i++){
            if($values[$i] == null) $query .= "`$columns[$i]` IS NULL";
            else $query .= "`$columns[$i]` = '$values[$i]'";
            if($i != count($columns)-1) $query .= " AND ";
        }
        $query .= ");";

        $result = $this->executeQuery($query);
        return mysqli_num_rows($result) > 0;
    }

    public function lineExists($column, $value){
        return $this->lineExistsArray(array($column), array($value));
    }

    public function getValueArray(array $columns, array $values, $search){
        if(count($columns) != count($values)) return;

        $query = "SELECT * FROM `$this->prefix$this->table` WHERE (";
        for($i = 0; $i < count($columns); $i++){
            if($values[$i] == null) $query .= "`$columns[$i]` IS NULL";
            else $query .= "`$columns[$i]` = '$values[$i]'";
            if($i != count($columns)-1) $query .= " AND ";
        }
        $query .= ");";

        $result = $this->executeQuery($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            $res = $row[$search];
            $result->free();
            return $res;
        }
        return null;
    }

    public function getValue($column, $value, $search){
        return $this->getValueArray(array($column), array($value), $search);
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