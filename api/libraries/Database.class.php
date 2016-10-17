<?php

/**
 * Created by IntelliJ IDEA.
 * User: Lebelo Nkadimeng
 * Date: 2016/10/16
 * Time: 6:20 PM
 */
class Database
{
    public $error_info="";			
    public $message_info="";        
    private $dbh=NULL;              
    public $user_name;	            
    public $password;               
    public $host_name;              
    public $db_name;                
    private $values=array();        
    public $query;                  
    public $rows_affected;          
    public $count_rows;             
    public $last_insert_id;         
    public $and_or_condition="and"; 
    public $group_by_column="";     
    public $order_by_column="";     
    public $limit_val="";           
    public $having="";              
    public $between_columns=array();
    public $in=array();             
    public $not_in=array();         
    public $like_cols=array();     		
    public $is_sanitize=true;       
    public $single_row=false;       
    public $backticks="";          
    public $fetch_mode="ASSOC";		


    public $rows_returned=0;           
    public $resetAllSettings=false;    
    public $beginTransaction=false;	   
    public $commitTransaction=false;   
    private $rollbackTransaction=false;

    public $isSameWhereCondition=false;  
    public $isSameColumns=false;  
    
    /******************************************** PDO Functions **********************************************************/
    /**
     * Connects to database
     * @param   string $hostname Host/Server name
     * @param   string $user_name User name
     * @param   string $password Password
     * @param   string $dbname
     * @internal param string $database Database-name
     */
    function dbConnect($hostname,$user_name,$password,$dbname)
    {
        $this->host_name=$hostname;
        $this->user_name=$user_name;
        $this->password=$password;
        $this->db_name=$dbname;
    }

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
                    $this->dbh = new PDO("mysql:host=$this->host_name;dbname=$this->db_name", $this->user_name, $this->password);
                    $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->dbh->beginTransaction();
                }
            }
            else
            {
                $this->dbh = new PDO("mysql:host=$this->host_name;dbname=$this->db_name", $this->user_name, $this->password);
                $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }

            $this->message_info="Connected to database";
            
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
        $this->values=array();

        foreach($update_array as $col => $val)
        {
            $colums_val=$colums_val."".trim($col)."=?,";
            $this->values[]=$val;
        }
        $colums_val=rtrim($colums_val,",");

        $where_condition=$this->getWhereCondition($update_condition_array);
        $where_condition=$this->getOrderbyCondition($where_condition);
        $where_condition=$this->getLimitCondition($where_condition);

        try
        {
            if($this->rollbackTransaction&&$this->beginTransaction)
                return;
            if($this->beginTransaction==true)
            {
                if($this->dbh==NULL)
                {
                    $this->dbh = new PDO("mysql:host=$this->host_name;dbname=$this->db_name", $this->user_name, $this->password);
                    $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->dbh->beginTransaction();
                }
            }
            else
            {
                $this->dbh = new PDO("mysql:host=$this->host_name;dbname=$this->db_name", $this->user_name, $this->password);
                $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            
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
        $this->values=array();
        $where_condition=$this->getWhereCondition($delete_where_condition);
        $where_condition=$this->getOrderbyCondition($where_condition);
        $where_condition=$this->getLimitCondition($where_condition);

        try
        {
            if($this->rollbackTransaction&&$this->beginTransaction)
                return;
            if($this->beginTransaction==true)
            {
                if($this->dbh==NULL)
                {
                    $this->dbh = new PDO("mysql:host=$this->host_name;dbname=$this->db_name", $this->user_name, $this->password);
                    $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->dbh->beginTransaction();
                }
            }
            else
            {
                $this->dbh = new PDO("mysql:host=$this->host_name;dbname=$this->db_name", $this->user_name, $this->password);
                $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            $this->message_info="Connected to database";
            
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
     * Select records from the single table. You can provide columns to be selected and where clause with
     * associative array of conditions with keys as columns and value as column value. Along with these function parameters,
     * you can set group by columnname, order by columnname, limit, like, in , not in, between clause etc.
     * This function will automatically creates query for you and select data.
     * @param   string $table_name The name of the table to select records.
     * @param   array $columns Array of columns to be selected
     * @param   array $select_where_condition Associative array with key as column name and values as column value for where clause.
     * @return array|mixed returns array as result of query.
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
            $this->dbh = new PDO("mysql:host=$this->host_name;dbname=$this->db_name", $this->user_name, $this->password);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->message_info="Connected to database";
            
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
        return "";
    }

    /**
     * Select records from the multiple table with join keyword. You can provide columns to be selected and where clause with
     * associative array of conditions with keys as columns and value as column value, group by, order by , limit etc.
     * You needs to specify join condition between different tables and join type (left join, right join etc.) to select data.
     * This function will automatically creates query for you and select data.
     * @param   array    $table_names                  Array of tables to be joined.
     * @param   array    $join_conditions             Array of join conditions between tables
     * @param   array    $join_type                   Array of join types
     * @param   array    $columns                     Array of columns to be selected
     * @param   array    $select_where_condition      Associative array with key as column name and values as column value for where clause.
     * @return   array                                 returns result of query as array
     */
    function dbSelectJoin($table_names,$join_conditions,$join_type,$columns=array(),$select_where_condition=array())
    {
        $this->values=array();

        $col=$this->getColumns($columns);
        $table_join=$this->getTableJoins($table_names,$join_conditions,$join_type);
        $where_condition=$this->getWhereCondition($select_where_condition);
        $where_condition=$this->getLikeCondition($where_condition);
        $where_condition=$this->getBetweenCondition($where_condition);
        $where_condition=$this->getInCondition($where_condition);
        $where_condition=$this->getNotInCondition($where_condition);
        $where_condition=$this->getGroupByCondition($where_condition);
        $where_condition=$this->getOrderbyCondition($where_condition);
        $where_condition=$this->getLimitCondition($where_condition);

        try
        {
            $this->dbh = new PDO("mysql:host=$this->host_name;dbname=$this->db_name", $this->user_name, $this->password);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->message_info="Connected to database";
            
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
     * @return   array               result of the query
     */
    function dbExecuteQuery($query,$parameter_values=array())
    {
        try
        {
            $this->dbh = new PDO("mysql:host=$this->host_name;dbname=$this->db_name", $this->user_name, $this->password);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->message_info="Connected to database";
            
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
        return false;
    }

    /**
     * Checks whether particular field of table contains some specific value or not. Most of the times,
     * We needs to check for specific values like username,passwords etc. You can use the select functions
     * also for this but this function is added seprately just to simplify it.
     * @param   string   $table_name         The name of table to check value
     * @param   string   $field_name         The name of column to check value against
     * @param   string   $field_val          Field value which needs to be checked in column name
     *
     * @return   boolean                      Returns true if value exists else returns false
     */
    function dbCheckValue($table_name,$field_name,$field_val)
    {
        try
        {
            $this->dbh = new PDO("mysql:host=$this->host_name;dbname=$this->db_name", $this->user_name, $this->password);
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
        return false;
    }
    /**
     * Retrives the column names from a given table
     * @param   string  $table    The name of the table to get columns.
     *
     * @return   array             column name in array
     */
    function dbGetColumnName($table_name)
    {
        try
        {
            $this->dbh = new PDO("mysql:host=$this->host_name;dbname=$this->db_name", $this->user_name, $this->password);
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
        return false;
    }

    /**
     * Retrives all the tables from database
     *
     * @return   array             table names in array
     */
    function dbGetTableName()
    {
        try
        {
            $this->dbh = new PDO("mysql:host=$this->host_name;dbname=$this->db_name", $this->user_name, $this->password);
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
        return false;
    }

    /** Select all records from the table, short-hand function for retriving all records from table.   
	 * @param   string   $tablename                  The name of the table to select records.
	 * @return   array                                 returns array as result of query.
	 */
    function dbGetAll($tablename)
    {
        $this->single_row=false;
        return $this->dbSelect($tablename);
    }


    /** Get single value of variable(coulmn name), based on the where condition	
     * @param   string   $tablename                  The name of the table to select records.
     * @param   array    $variable                    The name of variable(column name) to retrive value
     * @param   array    $select_where_condition      Associative array with key as column name and 
                                                       values as column value for where clause.	
     * @return   string                                returns variable(column) value as string
    */
    function dbGetVariable($tablename,$variable,$select_where_condition=array())
    {
        $this->single_row=true;
        $result=$this->dbSelect($tablename,array($variable),$select_where_condition);
        $this->single_row=false;
        return $result[$variable];
    }

    /*********************************************************** Internal Functions ******************************************/
    
    /**
     * Returns column names
     * @param array $columns
     * @return string
     */
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
        $table_join = "";
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
        $this->backticks="";
        $this->fetch_mode="ASSOC";
        $this->isSameColumns=false;
    }
}
?>