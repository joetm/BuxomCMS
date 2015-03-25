<?php

/* **************************************************************
 *  File: Mailqueue.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Mailqueue Class
*
*/
class Mailqueue {

	private $type = 'text';
	private $members = '';
	private $subject = '';
	private $body = '';
	private $from = '';
	private $to = '';
	private $pp = 2; //change this later!!!
	private $startat = 0;
	private $where = '';
	private $max;
	private $order_by = " ORDER BY m.id ASC ";

	const STARTAT = 'startat';
	const PP = 'pp';

/**
* Prepare Queue
*
* @access	public
*/
	public function Prepare() {

		$translate = Zend_Registry::get('translate');

		$_config['pp'] = Config::GetDBOptions('pp');

		/***database connect***/
		$db = DB::getInstance();

		if(isset($_GET[self::STARTAT])) $this->startat = intval($_GET[self::STARTAT]);
		else $this->startat = 0;

		if ($_GET[self::PP])
			$this->pp = intval($_GET[self::PP]);
		else
		{
			if ($_config['pp'])
				$this->pp = intval($_config['pp']);
			else
				//fallback
				$this->pp = 50;
		}

		//get mail data
		$temp = $db->FetchAll("SELECT `value`,`key`
					FROM `bx_temp`
					WHERE `key` = 'subject'
					OR `key`='mailbody'
					OR `key`='type'
					OR `key`='members'
					OR `key`='max'");

		//reduce result
		for($i=0, $s=count($temp); $i<$s; $i++)
		{
			$temp[$temp[$i]['key']] = $temp[$i]['value'];
			unset( $temp[$i] );
		}

			if($temp)
			{
				$this->type = 'text';
				if($temp['type'] == 'html') $this->type = "html";

				switch ($temp['members'])
				{
					case 'all':
						$this->members = 'all';
						break;
					case 'active':
						$this->members = 'active';
						break;
					case 'inactive':
						$this->members = 'inactive';
						break;
				}
				if(empty($this->members)) die($translate->_("Error").": ".$translate->_('Missing Argument').".");

				$this->body = stripslashes($temp['mailbody']);

				$this->subject = stripslashes($temp['subject']);
			}

		unset($temp);

	} //Prepare

/**
* Create Queue
*
* @access	public
*/
	public function Create_Queue()
	{
		$translate = Zend_Registry::get('translate');

		$this->getStartValues();

		/***database connect***/
		$db = DB::getInstance();

		$this->StatusMsg($translate->_("Initializing Mailqueue"));
		$this->StatusMsg($translate->_("Creating Mailqueue"),true);

		$this->max = $db->Column("SELECT `value` FROM `bx_temp` WHERE `key` = 'max'");

		//write data to mail queue

		$this->StatusMsg("<small>".$this->startat."/".$this->max."</small>", $nobr = true);

		if (($this->startat + $this->pp) <= $this->max)
				$this->limit = " LIMIT ".$this->startat.",".$this->pp;
		else
		{
				$residual = $this->max - $this->startat;
				$this->limit = " LIMIT ".$this->startat.",".$residual;
		}

		//we get a batch of emails
			$sql = "SELECT e.email FROM `bx_member_email` AS `e`
					JOIN `bx_member` AS `m` USING (`id`)"
					. $this->where
					. $this->order_by
					. $this->limit;
die($sql);
			$data = $db->FetchAll();
			if(!$data) die($translate->_("A: Database query failed"));

		//and write them to the mailqueue
			$datastr = '';
			//construct insert string
			foreach($data as $d)
			{
				$datastr .= $db->Prepare('(?,?,?),', array(NULL, $d['email'], $this->subject));
			}
			$datastr = trim($datastr, ',');

			$status = $db->Update("INSERT INTO `bx_mailqueue` VALUES ".$datastr); //(id,to,subject)
			if(!$status) die($translate->_("B: Database query failed"));

			//increment startat
			$increment = 0;
			$up = $this->startat + $this->pp;
			if($up <= $this->max)
			{
				$increment = $this->pp;
			}
			else
			{
				$increment = $this->max - $this->startat;
			}

			$this->startat = $this->startat + $increment;



//sleep(1);




		if($this->max - $this->startat > 0) //$increment != 0
			$this->Redirect('create_queue');
		else
			//reset running variables
			$this->startat = 0;
			$this->pp = Config::GetDBOptions('email_pp');
			$this->Redirect('run');

	} //Create_Queue

/**
* Init
*
* @access	public
*/
	public function Init()
	{
		$translate = Zend_Registry::get('translate');

		$this->StatusMsg($translate->_("Initializing Mailqueue"));
	}

/**
* Run
*
* @access	public
*/
	public function Run()
	{
		$translate = Zend_Registry::get('translate');

		$_config = Config::GetDBOptions(array('mailmethod','email','emailname','smtpport','smtppass','smtpuser','smtphost'));

//		$_config['mailmethod'] = Config::Get('mailmethod');
//		$_config['email'] = Config::Get('email');
//		$_config['smtp'] = Config::Get('smtp');

		$this->Prepare();

		$this->getStartValues();

		/***database connect***/
		$db = DB::getInstance();

		$this->StatusMsg($translate->_("Initializing Mailqueue"));
//		if($this->startat == 0)
//		{
//			$this->StatusMsg($translate->_("Creating Mailqueue"), $nobr = true);
//			$this->StatusMsg("<small>".$this->max."/".$this->max."</small>");
//		}
//		else
			$this->StatusMsg($translate->_("Creating Mailqueue"));
		$this->StatusMsg($translate->_("Processing Mailqueue"),true);


		$temp = $db->FetchAll("SELECT * FROM `bx_temp`");

		$t = array();
		//process array
		for($i = 0, $s = count($temp); $i<$s; $i++)
		{
			$t[ $temp[$i]['key'] ] = $temp[$i]['value'];
		}
		unset($temp);

		$this->max = $t['max'];
		$this->members = $t['members'];
		$this->type = $t['type'];
		$this->subject = $t['subject'];
		$this->mailbody = $t['mailbody'];
		unset($t);

		$this->StatusMsg("<small>".$this->startat."/".$this->max."</small>");


		$this->where = "";
		//empty where -> get all
		switch($this->members)
		{
			case 'inactive':
				$this->where = " WHERE m.status = 'inactive'";
				break;
			case 'chargeback':
				$this->where = " WHERE m.status = 'chargeback'";
				break;
			case 'active':
			default:
				$this->where = " WHERE m.status = 'active'";
				break;
		}


		$this->limit = " LIMIT ".$this->startat.",".$this->pp;
		$this->deletelimit = " LIMIT ".$this->pp;

		//we get a batch of emails
			$data = $db->FetchAll("SELECT e.email FROM `bx_member` AS `m`
						JOIN `bx_member_email` AS `e` USING (`id`)"
						. $this->where . $this->order_by . $this->limit);
			if(!$data) die($translate->_("C: Database query failed"));

		require_once "Zend/Mail.php";
		$mail = new Zend_Mail();


		if ($_config['mailmethod'] == 'smtp') //use smtp
		{
			require_once "Zend/Mail/Transport/Smtp.php";

			$details = array('auth' => 'login',
                			'username' => $_config['smtpuser'],
                			'password' => $_config['smtppass'],
	              			'port' => $_config['smtpport']
					);
			$transport = new Zend_Mail_Transport_Smtp($_config['smtphost'], $details);
			Zend_Mail::setDefaultTransport($transport);
		}

		@ini_set('sendmail_from', $_config['email']);

		Zend_Mail::setDefaultFrom($_config['email'], $_config['emailname']);
		//Zend_Mail::setDefaultReplyTo('replyto@example.com','Jane Doe');

		$mail->addHeader('X-MailGenerator', 'BuxomCMS');

//		$mail->setFrom($_config['email'], $_config['emailname']);
		$mail->setSubject($this->subject);

		if($this->type === 'html')
			$mail->setBodyHtml($this->mailbody);
		else
			$mail->setBodyText($this->mailbody);


echo "debug: ";
echo "From: ".$_config['email'].", Subject: ".$this->subject.", Mailbody: ".$this->mailbody . "<br><br>";

		ob_start();
		$okay = strtolower($translate->_("Okay"));
		$fail = strtolower($translate->_("Error"));

		try
		{
			foreach($data as $d){


				//send the email


sleep(1);

				$mail->addTo($d['email'], $_config['emailname']);

				if ($_config['mailmethod'] == 'smtp') //use smtp
				{
					echo ":smtp:";
					$mail->send($transport);
				}
				else
				{
					echo ":sendmail:";
					$mail->send();
				}


//was ist mit status==error??? ($fail)?
				$status = $okay;






				echo str_pad($d['email'] . "..." . $status . "<br>", 1024, ' ', STR_PAD_RIGHT) . PHP_EOL;

				Tools::flush_buffers();
			} //foreach
		}
		catch (Exception $e)
		{
			echo "<span style='color:#990000'>".$e->getMessage()."</span>";
		}




		//delete the entries from mailqueue
		$db->Update("DELETE FROM `bx_mailqueue` ORDER BY `id` ASC " . $this->deletelimit);





		//increment startat
		$increment = 0;
		$up = $this->startat + $this->pp;
		if($up <= $this->max)
		{
			$increment = $this->pp;
		}
		else
		{
			$increment = $this->max - $this->startat;
			$this->pp = $increment;
		}

		$this->startat = $this->startat + $increment;

		if($this->max - $this->startat > 0) //$increment != 0
			$this->Redirect('run');
		else
			$this->Redirect('final');

	} //Run

/**
* Success
*
* @access	public
*/
	public function Success()
	{
		$translate = Zend_Registry::get('translate');

		//on successful finish:
		$this->Cleanup();

//		$this->StatusMsg("Initializing Mailqueue...");
//		$this->StatusMsg("Creating Mailqueue...");
//		$this->StatusMsg("Processing Mailqueue...");

		$this->StatusMsg($translate->_("Success").'!');


		//unwind and show success message

		sleep(5);







		echo '<script type="text/javascript">parent.Unwind("success");</script>';

	} //Success

/**
* Get Start Values
*
* @access	public
*/
	private function getStartValues()
	{
				if($_GET[self::PP])
					$this->pp = intval($_GET[self::PP]);
				else
					$this->pp = 50;

				if($_GET[self::STARTAT])
					$this->startat = intval($_GET[self::STARTAT]);
				else
					$this->startat = 0;

	} //getStartValues

/**
* Redirect
*
* @access	public
* @param	string	$argument
*/
	public function Redirect($argument = '')
	{
		switch ($argument)
		{
			case 'create_queue':
			default:
//				if($this->startat != 0)
//				echo " <a href='mailprocess.php?do=create_queue&".self::STARTAT."=".$this->startat."&".self::PP."=".$this->pp."'><small>Redirect (".$this->startat."/".$this->max.")</small></a>";

				echo "<script type='text/javascript'>window.location='mailprocess.php?do=create_queue&".self::STARTAT."=".$this->startat."&".self::PP."=".$this->pp."';</script>";
				break;

			case 'run':
//				if($this->startat != 0)
//				echo " <a href='mailprocess.php?do=run&".self::STARTAT."=".$this->startat."&".self::PP."=".$this->pp."'><small>Redirect (".$this->startat."/".$this->max.")</small></a>";

				echo "<script type='text/javascript'>window.location='mailprocess.php?do=run&".self::STARTAT."=".$this->startat."&".self::PP."=".$this->pp."';</script>";
				break;

			case 'final':

				echo "<script type='text/javascript'>window.location='mailprocess.php?do=success';</script>";
				break;
		}

	} //Redirect

/**
* Cleanup
*
* @access	public
*/
	function Cleanup(){
		//clear the temp database table

		/***database connect***/
		$db = DB::getInstance();

		$db->Update("TRUNCATE TABLE `bx_temp`");

//		$db->Update("TRUNCATE TABLE `bx_mailqueue`");
//???
		$db->Update("OPTIMIZE TABLE `bx_mailqueue`");

	} //Cleanup

/**
* Status Message
*
* @access	public
* @param	string	$msg
* @param	bool	$nobr
* @return	string
*/
	private function StatusMsg($msg, $nobr = false)
	{
		echo $msg . ($nobr ? "":"<br>");

	} //StatusMsg

/**
* set From
*
* @access	public
* @param	string	$f
*/
	public function setFrom($f)
	{
		$this->from = Input::clean($f, 'NOHTML');

	} //setFrom

} //class