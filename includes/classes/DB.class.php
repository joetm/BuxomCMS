<?php

/* **************************************************************
 *  File: DB.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Database Class
*
*/
class DB
{

	public static $qcount = 0;
	public static $queries = '';

	/***error messages***/
    const ERROR_CONNECTDB = "Could not connect to the database.";
    const ERROR_SELECTDB = "Could not select the database.";
    const ERROR_FREE = 'Could not free the result set.';
    const ERROR_QUERY = 'Database query execution failed.';
    const ERROR_NUM_ROWS = 'Could not retrieve the number of rows from the result set.';
	const ERROR_NOT_CONNECTED = 'Not connected to the database server.';

    private $connected = false;
    private $connection = false;

	private static $dbInstance = null;


	//database variables
    private $database;
    private $hostname;
    private $username;
    private $password;

/**
* Constructor
*
* @access	public
*/
	private function __construct() //Array $config
    {
		$_config['db'] = Config::Get('db');
		$this->username = $_config['db']['__dbuser'];
		$this->password = $_config['db']['__dbpass'];
		$this->database = $_config['db']['__dbname'];
		$this->hostname = $_config['db']['__dbhost'];

		$this->Connect();
    }

/**
* Destructor
*
* @access	public
*/
    public function __destruct()
    {
        $this->Disconnect();
    }

/**
* Get Database Instance
*
* @access	public
* @return	object
*/
    public static function getInstance()
    {
        if (!self::$dbInstance)
        {
            self::$dbInstance = new DB;
        }

        return self::$dbInstance;
    }

/**
* Connect to Database
*
* @access	private
*/
	private function Connect()
	{

		if(!$this->connected)
		{

			$this->connection = @mysql_connect($this->hostname, $this->username, $this->password, true);

			if( $this->connection === false )
			{
				die(self::ERROR_CONNECTDB);
				//$this->db_error(self::ERROR_CONNECTDB);
			}
			else
			{
	            $this->connected = true;

	            if(@mysql_select_db($this->database, $this->connection) === false)
	            {
					$this->db_error(self::ERROR_SELECTDB);
    	        }
				else
				{
					@mysql_query("SET `wait_timeout` = 86400", $this->connection);
					@mysql_query("SET `interactive_timeout` = 86400", $this->connection);
				}
			} //else

		}
	}

/**
* Is Connected?
*
* @access	public
* @return	bool
*/
	public function is_connected(){
		if ($this->connection){
			return true;
		}
		else {
			return false;
		}
	}

/**
* Disconnect
*
* @access	public
*/
	public function Disconnect()
	{
		if($this->connected)
		{
			mysql_close($this->connection);
			$this->connection = false;
			$this->connected = false;
		}
	}

/**
* Free Result
*
* @access	private
* @param	resource	$result
*/
	private function free($result)
	{
		if( @mysql_free_result($result) === false )
		{
			$this->db_error(self::ERROR_FREE);
		}
	}

/**
* NOW Helper Function
*
* @access	public
* @return	string
*/
	public static function NOW()
	{
		return date(Config::Get('datetime_string'));
	}
/**
* UNIXNOW Helper Function
*
* @access	public
* @return	integer
*/
	public static function UNIXNOW()
	{
		return time();
	}

/**
* Query
*
* @access	public
* @param	string	$query
* @param	array	$binds
* @return	resource
*/
    public function query($query, $binds = array())
    {
        $query = $this->Prepare($query, $binds);

        $result = mysql_query($query, $this->connection);

		//debug
		//echo $query . "<br>";

		if( $result === false )
		{
			$this->db_error(self::ERROR_QUERY, $query);
		}

		self::$qcount++;

		if(__showdebugqueries)
			if(Config::Get('debug'))
				self::$queries .= htmlentities($query) . "<br>".PHP_EOL;

		return $result;
	}

/**
* Query Single Column
*
* @access	public
* @param	string	$query
* @param	array	$binds
* @return	mixed	string | false
*/
	public function Column($query, $binds = array())
	{
		$result = $this->query($query, $binds);

		$row = mysql_fetch_array($result, MYSQL_NUM);
		$this->free($result);

		if(is_array($row))
		{
			return $row[0];
		}
		else
		{
			return false;
		}
	}

/**
* Fetch Object
*
* @access	public
* @param	string	$query
* @param	array	$binds
* @param	string	$classname
* @return	object
*/
	public function FetchObject($query, $binds = array(), $classname = ''){

		$res = $this->query($query);
	    $obj = mysql_fetch_object($res, $classname);

		$this->free($res);

		return $obj;
	}

/**
* FetchAll
*
* @access	public
* @param	string	$query
* @param	array	$binds
* @return	mixed	null | array
*/
	public function FetchAll($query, $binds = array())
	{
		$result = null;
		if(is_bool($query))
		{
			return array();
		}
		else if(is_resource($query))
		{
			$result = $query;
		}
		else
		{
            $result = $this->query($query, $binds);
		}

		$results = array();
		while( $row = mysql_fetch_assoc($result) )
		{
			$results[] = $row;
		}

		$this->free($result);

		return $results;
	}

/**
* Fetch Row
*
* @access	public
* @param	string	$query
* @param	array	$binds
* @return	mixed	array | false
*/
	public function Row($query, $binds = array())
	{
		$row = $this->FetchAll($query, $binds);

		if($row)	return $row[0];
		else		return false;
	}

/**
* Update
*
* @access	public
* @param	string	$query
* @param	array	$binds
* @return	array
*/
	public function Update($query, $binds = array())
	{
		$result = $this->query($query, $binds);

		return mysql_affected_rows($this->connection);
	}

/**
* Number of Result Rows
*
* @access	public
* @param	resource	$result
* @return	integer
*/
	public function NumRows($result)
	{
		if( ($rows = @mysql_num_rows($result)) === false )
		{
			$this->db_error(self::ERROR_NUM_ROWS);
		}

		return $rows;
	}

/**
* Next Row
*
* @access	public
* @param	resource	$result
* @return
*/
	public function NextRow($result)
	{
		return mysql_fetch_assoc($result);
	}

/**
* Last Insert Id
*
* @access	public
* @return	integer
*/
	public function LastInsertId()
	{
		return mysql_insert_id($this->connection);
	}

/**
* Mysql Field Array
*
* @access	public
* @param	string	$query
* @return	array
*/
    public function mysql_field_array($query) {

        $field = mysql_num_fields($query);

        for ($i = 0;$i < $field; $i++) {
            $names[] = mysql_field_name( $query, $i );
        }

        return $names;
    }

/**
* Get Tables
*
* @access	public
* @return	array
*/
	public function GetTables()
	{
		$tables = array();
		$result = $this->query("SHOW TABLE STATUS");

		$name = 'Name';
		$comment = 'Comment';

		while($row = $this->NextRow($result))
		{
			$tables[$row[$name]] = array('name' => $row[$name], 'description' => $row[$comment]);
		}

		$this->free($result);

		return $tables;
	}

/**
* Get Columns
*
* @access	public
* @param	string	$table
* @param	bool	$assoc
* @return	array
*/
    public function GetColumns($table, $assoc = false)
    {

		$columns = array();

		$result = $this->query('DESCRIBE '. $table);

		$field = mysql_field_name($result, 0);

		while ($column = $this->NextRow($result))
		{
			if($assoc)
			{
				$columns[$column[$field]] = $column[$field];

				switch($column['Key'])
				{
					case 'PRI': //primary
					case 'MUL': //foreign
						$columns[$column[$field]] = $column['Key'];
						break;
					default:
						//regular key
						$columns[$column[$field]] = 0;
						break;
				}

				if($column['Key']=='PRI')
				{
					//mark as primary
					$columns[$column[$field]] = 'PRI';
				}
				else
					$columns[$column[$field]] = 0;
			}
			else
			{
				$arr = array('name' => $column[$field]);

				//echo $column[$field] . ": " . $column['Key']."<br>";

				switch($column['Key'])
				{
					case 'PRI':
						//mark as primary
						$arr['type'] = 'PRI';
						break;
					case 'MUL':
						//mark as foreign
						$arr['type'] = 'MUL';
						break;
					default:
						//regular key
						$arr['type'] = 0;
						break;
				}

				$columns[] = $arr;
			}
		}

		$this->free($result);

		return $columns;
	}

/**
* Prepare Query
*
* @access	public
* @param	string	$query
* @param	array	$binds
* @return	string
*/
	public function Prepare($query, $binds = array())
	{
		if(empty($binds))
		{
			return $query;
		}

		$qresult = '';
		$i = 0;

		$pieces = preg_split('/(\?|#)/', $query, -1, PREG_SPLIT_DELIM_CAPTURE);
		foreach( $pieces as $piece )
		{
			if( $piece == '?' )
			{
				if( $binds[$i] === null )
				{
					$qresult .= 'NULL';
				}
				else
				{
					$qresult .= "'" . mysql_real_escape_string($binds[$i], $this->connection) . "'";
				}

				$i++;
			}
			else if( $piece == '#' )
			{
				$binds[$i] = str_replace('`', '\`', $binds[$i]);
				$qresult .= "`" . $binds[$i] . "`";
				$i++;
			}
			else
			{
				$qresult .= $piece;
			}
		}

		return $qresult;
	}

/**
* Create Unique Slug (recursive)
*
* @access	public
* @param	string	$slug
* @param	string	$type
* @return	string
*/
	public function unique_slug($slug, $type=''){

		// return unique slug
		/***database connect***/
		$db = DB::getInstance();

		if(empty($type))
		{
			//query slugs of the content by default
			$sql = "SELECT `slug` FROM `bx_content` WHERE `slug` = ? AND (`type` = 'videos' OR `type` = 'pics' OR `type` = 'models') LIMIT 1";
		}
		else
		{
			//query slugs from specified type (for example 'models', 'pics' or 'videos')
			$sql = "SELECT `slug` FROM `bx_content` WHERE `slug` = ? AND `type` = ? LIMIT 1";
		}

		$slug_name_check = $db->Column($sql, array($slug, $type));

		if($slug_name_check)
		{
			$suffix = 2;

			$alt_post_name = $slug;

			do
			{
				$lastchars = substr($alt_post_name, -2);

				if(is_numeric($lastchars[1]) && $lastchars[0] == '-' && $lastchars[1] < 9)
				{
						$alt_post_name = substr($alt_post_name, 0, -1 ).($lastchars[1]+1);
				}
				else
				{
					$alt_post_name = $alt_post_name . "-" . $suffix;
				}

				$slug_name_check = $db->Column($sql, array($alt_post_name, $type));

//				$suffix++;
			}
			while ($slug_name_check);

			$slug = $alt_post_name;
		}

		return $slug;

	} // unique_slug

/**
* DB Error
*
* @access	private
* @param	string	$msg
* @param	string	$query
* @param	string	$line
*/
	private function db_error($msg = '', $query = '', $line = '') {

		if($this->connection){
			$this->error = mysql_error($this->connection);
			$this->errno = mysql_errno($this->connection);
		}
		else{
			$this->error = mysql_error();
			$this->errno = mysql_errno();
		}

		$heading = 'Database Error';

		$template = Path::Get('path:site').'/includes/errors/error_db.php';

			$tpl = new Template('error_db');

//		@ini_set("short_open_tag", 0);
		ob_start();
		include($template);
		$html = ob_get_clean();
		echo $html;

			if(Config::Get('debug')){
				echo "<br>";
				$tpl->debug();
			}

		$this->Disconnect();
		die();

	} //error

} //class DB
