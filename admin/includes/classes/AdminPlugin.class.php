<?php

/* **************************************************************
 *  File: AdminPlugin.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* AdminPlugin Class
*
*/
class AdminPlugin {

/**
* Get Plugins
*
* @access	public
* @return	array
*/
	public static function GetPlugins()
	{
		//permission check
		//editors do not have access to options
		Authentification::CheckPermission('administrator');


		$path = Path::Get('path:admin').DIR_SEP.'plugins';
		if(!is_dir($path) || !is_readable($path))
			return false;

		$plugins = array();
		$matches = array();
		$dir = opendir($path);
		while( $entry = readdir( $dir ))
		{
				$pluginpath = $path . DIR_SEP . $entry;

				if(is_dir($pluginpath)) continue;

				if(preg_match('~(.*).plugin.php$~', $entry, $matches))
				{
					require_once $pluginpath;

					$class = $matches[1]."_plugin";

					$class = Input::clean($class,'FILENAME');
					$matches[1] = Input::clean($matches[1],'FILENAME');

					$plugins[] = array(
									'name'=>$matches[1],
									'classname'=>$class,
									'path'=>$pluginpath,
								);
				}
		}
		closedir($dir);

		return $plugins;

	} //GetAdminPlugins

} //class