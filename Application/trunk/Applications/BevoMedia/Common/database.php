<?php
class SqlConnection
{
	var $host;
	var $schema;
	var $user;
	var $password;
	
	var $connection;
	var $result;

	function SqlConnection($host = DATABASE_HOST, $user = DATABASE_USER, $password = DATABASE_PASS, $schema = DATABASE_NAME)
	{
		$this->host = $host;
		$this->user = $user;
		$this->password = $password;
		$this->schema = $schema;
	}
	
	function open() {
		$this->connection = mysql_connect($this->host,$this->user,$this->password,true)
			or die('Connection: '.mysql_error());
			
		mysql_select_db($this->schema)
			or die('Schema: '.mysql_error());
				
		register_shutdown_function(array(&$this, 'close'));
		return true;
	}
	
	function close() {
		return @mysql_close($this->connection);
	}
}

class SqlCommand
{

	var $query;
	var $connection;
	var $parameters = array();
	var $preppedQuery;
	
	function SqlCommand($query, $connection) {
		$this->query = $query;
		$this->connection = $connection->connection;
	}
	
	function prepareQuery() {
		$query = $this->query;
		foreach($this->parameters as $parameter=>$value)
		{
			$query = eregi_replace('(.*)\@'.$parameter.'([^a-z0-9]|$)(.*)', '\1^*^*^\2\3', $query);
			$query = str_replace('^*^*^', $this->escapeValue($value), $query);
		}
		$this->preppedQuery = $query;
		//echo $query;
		return $query;
	}
	
	function execute() {
		$this->result = mysql_query($this->prepareQuery(), $this->connection)
			or die('<p>Query error: '.mysql_error().'</p>');
		return $this->result;
	}
	
	function executeScalar($id = 0) {
		$this->execute();
		if($this->rowCount() == 0) {
			die('<p>No return value when scalar expected.</p>');
		} else {
			$row = $this->getRow();
			return $row[$id];
		}
	}
	
	function executeNonQuery() {
		$this->execute();
	}
	
	function getRow() {
		return mysql_fetch_array($this->result);
	}
	
	function getAllRows() {
		$all = array();
		while ($row = mysql_fetch_assoc($this->result)) { $all[] = $row; }
		return $all;
	}
	
	function rowCount() {
		return mysql_num_rows($this->result);
	}
	
	public function escapeValue($value) {
		if(!isset($value)) return 'NULL';
		
		//if(strtotime($value)) return date("'Y-m-d H:i:s'", strtotime($value));

		$escaped = $value;

		if(function_exists('get_magic_quotes_gpc')) {
			if(get_magic_quotes_gpc()) {
				$escaped = stripslashes($escaped);
			}
		}

		if(!is_int($value) || $value[0] == '0') {
			// Leading zeros are treated as text
			$escaped = "'".mysql_real_escape_string($escaped)."'";
		}
		return $escaped;
	}
	
	function getLastId() {
		return mysql_insert_id($this->connection);
	}

}