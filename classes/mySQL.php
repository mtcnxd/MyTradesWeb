<?php

namespace classes;

class DataBase
{
    public $table;
    public $queryresult;

    public function __construct()
    {
        return 'Conexion a la base de datos';
    }

    public function setTable($tbl)
    {
        $this->table = $tbl;
    }


    public function insert($data)
    {
        $query = "INSERT INTO ". $this->table;
        $query .= " (". implode(', ', array_keys($data)) . ") VALUES (";
        foreach($data as $values){
            $value[] = "'". $values ."'";
        }
        $query .= implode (',',$value) .")";
        
        return $query;
    }    

    public function where($where)
    {
        $query = "SELECT * FROM ". $this->table;
        $query .= ' WHERE ';
        foreach ($where as $key => $value) {
            $item_where[] = $key  ." = '". $value ."'";
        }
        $query .= implode (' AND ',$item_where);

        return $query;
    }

    public function update($data, $where)
    {
        $query = 'UPDATE '. $this->table .' SET ';
        $item = array();
        foreach ($data as $key => $value) {
            $item_update[] = $key  ." = '". $value ."'";
        }
        $query .= implode (', ',$item_update);
        $query .= ' WHERE ';
        foreach ($where as $key => $value) {
            $item_where[] = $key  ." = '". $value ."'";
        }
        $query .= implode (', ',$item_where);
        
        return $query;
    }

    public function delete($where)
    {
        $query = "DELETE FROM ". $this->table;
        $query .= ' WHERE ';
        foreach ($where as $key => $value) {
            $item_where[] = $key  ." = '". $value ."'";
        }
        $query .= implode (' AND ',$item_where);

        return $query;
    }

}

class MySQL {
	protected $host 	= 'localhost';
	protected $username = 'fortechm';
	protected $password = 'IrSW8=m)W$9U';
	protected $database = 'fortechm_test';
	protected $connect;
	protected $queryResult;
	
	public function __construct()
	{
		$this->mySQLconnect();
	}
	
	protected function mySQLconnect() 
	{
		try {
			$this->connect = mysqli_connect($this->host, $this->username, $this->password);
			mysqli_select_db($this->connect, $this->database);
		} catch (Exception $e){
			throw new Exception ('Message: '. $e->getMessage());
		}

	}
	
	public function mySQLinsert($tbl, $data)
	{
		$query  = "INSERT INTO ". $tbl ."(" ;
		$query .= implode(', ', array_keys($data)) . ") VALUES (". implode(',', array_values($data)).")";

		$this->queryResult = $query;

		if ( mysqli_query($this->connect, $query) ){
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function mySQLselect($tbl, $where, $order) 
	{
		$query = "SELECT * FROM ". $tbl;
		if ($where) $query .= " WHERE ". $where;
		if ($order) $query .= " ORDER BY ". $order; 
		
		$result = mysql_query($query);
		$value = array();
		while ($row = mysql_fetch_object($result)){
			$value[] = $row;
		}
		return $value;
	}
	
	public function mySQLquery($query) 
	{
		$result = mysqli_query($this->connect, $query);
		$data = array();
		while ($row = mysqli_fetch_object($result)){
			$data[] = $row;
		}
		return $data;
	}	
	
	public function mySQLnumows($query) 
	{
		$rows = mysqli_query($query);
		$rows = mysqli_num_rows($rows);
		return $rows;
	}	
	
	public function mySQLupdate($tbl, $data, $where)
	{
		$query  = "UPDATE ". $tbl ." SET ";
		$item = array();
		foreach ($data as $key => $value) {
			$item[] = $key  ." = '". $value ."'";
		}
		$query .= implode (',',$item);
		$query .= " WHERE " . $where;

		$this->queryResult = $query;

		if (mysqli_query($this->connect, $query)){
			return true;
		}
		return false;

	}

	public function getQueryResult()
	{
		return $this->queryResult;
	}

}