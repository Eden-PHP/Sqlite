<?php //-->
/**
 * This file is part of the Eden PHP Library.
 * (c) 2014-2016 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */
 
class EdenSqliteTestSqliteSearchTest extends PHPUnit_Framework_TestCase
{
	public static $database;
	
	public function setUp() {
		date_default_timezone_set('GMT');
		self::$database = eden('sqlite', realpath(__DIR__.'/assets').'/unit.db');
	}
	
	/* FACTORY METHODS */
    public function testSearch() 
    {
		self::$database->model(array(
			'post_slug'			=> 'unit-test-1',
			'post_title' 		=> 'Unit Test 1',
			'post_detail' 		=> 'Unit Test Detail 1',
			'post_published' 	=> date('Y-m-d'),
			'post_created' 		=> date('Y-m-d H:i:s'),
			'post_updated' 		=> date('Y-m-d H:i:s')))
			->save('unit_post');
		
		self::$database->model(array(
			'post_slug'			=> 'unit-test-2',
			'post_title' 		=> 'Unit Test 2',
			'post_detail' 		=> 'Unit Test Detail 2',
			'post_published' 	=> date('Y-m-d'),
			'post_created' 		=> date('Y-m-d H:i:s'),
			'post_updated' 		=> date('Y-m-d H:i:s')))
			->save('unit_post');
		
		self::$database->model(array(
			'post_slug'			=> 'unit-test-3',
			'post_title' 		=> 'Unit Test 3',
			'post_detail' 		=> 'Unit Test Detail 3',
			'post_published' 	=> date('Y-m-d'),
			'post_created' 		=> date('Y-m-d H:i:s'),
			'post_updated' 		=> date('Y-m-d H:i:s')))
			->save('unit_post');
			
		$collection = self::$database
			->search('unit_post')
			->filterByPostActive(1)
			->sortByPostId('DESC')
			->getCollection()
			->setPostTitle('Unit Test X')
			->save();
			
		$this->assertSame('Unit Test X', $collection[0]['post_title']);
		$this->assertSame('Unit Test X', $collection[1]['post_title']);
		$this->assertSame('Unit Test X', $collection[2]['post_title']);
		
		$row = self::$database->getRow('unit_post', 'post_title', 'Unit Test X');
		
		$this->assertTrue(!empty($row));
    }
}