<?php

class Config
{
    public static function get()
    {
        return [
            'db'      => [
                'hostname' => 'localhost',
                'user'     => 'root',
                'password' => 'root',
                'name'     => 'duser041_testing',
            ],
            'routing' => [
                'index' => ["pattern" => "/", 'controller' => "Home"],
                'ajax'  => ["pattern" => "/ajax/(?'function_name'[\w\-\d\%\_]+)", 'controller' => "Ajax"],
                'admin' => ["pattern" => "/admin/(?'function_name'[\w\-\d\%\_]+)((/(?'id'\d+))|(/(?'action_name'[\w\-\d\%\_]+)/(?'param_id'\d+))|)", 'controller' => "Admin"],
            ],
        ];
    }
}