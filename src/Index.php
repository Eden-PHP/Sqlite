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
 * Abstractly defines a layout of available methods to
 * connect to and query a SQLite database. This class also
 * lays out query building methods that auto renders a
 * valid query the specific database will understand without
 * actually needing to know the query language. Extending
 * all SQL classes, comes coupled with loosely defined
 * searching, collections and models.
 *
 * @vendor   Eden
 * @package  Sqlite
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Index extends \Eden\Sql\Index
{
    /**
     * @var string $path Sqlite file path
     */
    protected $path = null;
    
    /**
     * Construct: Store connection information
     *
     * @param *string $path Sqlite file path
     */
    public function __construct($path)
    {
        //argument test
        Argument::i()->test(1, 'string');
        $this->path = $path;
    }
    
    /**
     * Returns the alter query builder
     *
     * @param *string $name Name of table
     *
     * @return Eden\Sqlite\Alter
     */
    public function alter($name = null)
    {
        //Argument 1 must be a string or null
        Argument::i()->test(1, 'string', 'null');
        
        return Alter::i($name);
    }
    
    /**
     * Connects to the database
     *
     * @param array $options The connection options
     *
     * @return Eden\Sqlite\Index
     */
    public function connect(array $options = array())
    {
        $this->connection = new \PDO('sqlite:'.$this->path);
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->trigger('connect');
        
        return $this;
    }
    
    /**
     * Returns the create query builder
     *
     * @param string $name Name of table
     *
     * @return Eden\Sqlite\Create
     */
    public function create($name = null)
    {
        //Argument 1 must be a string or null
        Argument::i()->test(1, 'string', 'null');
        
        return Create::i($name);
    }
    
    /**
     * Returns the columns and attributes given the table name
     *
     * @param *string $table The name of the table
     *
     * @return array|false
     */
    public function getColumns($table)
    {
        //Argument 1 must be a string
        Argument::i()->test(1, 'string');
        
        $query = $this->utility()->showColumns($table);
        $results = $this->query($query, $this->getBinds());
        
        $columns = array();
        foreach ($results as $column) {
            $key = null;
            if ($column['pk'] == 1) {
                $key = 'PRI';
            }
            
            $columns[] = array(
                'Field'     => $column['name'],
                'Type'      => $column['type'],
                'Default'   => $column['dflt_value'],
                'Null'      => $column['notnull'] != 1,
                'Key'       => $key);
        }
        
        return $columns;
    }
    
    /**
     * Peturns the primary key name given the table
     *
     * @param *string $table The table name
     *
     * @return string
     */
    public function getPrimaryKey($table)
    {
        //Argument 1 must be a string
        Argument::i()->test(1, 'string');
        
        $query = $this->utility();
        $results = $this->getColumns($table, "`Key` = 'PRI'");
        return isset($results[0]['Field']) ? $results[0]['Field'] : null;
    }
    
    /**
     * Returns the whole enitre schema and rows
     * of the current databse
     *
     * @return string
     */
    public function getSchema()
    {
        $backup = array();
        $tables = $this->getTables();
        foreach ($tables as $table) {
            $backup[] = $this->getBackup();
        }
        
        return implode("\n\n", $backup);
    }
    
    /**
     * Returns the whole enitre schema and rows
     * of the current table
     *
     * @param *string $table The table name
     *
     * @return string
     */
    public function getTableSchema($table)
    {
        //Argument 1 must be a string
        Argument::i()->test(1, 'string');
        
        $backup = array();
        //get the schema
        $schema = $this->getColumns($table);
        if (count($schema)) {
            //lets rebuild this schema
            $query = $this->create()->setName($table);
            foreach ($schema as $field) {
                //first try to parse what we can from each field
                $fieldTypeArray = explode(' ', $field['Type']);
                $typeArray = explode('(', $fieldTypeArray[0]);
                
                $type = $typeArray[0];
                $length = str_replace(')', '', $typeArray[1]);
                $attribute = isset($fieldTypeArray[1]) ? $fieldTypeArray[1] : null;
                
                $null = strtolower($field['Null']) == 'no' ? false : true;
                
                $increment = strtolower($field['Extra']) == 'auto_increment' ? true : false;
                
                //lets now add a field to our schema class
                $q->addField($field['Field'], array(
                    'type'              => $type,
                    'length'            => $length,
                    'attribute'         => $attribute,
                    'null'              => $null,
                    'default'           => $field['Default'],
                    'auto_increment'    => $increment));
                
                //set keys where found
                switch ($field['Key']) {
                    case 'PRI':
                        $query->addPrimaryKey($field['Field']);
                        break;
                    case 'UNI':
                        $query->addUniqueKey($field['Field'], array($field['Field']));
                        break;
                    case 'MUL':
                        $query->addKey($field['Field'], array($field['Field']));
                        break;
                }
            }
            
            //store the query but dont run it
            $backup[] = $query;
        }
        
        //get the rows
        $rows = $this->query($this->select->from($table)->getQuery());
        if (count($rows)) {
            //lets build an insert query
            $query = $this->insert($table);
            foreach ($rows as $index => $row) {
                foreach ($row as $key => $value) {
                    $query->set($key, $this->getBinds($value), $index);
                }
            }
            
            //store the query but dont run it
            $backup[] = $query->getQuery(true);
        }
        
        return implode("\n\n", $backup);
    }
    
    /**
     * Returns a listing of tables in the DB
     *
     * @param string|null $like The like pattern
     *
     * @return attay|false
     */
    public function getTables($like = null)
    {
        //Argument 1 must be a string or null
        Argument::i()->test(1, 'string', 'null');
        
        $query = $this->utility();
        $like = $like ? $this->bind($like) : null;
        $results = $this->query($query->showTables($like), $q->getBinds());
        $newResults = array();
        foreach ($results as $result) {
            foreach ($result as $key => $value) {
                $newResults[] = $value;
                break;
            }
        }
        
        return $newResults;
    }
    
    /**
     * Inserts multiple rows into a table
     *
     * @param *string    $table   Table name
     * @param array      $setting Key/value 2D array matching table columns
     * @param bool|array $bind    Whether to compute with binded variables
     *
     * @return Eden\Sqlite\Index
     */
    public function insertRows($table, array $settings, $bind = true)
    {
        //argument test
        Argument::i()
            //Argument 1 must be a string
            ->test(1, 'string')
            //Argument 3 must be an array or bool
            ->test(3, 'array', 'bool');
        
        //this is an array of arrays
        foreach ($settings as $index => $setting) {
            //SQLite no available multi insert
            //there's work arounds, but no performance gain
            $this->insertRow($table, $setting, $bind);
        }
        
        return $this;
    }
    
    /**
     * Returns the select query builder
     *
     * @param string|array $select Column list
     *
     * @return Eden\Sql\Select
     */
    public function select($select = 'ROWID,*')
    {
        //Argument 1 must be a string or array
        Argument::i()->test(1, 'string', 'array');
        
        return \Eden\Sql\Select::i($select);
    }
    
    /**
     * Returns the alter query builder
     *
     * @return Eden\Sqlite\Utility
     */
    public function utility()
    {
        return Utility::i();
    }
}
