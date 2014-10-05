<?php

class DbManager extends Manager {
	use DbTrait;

	public function getOne($query, $params = array()) {
		$resource = $this->_prepare($query, $params);

		return $this->_getOne($resource);
	}

	public function getMany($query, $params = array()) {
		$resource = $this->_prepare($query, $params);

		return $this->_getMany($resource);
	}

	public function getOneSafe($query) {
		$resource = $this->_query($query);

		return $this->_getOne($resource);
	}

	public function getManySafe($query) {
		$resource = $this->_query($query);

		return $this->_getMany($resource);
	}

	/**
	 * For soft queries (where sql injection is not possible)
	 *
	 * @param $query
	 *
	 * @return bool
	 */
	public function query($query) {
		return $this->_query($query);
	}

	/**
	 * We don't care about the return, but we need a prepared statement
	 *
	 * @param       $query
	 * @param array $params
	 *
	 * @return bool|unknown
	 */
	public function exec($query, $params = array()) {
		return $this->_prepare($query, $params);
	}

	public function short($text, $count = 100) {
		if (strlen($text) <= $count) return $text;
		$text = explode(' ', strip_tags($text, '<b><em><i><strong><a>'));
		$out  = '';
		foreach ($text as $t) {
			if (strlen(trim($out)) && strlen($out . $t) > $count) return trim($out, '. ') . '...';
			$out .= ' ' . $t;
		}

		return trim($out, ' ') . '.';
	}

}