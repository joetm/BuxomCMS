<?php

/* **************************************************************
 *  File: Step2Process.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Step 2 Image Process
*
*/
class Step2Process {

	private $folder = '';

	public static $slug = '';

	public static $type = '';

/**
* Constructor
*
* @access	public
* @param	string	$folder
* @param	string	$slug
*/
	public function __construct($folder, $slug, $type = null)
	{
		if(is_null($type)) $type = 'set';

		self::$type = $type;

		$this->folder = $folder;

		self::$slug = $slug;

	} //construct

/**
* relayErrorMsg
*
* @access	private
* @param	Error Object	$e
*/
	private function relayErrorMsg($e)
	{
		//get translation
		$translate = Zend_Registry::get('translate');

		$error = $e->getMessage();
		echo $translate->_("Error").': '.  $error . PHP_EOL;

		//relay to parent frame
		echo '<script type="text/javascript">parent.ErrorMsg("'.$error.'");</script>';
	}

/**
* Run
*
* @access	public
*/
	public function run()
	{
		ob_start();

//		if ($_POST['securitytoken'] == Session::GetToken()){

			try{
				if(self::$type == 'set')
				{
					$imgp = new ImageProcessor();
					$imgp->setFolder($this->folder);
					$imgp->process();
				}
				elseif(self::$type == 'video')
				{
					$vp = new VideoProcessor();
					$vp->setFolder($this->folder);
					$vp->process();
				}
			}
			catch (IMGProcessorException $e){
				$this->relayErrorMsg($e);
			}
			catch (VIDProcessorException $e){
				$this->relayErrorMsg($e);
			}
			catch (Exception $e){
				$this->relayErrorMsg($e);
			}

//		} //security token
//		else
//		{
//			throw new Exception($translate->_('Security token mismatch'));
//		}

	} //run

} //class