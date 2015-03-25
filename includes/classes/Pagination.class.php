<?php

/* **************************************************************
 *  File: Pagination.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Pagination Class
*
*/
class Pagination
{
	public $pageno;
	public $current;
	public $links = "";

	public $pagination = array();

/**
* Paginate
*
* @access	public
* @param	string	$query
* @param	integer	$perpage
* @param	string	$target
*/
	public function paginate($query, $perpage = "20", $target = "")
	{
		/***database connect***/
		$db = DB::getInstance();

		$_separator = "&amp;";
		if(!strstr($target,"?")) //check if $target contains "?"
		{
			$_separator = "?";
		}

		if (isset($_GET['page'])) {
			$this->pageno = intval($_GET['page']);
		} else {
			$this->pageno = 1;
		}

		$result = $db->query($query); // or trigger_error("SQL", E_USER_ERROR);
		$query_data = mysql_fetch_row($result);
		$numrows = $query_data[0];
		unset($result);
		unset($query);
		unset($query_data);

		//all results, per page, current page
		$arr = array();
		$arr = $this->calculate_pages($numrows, $perpage, $this->pageno);

		//construct pagination links

		if($this->pageno != 1){
			$this->links .= "<a href=\"$target".$_separator."page=$arr[previous]\">Previous</a>";
		}
		foreach ($arr['pages'] as $page) {
			if ($page != $arr['current']) {
				$l = " <a href=\"$target".$_separator."page=$page\">$page</a> ";
			}
			else{
				$l = $page;
			}
			$this->links .= $l;
		}
		//add next link (not on last page)
		if($this->pageno != $arr['last']){
			$this->links .= "<a href=\"$target".$_separator."page=$arr[next]\">Next</a>";
		}

		$arr['links'] = $this->links;

//		return $arr;
		$this->pagination = $arr;

	}

/**
* Calculate Pages
*
* @access	private
* @param	integer	$total_rows
* @param	integer	$rows_per_page
* @param	integer	$page_num
* @return	array
*/
	private function calculate_pages(&$total_rows, $rows_per_page, &$page_num)
	{
		$arr = array();
		// calculate last page
		$last_page = ceil($total_rows / $rows_per_page);
		// make sure we are within limits
		$page_num = (int) $page_num;
		if ($page_num < 1)
		{
		   $page_num = 1;
		}
		elseif ($page_num > $last_page)
		{
		   $page_num = $last_page;
		}
		if($page_num != 0){
			$upto = ($page_num - 1) * $rows_per_page;
		}
		else{
			$upto = 0;
		}
		$arr['limit'] = ' LIMIT '.$upto.',' .$rows_per_page;
		$arr['current'] = $page_num;
		$this->current = $page_num;
		if ($page_num == 1)
			$arr['previous'] = $page_num;
		else
			$arr['previous'] = $page_num - 1;
		if ($page_num == $last_page)
			$arr['next'] = $last_page;
		else
			$arr['next'] = $page_num + 1;
		$arr['last'] = $last_page;
		$arr['info'] = 'Page ('.$page_num.' of '.$last_page.')';
		$arr['pages'] = $this->get_surrounding_pages($page_num, $last_page, $arr['next']);

		return $arr;
	}

/**
* Get Surrounding Pages
*
* @access	private
* @param	integer	$page_num
* @param	integer	$last_page
* @param	integer	$next
* @return	array
*/
	private function get_surrounding_pages($page_num, $last_page, $next)
	{
		$arr = array();
		$show = 5; // how many boxes
		// at first
		if ($page_num == 1)
		{
			// case of 1 page only
			if ($next == $page_num) return array(1);
			for ($i = 0; $i < $show; $i++)
			{
				if ($i == $last_page) break;
				array_push($arr, $i + 1);
			}
			return $arr;
		}
		// at last
		if ($page_num == $last_page)
		{
			$start = $last_page - $show;
			if ($start < 1) $start = 0;
			for ($i = $start; $i < $last_page; $i++)
			{
				array_push($arr, $i + 1);
			}
			return $arr;
		}
		// at middle
		$start = $page_num - $show;
		if ($start < 1) $start = 0;
		for ($i = $start; $i < $page_num; $i++)
		{
			array_push($arr, $i + 1);
		}
		for ($i = ($page_num + 1); $i < ($page_num + $show); $i++)
		{
			if ($i == ($last_page + 1)) break;
			array_push($arr, $i);
		}
		return $arr;
	}

/**
* Assign Pagination to Template
*
* @access	public
* @param	object	$tpl
*/
	public function assign($tpl)
	{
		if ($this->current !=0 && $this->links != '1'){
			//current=0 => no updates to show
			//links=1 => only one page
			$tpl->assign("pagination", $this->pagination);
		}
	} //assign

} //class
