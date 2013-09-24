<?php //-->
/*
 * This file is part of the Sqlite package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Eden\Sqlite;

use Eden\Sql\Query as SqlQuery;

/**
 * Generates create table query string syntax
 *
 * @vendor Eden
 * @package Sqlite
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Create extends SqlQuery {
	protected $name	= null;
	protected $comments = null;
	protected $fields = array();
	protected $keys = array();
	protected $uniqueKeys = array();
	protected $primaryKeys = array();
	
	/**
	 * Construct: Set the table, if any
	 *
	 * @param string|null
	 */
	public function __construct($name = null) 
	{
		if(is_string($name)) {
			$this->setName($name);
		}
	}
	
	/**
	 * Adds a field in the table
	 *
	 * @param string name
	 * @param array attributes
	 * @return Eden\Sqlite\Create
	 */
	public function addField($name, array $attributes) 
	{
		//Argument 1 must be a string
		Argument::i()->test(1, 'string');
		
		$this->fields[$name] = $attributes;
		return $this;
	}
	
	/**
	 * Adds an index key
	 *
	 * @param string name
	 * @param array fields
	 * @return Eden\Sqlite\Create
	 */
	public function addForeignKey($name, $table, $key) 
	{
		//argument test
		Argument::i()
			->test(1, 'string')		//Argument 1 must be a string
			->test(2, 'string')		//Argument 2 must be a string
			->test(3, 'string');	//Argument 3 must be a string
		
		$this->keys[$name] = array($table, $key);
		return $this;
	}
	
	/**
	 * Adds a unique key
	 *
	 * @param string name
	 * @param array fields
	 * @return Eden\Sqlite\Create
	 */
	public function addUniqueKey($name, array $fields) 
	{
		//Argument 1 must be a string
		Argument::i()->test(1, 'string');
		
		$this->uniqueKeys[$name] = $fields;
		return $this;
	}
	
	/**
	 * Returns the string version of the query 
	 *
	 * @param  bool
	 * @return string
	 */
	public function getQuery($unbind = false) 
	{	
		$table = '"'.$this->name.'"';
		
		$fields = array();
		foreach($this->fields as $name => $attr) {
			$field = array('"'.$name.'"');
			if(isset($attr['type'])) {	
				$field[] = isset($attr['length']) ? 
					$attr['type'] . '('.$attr['length'].')' : 
					$attr['type'];
			}
			
			if(isset($attr['primary'])) {
				$field[] = 'PRIMARY KEY';
			}
			
			if(isset($attr['attribute'])) {
				$field[] = $attr['attribute'];
			}
			
			if(isset($attr['null'])) {
				if($attr['null'] == false) {
					$field[] = 'NOT NULL';
				} else {
					$field[] = 'DEFAULT NULL';
				}
			}
			
			if(isset($attr['default'])&& $attr['default'] !== false) {
				if(!isset($attr['null']) || $attr['null'] == false) {
					if(is_string($attr['default'])) {
						$field[] = 'DEFAULT \''.$attr['default'] . '\'';
					} else if(is_numeric($attr['default'])) {
						$field[] = 'DEFAULT '.$attr['default'];
					}
				}
			}
			
			$fields[] = implode(' ', $field);
		}
		
		$fields = !empty($fields) ? implode(', ', $fields) : '';
		
		$uniques = array();
		foreach($this->uniqueKeys as $key => $value) {
			$uniques[] = 'UNIQUE "'. $key .'" ("'.implode('", "', $value).'")';
		}
		
		$uniques = !empty($uniques) ? ', ' . implode(", \n", $uniques) : '';
		
		$keys = array();
		foreach($this->keys as $key => $value) {
			$keys[] = 'FOREIGN KEY "'. $key .'" REFERENCES '.$value[0].'('.$value[1].')';
		}
		
		$keys = !empty($keys) ? ', ' . implode(", \n", $keys) : '';
		
		return sprintf(
			'CREATE TABLE %s (%s%s%s)',
			$table, $fields, $unique, $keys);
	}
	
	/**
	 * Sets comments
	 *
	 * @param string comments
	 * @return Eden\Sqlite\Create
	 */
	public function setComments($comments) 
	{
		//Argument 1 must be a string
		Argument::i()->test(1, 'string');
		
		$this->comments = $comments;
		return $this;
	}
	
	/**
	 * Sets a list of fields to the table
	 *
	 * @param array fields
	 * @return Eden\Sqlite\Create
	 */
	public function setFields(array $fields) 
	{
		$this->fields = $fields;
		return $this;
	}
	
	/**
	 * Sets a list of keys to the table
	 *
	 * @param array keys
	 * @return Eden\Sqlite\Create
	 */
	public function setForiegnKeys(array $keys) 
	{
		$this->keys = $keys;
		return $this;
	}
	
	/**
	 * Sets the name of the table you wish to create
	 *
	 * @param string name
	 * @return Eden\Sqlite\Create
	 */
	public function setName($name) 
	{
		//Argument 1 must be a string
		Argument::i()->test(1, 'string');
		
		$this->name = $name;
		return $this;
	}
	
	/**
	 * Sets a list of unique keys to the table
	 *
	 * @param array uniqueKeys
	 * @return Eden\Sqlite\Create
	 */
	public function setUniqueKeys(array $uniqueKeys) 
	{
		$this->uniqueKeys = $uniqueKeys;
		return $this;
	}
}