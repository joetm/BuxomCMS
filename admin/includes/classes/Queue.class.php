<?php

/* **************************************************************
 *  File: Queue.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Queue Class
*
*/
class Queue {

	public $queue = array();


	public function push($object) {
		array_push($this->queue,$object);
	}

	public function pop() {
		if ($this->size() != 0) {
			return array_shift($this->queue);
		}
		else {
			return false;
		}
	}

	public function element($no) {
		return $this->queue[$no];
	}

	public function firstElement() {
		return reset($this->queue);
	}

	public function nextElement() {
		return next($this->queue);
	}

	public function prevElement() {
		return prev($this->queue);
	}

	public function replaceElement($element) {
		$key = $this->currentKey();
		$this->queue[$key] = $element;
	}

	public function removeElement() {
		$key = $this->currentKey();
		unset($this->queue[$key]);
	}

	public function currentElement() {
		return current($this->queue);
	}

	public function currentKey() {
		return key($this->queue);
	}

	public function size() {
		return sizeof($this->queue);
	}

} //Queue class