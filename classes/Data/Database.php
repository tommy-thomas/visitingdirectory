<?php
namespace UChicago\AdvisoryCouncil\Data;

use PDO;
use UChicago\AdvisoryCouncil\Application;

class Database
{
    private $_db;
    private $tables = array('member_data','membership_data');

    public function __construct()
    {
        try{
            $this->_db = new \PDO("sqlite:".Application::DB_PATH);
            $this->_db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY , false);
        } catch (\PDOException $exception ){
            print $exception->getMessage();
        }
    }

    public function set( $table_name, $data ){
        $sql = "update ".$this->cleanTable($table_name)." set data = ? where pk = 1";
        try {
            $sth = $this->_db->prepare($sql);
            return $sth->execute(array(serialize($data)));
        } catch ( \PDOException $exception){
            print $exception->getMessage();
        }
    }

    public function get( $table_name){
        $sql = "select data from ".$this->cleanTable($table_name)." where pk = 1";
        try {
            $sth = $this->_db->prepare($sql);
            $sth->execute();
            return unserialize($sth->fetchAll()[0]['data']);
        } catch ( \PDOException $exception){
            print $exception->getMessage();
        }
    }

    public function cleanTable($table){
        return in_array($table, $this->tables) ? sqlClean($table) : "";
    }
}