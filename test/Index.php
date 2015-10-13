<?php //-->
/**
 * This file is part of the Eden PHP Library.
 * (c) 2014-2016 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */
 
class EdenSqliteTestSqliteFactoryTest extends PHPUnit_Framework_TestCase
{
	public static $database;
	
	public function setUp() {
		date_default_timezone_set('GMT');
		self::$database = eden('sqlite', realpath(__DIR__.'/assets').'/unit.db');
		
		/*self::$database->query('CREATE TABLE "unit_post" (
			"post_id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
			"post_slug" VARCHAR NOT NULL UNIQUE, "post_title" VARCHAR, 
			"post_detail" TEXT, "post_active" SMALLINT NOT NULL DEFAULT 1, 
			"post_type" VARCHAR NOT NULL DEFAULT post, 
			"post_flag" SMALLINT NOT NULL DEFAULT 0, 
			"post_visibility" VARCHAR NOT NULL DEFAULT public, 
			"post_status" VARCHAR NOT NULL DEFAULT published, 
			"post_published" DATE, 
			"post_created" DATETIME NOT NULL, 
			"post_updated" DATETIME NOT NULL);');*/
		
		/* Schema 
			CREATE TABLE "unit_post" (
			"post_id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
			"post_slug" VARCHAR NOT NULL UNIQUE, "post_title" VARCHAR, 
			"post_detail" TEXT, "post_active" SMALLINT NOT NULL DEFAULT 1, 
			"post_type" VARCHAR NOT NULL DEFAULT post, 
			"post_flag" SMALLINT NOT NULL DEFAULT 0, 
			"post_visibility" VARCHAR NOT NULL DEFAULT public, 
			"post_status" VARCHAR NOT NULL DEFAULT published, 
			"post_published" DATE, 
			"post_created" DATETIME NOT NULL, 
			"post_updated" DATETIME NOT NULL); */
	}
	
	/* FACTORY METHODS */
    public function testAlter() 
    {
		$query = self::$database->alter();
		
		$this->assertInstanceOf('Eden\\Sqlite\\Alter', $query);
    }
	
	public function testCollection() 
    {
		$collection = self::$database->collection();
		
		$this->assertInstanceOf('Eden\\Sql\\Collection', $collection);
    }
	
	public function testCreate() 
    {
		$query = self::$database->create();
		
		$this->assertInstanceOf('Eden\\Sqlite\\Create', $query);
    }
	
	public function testDelete() 
    {
		$query = self::$database->delete();
		
		$this->assertInstanceOf('Eden\\Sql\\Delete', $query);
    }
	
	public function testInsert() 
    {
		$query = self::$database->insert();
		
		$this->assertInstanceOf('Eden\\Sql\\Insert', $query);
    }
	
	public function testModel() 
    {
		$query = self::$database->model();
		
		$this->assertInstanceOf('Eden\\Sql\\Model', $query);
    }
	
	public function testSearch() 
    {
		$search = self::$database->search();
		
		$this->assertInstanceOf('Eden\\Sql\\Search', $search);
    }
	
	public function testSelect() 
    {
		$query = self::$database->select();
		
		$this->assertInstanceOf('Eden\\Sql\\Select', $query);
    }
	
	public function testUpdate() 
    {
		$query = self::$database->update();
		
		$this->assertInstanceOf('Eden\\Sql\\Update', $query);
    }
	
	public function testUtility() 
    {
		$query = self::$database->utility();
		
		$this->assertInstanceOf('Eden\\Sqlite\\Utility', $query);
    }
	
	/* CRUD METHODS */
	public function testInsertRow() {
		$total = self::$database->search('unit_post')->getTotal();
		
		self::$database->insertRow('unit_post', array(
			'post_slug'			=> 'unit-test-1',
			'post_title' 		=> 'Unit Test 1',
			'post_detail' 		=> 'Unit Test Detail 1',
			'post_published' 	=> date('Y-m-d'),
			'post_created' 		=> date('Y-m-d H:i:s'),
			'post_updated' 		=> date('Y-m-d H:i:s')));
		
		$id = self::$database->getLastInsertedId();
		
		$now = self::$database->search('unit_post')->getTotal();
		
		$this->assertTrue($id > 0);
		$this->assertEquals($total+1, $now);
	}
	
	public function testGetRow() {
		$row = self::$database->getRow('unit_post', 'post_slug', 'unit-test-1');
		$this->assertEquals('Unit Test 1', $row['post_title']);
	}
	
	public function testUpdateRows() {
		self::$database->updateRows('unit_post', array(
			'post_title' 		=> 'Unit Test 2',
			'post_updated' 		=> date('Y-m-d H:i:s')),
			array('post_slug=%s', 'unit-test-1'));
		
		$row = self::$database->getRow('unit_post', 'post_slug', 'unit-test-1');
		
		$this->assertEquals('Unit Test 2', $row['post_title']);
	}
	
	public function testInsertRows() {
		$total = self::$database->search('unit_post')->getTotal();
		
		self::$database->insertRows('unit_post', array(
			array(
				'post_slug'			=> 'unit-test-2',
				'post_title' 		=> 'Unit Test 2',
				'post_detail' 		=> 'Unit Test Detail 2',
				'post_published' 	=> date('Y-m-d'),
				'post_created' 		=> date('Y-m-d H:i:s'),
				'post_updated' 		=> date('Y-m-d H:i:s')),
			array(
				'post_slug'			=> 'unit-test-3',
				'post_title' 		=> 'Unit Test 3',
				'post_detail' 		=> 'Unit Test Detail 3',
				'post_published' 	=> date('Y-m-d'),
				'post_created' 		=> date('Y-m-d H:i:s'),
				'post_updated' 		=> date('Y-m-d H:i:s'))
		));
		
		$now = self::$database->search('unit_post')->getTotal();
		
		$this->assertEquals($total+2, $now);
	}
	
	public function testSetRow() {
		$total = self::$database->search('unit_post')->getTotal();
		
		self::$database->setRow('unit_post', 'post_slug', 'unit-test-4', array(
			'post_slug'			=> 'unit-test-4',
			'post_title' 		=> 'Unit Test 4',
			'post_detail' 		=> 'Unit Test Detail 4',
			'post_published' 	=> date('Y-m-d'),
			'post_created' 		=> date('Y-m-d H:i:s'),
			'post_updated' 		=> date('Y-m-d H:i:s')));
		
		$now = self::$database->search('unit_post')->getTotal();
		
		$this->assertEquals($total+1, $now);
		
		self::$database->setRow('unit_post', 'post_slug', 'unit-test-4', array(
			'post_slug'			=> 'unit-test-4',
			'post_title' 		=> 'Unit Test 5',
			'post_detail' 		=> 'Unit Test Detail 4',
			'post_published' 	=> date('Y-m-d'),
			'post_created' 		=> date('Y-m-d H:i:s'),
			'post_updated' 		=> date('Y-m-d H:i:s')));
			
		$now = self::$database->search('unit_post')->getTotal();
		
		$this->assertEquals($total+1, $now);
		
		$row = self::$database->getRow('unit_post', 'post_slug', 'unit-test-4');
		
		$this->assertEquals('Unit Test 5', $row['post_title']);
	}
	
	/* Search Collection Models */
	public function testGetModel() {
		$model1 = self::$database->getModel('unit_post', 'post_slug', 'unit-test-1');
		$model2 = self::$database->getModel('unit_post', 'post_slug', 'doesnt exist');
		
		$this->assertInstanceOf('Eden\\Sql\\Model', $model1);
		$this->assertEquals('Unit Test 2', $model1['post_title']);
		$this->assertNull($model2);
	}
	
	public function testDeleteRows() {
		$total = self::$database->search('unit_post')->getTotal();
		
		self::$database->deleteRows('unit_post', array('post_slug LIKE %s', 'unit-test-%'));
		
		$now = self::$database->search('unit_post')->getTotal();
		
		$this->assertEquals($total-4, $now);
	}
}
