<?php

namespace Plywood\Plywood;

trait DbTrait
{
    /**
     * @var \PDO
     */
    private $handler = null;

    /**
     * Uses PDO to connect
     *
     * @param null $dbName
     *
     * @return bool|\PDO
     */
    private function _connect($dbName = null)
    {
        if ($dbName == null) {
            $dbName = Core::$config['db']['name'];
        }

        $dsn = 'mysql:dbname=' . $dbName . ';host=' . Core::$config['db']['hostname'] . ';charset=utf8';
        try {
            $this->handler = new \PDO($dsn, Core::$config['db']['user'], Core::$config['db']['password']);
        } catch (\PDOException $e) {
            $this->log('Connection failed: ' . $e->getMessage(), 'db_trait.log');
            $this->handler = null;

            return false;
        }

        return $this->handler;
    }

    public function _resetDb($dbName = null)
    {
        return $this->_connect($dbName);
    }

    /**
     * Returns prepared PDO statement
     *
     * @param       $query
     * @param array $params
     *
     * @return bool
     */
    public function _prepare($query, $params = array())
    {
        if ($this->handler == null)
            if (!$this->_connect())
                return false;
        $sth   = $this->handler->prepare($query);
        $error = $this->handler->errorInfo();
        if (isset($error[0]) && (int)$error[0]) {
            $this->log('PDO::prepare: ' . implode(' ', $error) . "\nQuery:" . $query . "\nParams:" . implode(' ', $params), 'db_trait.log');
        }
        $sth->execute($params);
        $error = $sth->errorInfo();
        if (isset($error[0]) && (int)$error[0]) {
            $this->log('PDO::execute: ' . implode(' ', $error) . "\nQuery:" . $query . "\nParams:" . implode(' ', $params), 'db_trait.log');
        }

        return $sth;
    }

    /**
     * Executes an SQL statement in a single function call, returning the result set
     *
     * @param $query
     *
     * @return bool
     */
    public function _query($query)
    {
        if ($this->handler == null)
            if (!$this->_connect())
                return false;
        $sth   = $this->handler->query($query);
        $error = $this->handler->errorInfo();
        if (isset($error[0]) && (int)$error[0]) {
            $this->log('PDO::query: ' . implode(' ', $error) . "\nQuery:" . $query, 'db_trait.log');
        }

        return $sth;
    }

    /**
     * Execute an SQL statement and return the number of affected rows
     *
     * @param $query
     *
     * @return bool
     */
    public function _exec($query)
    {
        if ($this->handler == null)
            if (!$this->_connect())
                return false;

        return $this->handler->exec($query);
    }

    /**
     * @return mixed
     */
    public function lastInsertedId()
    {
        return $this->handler->lastInsertId();
    }

    private function orm_($resource, $forceMulti = false)
    {
        $array = $this->convert($resource);
        if (count($array) == 1 && !$forceMulti) {
            return $this->atoo($array[0]);
        } else {
            $obj = array();
            foreach ($array as $a)
                $obj[] = $this->atoo($a);

            return $obj;
        }
    }

    private function atoo($array)
    {
        $obj = new \stdClass();
        foreach ($array as $key => $value)
            if (!is_numeric($key))
                $obj->$key = $value;

        return $obj;
    }

    private function convert($resource)
    {
        $array = array();
        while ($r = $resource->fetch()) {
            $array[] = $r;
        }

        return $array;
    }

    public function _getOne($resource)
    {
        //return $this->orm_($resource, false);
        $many = $this->orm_($resource, true);
        if (!empty($many)) return $many[0];

        return array();
    }

    public function _getMany($resource)
    {
        return $this->orm_($resource, true);
    }

}