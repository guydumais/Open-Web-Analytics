<?php

//
// Open Web Analytics - An Open Source Web Analytics Framework
//
// Copyright 2006 Peter Adams. All rights reserved.
//
// Licensed under GPL v2.0 http://www.gnu.org/copyleft/gpl.html
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
//
// $Id$
//


define('OWA_DTD_BIGINT', 'BIGINT'); 
define('OWA_DTD_INT', 'INT');
define('OWA_DTD_TINYINT', 'TINYINT(1)');
define('OWA_DTD_TINYINT2', 'TINYINT(2)');
define('OWA_DTD_TINYINT4', 'TINYINT(4)');
define('OWA_DTD_BOOLEAN', 'BOOLEAN');
define('OWA_DTD_SERIAL', 'SERIAL');
define('OWA_DTD_PRIMARY_KEY', 'PRIMARY KEY');
define('OWA_DTD_VARCHAR10', 'VARCHAR(10)');
define('OWA_DTD_VARCHAR255', 'VARCHAR(255)');
define('OWA_DTD_VARCHAR', 'VARCHAR(%s)');
define('OWA_DTD_TEXT', 'TEXT'); 
define('OWA_DTD_INDEX', 'KEY');
define('OWA_DTD_AUTO_INCREMENT', 'AUTO_INCREMENT');
define('OWA_DTD_NOT_NULL', 'NOT NULL');
//define('OWA_DTD_UNIQUE', 'UNIQUE'); 
define('OWA_DTD_UNIQUE', 'PRIMARY KEY(%s)');
define('OWA_SQL_ADD_COLUMN', 'ALTER TABLE %s ADD %s %s');   
define('OWA_SQL_DROP_COLUMN', 'ALTER TABLE %s DROP %s'); 
define('OWA_SQL_MODIFY_COLUMN', 'ALTER TABLE %s MODIFY %s %s'); 
define('OWA_SQL_RENAME_TABLE', 'ALTER TABLE %s RENAME %s'); 
define('OWA_SQL_CREATE_TABLE', 'CREATE TABLE IF NOT EXISTS %s (%s) %s'); 
define('OWA_SQL_DROP_TABLE', 'DROP TABLE IF EXISTS %s');  
define('OWA_SQL_INSERT_ROW', 'INSERT into %s (%s) VALUES (%s)');
define('OWA_SQL_UPDATE_ROW', 'UPDATE %s SET %s %s');
define('OWA_SQL_DELETE_ROW', "DELETE from %s %s");
define('OWA_SQL_CREATE_INDEX', 'CREATE INDEX %s ON %s (%s)');
define('OWA_SQL_DROP_INDEX', 'DROP INDEX %s ON %s');
define('OWA_SQL_INDEX', 'INDEX (%s)');
define('OWA_SQL_BEGIN_TRANSACTION', 'BEGIN');
define('OWA_SQL_END_TRANSACTION', 'COMMIT');
define('OWA_DTD_TABLE_TYPE', 'ENGINE = %s');
define('OWA_DTD_TABLE_TYPE_DEFAULT', 'INNODB');
define('OWA_DTD_TABLE_TYPE_DISK', 'INNODB');
define('OWA_DTD_TABLE_TYPE_MEMORY', 'MEMORY');
define('OWA_SQL_ALTER_TABLE_TYPE', 'ALTER TABLE %s ENGINE = %s');
define('OWA_SQL_JOIN_LEFT_OUTER', 'LEFT OUTER JOIN');
define('OWA_SQL_JOIN_LEFT_INNER', 'LEFT INNER JOIN');
define('OWA_SQL_JOIN_RIGHT_OUTER', 'RIGHT OUTER JOIN');
define('OWA_SQL_JOIN_RIGHT_INNER', 'RIGHT INNER JOIN');
define('OWA_SQL_DESCENDING', 'DESC');
define('OWA_SQL_ASCENDING', 'ASC');


/**
 * MySQL Data Access Class
 * 
 * @author      Peter Adams <peter@openwebanalytics.com>
 * @copyright   Copyright &copy; 2006 Peter Adams <peter@openwebanalytics.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GPL v2.0
 * @category    owa
 * @package     owa
 * @version		$Revision$	      
 * @since		owa 1.0.0
 */
class owa_db_mysql extends owa_db {

	/**
	 * Constructor
	 *
	 * @return owa_db_mysql
	 * @access public
	 */
	function owa_db_mysql() {

		return owa_db_mysql::__construct();
	}
	
	function __construct() {
		
		return parent::__construct();
	}
	
	function __destruct() {
		
		$this->close();
	}
	
	function connect() {
	
		$this->connection = mysql_connect(
				OWA_DB_HOST,
				OWA_DB_USER,
				OWA_DB_PASSWORD,
				true
    	);
		
		$this->database_selection = mysql_select_db(OWA_DB_NAME, $this->connection);
			
		if (!$this->connection || !$this->database_selection):
				$this->e->alert('Could not connect to database.');
				$this->connection_status = false;
				return false;
		else:
				$this->connection_status = true;
		endif;
		
		return;
	
	}
	
	
	/**
	 * Database Query
	 *
	 * @param 	string $sql
	 * @access 	public
	 * 
	 */
	function query($sql) {
  
  		if ($this->connection_status == false):
  		owa_coreAPI::profile($this, __FUNCTION__, __LINE__);
  			$this->connect();
  		owa_coreAPI::profile($this, __FUNCTION__, __LINE__);
  		endif;
  
  		owa_coreAPI::profile($this, __FUNCTION__, __LINE__);
		$this->e->debug(sprintf('Query: %s', $sql));
		
		$this->result = '';
		$this->new_result = '';	
		
		if (!empty($this->new_result)):
			mysql_free_result($this->new_result);
		endif;
		owa_coreAPI::profile($this, __FUNCTION__, __LINE__, $sql);
		$result = @mysql_unbuffered_query($sql, $this->connection);
		owa_coreAPI::profile($this, __FUNCTION__, __LINE__);			
		// Log Errors
		if (mysql_errno($this->connection)):
			$this->e->debug(sprintf('A MySQL error occured. Error: (%s) %s. Query: %s',
			mysql_errno($this->connection),
			htmlspecialchars(mysql_error($this->connection)),
			$sql));
		endif;			
		owa_coreAPI::profile($this, __FUNCTION__, __LINE__);
		$this->new_result = $result;
		
		return $this->new_result;
		
	}
	
	function close() {
		
		@mysql_close($this->connection);
		return;
		
	}
	
	/**
	 * Fetch result set array
	 *
	 * @param 	string $sql
	 * @return 	array
	 * @access  public
	 */
	function get_results($sql) {
	
		if ($sql):
			$this->query($sql);
		endif;
	
		$num_rows = 0;
		
		while ( $row = @mysql_fetch_assoc($this->new_result) ) {
			$this->result[$num_rows] = $row;
			$num_rows++;
		}
		
		if ($this->result):
					
			return $this->result;
			
		else:
			return null;
		endif;
	}
	
	/**
	 * Fetch Single Row
	 *
	 * @param string $sql
	 * @return array
	 */
	function get_row($sql) {
		
		$this->query($sql);
		
		//print_r($this->result);
		$row = @mysql_fetch_assoc($this->new_result);
		
		return $row;
	}
	
	/**
	 * Prepares and escapes string
	 *
	 * @param string $string
	 * @return string
	 */
	function prepare($string) {
		
		if ($this->connection_status == false):
  			$this->connect();
  		endif;
		
		return mysql_real_escape_string($string, $this->connection); 
		
	}
	
	
	/**
	 * Creates a new table
	 *
	 */
	function createTable($entity) {
	
		//create column defs
		
		$all_cols = $entity->getColumns();
		
		$columns = '';
	
		$table_defs = '';
		
		$i = 0;
		$count = count($all_cols);
		
		// Control loop
		
		foreach ($all_cols as $k => $v){
			
			// get column definition 
			$columns .= $v.' '.$entity->getColumnDefinition($v);
						
			// Add commas to column statement
			if ($i < $count - 1):
				
				$columns .= ', ';
					
			endif;	
			
			$i++;
				
		}
		
		// make table options
		$table_options = '';
		$options = $entity->getTableOptions();
		
		// table type
		switch ($options['table_type']) {
		
			case "disk":
				$table_type = OWA_DTD_TABLE_TYPE_DISK;
				break;
			case "memory":
				$table_type = OWA_DTD_TABLE_TYPE_MEMORY;
				break;
			default:
				$table_type = OWA_DTD_TABLE_TYPE_DEFAULT;
	
		}
		
		$table_options .= sprintf(OWA_DTD_TABLE_TYPE, $table_type);
			
		return $this->query(sprintf(OWA_SQL_CREATE_TABLE, get_class($entity), $columns, $table_options));
		
	}
	

	
	/**
	 * Begins a SQL transaction statement
	 *
	 */
	function beginTransaction() {
	
		return $this->query(OWA_SQL_BEGIN_TRANSACTION);
	}
	
	/**
	 * Ends a SQL transaction statement
	 *
	 */
	function endTransaction() {
	
		return $this->query(OWA_SQL_END_TRANSACTION);
	}
	
	
	
}

?>