<?php //-->
/*
 * This file is part of the Utility package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */
 
class Eden_Sqlite_Tests_Sqlite_CollectionTest extends \PHPUnit_Framework_TestCase
{
	public static $database;
	
	public function setUp() {
		date_default_timezone_set('GMT');
		self::$database = eden('sqlite', realpath(__DIR__.'/../assets').'/unit.db');
	}
	
	/* FACTORY METHODS */
    public function testAlter() 
    {
		
    }
}