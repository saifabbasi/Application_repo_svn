<?php 

	require_once 'Zend/Db/Adapter/Pdo/Mysql.php';

	class RCS_Db_Adapter extends Zend_Db_Adapter_Pdo_Mysql {
		
		private $_connections = array();
		
		
		public function __construct($config) {
			
//			echo '<pre>'; print_r($config);die;
			
			foreach ($config['slaveServers'] as $server) {
				$this->_connections[] = new Zend_Db_Adapter_Pdo_Mysql($server);
			}
//			echo '<pre>'; print_r($this);die;
			return parent::__construct($config);
		}
		
		public function __destruct() {
//			$i = rand(0, count($this->_connections)-1);
//			
//			echo '<pre>';
//			echo $i."<br />";
//			print_r($this->_connections[$i]);
//			die('ddsa djks');
		}
		
		public function query($sql, $bind = array()) {
			
			$sqlTemp = trim($sql);
			$selectCheck = substr($sqlTemp, 0, 6);
			
			$i = rand(0, count($this->_connections)-1);
			
			if (stristr($selectCheck, "select")===false) {
				return parent::query($sql, $bind);				
			} else {
				
			}
			
			
			return $this->_connections[$i]->query($sql, $bind);
		}
		
		public function insert($table, array $bind) {
			return parent::insert($table, $bind);
		}
		
		public function update($table, array $bind, $where = '') {
			return parent::update($table, $bind, $where);
		}
		
		public function delete($table, $where = '') {
			return parent::delete($table, $where);
		}
		
		
		
		
	};
