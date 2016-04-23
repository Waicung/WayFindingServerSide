<?php

/*
 *A class file to connect to database
 */

class DB_CONNECT
{

    // constructor
    function __construct()
    {
        
    }

    // destructor
    function __destruct()
    {
        // closing db connection
        $this->close();
    }

    function connect()
    {
        // import database connection variables
        require_once __DIR__ . '/db_config.php';

        // Connecting to mysql database
        $con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
        // Check connection
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        } else {
            // returning connection cursor
            return $con;
        }
    }

    function close() {
        // closing db connection
        mysqli_close(self::connect());
    }

}

?>

?>