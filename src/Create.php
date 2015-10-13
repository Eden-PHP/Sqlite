<?php //-->
/**
 * This file is part of the Eden PHP Library.
 * (c) 2014-2016 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Eden\Sqlite;

/**
 * Generates create table query string syntax
 *
 * @vendor   Eden
 * @package  Sqlite
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Create extends \Eden\Sql\Query
{
    /**
     * @var string|null $name Name of table
     */
    protected $name = null;

    /**
     * @var string|null $comments Table comments
     */
    protected $comments = null;

    /**
     * @var array $fields List of fields
     */
    protected $fields = array();

    /**
     * @var array $keys List of key indexes
     */
    protected $keys = array();

    /**
     * @var array $uniqueKeys List of unique keys
     */
    protected $uniqueKeys = array();

    /**
     * @var array $primaryKeys List of primary keys
     */
    protected $primaryKeys = array();
    
    /**
     * Construct: Set the table, if any
     *
     * @param string|null $name Name of table
     */
    public function __construct($name = null)
    {
        if (is_string($name)) {
            $this->setName($name);
        }
    }
    
    /**
     * Adds a field in the table
     *
     * @param *string $name       Column name
     * @param *array  $attributes Column attributes
     *
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
     * @param *string $name  Name of column
     * @param *string $table Name of foreign table
     * @param *string $key   Name of key
     *
     * @return Eden\Sqlite\Create
     */
    public function addForeignKey($name, $table, $key)
    {
        //argument test
        Argument::i()
            ->test(1, 'string')         //Argument 1 must be a string
            ->test(2, 'string')         //Argument 2 must be a string
            ->test(3, 'string');    //Argument 3 must be a string
        
        $this->keys[$name] = array($table, $key);
        return $this;
    }
    
    /**
     * Adds a unique key
     *
     * @param *string $name   Name of key
     * @param *array  $fields List of key fields
     *
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
     * @param bool $unbind Whether to unbind variables
     *
     * @return string
     */
    public function getQuery($unbind = false)
    {
        $table = '"'.$this->name.'"';
        
        $fields = array();
        foreach ($this->fields as $name => $attr) {
            $field = array('"'.$name.'"');
            if (isset($attr['type'])) {
                $field[] = isset($attr['length']) ?
                    $attr['type'] . '('.$attr['length'].')' :
                    $attr['type'];
            }
            
            if (isset($attr['primary'])) {
                $field[] = 'PRIMARY KEY';
            }
            
            if (isset($attr['attribute'])) {
                $field[] = $attr['attribute'];
            }
            
            if (isset($attr['null'])) {
                if ($attr['null'] == false) {
                    $field[] = 'NOT NULL';
                } else {
                    $field[] = 'DEFAULT NULL';
                }
            }
            
            if (isset($attr['default'])&& $attr['default'] !== false) {
                if (!isset($attr['null']) || $attr['null'] == false) {
                    if (is_string($attr['default'])) {
                        $field[] = 'DEFAULT \''.$attr['default'] . '\'';
                    } else if (is_numeric($attr['default'])) {
                        $field[] = 'DEFAULT '.$attr['default'];
                    }
                }
            }
            
            $fields[] = implode(' ', $field);
        }
        
        $fields = !empty($fields) ? implode(', ', $fields) : '';
        
        $uniques = array();
        foreach ($this->uniqueKeys as $key => $value) {
            $uniques[] = 'UNIQUE "'. $key .'" ("'.implode('", "', $value).'")';
        }
        
        $uniques = !empty($uniques) ? ', ' . implode(", \n", $uniques) : '';
        
        $keys = array();
        foreach ($this->keys as $key => $value) {
            $keys[] = 'FOREIGN KEY "'. $key .'" REFERENCES '.$value[0].'('.$value[1].')';
        }
        
        $keys = !empty($keys) ? ', ' . implode(", \n", $keys) : '';
        
        return sprintf(
            'CREATE TABLE %s (%s%s%s)',
            $table,
            $fields,
            $unique,
            $keys
        );
    }
    
    /**
     * Sets comments
     *
     * @param *string $comments Table comments
     *
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
     * @param *array $fields List of fields
     *
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
     * @param array $keys A list of foreign keys
     *
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
     * @param *string $name Table name
     *
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
     * @param *array $uniqueKeys List of unique keys
     *
     * @return Eden\Sqlite\Create
     */
    public function setUniqueKeys(array $uniqueKeys)
    {
        $this->uniqueKeys = $uniqueKeys;
        return $this;
    }
}
