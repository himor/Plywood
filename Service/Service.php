<?php

class Service
{
    use LoggerTrait;

    protected static $instances = [];

    public static function get($name)
    {
        if (!$name) {
            throw new Exception('You must specify a name');
        }

        $name = ucfirst($name) . 'Service';

        if (isset(self::$instances[$name])) {
            return self::$instances[$name];
        }

        if (!file_exists(ROOT_DIR . 'Service/' . $name . '.php')) {
            throw new Exception('Class not found ' . $name);
        }

        if (!class_exists($name)) {
            include_once ROOT_DIR . 'Service/' . $name . '.php';
        }

        self::$instances[$name] = new $name();

        return self::$instances[$name];
    }
}