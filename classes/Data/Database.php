<?php
namespace UChicago\AdvisoryCouncil\Data;

use PDO;

class Database
{
    private $_db;

    public function __construct()
    {
        try{
            $this->_db = new \PDO("sqlite:./db/committee_data.db");
            $this->_db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY , false);
        } catch (\PDOException $exception ){
            print $exception->getMessage();
        }
    }

    public function set( $table_name, $data ){
        $sql = "update ".$table_name." set data = ? where pk = 1";
        try {
            $sth = $this->_db->prepare($sql);
            return $sth->execute(serialize(array($data)));
        } catch ( \PDOException $exception){
            print $exception->getMessage();
        }
    }
}