<?php
/**
* SimplifiedDB - Library of PHP functions for intracting database using PDO
* File: SimplifiedDB.php
* Author: Pritesh Gupta
* Version: 1.3.0
* Date: 05/10/2013
* Copyright (c) 2013 Pritesh Gupta. All Rights Reserved.

/* ABOUT THIS FILE:
   -------------------------------------------------------------------------
* SimplifiedDB Class provides set of functions for interacting database using PDO extension.
* You don't need to write any query to perform insert, update, delete and select operations(CRUD operations).
* You need to call these functions with appropriate parameters and these functions will perform required 
* Database operations. 
* There are also some useful functions which helps you to create html forms, show results in table format etc. by
* directly interacting with Database Tables or from array of result of select query. 
   -------------------------------------------------------------------------
*/
class SimplifiedDB
{
	public $error_info="";			// Display the error message, if any. Use this for debugging purpose 
	public $message_info="";        // Display the last message associated with the task like  connected to database
	private $dbh=NULL;              // Used for database connection object
	public $user_name;	            // Username for the database
	public $password;               // Password for database
	public $host_name;              // hostname/server for database
	public $db_name;                // Database name
	private $values=array();        // array of values  
	public $query;                  // Display the last query executed
	public $rows_affected;          // Display the no. of rows affected
	public $count_rows;             // Display no. of rows returned by select query operation
	public $last_insert_id;         // Display the insert id of last insert operation executed	
	public $and_or_condition="and"; // Use 'and'/'or' in where condition of select statement, default is 'and'	
	public $group_by_column="";     // Set it to column names you wants to GROUP BY e.g. 'gender' where gender is column name
	public $order_by_column="";     // Set it to column names you wants to ORDER BY e.g. 'colName DESC'	
	public $limit_val="";           // Set it to limit the no. of rows returned e.g. '0,10', it generates 'LIMIT 0,10'
	public $having="";              // Set it to use 'HAVING' keyword in select query e.g. $having="sum(col1)>1000"	
	public $between_columns=array();// Set it to use 'BETWEEN' keyword in select query e.g. $between=array ("col1"=>val1,"col1"=>val2)
	public $in=array();             // Set it to use 'IN' keyword in select query e.g. $in=array("col1"=>"val1,val2,val3")
	public $not_in=array();         // Set it to use 'NOT IN' keyword in select query e.g. $not_in=array("col1"=>"val1,val2,val3")
	public $like_cols=array();      // Set it to use 'LIKE' keyword in select query e.g. $like_col=array("col1"=>"%v%","col2"=>"c%")				
	public $is_sanitize=true;       // Checks whether basic sanitization of query varibles needs to be done or not.
	public $single_row=false;       // Returns single row of select query operation if true, else return all rows
	public $backticks="";          // Backtick for preventing error if columnname contains reserverd mysql keywords.
									// Set it to empty if you want to use alias
									// for column names then set it empty string.
	public $fetch_mode="ASSOC";		// Determines fetch mode of the result of select query,Possible values are 
									// ASSOC,NUM,BOTH,COLUMN and OBJ

   /*Version 1.1 new variables added*/
	public $charset="";                // If you want to use any other charset than default one, then set this charset value.
	public $rows_returned=0;           // It shows no. of rows returned in select operation
    public $resetAllSettings=false;    // It reset all the values on that object after each sql operation if it is set true
	public $beginTransaction=false;	   // Set this true to use transaction functionality			
	public $commitTransaction=false;   // To commit transaction, set this true
	private $rollbackTransaction=false;// Used internally, for roll back operation
	
	/*Version 1.2 new variables added*/
	public $isSameWhereCondition=false;  // If true then statement will be prepared once for faster update/delete batch operation
	/*Version 1.3 new variables added*/
	public $isSameColumns=false;         // Must be true for 'and' 'or' condition with same column name to work
	/******************************************** PDO Functions **********************************************************/
	/**
	 * Connects to database

	 * @param   string  $hostname          Host/Server name 
	 * @param   string  $user_name         User name 
	 * @param   string  $password          Password 
	 * @param   string  $database          Database-name
	 *
	*/
	
	function dbConnect($hostname,$user_name,$password,$Database)
	{	
		$this->host_name=$hostname;
	   	$this->user_name=$user_name;
	   	$this->password=$password;
	   	$this->db_name=$Database;	
	}
	
	/*Version 1.1 new function added*/
	/**
	 * Commits result to the database, if there is no rollback
	*/
	
	function commitTransaction()
	{
		try
		{
			if($this->dbh!=NULL)
			{
				$this->dbh->commit();
				$this->beginTransaction=false;
			}
		}
		catch(PDOException $e)
		{		
			$this->error_info=$e->getMessage();
		}
	}
	
	/**
	 * Insert new records in a table using associative array. Instead of writing long insert queries, you needs to pass
	 * array of keys(columns) and values(insert values). This function will automatically create query for you and inserts data.
	 * @param   string   $table_name              The name of the table to insert new records.
	 * @param   array    $insert_array            Associative array with key as column name and values as column value.
	 *
 	 */
	function dbInsert($table_name,$insert_array)
	{
		$columns="";
		$this->values=array();
		$parameters="";
		
		foreach($insert_array as $col => $val)
		{
			$columns.="".trim($col).",";
			$parameters.="?,";
			$this->values[]=$val;
		}
		
		$columns=rtrim($columns,",");		
		$parameters=rtrim($parameters,",");		
		
		try 
		{	
			
			if($this->rollbackTransaction&&$this->beginTransaction)				
				return;
				
			if($this->beginTransaction==true)
			{
				if($this->dbh==NULL)
				{
					$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
					$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$this->dbh->beginTransaction();					
				}
			}
			else
			{
				$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
				$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			
			$this->message_info="Connected to database";
			if($this->charset)
				$this->dbh->exec("set names ".$this->charset);
			$this->query="INSERT INTO $table_name ($columns) values ($parameters)";
			$stmt = $this->dbh->prepare($this->query);
			$stmt->execute($this->values);
			$this->rows_affected=$stmt->rowCount();
			$this->last_insert_id=$this->dbh->lastInsertId();
			if($this->beginTransaction==false)
				$this->dbh = NULL;
			if($this->resetAllSettings==true)
				$this->resetSettings();
		}
		catch(PDOException $e)
		{
			if($this->beginTransaction==true)
			{
				$this->rollbackTransaction=true;
				$this->dbh->rollBack();
			}
			$this->error_info=$e->getMessage();
		}
	}
	
	/**
	 * Insert batch records in a table using array of associative array.This function will insert multiple rows using array
	 * of associative array. 
	 * @param   string   $table_name                    The name of the table to insert new records.
	 * @param   array    $insert_batch_array            Array of associative array with key as column name and values as column value.
	 *
 	 */
	function dbInsertBatch($table_name,$insert_batch_array)
	{	
		try 
		{			
			if($this->rollbackTransaction&&$this->beginTransaction)				
				return;
				
			if($this->beginTransaction==true)
			{
				if($this->dbh==NULL)
				{
					$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
					$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$this->dbh->beginTransaction();
				}
			}
			else
			{
				$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
				$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			$this->message_info="Connected to database";
			if($this->charset)
				$this->dbh->exec("set names ".$this->charset);
			$is_stm_prepared=false;
			$rows_affected=0;
			foreach($insert_batch_array as $insert_array)
			{
				$columns="";
				$this->values=array();
				$parameters="";			
				
				foreach($insert_array as $col => $val)
				{
					$columns.="".trim($col).",";
					$parameters.="?,";
					$this->values[]=$val;
				}				
				//Prepare statement for the first time only to make insert operation faster
				if(!$is_stm_prepared)
				{
					$columns=rtrim($columns,",");		
					$parameters=rtrim($parameters,",");		
					$this->query="INSERT INTO $table_name ($columns) values ($parameters)";
					$stmt = $this->dbh->prepare($this->query);
					$is_stm_prepared=true;
				}				
				$stmt->execute($this->values);
				$rows_affected=$rows_affected+$stmt->rowCount();
				$this->last_insert_id=$this->dbh->lastInsertId();
			}
			$this->rows_affected=$rows_affected;
		    if($this->beginTransaction==false)
				$this->dbh = NULL;
			if($this->resetAllSettings==true)			
				$this->resetSettings();
			
		}
		catch(PDOException $e)
		{
			if($this->beginTransaction==true)
			{
				$this->rollbackTransaction=true;
				$this->dbh->rollBack();
			}
			
			$this->error_info=$e->getMessage();
		}
	}
	/**
	 * Update existing records in a table using associative array. Instead of writing long update queries, you needs to pass
	 * array of keys(columns) and values(update values) and associative array of conditions with keys as 
	 * columns and value as column value.
	 * This function will automatically create query for you and updates data.
	 * Note: The WHERE clause specifies which record or records that should be updated. If you omit the WHERE clause, 
	 * all records will be updated!
	 * @param   string   $table_name                  The name of the table to update old records.
	 * @param   array    $update_array                Associative array with key as column name and values as column value.
	 * @param   array    $update_condition_array      Associative array with key as column name and values as column value for where clause.	
	 *
	 */
	function dbUpdate($table_name,$update_array,$update_condition_array=array())
	{
		$colums_val="";
		$where_condition="";
		$this->values=array();
		$and_val="";
		
		foreach($update_array as $col => $val)
		{
			$colums_val=$colums_val."".trim($col)."=?,";			
			$this->values[]=$val;
		}
		$colums_val=rtrim($colums_val,",");
		
		$where_condition=$this->getWhereCondition($update_condition_array);		
		/* Add Order By condition */						
		$where_condition=$this->getOrderbyCondition($where_condition);
		/* Add Limit condition */								
		$where_condition=$this->getLimitCondition($where_condition);	
		
		try 
		{	
			if($this->rollbackTransaction&&$this->beginTransaction)				
				return;
			if($this->beginTransaction==true)
			{
				if($this->dbh==NULL)
				{
					$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
					$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$this->dbh->beginTransaction();
				}
			}
			else
			{
				$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
				$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			$this->message_info="Connected to database";
			if($this->charset)
				$this->dbh->exec("set names ".$this->charset);
			$this->query="UPDATE $table_name SET $colums_val $where_condition";
  		    $stmt = $this->dbh->prepare($this->query);
			$stmt->execute($this->values);
			$this->rows_affected=$stmt->rowCount();
		    if($this->beginTransaction==false)
				$this->dbh = NULL;
			if($this->resetAllSettings==true)
				$this->resetSettings();

		}
		catch(PDOException $e)
		{
			if($this->beginTransaction==true)
			{				
				$this->rollbackTransaction=true;
				$this->dbh->rollBack();
			}
			$this->error_info=$e->getMessage();
		}
	}
	
	/**
	 * Update batch of existing records in a table using array of associative array. 
	 * This function is helpful for updating various rows by passing array of associative array.
	 * Note: The WHERE clause specifies which record or records that should be updated. If you omit the WHERE clause, 
	 * all records will be updated!
	 * @param   string   $table_name                     The name of the table to update old records.
	 * @param   array    $update_batch_array             Array of Assoc. array with key as column and values as column value.
	 * @param   array    $update_batch_condition_array   Array of Associative array with key as column name and values as column value for where clause.	
	 *
	 */
	function dbUpdateBatch($table_name,$update_batch_array,$update_batch_condition_array=array())
	{
		
		try 
		{	
			if($this->rollbackTransaction&&$this->beginTransaction)				
				return;
			if($this->beginTransaction==true)
			{
				if($this->dbh==NULL)
				{
					$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
					$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$this->dbh->beginTransaction();
				}
			}
			else
			{
				$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
				$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			$this->message_info="Connected to database";
			if($this->charset)
				$this->dbh->exec("set names ".$this->charset);
			
			$update_count=0;	
			$is_stmt_prepared=false;
			$rows_affected=0;
			foreach($update_batch_array as $update_array)
			{
				$colums_val="";
				$where_condition="";
				$this->values=array();
				$and_val="";
				
				foreach($update_array as $col => $val)
				{
					$colums_val=$colums_val."".trim($col)."=?,";			
					$this->values[]=$val;
				}
				$colums_val=rtrim($colums_val,",");
				
				if(is_array($update_batch_condition_array[$update_count]))
				{
					foreach($update_batch_condition_array[$update_count] as $col => $val)
					{			
						$where_condition=$where_condition.$and_val." ".trim($col)."=? ";			
						$this->values[]=$val;
						$and_val=$this->and_or_condition;
					}
				}
				
				if($where_condition)
					$where_condition=" WHERE ".rtrim($where_condition,",");
				
				$where_condition=$this->getOrderbyCondition($where_condition);					
				$where_condition=$this->getLimitCondition($where_condition);					
			
				if($this->isSameWhereCondition)
				{
					if(!$is_stmt_prepared)
					{	
						$this->query="UPDATE $table_name SET $colums_val $where_condition";
						$stmt = $this->dbh->prepare($this->query);
						$is_stmt_prepared=true;
					}
				}
				else
				{
					$this->query="UPDATE $table_name SET $colums_val $where_condition";
					$stmt = $this->dbh->prepare($this->query);
				}
		    	$stmt->execute($this->values);
				$rows_affected=$rows_affected+$stmt->rowCount();
				$update_count++;
			}	
		    $this->rows_affected=$rows_affected;
			if($this->beginTransaction==false)
				$this->dbh = NULL;			
			if($this->resetAllSettings==true)
				$this->resetSettings();

		}
		catch(PDOException $e)
		{
			if($this->beginTransaction==true)
			{				
				$this->rollbackTransaction=true;
				$this->dbh->rollBack();
			}
			$this->error_info=$e->getMessage();
		}
	}
	/**
	 * Delete records in a table using associative array. Instead of writing long delete queries, you needs to pass
	 * associative array of conditions with keys as columns and value as column value.
	 * This function will automatically create query for you and deletes records.
	 * Note: The WHERE clause specifies which record or records that should be deleted. If you omit the WHERE clause, 
	 * all records will be deleted!	 
	 * @param   string   $table_name                  The name of the table to delete records.
	 * @param   array    $delete_where_condition      Associative array with key as column name and values as column
	 												  value for where clause.	
	 *
	 */

	function dbDelete($table_name,$delete_where_condition=array())
	{
		$where_condition="";
		$this->values=array();
		$and_val="";

		/* where condition */
		$where_condition=$this->getWhereCondition($delete_where_condition);
		
		/* Add Order By condition */						
		$where_condition=$this->getOrderbyCondition($where_condition);					
		
		/* Add Limit condition */								
		$where_condition=$this->getLimitCondition($where_condition);	
		
		try 
		{
			if($this->rollbackTransaction&&$this->beginTransaction)				
				return;	
			if($this->beginTransaction==true)
			{
				if($this->dbh==NULL)
				{
					$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
					$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$this->dbh->beginTransaction();
				}
			}
			else
			{
				$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
				$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			$this->message_info="Connected to database";
			if($this->charset)
				$this->dbh->exec("set names ".$this->charset);
			$this->query="DELETE FROM $table_name $where_condition";
  		    $stmt = $this->dbh->prepare($this->query);
			$stmt->execute($this->values);
			$this->rows_affected=$stmt->rowCount();
			
		    if($this->beginTransaction==false)
				$this->dbh = NULL;			
			if($this->resetAllSettings==true)
				$this->resetSettings();
			
		}
		catch(PDOException $e)
		{
			if($this->beginTransaction==true)
			{
				$this->rollbackTransaction=true;				
				$this->dbh->rollBack();
			}
			$this->error_info=$e->getMessage();
		}	
	}
	
	/**
	 * Delete batch records in a table using array of associative array. 
	 * This function helps in deleting many rows using array of associative array.
	 * Note: The WHERE clause specifies which record or records that should be deleted. If you omit the WHERE clause, 
	 * all records will be deleted!	 
	 * @param   string   $table_name                    The name of the table to delete records.
	 * @param   array    $delete_batch_where_condition  Array of associative array with key as column name and values as column
	 												    value for where clause.	
	 *
	 */

	function dbDeleteBatch($table_name,$delete_batch_where_condition=array())
	{
		try 
		{
			if($this->rollbackTransaction&&$this->beginTransaction)				
				return;	
			if($this->beginTransaction==true)
			{
				if($this->dbh==NULL)
				{
					$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
					$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$this->dbh->beginTransaction();
				}
			}
			else
			{
				$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
				$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			$this->message_info="Connected to database";
			if($this->charset)
				$this->dbh->exec("set names ".$this->charset);
			$is_stmt_prepared=false;	
			$rows_affected=0;
			foreach($delete_batch_where_condition as $delete_where_condition)
			{
				$where_condition="";
				$this->values=array();
				$and_val="";
								
				foreach($delete_where_condition as $col => $val)
				{
					$where_condition=$where_condition.$and_val." ".trim($col)."=? ";			
					$this->values[]=$val;
					$and_val=$this->and_or_condition;
				}
				
				if($where_condition)
					$where_condition=" WHERE ".rtrim($where_condition,",");	
				$where_condition=$this->getOrderbyCondition($where_condition);					
				$where_condition=$this->getLimitCondition($where_condition);	
				
				if($this->isSameWhereCondition)
				{
					if(!$is_stmt_prepared)
					{			
						$this->query="DELETE FROM $table_name $where_condition";
						$stmt = $this->dbh->prepare($this->query);
						$is_stmt_prepared=true;
					}
				}
				else
				{
					$this->query="DELETE FROM $table_name $where_condition";
					$stmt = $this->dbh->prepare($this->query);
				}
				
				$stmt->execute($this->values);
				$rows_affected=$rows_affected+$stmt->rowCount();
			}
			$this->rows_affected=$rows_affected;
		    if($this->beginTransaction==false)
				$this->dbh = NULL;			
			if($this->resetAllSettings==true)
				$this->resetSettings();
			
		}
		catch(PDOException $e)
		{
			if($this->beginTransaction==true)
			{
				$this->rollbackTransaction=true;				
				$this->dbh->rollBack();
			}
			$this->error_info=$e->getMessage();
		}	
	}


	/**
	 * Select records from the single table. You can provide columns to be selected and where clause with
	 * associative array of conditions with keys as columns and value as column value. Along with these function parameters,
	 * you can set group by columnname, order by columnname, limit, like, in , not in, between clause etc. 
	 * This function will automatically creates query for you and select data.
	 * @param   string   $table_name                  The name of the table to select records.
	 * @param   array    $columns                     Array of columns to be selected
	 * @param   array    $select_where_condition      Associative array with key as column name and values as column value for where clause.	
	 * return   array                                 returns array as result of query.
	*/
	function dbSelect($table_name,$columns=array(),$select_where_condition=array())
	{		
		$this->values=array();		
		/* Get Columns */
		$col=$this->getColumns($columns);		
		/* Add where condition */
		$where_condition=$this->getWhereCondition($select_where_condition);
		
		/* Add like condition */
		$where_condition=$this->getLikeCondition($where_condition);
		
		/* Add Between condition */		
		$where_condition=$this->getBetweenCondition($where_condition);		
		
		/* Add In condition */				
		$where_condition=$this->getInCondition($where_condition);
		
		/* Add Not In condition */						
		$where_condition=$this->getNotInCondition($where_condition);
		
		/* Add Group By and Having condition */						
		$where_condition=$this->getGroupByCondition($where_condition);		
		
		/* Add Order By condition */						
		$where_condition=$this->getOrderbyCondition($where_condition);					
		
		/* Add Limit condition */								
		$where_condition=$this->getLimitCondition($where_condition);	
			
		try 
		{	
			$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->message_info="Connected to database";
			if($this->charset)
				$this->dbh->exec("set names ".$this->charset);
			$this->query="SELECT ".$col." FROM ".$this->backticks.trim($table_name).$this->backticks.$where_condition;
  		    $stmt = $this->dbh->prepare($this->query);
			$stmt->execute($this->values);
			if($this->single_row==true)
				$result=$stmt->fetch($this->getPDOFetchmode());
			else
				$result=$stmt->fetchAll($this->getPDOFetchmode());
		    $this->dbh = NULL;
			if(is_array($result))
				$this->rows_returned=count($result);
			if($this->single_row==true)	
				$this->rows_returned=1;				
			if($this->resetAllSettings==true)
				$this->resetSettings();
			
			return $result;
		}
		catch(PDOException $e)
		{
			$this->error_info=$e->getMessage();
		}			
	}
	
	/**
	 * Select records from the multiple table with join keyword. You can provide columns to be selected and where clause with
	 * associative array of conditions with keys as columns and value as column value, group by, order by , limit etc.
	 * You needs to specify join condition between different tables and join type (left join, right join etc.) to select data. 
	 * This function will automatically creates query for you and select data.
	 * @param   array    $table_name                  Array of tables to be joined.
	 * @param   array    $join_conditions             Array of join conditions between tables
	 * @param   array    $join_type                   Array of join types
	 * @param   array    $columns                     Array of columns to be selected
	 * @param   array    $select_where_condition      Associative array with key as column name and values as column value for where clause.	
	 *
	 * return   array                                 returns result of query as array
	*/

	function dbSelectJoin($table_names,$join_conditions,$join_type,$columns=array(),$select_where_condition=array())
	{
		$this->values=array();

		if($this->backticks)
		{
			$backtick_on=true;
			$this->backticks="";
		}

		/* Get Columns */
		$col=$this->getColumns($columns);
		
		/* Get Join condition */
		$table_join=$this->getTableJoins($table_names,$join_conditions,$join_type);
		
		/* Add where condition */
		$where_condition=$this->getWhereCondition($select_where_condition);
		
		/* Add like condition */
		$where_condition=$this->getLikeCondition($where_condition);
		
		/* Add Between condition */		
		$where_condition=$this->getBetweenCondition($where_condition);		
		
		/* Add In condition */				
		$where_condition=$this->getInCondition($where_condition);
		
		/* Add Not In condition */						
		$where_condition=$this->getNotInCondition($where_condition);
		
		/* Add Group By and Having condition */						
		$where_condition=$this->getGroupByCondition($where_condition);		
		
		/* Add Order By condition */						
		$where_condition=$this->getOrderbyCondition($where_condition);					
		
		/* Add Limit condition */								
		$where_condition=$this->getLimitCondition($where_condition);	
		
		
		try 
		{	
			$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->message_info="Connected to database";
			if($this->charset)
				$this->dbh->exec("set names ".$this->charset);
			$this->query="SELECT ".$col." FROM ".$table_join." ".$where_condition;
  		    $stmt = $this->dbh->prepare($this->query);
			$stmt->execute($this->values);
			if($this->single_row==true)
				$result=$stmt->fetch($this->getPDOFetchmode());
			else
				$result=$stmt->fetchAll($this->getPDOFetchmode());
		    $this->dbh = NULL;
			if(is_array($result))
				$this->rows_returned=count($result);
			if($this->single_row==true)	
				$this->rows_returned=1;	
			
			if($this->resetAllSettings==true)
			{
				$this->resetSettings();
			}
			if($backtick_on)
				$this->backticks="";
			return $result;
		}
		catch(PDOException $e)
		{
			$this->error_info=$e->getMessage();
		}	

	}
	
	/**
	 * Executes any mysql query and returns the result array(in case of select query). 
	 * Use this for running any other queries that can't be run using the other select,insert,update,delete functions
	 * @param   string  $query       			Query to be executed
	 * @param   array   $parameter_values       values of the columns passed
	 *
	 * return   array               result of the query
	*/	
	function dbExecuteQuery($query,$parameter_values=array())
	{
		try 
		{	
			$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->message_info="Connected to database";
			if($this->charset)
				$this->dbh->exec("set names ".$this->charset);
			$this->query=$query;
  		    $stmt = $this->dbh->prepare($query);
			$stmt->execute($parameter_values);
			if($this->single_row==true)
				$result=$stmt->fetch($this->getPDOFetchmode());
			else
				$result=$stmt->fetchAll($this->getPDOFetchmode());		
		    $this->dbh = NULL;
			if(is_array($result))
				$this->rows_returned=count($result);
			if($this->single_row==true)	
				$this->rows_returned=1;	
			if($this->resetAllSettings==true)			
				$this->resetSettings();			
			return $result;
		}
		catch(PDOException $e)
		{
			$this->error_info=$e->getMessage();
		}	
	}
	
	/**
	 * Checks whether particular field of table contains some specific value or not. Most of the times,
	 * We needs to check for specific values like username,passwords etc. You can use the select functions
	 * also for this but this function is added seprately just to simplify it. 
	 * @param   string   $table_name         The name of table to check value 
	 * @param   string   $field_name         The name of column to check value against
	 * @param   string   $field_val          Field value which needs to be checked in column name
	 *
	 * return   boolean                      Returns true if value exists else returns false
	*/	
	function dbCheckValue($table_name,$field_name,$field_val)
	{
		try 
		{	
			$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->message_info="Connected to database";
			$this->query="SELECT ".$this->backticks.$field_name.$this->backticks." FROM ".$this->backticks.$table_name.
						  $this->backticks." WHERE ".$this->backticks.trim($field_name).$this->backticks."=?";
  		    $stmt = $this->dbh->prepare($this->query);
			$stmt->execute(array($field_val));
			$result=true;
			if($stmt->rowCount()==0)
				$result=false;					
		    $this->dbh = NULL;
			
			if($this->resetAllSettings==true)			
				$this->resetSettings();			
			return $result;
		}
		catch(PDOException $e)
		{
			$this->error_info=$e->getMessage();
		}		
		
	}
	/**
	 * Retrives the column names from a given table
	 * @param   string  $table    The name of the table to get columns.
	 *
	 * return   array             column name in array
	*/	
	function dbGetColumnName($table_name)
	{
		try 
		{	
			$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->message_info="Connected to database";
			$this->query="DESCRIBE $table_name";
  		    $stmt = $this->dbh->prepare($this->query);
			$stmt->execute();
			$result= $stmt->fetchAll(PDO::FETCH_COLUMN);;
		    $this->dbh = NULL;
			if($this->resetAllSettings==true)
				$this->resetSettings();	
			
			return $result;
		}
		catch(PDOException $e)
		{
			$this->error_info=$e->getMessage();
		}	
	}
	
	/**
	 * Retrives the primary key of a given table
	 * @param   string  $table       The name of table to get the primary key
	 *
	 * return   array                result of query as array
	*/
	function dbGetPrimaryKey($table_name)
	{
		try 
		{	
			$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->message_info="Connected to database";
			$this->query="SHOW INDEXES FROM $table_name WHERE Key_name = 'PRIMARY'";
  		    $stmt = $this->dbh->prepare($this->query);
			$stmt->execute();
			$result= $stmt->fetchAll(PDO::FETCH_COLUMN);;
		    $this->dbh = NULL;			
			if($this->resetAllSettings==true)
				$this->resetSettings();			
			return $result;
		}
		catch(PDOException $e)
		{
			$this->error_info=$e->getMessage();
		}	
	}
	
	

	/******************************************** General Functions **********************************************************/	

	/**
	 * Generate the standard html form based on the columns of the table of database. It will 
	 * by default create input type='text' for all columns of the table. You can copy
	 * source code and modify it as per your requirement.
	 *
	 * @param   string/array  $table_name         Single Table name or array of tables for which html form needs to be generated
	 * @param   string        $action_url         Html form action parameter(default is "", i.e. same page)
	 * @param   string        $method             Html form submission method(GET or POST)(Default value=POST)
	 * @param   string        $input_css_class    Css class for all input type text
	 *
	 * return   string                            Html form
	 */
	 
	function getHtmlFormWithDBTable($table_name,$action_url="",$method="POST",$input_css_class="textfield")
	{
		$columns=array();
		$columns=$this->dbGetColumnName($table_name);
		
		echo "<form action='$action_url' method='$method' class='frm_simplifieddb'>";
		echo "<fieldset>";
	
		foreach($columns as $column)
		{		
			?>
			<dl>
				<dt><label for="<?php echo $column;?>"><?php echo ucfirst(str_replace("_"," ",$column));?></label></dt>
				<dd><input type="text" name="<?php echo $column;?>" id="<?php echo $column;?>" class="<?php echo $input_css_class;?>" /></dd>
			</dl>
		<?php
        }
		echo "</fieldset>";
		echo "</form>";
	}

	/**
	 * Generates the html table as output from the array provided. You can pass select operation result directly 
	 * as an input array or pass a manually created associative array .
	 * @param   array     $input_array             Associative array with key as column name and value as table values.
	 * @param   string    $table_css_class         Css class for html table
	 * @param   string    $tr_css_class            Css class for tr
	 *
	 * return   string                 			   returns the display in html table format
	*/	
	function getHtmlTableFromArray($input_array,$table_css_class="sdb_tbl_cls",$tr_css_class="sdb_tr_cls")
	{
		$table_output="<table class='".$table_css_class."'>";
		$table_head="<thead><tr class='".$tr_css_class."'>";
		$table_body="<tbody>";
		$loop_count=0;
		
		foreach($input_array as $k=>$v)
		{
			$table_body.="<tr class='".$tr_css_class."' id='tr_".$loop_count."'>";			
			foreach($v as $col=>$row)
			{				
				if($loop_count==0)
					$table_head.="<td>".$col."</td>";								
				
				$table_body.="<td>".$row."</td>";	
			}
			$table_body.="</tr>";
			$loop_count++;
		}		
		
		$table_head.="</thead></tr>";
		$table_body.="</tbody>";
		$table_output=$table_output.$table_head.$table_body."</table>";
		
		return $table_output;
	}
	
	/**
	 * Retrives all the tables from database
	 *
	 * return   array             table names in array
	*/	
	function dbGetTableName()
	{
		try 
		{	
			$this->dbh = new PDO("sqlsrv:server = tcp:$this->host_name;Database=$this->db_name", $this->user_name, $this->password);	
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->message_info="Connected to database";
			$this->query="show tables";
  		    $stmt = $this->dbh->prepare($this->query);
			$stmt->execute();
			$result= $stmt->fetchAll($this->getPDOFetchmode());;
		    $this->dbh = NULL;
			if($this->resetAllSettings==true)
				$this->resetSettings();
			return $result;
		}
		catch(PDOException $e)
		{
			$this->error_info=$e->getMessage();
		}	
	}
	
    /* Select all records from the table, short-hand function for retriving all records from table.   
	 * @param   string   $table_name                  The name of the table to select records.
	 * return   array                                 returns array as result of query.
	 */
	
	function dbGetAll($tablename)
	{
	  $this->single_row=false;
	  return $this->dbSelect($tablename);	  	
	}
	
	
	/* Get single value of variable(coulmn name), based on the where condition	
	 * @param   string   $table_name                  The name of the table to select records.
	 * @param   array    $variable                    The name of variable(column name) to retrive value
	 * @param   array    $select_where_condition      Associative array with key as column name and 
	 												  values as column value for where clause.	
	 * return   string                                returns variable(column) value as string
	
	*/
	function dbGetVariable($tablename,$variable,$select_where_condition=array())
	{
	   $this->single_row=true;
	   $result=$this->dbSelect($tablename,array($variable),$select_where_condition);
	   $this->single_row=false;
	   return $result[$variable];
	}
	
/*********************************************************** Internal Functions ******************************************/

  /*Returns column names */
  private function getColumns($columns=array())
   {
	   $col="*";
	   if(count($columns)>0&&is_array($columns))
		{		
		 $col="";	
  		 foreach($columns as $column)
 		  {
			$col=$col.$this->backticks.trim($column).$this->backticks.",";
		  }
		  $col=rtrim($col,",");
	    }
		return $col;
   }
   
  /*Returns where condition */   
  private function getWhereCondition($select_where_condition=array())
   {
		$where_condition="";
		$matches=array();
		$sameColName;
	   	if(is_array($select_where_condition))
		{
			foreach($select_where_condition as $cols => $vals)
			{
				$compare="=";	
				if(preg_match("#([^=<>!]+)\s*(=|(>=)|(<=)|(>=)|<|>|(!=))#", strtolower(trim($cols)), $matches))
				{
					$compare=$matches[2];
					$cols=trim($matches[1]);
				}
				if(isset($sameColName)&&$this->isSameColumns)
					$cols=$sameColName;
									
				$this->values[]=$vals;							
				$where_condition=$where_condition." ".$this->backticks.$cols.$this->backticks.$compare."? ".$this->and_or_condition;
				
				if($this->isSameColumns)
					$sameColName=$cols;
			}
			if($where_condition)
				$where_condition=" WHERE ".rtrim($where_condition,$this->and_or_condition);					
		}  
		return $where_condition;	   
   }
   
  /*Returns like condition */      
   private function getLikeCondition($where_condition="")
   {
	   if(is_array($this->like_cols)&&count($this->like_cols)>0)
		{		
			$like="";	
			foreach($this->like_cols as $cols => $vals)
			{
				$like.=$this->backticks.$cols.$this->backticks." Like ? ".$this->and_or_condition;
				$this->values[]=$vals;
			}
			
			if($where_condition)
				$where_condition.=" ".$this->and_or_condition." ".rtrim($like,$this->and_or_condition);
			else
				$where_condition=" WHERE ".rtrim($like,$this->and_or_condition);
		}
		return $where_condition;
   }
   
  /*Returns between condition */      
   private function getBetweenCondition($where_condition="")
   {
	   if(is_array($this->between_columns)&&count($this->between_columns)>0)
		{		
			reset($this->between_columns);
			$between=key($this->between_columns)." BETWEEN ? and ?";	
			
			foreach($this->between_columns as $cols => $vals)
			{			
				$this->values[]=$vals;
			}
			
			
			if($where_condition)
				$where_condition.=" ".$this->and_or_condition." ".$between;
			else
				$where_condition=" WHERE ".$between;
		}
		
		return $where_condition;	
   }
   
  /*Returns in condition */      
   private function getInCondition($where_condition="")
   {
	   if($this->in&&count($this->in)>0)
		{
			$in="";	
			foreach($this->in as $cols => $vals)
			{
				$in.=$this->backticks.$cols.$this->backticks." IN (".$vals.") ".$this->and_or_condition;
			}
			
			if($where_condition)
				$where_condition.=" ".$this->and_or_condition." ".rtrim($in,$this->and_or_condition);
			else
				$where_condition=" WHERE ".rtrim($in,$this->and_or_condition);
		}
		return $where_condition;	
   }
   
  /*Returns not in condition */      
   private function getNotInCondition($where_condition="")
   {
	   if($this->not_in&&count($this->not_in)>0)
		{
			$not_in="";	
			foreach($this->not_in as $cols => $vals)
			{
				$not_in.=$this->backticks.$cols.$this->backticks." NOT IN (".$vals.") ".$this->and_or_condition;
			}
			
			if($where_condition)
				$where_condition.=" ".$this->and_or_condition." ".rtrim($not_in,$this->and_or_condition);
			else
				$where_condition=" WHERE ".rtrim($not_in,$this->and_or_condition);
		}
		return $where_condition;
   }
   
  /*Returns group by condition */      
   private function getGroupByCondition($where_condition="")
   {
	   	if($this->group_by_column)
			$where_condition.=" GROUP BY ".$this->group_by_column;
			
		if($this->group_by_column&&$this->having)
			$where_condition.=" HAVING ".$this->having;
		
		return $where_condition;	
   }
   
  /*Returns order by  condition */      
   private function getOrderbyCondition($where_condition="")
   {
	   if($this->order_by_column)
			$where_condition.=" ORDER BY ".$this->order_by_column;	
			
	   return $where_condition;	
   }
   
  /*Returns limit condition */      
   private function getLimitCondition($where_condition="")
   {
	   	if($this->limit_val)
			$where_condition.=" LIMIT ".$this->limit_val;
			
	   return $where_condition;	
   }
   
  /*Returns join condition */      
   private function getTableJoins($table_names,$join_conditions,$join_type)
   {
	   if(is_array($table_names))
		{	
			$loop_table=0;		
			
			foreach($table_names as $table_name)
			{
				if($loop_table==0)
					$table_join=$this->backticks.trim($table_name).$this->backticks;
				else
					$table_join.=" ".$join_type[$loop_table-1]." ".$this->backticks.$table_name.
							$this->backticks." ON ".$join_conditions[$loop_table-1];
					
				$loop_table++;
			}
		}	
		return $table_join;
   }
   
   /**
	 * Returns the current fetch mode for the pdo.
	 * return   long       fetch mode for the pdo.
	*/	
	private function getPDOFetchmode()
	{
		switch ($this->fetch_mode)
		{
			case "BOTH":  return PDO::FETCH_BOTH;
			case "NUM":   return PDO::FETCH_NUM;
			case "ASSOC": return PDO::FETCH_ASSOC;
			case "OBJ":   return PDO::FETCH_OBJ;
			case "COLUMN":return PDO::FETCH_COLUMN;
			default:      return PDO::FETCH_ASSOC;
		}		
	}	
	
	/**
	 * Reset all values to default values
	*/	
   private function resetSettings()
	{
		$this->and_or_condition="and";
		$this->group_by_column="";     
		$this->order_by_column="";     
		$this->limit_val="";           
		$this->having="";              
		$this->between_columns=array();
		$this->in=array();             
		$this->not_in=array();         
		$this->like_cols=array();      				
		$this->is_sanitize=true;       
		$this->single_row=false;       
		$this->backticks="`";         
		$this->fetch_mode="ASSOC";		
		$this->isSameColumns=false;
	}
}
?>