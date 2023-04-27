<?php
namespace UChicago\AdvisoryCouncil\Data;

use PDO;

class Database
{
    private $_db;

    public function __construct()
    {
        try{
            $db_path = $_SERVER['DOCUMENT_ROOT']."/db/profiles.db";
            $this->_db = new \PDO("sqlite:".$db_path);
            $this->_db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY , false);
        } catch (\PDOException $exception ){
            print $exception->getMessage();
        }
    }

    public function set( $table_name, $data ){
        $sql = "update ".$table_name." set data = ? where pk = 1";
        try {
            $sth = $this->_db->prepare($sql);
            $serialized_data = serialize($data);
            return $sth->execute(array($serialized_data));
        } catch ( \PDOException $exception){
            print $exception->getMessage();
        }
    }
}