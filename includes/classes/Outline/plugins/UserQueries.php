<?php

/***************************************************************
 * File: UserQueries.php
 * Version: 1.0
 *
 * Custom tags for the template engine
 *
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 ***************************************************************/

//you can specify your own template tags in this file.
//take a look at /includes/classes/Outline/system.php for possible syntax

//example:
//{{your_custom_tag myvar=123}}


class UserQueries extends OutlinePlugin {

	public static function register (&$compiler)
	{

		$compiler->registerTag('your_custom_tag', 'custom_tag_func');

	}

	public function custom_tag_func($args) {

		$this->compiler->code($args.';');

	}

} //class