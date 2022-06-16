<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
        $router->withModule('Panel')->withPath('panel')
            ->addRoute('voucher/create', 'Voucher:create')
            ->addRoute('voucher/<id>/edit', 'Voucher:edit')
            ->addRoute('vouchers', 'Voucher:default');


        $router->withModule('Auth')->withPath('auth')
            ->addRoute('signin', 'Signin:default')
            ->addRoute('out', 'Signin:out')
            ->addRoute('signup', 'Signup:default');


        $router->addRoute('/', 'Base:Voucher:default');

		$router->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');


		return $router;
	}
}
