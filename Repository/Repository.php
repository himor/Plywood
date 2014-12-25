<?php

/**
 * Class Repository - base class for repository
 */
class Repository {
	use LoggerTrait;

	protected static $instances = [];

	/**
	 * Returns an instance of repository
	 *
	 * @param $name
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public static function get($name) {
		if (!$name) {
			throw new Exception('You must specify a name');
		}

		$name = ucfirst($name) . 'Repository';

		if (isset(self::$instances[$name])) {
			return self::$instances[$name];
		}

		if (!file_exists(ROOT_DIR . 'Repository/' . $name . '.php')) {
			throw new Exception('Class not found ' . $name);
		}

		if (!class_exists($name)) {
			include_once ROOT_DIR . 'Repository/' . $name . '.php';
		}

		self::$instances[$name] = new $name();

		return self::$instances[$name];
	}

	/**
	 * Persistence
	 * All properties starting with _ will be ignored!
	 *
	 * @param Entity $entity
	 */
	public function persist(Entity $entity) {
		$table = $entity->_table;

		$vars = get_object_vars($entity);
		$a    = [];
		$b    = [];
		$c    = [];
		foreach ($vars as $key => $value) {
			if (substr($key, 0, 1) == '_') continue;
			$a[]           = $key;
			$b[':' . $key] = $value;
			$c[]           = '`' . $key . '` = :' . $key;
		}

		$manager = Manager::get('db');

		if (!isset($entity->id)) {
			$manager->exec("
				INSERT INTO " . $table . "
				(`" . implode('`,`', $a) . "`)
				VALUES (" . implode(',', array_keys($b)) . ")"
				, $b);

			return $manager->lastInsertedId();
		} else {
			$manager->exec("
					UPDATE " . $table . "
					SET " . implode(',', $c) . "
					WHERE `id` = :id", $b);

			return $entity->id;
		}
	}

	/**
	 * Loads and returns an entity
	 *
	 * @param $name
	 * @param $id
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public static function load($name, $id) {
		if (!$name) {
			throw new Exception('You must specify a name');
		}

		$id = (int)$id;

		if (!$id) {
			throw new Exception('You must specify an id');
		}

		$name = ucfirst($name) . 'Entity';

		if (!file_exists(ROOT_DIR . 'Entity/' . $name . '.php')) {
			throw new Exception('Class not found ' . $name);
		}

		if (!class_exists($name)) {
			include_once ROOT_DIR . 'Entity/' . $name . '.php';
		}

		$tempEntity = new $name();
		$manager    = Manager::get('db');
		$params     = $manager->getOneSafe("
				SELECT * FROM " . $tempEntity->_table . " WHERE id = " . $id . "
				");
				
		if (empty($params)) {
			return null;
		}

		return new $name($params);
	}

}
