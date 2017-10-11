<?php
	
	return function () {
		
		$new = function () {
			
			$_connections = array();
			$_credentials = array();
			
			$add = function ($key, $host, $port, $char, $name, $user, $pass) use (&$_credentials) {
				$_credentials[hash('sha512', $key)] = array(
					'host' => $host,
					'port' => $port,
					'char' => $char,
					'name' => $name,
					'user' => $user,
					'pass' => $pass
				);
			};
			
			$connect = function ($key) use (&$_connections, &$_credentials) {
				$credentials = $_credentials[hash('sha512', $key)];
				if ($credentials) {
					$connection = hash('sha512', $credentials['host'].$credentials['port'].$credentials['char'].$credentials['name'].$credentials['user'].$credentials['pass']);
					if (isset($_connections[$connection]) && $_connections[$connection]) {
						return array(null, $_connections[$connection]);
					} else {
						try {
							$_connections[$connection] = new \PDO('mysql:host='.$credentials['host'].';'.((strlen($credentials['port'])) ? 'port='.intval($credentials['port']).';' : '').'dbname='.$credentials['name'].';charset='.((strlen($credentials['char'])) ? $credentials['char'] : 'utf8').';', $credentials['user'], $credentials['pass'], array(\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION, \PDO::ATTR_EMULATE_PREPARES=>false));
							return $_connections[$connection] ? array(null, $_connections[$connection]) : array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__);
						} catch (\PDOException $err) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
					}
				}
				return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__);
			};
			
			$close = function () use (&$_connections, &$_credentials) {
				foreach ($_connections as $k => $v) {
					$_connections[$k] = null;
				}
				foreach ($_credentials as $k => $v) {
					$credential[$k] = null;
				}
			};
			
			$create = function ($key, $query, $params) use ($connect) {
				list($error, $connection) = $connect($key);
				if (!!$error) { return array($error); }
				if (!$connection) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				try {
					$statement = $connection->prepare($query);
					$result = $statement->execute($params) ? true : false;
					$statement = null;
					return array(null, $result);
				} catch (\PDOException $err) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			};
			
			$delete = function ($key, $query, $params) use ($connect) {
				list($error, $connection) = $connect($key);
				if (!!$error) { return array($error); }
				if (!$connection) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				try {
					$statement = $connection->prepare($query);
					$statement->execute($params);
					$result = intval($statement->rowCount());
					$statement = null;
					return array(null, $result);
				} catch (\PDOException $err) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			};
			
			$insert = function ($key, $query, $params) use ($connect) {
				list($error, $connection) = $connect($key);
				if (!!$error) { return array($error); }
				if (!$connection) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				try {
					$statement = $connection->prepare($query);
					$result = $statement->execute($params) ? true : false;
					$statement = null;
					return array(null, $result);
				} catch (\PDOException $err) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			};
			
			$select = function ($key, $query, $params) use ($connect) {
				list($error, $connection) = $connect($key);
				if (!!$error) { return array($error); }
				if (!$connection) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				try {
					$statement = $connection->prepare($query);
					$statement->execute($params);
					$result = array(); while($row = $statement->fetch(\PDO::FETCH_ASSOC)) { $result[] = $row; }
					$statement = null;
					return array(null, $result);
				} catch (\PDOException $err) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			};
			
			$update = function ($key, $query, $params) use ($connect) {
				list($error, $connection) = $connect($key);
				if (!!$error) { return array($error); }
				if (!$connection) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				try {
					$statement = $connection->prepare($query);
					$statement->execute($params);
					$result = intval($statement->rowCount());
					$statement = null;
					return array(null, $result);
				} catch (\PDOException $err) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			};
			
			return array(null, array(
				'add' => $add,
				'close' => $close,
				'create' => $create,
				'delete' => $delete,
				'insert' => $insert,
				'select' => $select,
				'update' => $update
			));
			
		};
		
		return array(
			'new' => $new
		);
		
	};
	
?>