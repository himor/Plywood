<?php

namespace Plywood\Plywood;

class Config
{
    public static function get()
    {
        return [
            'db'      => [
                'hostname' => 'localhost',
                'user'     => 'plywood',
                'password' => 'plwd1234',
                'name'     => 'testing',
            ],
            'routing' => [
                'index' => ["pattern" => "/", 'controller' => "Home"],
                'ajax'  => ["pattern" => "/ajax/(?'function_name'[\w\-\d\%\_]+)", 'controller' => "Ajax"],
                'admin' => ["pattern" => "/admin/(?'function_name'[\w\-\d\%\_]+)((/(?'id'\d+))|(/(?'action_name'[\w\-\d\%\_]+)/(?'param_id'\d+))|)", 'controller' => "Admin"],
            ],
        ];
    }
}

/**
 CREATE USER 'plywood'@'localhost' IDENTIFIED BY 'plwd1234';
 CREATE DATABASE testing;
 USE testing;
 GRANT ALL PRIVILEGES ON testing.* TO 'plywood'@'localhost';
 CREATE TABLE IF NOT EXISTS `user` (
id BIGINT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(50),
age INT);
 INSERT INTO user VALUES(1,"Alex",33);
 */