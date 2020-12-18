<?php

namespace app\models\lib;

use PDO;
use PDOException;

class Database {
    /**
     * @var Database
     */
    protected static $_dbInstance = null;
    /**
     * @var PDO
     */
    protected $_dbHandle;
    /**
     * @return Database
     */
    public static function getInstance()
    {
        if (self::$_dbInstance === null) {

            if ($GLOBALS['LOCAL_DB']) {
                $username ='root';
                $password = '';
                $host = 'localhost';
                $dbName = 'sgb779_cli_serv_auction';
            } else {
                $username ='sgb779';
                $password = $_ENV['DB_PASSWORD'];
                $host = 'poseidon.salford.ac.uk';
                $dbName = $_ENV['DB_NAME'];
            }

            //checks if the PDO exists

            // creates new instance if not, sending in connection info
            self::$_dbInstance = new self($username, $password, $host, $dbName);
        }
        return self::$_dbInstance;
    }
    /**
     * @param $username
     * @param $password
     * @param $host
     * @param $database
     */
    private function __construct($username, $password, $host, $database)
    {
        try {
            $this->_dbHandle = new PDO("mysql:host=$host;dbname=$database",  $username, $password); // creates the database handle with connection info
            $this->_dbHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_dbHandle->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            //$this->_dbHandle = new PDO('mysql:host=' . $host . ';dbname=' . $database,  $username, $password); // creates the database handle with connection info
        }
        catch (PDOException $e) { // catch any failure to connect to the database
            echo $e->getMessage();
        }
    }
    /**
     * @return PDO
     */
    public function getdbConnection()
    {
        return $this->_dbHandle; // returns the PDO handle to be usedelsewhere
    }
    public function __destruct()
    {
        $this->_dbHandle = null; // destroys the PDO handle when nolonger needed
    }

    public static function db() {
        return Database::getInstance()->getdbConnection();
    }
}