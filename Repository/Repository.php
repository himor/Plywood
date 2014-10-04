<?php

class Repository
{
	use LoggerTrait;

	protected static $instances = [];

	public static function get($name)
	{
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
	public function persist(Entity $entity)
	{
		$table = $entity->_table;

		$vars = get_object_vars($entity);
		$a = [];
		$b = [];
		$c = [];
		foreach ($vars as $key => $value) {
			if (substr($key, 0, 1) == '_') continue;
			$a[] = $key;
			$b[':' . $key] = $value;
			$c[] = '`' . $key . '` = :' . $key;
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
	 * Load the entity
	 *
	 * @param unknown $id
	 */
	public function load($entity, $id)
	{
		$table = $entity->_table;

		$manager = Manager::get('db');
		return $manager->getOneSafe("
				SELECT * FROM " . $table . " WHERE id = " . $id . "
				");
	}
}