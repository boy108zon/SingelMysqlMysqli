<?php

/*
 *  Created By: Boy108zon
 *  Below class handle mysql and mysqli
 *  connection queries
 *  Plz don't remove comments.
 */
require_once 'config.php';
Class MysqlDatabaseMysqli {

    protected $connection = NULL;
    protected $connection_string = NULL;
    protected $associate_type = NULL;

    function __construct() {

        global $resource_type;
        $this->resource_type_string = strtolower($resource_type);
        switch ($resource_type) {
            case 'MySql':
                $this->associate_type = MYSQL_ASSOC;
                $this->msl_connect();
                break;
            case 'MySqli':
                $this->associate_type = MYSQLI_ASSOC;
                $this->msli_connect();
                break;
            default:
                echo "No resource type define";
                exit;
                break;
        }
    }

    /*
     *  Created By: Boy108zon
     *  msl->mysql
     */

    function msl_connect() {
        global $dbconfig;
        try {
            if ($link_identifier = @mysql_connect($dbconfig['db_server'], $dbconfig['db_username'], $dbconfig['db_password'])) {
                $select_db = @mysql_select_db($dbconfig['db_name'], $link_identifier);
                if (!$select_db) {
                    throw new Exception(mysql_errno() . ' ' . mysql_error());
                }
                $this->connection = $link_identifier;
            } else {
                throw new Exception(mysql_errno() . ' ' . mysql_error());
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /*
     *  Created By: Boy108zon
     *  msli->mysqli
     */

    function msli_connect() {
        global $dbconfig;
        try {
            if ($link_identifier = @mysqli_connect($dbconfig['db_server'], $dbconfig['db_username'], $dbconfig['db_password'])) {
                $select_db = @mysqli_select_db($link_identifier, $dbconfig['db_name']);
                if (!$select_db) {
                    throw new Exception(mysqli_connect_errno() . ' Unable to select db');
                }
                $this->connection = $link_identifier;
            } else {
                throw new Exception(mysqli_connect_errno() . ' Unable to connect');
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /*
     *  Created By: Boy108zon
     *  @table_name: string
     *  @where: Array
     */

    function get_records($table_name, $where=NULL) {

        $connect = $this->resource_type_string . '_query';
        $num_rows = $this->resource_type_string . '_num_rows';
        $connect_close = $this->resource_type_string . '_close';

        /* if where included */
        $qwhere = '';
        if (is_array($where)) {
            $counter = 0;
            foreach ($where as $key => $value) {
                if ($counter > 0) {
                    $qwhere .= ' AND ';
                }
                $key = $key;
                $value = $value;
                $qwhere .= "$key = '$value'";
                $counter++;
            }
            $qwhere = 'WHERE ' . $qwhere;
        }
        $query = "SELECT * from $table_name $qwhere";

        if ($this->resource_type_string == 'mysqli') {
            $result_resource = $connect($this->connection, $query);
        } else {
            $result_resource = $connect($query, $this->connection);
        }

        $noOfrows = $num_rows($result_resource);
        if ($noOfrows > 0) {
            return $this->db_to_array($result_resource);
        } else {
            return NULL;
        }
        //close connection
        @$connect_close($this->connection);
    }

    /*
     *  Created By: Boy108zon
     *  @table_name: string
     *  @where: Array
     */

    function remove_records($table_name, $where=NULL) {

        $connect = $this->resource_type_string . '_query';
        $affected_rows = $this->resource_type_string . '_affected_rows';
        $connect_close = $this->resource_type_string . '_close';

        /* if where included */
        $qwhere = '';
        if (is_array($where)) {
            $counter = 0;
            foreach ($where as $key => $value) {
                if ($counter > 0) {
                    $qwhere .= ' AND ';
                }
                $key = $key;
                $value = $value;
                $qwhere .= "$key = '$value'";
                $counter++;
            }
            $qwhere = 'WHERE ' . $qwhere;
        }
        $query = "DELETE from $table_name $qwhere";

        if ($this->resource_type_string == 'mysqli') {
            $result_resource = $connect($this->connection, $query);
        } else {
            $result_resource = $connect($query, $this->connection);
        }

        $idds = $affected_rows($this->connection);
        if ($idds > 0) {
            return $idds;
        } else {
            return NULL;
        }
        //close connection
        @$connect_close($this->connection);
    }

    /*
     *  Created By: Boy108zon
     *  @result->mysql OR mysqli
     */

    function db_to_array($result) {

        $connection_string = $this->resource_type_string . '_fetch_array';
        $rows = array();
        while ($row = $connection_string($result, $this->associate_type)) {
            $rows[] = $row;
        }
        return $rows;
    }

    /*
     *  Created By: Boy108zon
     *  @table_name: String
     *  @post_array:Array
     *  @where:Array
     *  @mode:'save' OR edit
     *  Based on mode return type affected rows and latest insert id
     */

    function save_records($table_name, $post_array, $where=NULL, $mode='save') {

        $connect = $this->resource_type_string . '_query';
        $insert_id = $this->resource_type_string . '_insert_id';
        $affected_rows = $this->resource_type_string . '_affected_rows';
        $connect_close = $this->resource_type_string . '_close';

        $count = 0;
        $fields = '';
        foreach ($post_array as $col => $val) {
            if ($count > 0) {
                $fields .= ', ';
            }
            $col = $col;
            $val = $val;
            $fields .= "$col = '$val'";
            $count++;
        }

        $qwhere = '';
        if (is_array($where)) {
            $counter = 0;
            foreach ($where as $key => $value) {
                if ($counter > 0) {
                    $qwhere .= ' AND ';
                }
                $key = $key;
                $value = $value;
                $qwhere .= "$key = '$value'";
                $counter++;
            }
            $qwhere = 'WHERE ' . $qwhere;
        }

        if ($mode == 'edit') {
            $query = "UPDATE " . $table_name . " SET $fields $qwhere;";
        } else {
            $query = "INSERT INTO " . $table_name . " SET $fields;";
        }

        if ($this->resource_type_string == 'mysqli') {
            $result_resource = $connect($this->connection, $query);
        } else {
            $result_resource = $connect($query, $this->connection);
        }

        if ($mode == 'edit') {
            $idds = $affected_rows($this->connection);
        } else {
            $idds = $insert_id($this->connection);
        }

        if ($idds > 0) {
            return $idds;
        } else {
            return NULL;
        }
        //close connection
        @$connect_close($this->connection);
    }

    /*
     *  Created By: Boy108zon
     *  @query: full query including inner , where
     *  if required we can also use it.
     */

    function get_direct_query_records($query) {

        $connect = $this->resource_type_string . '_query';
        $num_rows = $this->resource_type_string . '_num_rows';
        $connect_close = $this->resource_type_string . '_close';

        if ($this->resource_type_string == 'mysqli') {
            $result_resource = $connect($this->connection, $query);
        } else {
            $result_resource = $connect($query, $this->connection);
        }

        $noOfrows = $num_rows($result_resource);
        if ($noOfrows > 0) {
            return $this->db_to_array($result_resource);
        } else {
            return NULL;
        }

        //close connection
        @$connect_close($this->connection);
    }

}

?>