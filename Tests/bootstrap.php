<?php //-->
/*
 * This file is part of the Mysql package of the Eden PHP Library.
 * (c) 2012-2013 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */
 
require_once __DIR__.'/../../Core/Loader.php';
Eden\Core\Loader::i()
	->addRoot(true, 'Eden\\Core')
	->addRoot(realpath(__DIR__.'/../..'), 'Eden\\Utility')
	->addRoot(realpath(__DIR__.'/../..'), 'Eden\\Sql')
	->addRoot(realpath(__DIR__.'/../..'), 'Eden\\Sqlite')
	->register()
	->load('Controller');