<?php

/**
 * Base class for Entity
 */
class Entity {
	use LoggerTrait;

	/**
	 * Returns a new entity
	 *
	 * @param       $name
	 * @param array $params
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public static function make($name, $params = []) {
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
	 * Creates new entity and its properties
	 *
	 * @param array $params
	 */
	public function __construct($params = []) {
		foreach ($params as $key => $value) {
			if (substr($key, 0, 1) != '_') {
				$this->$key = $value;
			}
		}
	}
}