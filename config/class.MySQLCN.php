<?php

class MySQLCN {

    function __construct() {


        // $user = "konarkwe_giftapp";
        // $pass = "tYr3Lq7cmaHPHJNr5eyc";
        // $server = "localhost";
		// $dbase = "konarkwe_giftapp";

        $user = "root";
        $pass = "";
        $server = "localhost";
		$dbase = "gift_notification_dev";
        $conn = mysqli_connect($server, $user, $pass, $dbase);
        mysqli_set_charset($conn,"utf8");

        //$conn = mysql_connect($server,$user,$pass);
        if (!$conn || mysqli_connect_errno()) {
            $this->error("Connection attempt failed");
        }

        //if(!mysql_select_db($dbase,$conn)) {
        //$this->error("Dbase Select failed");
        //}

        $this->CONN = $conn;
        return true;
    }

    function close() {
        $conn = $this->CONN;
        $close = mysqli_close($con);
        if (!$close) {
            $this->error("Connection close failed");
        }
        return true;
    }

    function error($text) {
        $conn = $this->CONN;
        $no = mysqli_errno($conn);
        $msg = mysqli_error($conn);
        ;
        exit;
    }

    function select($sql = "") {
        if (empty($sql)) {
            return false;
        }
        if (!preg_match("/^select/i", $sql)) {
            echo "Wrong Query<hr>$sql<p>";
            echo "<H2>Wrong function silly!</H2>\n";
            return false;
        }
        if (empty($this->CONN)) {
            return false;
        }
        $conn = $this->CONN;
        $results = mysqli_query($conn, $sql);
        if ((!$results) or ( empty($results))) {
            return false;
        }
        $count = 0;
        $data = array();
        while ($row = mysqli_fetch_array($results)) {
            $data[$count] = $row;
            $count++;
        }
        mysqli_free_result($results);
        return $data;
    }

    function insert($sql = "") {
        if (empty($sql)) {
            return false;
        }
        if (!preg_match("/^insert/i", $sql)) {
            return false;
        }
        if (empty($this->CONN)) {
            return false;
        }
        $conn = $this->CONN;
        $results = mysqli_query($conn, $sql);
        if (!$results) {
            echo "Insert Operation Failed..<hr>" . mysqli_error($this->CONN);
            $this->error("Insert Operation Failed..");
            $this->error("<H2>No results!</H2>\n");
            return false;
        }
        $id = mysqli_insert_id($this->CONN);
        return $id;
    }

    //Dont remove this - Added by sreejan//
    function adder($sql = "") {
        if (empty($sql)) {
            return false;
        }
        if (!preg_match("/^insert/i", $sql)) {
            return false;
        }
        if (empty($this->CONN)) {
            return false;
        }
        $conn = $this->CONN;
        $results = @mysql_query($sql, $conn);

        if (!$results)
            $id = "";
        else
            $id = mysql_insert_id();

        return $id;
    }

    function edit($sql = "") {
        if (empty($sql)) {
            return false;
        }
        if (!preg_match("/^update/i", $sql)) {
            return false;
        }
        if (empty($this->CONN)) {
            return false;
        }
        $conn = $this->CONN;
        $results = mysqli_query($conn, $sql);
        if (!$results) {
            $this->error("<H2>No results!</H2>\n");
            return false;
        }
        $rows = 0;
        $rows = mysqli_affected_rows($conn);
        return $rows;
    }

    function sql_query($sql = "") {
        if (empty($sql)) {
            return false;
        }
        if (empty($this->CONN)) {
            return false;
        }
        $conn = $this->CONN;
        $results = mysqli_query($conn, $sql) or die("Query Failed..<hr>" . mysqli_error($conn));
        if (!$results) {
            $message = "Query went bad!";
            $this->error($message);
            return false;
        }
        // (Martin Huba) also SHOW... commands return some results
        if (!(preg_match("/^select/i", $sql) || preg_match("/^show/i", $sql))) {
            return true;
        } else {
            $count = 0;
            $data = array();
            while ($row = mysqli_fetch_array($results)) {
                $data[$count] = $row;
                $count++;
            }
            mysqli_free_result($results);
            return $data;
        }
    }
	
	function delete($sql = "") {
        if (empty($sql)) {
            return false;
        }
        if (!preg_match("/^delete/i", $sql)) {
            echo "Wrong Query<hr>$sql<p>";
            echo "<H2>Wrong function silly!</H2>\n";
            return false;
        }
        if (empty($this->CONN)) {
            return false;
        }
        $conn = $this->CONN;
        $results = mysqli_query($conn, $sql);
        if ((!$results) or ( empty($results))) {
            return false;
        }
    }

//ends the class over here
}

?>