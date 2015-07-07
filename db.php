<?php
/*
	/*
		Author: Ano Tisam
		Email: an0tis@gmail.com
		Website: http://www.whupi.com
		Description: Mysql database class - only one connection alowed
		LICENSE: Free for personal and commercial use licensed under the Creative Commons Attribution 3.0 License, which means you can:

					Use them for personal stuff
					Use them for commercial stuff
					Change them however you like

				... all for free, yo. In exchange, just give the AUthor credit for the program and tell your friends about it :)
*/
class Database {
	private $_connection;
	private static $_instance; //The single instance
	private $_host = EW_CONN_HOST;
	private $_username = EW_CONN_USER;
	private $_password = EW_CONN_PASS;
	private $_database = EW_CONN_DB;

	/*
	Get an instance of the Database
	@return Instance
	*/
	public static function getInstance() {
		if(!self::$_instance) { // If no instance then make one
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	// Constructor
	private function __construct() {
		$this->_connection = new mysqli($this->_host, $this->_username, 
			$this->_password, $this->_database);
	
		// Error handling
		if(mysqli_connect_error()) {
			trigger_error("Failed to conencto to MySQL: " . mysql_connect_error(),
				 E_USER_ERROR);
		}
	}

	// Magic method clone is empty to prevent duplication of connection
	private function __clone() { }

	// Get mysqli connection
	public function getConnection() {
		return $this->_connection;
	}
}
?>