<?php
require_once 'Database.class.php';
class Model extends Database{

    public $dbh = null;
    public $error = "";

    public function __construct() {
        $this->dbh = new Database();
        $this->dbh->dbConnect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    }

    public function __destruct() {
        $this->dbh = null;
    }

    /**
     * Function takes an array and removes any entries with NULL values or entries where the key is an id.
     * @param array $array
     * @return array
     */
    public function appendArray($array){
        $queryArray = array();
        foreach ($array as $key => $value) {
            if(($value == NULL || empty($value)) && !is_bool($value) && ($value !== 0 || $value !== '0')){
                continue;
            } else {
                $queryArray[$key] = $value;
            }
        }
        return $queryArray;
    }

    /**
     * @param $table string
     * @param $insert array
     * @return mixed|bool|array Returns FALSE if query failed. Returns an array of the inserted query if successful.
     */
    public function insert($table, $insert){
        $this->dbh->dbInsert($table, $insert);
        if($this->dbh->rows_affected > 0){
            return $this->dbh->last_insert_id;
        } else {
            return FALSE;
        }
    }

    /**
     * @param $table
     * @param $update
     * @param $condition
     * @return bool Returns TRUE if query was successful. Returns FALSE if an error occurred.
     */
    public function update($table, $update, $condition){
        $this->dbh->dbUpdate($table, $update, $condition);
        if($this->dbh->rows_affected >= 0 && !is_null($this->dbh->rows_affected)){
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @param $table
     * @param array $select
     * @param array $condition
     * @param bool $statement
     * @return array|bool|mixed Returns True if query was successful but no results exist. Returns FALSE if an error occurred
     * and returns an array of the results if any records were found.
     */
    public function select($table, $select = array(), $condition = array(), $statement = FALSE, $state = ""){
        if($statement){
            if($statement !== TRUE){
                switch($statement){
                    case "is_null":
                        $this->is_null = "is_null";
                        break;
                    case "gbc":
                        $this->dbh->group_by_column = $state;
                        break;
                }
            }else{
                $this->dbh->and_or_condition = "or";
            }
        }
        if (is_string($statement))
        {
            switch($statement)
            {
                case "gbc":
                    $this->dbh->group_by_column = $state;
                    break;
            }
        }
        $query = $this->dbh->dbSelect($table, $select, $condition);
        if($this->dbh->rows_returned >= 0){
            return $query;
        } else {
            return FALSE;
        }
    }

    /**
     * @param $tables
     * @param $joins
     * @param $join_conditions
     * @param array $where
     * @param array $columns
     * @return array|bool Returns TRUE if query was successful but no results are available. Returns FALSE if query failed.
     * Returns an array of requested data.
     */
    public function join($tables, $joins, $join_conditions, $where = array(), $columns = array(), $statement = FALSE, $state = ""){
        if (is_string($statement))
        {
            switch($statement)
            {
                case "gbc":
                    $this->dbh->group_by_column = $state;
                    break;
            }
        }
        $query = $this->dbh->dbSelectJoin($tables, $join_conditions, $joins, $columns, $where);
        if($this->dbh->rows_returned >= 0){
            if($this->dbh->rows_returned > 0){
                return $query;
            } else {
                return TRUE;
            }
        } else {
            return FALSE;
        }
    }

    public function delete($table, $delete){
        $this->dbh->dbDelete($table, $delete);
        if($this->dbh->rows_affected > 0){
            return TRUE;
        } else {
            return FALSE;
        }
    }



}