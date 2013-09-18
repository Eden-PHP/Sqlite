<?php //-->
/*
 * This file is part of the Mysql package of the Eden PHP Library.
 * (c) 2012-2013 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */
 
require_once __DIR__.'/../../Core/library/Eden/Core/Loader.php';
Eden\Core\Loader::i()
	->addRoot(true, 'Eden\\Core')
	->addRoot(realpath(__DIR__.'/../../Utility/library')	, 'Eden\\Utility')
	->addRoot(realpath(__DIR__.'/../../Sql/library')		, 'Eden\\Sql')
	->addRoot(realpath(__DIR__.'/../../Sqlite/library')		, 'Eden\\Sqlite')
	->register()
	->load('Controller');