<?php

namespace Plywood\Entity;

/**
 * Base class for Entity
 */
class Entity
{
    use \Plywood\Plywood\LoggerTrait;

    /**
     * Returns a new entity
     *
     * @param       $name
     * @param array $params
     *
     * @return mixed
     * @throws \Exception
     */
    public static function make($name, $params = [])
    {
        if (!$name) {
            throw new \Exception('You must specify a name');
        }

        $name = "\\" . __NAMESPACE__ . "\\" . ucfirst($name) . 'Entity';

        return new $name($params);
    }

    /**
     * Creates new entity and its properties
     *
     * @param array $params
     */
    public function __construct($params = [])
    {
        foreach ($params as $key => $value) {
            if (substr($key, 0, 1) != '_') {
                $this->$key = $value;
            }
        }
    }
}