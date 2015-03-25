<?php

/* **************************************************************
 *  File: DB_Exception.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Database Exception Class
*
* @deprecated
*
*/
class DB_Exception extends Exception
{

    private $extras = array();

/**
* Constructor
*
* @access	public
*/
    public function __construct()
    {
        $arguments = func_get_args();
        $message = array_shift($arguments);

        $this->extras = $arguments;
        parent::__construct($message);
    }

/**
* Get Extras
*
* @access	public
* @return	string
*/
    public function getExtras()
    {
        if( count($this->extras) > 0 )
        {
            return PHP_EOL . join("\n", $this->extras);
        }
        else
        {
            return '';
        }
    }

/**
* Get Trace As HTML
*
* @access	public
* @return	string
*/
	public function getTraceAsHtml()
	{
		return nl2br(strip_tags($this->getTraceAsString()));
	}

} //class DB_Exception
