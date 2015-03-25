<?php

/* **************************************************************
 *  File: export.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class export extends BaseController
{
	private static $token = false;

	private $error = false;

	//defaults
	private $export = '';
	private $data = array();

	private $onscreen = 'yes';
	private $members = 'all';
	private $separator = ',';

/**
* index
*
* @access private
*/
	private function index()
	{
		//get template
		$tpl = Zend_Registry::get('tpl');

		//get translation
		$translate = Zend_Registry::get('translate');

		/***page setup***/
		$tpl->title = $translate->_("Export Member Data");
		/***page setup***/

		/***database connect***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

			$tables = array();
			$tables['member'] = $db->GetColumns('bx_member', true);
			$tables['join'] = $db->GetColumns('bx_join', true);
			$tables['member_session'] = $db->GetColumns('bx_member_session', true);
			$tables['member_email'] = $db->GetColumns('bx_member_email', true);
			$tables['member_activitylog'] = $db->GetColumns('bx_member_activitylog', true);

		/***database disconnect***/
		unset($db);

		//tables for checkboxes
		$tpl->assign('tables', $tables);

		//the exported data
		$tpl->assign("export", $this->export);

		//preselect checkboxes
		if(!empty($this->data))
			$tpl->assign("data", $this->data);

		//preselect checkboxall
		if(isset($_POST['checkboxall']))
			$tpl->assign("checkboxall", 'on');

		//preselect
		if(!isset($_POST['outputformat']))
			$tpl->assign("outputformat", "CSV");
		else
			$tpl->assign("outputformat", Input::clean_single('p','outputformat','NOHTML'));

		$tpl->assign("onscreen", $this->onscreen);
		$tpl->assign("members", $this->members);

		//error
		if($this->error) $tpl->errormessage($this->error);

		//security token
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		//internationalization
		$_t = $translate->translateArray(array(
			"export" => "Export",
			"export_active_members" => "Export Active Members",
			"export_chargebacks" => "Export Chargebacks",
			"export_inactive_members" => "Export Inactive Members",
			"export_member_data" => "Export Member Data",
			"members" => "Members",
			"output_format" => "Output Format",
			"select_all" => "Select All",
		));
		$tpl->assign("_t", $_t);

		$tpl->display();

		$tpl->debug();

	} //index

/**
* querydata
*
* Get the data based on the checkbox selection
*
* @access private
*/
	private function querydata()
	{
		$this->export = '';

		/***database connect***/
		$db = DB::getInstance();

		//who to query?
		$this->members = Input::clean_single('p','members','NOHTML');
		//all, active, inactive, or chargeback
		if(!$this->members) $this->members = 'all';


		//output to screen?
		$this->onscreen = Input::clean_single('p','onscreen','UINT');

		//separator
		$this->separator = Input::clean_single('p','separator','NOHTML');
		if(empty($this->separator)) $this->separator = ",".PHP_EOL;


		/*sanitize all the checkboxes and build queries*/
		$sql = array();
		foreach($_POST['data'] as $table => $value)
		{
			//bx_KEY
			$sql[$table] = '';
			if(!empty($_POST['data'][$table]))
			foreach($_POST['data'][$table] as $key => $value)
			{
				$key = Input::clean($key, 'NOHTML');
				$value = Input::clean($value, 'UINT');

				//for prefill
				$input[$key] = $this->data[$table][$key] = $value;

				if($input[$key] == 1)
					$sql[$table] .= "`".$table."`.".$db->Prepare('#', array($key)).', ';
			}
			$sql[$table] = trim($sql[$table], ', ');
		}


		//build query
		$the_sql = 'SELECT '.implode(",", $sql). " FROM
			`bx_join` as `join`
			LEFT JOIN `bx_member` as `member` ON (join.member_id = member.id)
			LEFT JOIN `bx_member_session` as `member_session` ON (member_session.member_id = member.id)
			LEFT JOIN `bx_member_email` as `member_email` ON (join.member_id = member_email.id)
			LEFT JOIN `bx_member_activitylog` as `member_activitylog` ON (member_activitylog.member_id = member.id)
		";
		$the_sql .= ($this->members == 'all' ? '' : $db->Prepare(" WHERE join.status = ?", array($this->members)) );


		//query the data
		if(!empty($the_sql))
			$this->export = $db->FetchAll($the_sql);
		else
		{
			$this->error = "No fields selected.";
		}
		unset($sql, $the_sql);



		//output formatting
		switch($_POST['outputformat'])
		{
			case 'JSON':
				require_once "Zend/Json.php";
				try
				{
					$json = Zend_Json::encode($this->export);
					$this->export = Zend_Json::prettyPrint($json, array("indent" => " "));
				}
				catch(Exception $e)
				{
					$this->export = "Error creating Json data: ".$e->getMessage();
				}
			break;
			case 'XML':
				$this->export = String::toXml($this->export, 'members', 'member_');
			break;
			case 'CSV':
			default:
				$this->CSVformat();
			break;
		}

		//nothing to show?
		if(empty($this->export))
			$this->error = "No results found.";

		//output to the screen or force download?
		if('yes' != $this->onscreen && !empty($this->export))
		{
			//force download instead of onscreen display

			// Set headers
			header("Content-Description: File Transfer");

			switch($_POST['outputformat'])
			{
				case 'XML':
					header("Content-Type: text/xml");
					$ending = 'xml';
				break;
				case 'JSON':
					header("Content-Type: text/json");
					$ending = 'json';
				break;
				case 'CSV':
				default:
					header("Content-Type: text/csv");
					$ending = 'csv';
				break;
			}
			header('Content-Disposition: attachment; filename=export.'.$ending);
			header("Content-Transfer-Encoding: binary");


			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');

			header('Content-Length: ' . strlen($this->export));

			@ob_clean();
			@flush();

			echo $this->export;

			//no output
			$this->export = null;

			exit;
		}
		elseif(empty($this->export))
		{
			$this->error = "No results found.";
		}

	} //querydata

/**
* ShowIndex
*
* @access public
*/
	private function CSVformat()
	{
		if(empty($this->export))
			return;

		$keys = '';
		$ex = '';

		//header row
		foreach(array_keys($this->export[0]) as $k)
		{
			$keys .= $k. $this->separator;
		}
		$keys = trim($keys, $this->separator) .PHP_EOL;

		//data rows
		foreach($this->export as $d)
		{
			foreach($d as $key => $value)
			{
				$ex .= $value . $this->separator;
			}
			$ex = trim($ex, $this->separator).PHP_EOL;
		}
		$this->export = $keys . $ex;

	} //CSVformat

/**
* ShowIndex
*
* @access public
*/
	public function ShowIndex()
	{
		//only allow administrator access
		Authentification::CheckPermission('administrator');

		//get translation
		$translate = Zend_Registry::get('translate');

		self::$token = Session::GetToken();

		if(!empty($_POST))
		{
			try {
				if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] != self::$token)
					throw new Exception($translate->_('Security Token mismatch'));

				/***activate members***/
				if (isset($_POST['submit']))
					$this->querydata();
			}
			catch (Exception $e) {
					echo $translate->_('Error').': ',  $e->getMessage(), "<br>".PHP_EOL;
			}
		}

		$this->index();
	}

} //class
