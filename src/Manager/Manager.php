<?php

namespace Plywood\Manager;

class Manager
{
    use \Plywood\Plywood\LoggerTrait;

    protected static $instances = [];

    public static function get($name)
    {
        if (!$name) {
            throw new \Exception('You must specify a name');
        }

        $name = "\\" . __NAMESPACE__ . "\\" . ucfirst($name) . 'Manager';

        if (isset(self::$instances[$name])) {
            return self::$instances[$name];
        }

        self::$instances[$name] = new $name();

        return self::$instances[$name];
    }

}