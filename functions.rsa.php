<?php
include_once("../config.php");
/**
 * UI Section
 */
function getTopLink(){

}

/**
 * Management Section
 */
class Management
{
    var $conn;
    function __construct(){
        include("class.db.config.php");
        $this->conn = getMYSQLIConnection();
    }
    /**
     *Verify username & password& return access level
     * Access level can be :
     * -2   : account suspended
     * -1   : username invalid
     *  0   : password invalid
     *  1   : normal client
     *  2   : admin
     *  3   : super admin
     * @param $email
     * @param $password
     * @return string
     */
    public function login($email, $password)
    {
        $access = '0';
        if ($email == "client") $access = 1;
        if ($email == "admin") $access = 2;

        $_SESSION['access_level'] = $access;
        return $access;
    }


    /**
     * @param $username
     * @param $password
     * @param $access_level
     * @param $detail
     * @return string
     */
    public function register($username, $password, $access_level, $detail)
    {
        $v1="'" . $this->conn->real_escape_string($username) . "'";

        $sql="INSERT INTO tbl (col1_varchar, col2_number) VALUES ($v1,10)";

        if($conn->query($sql) === false) {
            trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
        } else {
            $last_inserted_id = $conn->insert_id;
            $affected_rows = $conn->affected_rows;
        }

    }

    public function getCredit(){

    }
}


class client
{

    var $username ="007";
    function getCredits()
    {

    }

    function addCredit(){

    }

    function addTransaction(){

    }

    function commitTransaction(){

    }

    function addScan(){

    }

    function getScans(){

    }

    function getScan(){

    }

    function getReports(){

    }

    function getReport(){

    }
}
