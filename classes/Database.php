<?php

class Database
{
    protected $_db_host;
    protected $_db_name;
    protected $_db_user;
    protected $_db_pass;

    public function __construct($host, $name, $user, $password)
    {
        $this->_db_host = $host;
        $this->_db_name = $name;
        $this->_db_user = $user;
        $this->_db_pass = $password;
    }

    public function getConn()
    {

        $dsn = 'mysql:host=' . $this->_db_host . ';dbname=' . $this->_db_name . ';charset=utf8';

        try {
            $db = new PDO($dsn, $this->_db_user, $this->_db_pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $db;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
