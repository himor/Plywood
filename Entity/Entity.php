<?php

class Entity
{
	use LoggerTrait;

	/**
	 * Returns an entity
	 *
	 * @param unknown $name
	 * @param unknown $params
	 */
	public static function make($name, $params = [])
	{
		if (!$name) {
			throw new Exception('You must specify a name');
		}

		$name = ucfirst($name) . 'Entity';

		if (!file_exists(ROOT_DIR . 'Entity/' . $name . '.php')) {
			throw new Exception('Class not found ' . $name);
		}

		if (!class_exists($name)) {
			include_once ROOT_DIR . 'Entity/' . $name . '.php';
		}

		return new $name($params);
	}

	/**
	 * Loads and returns an entity
	 *
	 * @param unknown $name
	 * @param unknown $params
	 */
	public static function load($name, $id)
	{
		if (!$name) {
			throw new Exception('You must specify a name');
		}

		$id = (int)$id;

		if (!$id) {
			throw new Exception('You must specify an id');
		}

		$orig = $name;
		$name = ucfirst($name) . 'Entity';

		if (!file_exists(ROOT_DIR . 'Entity/' . $name . '.php')) {
			throw new Exception('Class not found ' . $name);
		}

		if (!class_exists($name)) {
			include_once ROOT_DIR . 'Entity/' . $name . '.php';
		}

		$params = Repository::get($orig)->load(new $name(), $id);

		return new $name($params);
	}

	public function __construct($params = [])
	{
		foreach ($params as $key => $value)
		{
			$this->$key = $value;
		}
	}
}