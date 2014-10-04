<?php

class HomeController extends BaseController
{
	protected static $log = 'home.log';

	public function indexAction($params)
	{
	 	$user1 = Entity::make('user', [
			'name' => 'Jake',
			'age'  => rand(10, 99)
		]);

		$user2 = Entity::load('user', 1);

		$user1->id = Repository::get('user')->persist($user1);
		$user2->id = Repository::get('user')->persist($user2);

		return [
			'layout' => 'index',
			'user1'   => $user1,
			'user2'   => $user2
		];
	}

}