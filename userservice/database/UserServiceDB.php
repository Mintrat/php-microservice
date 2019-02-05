<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 22.11.2018
 * Time: 21:16
 */

namespace MicroService\Database;

use MicroService\Database\Table;


class UserServiceDB
{
    private $propertiesDB;
    private $db;
    private static $pathToProperties = __DIR__ . '/../service.properties.php';

    function __construct()
    {
        $this->retrieveProperties();
        $this->db = $this->connectDB();
    }

    /** instance Table if table exist in database
     * @param $name string - table name
     * @return object - Tables
     */
    function __get($name)
    {
        if (!$this->$name && $this->tableExist($name)) {
            $this->$name = new Table($name, $this->db);
        }

        return $this->$name;
    }

    /**
     * write options in propertiesDB
     * @throws \Exception else not found file with DB options
     */
    private function retrieveProperties()
    {
        $pathToProperties = self::$pathToProperties;
        if (file_exists($pathToProperties)) {
            $properties = include $pathToProperties;
            $this->checkPropertiesDB($properties);
            $this->propertiesDB = $properties;
        } else {
            throw new \Exception('File with properties for database not found');
        }
    }

    /**
     * validates parameters
     * @param $properties
     * @throws \Exception
     */
    private function checkPropertiesDB($properties)
    {
        $errors = '';
        foreach ($properties as $key => $value) {
            if ($key === 'db_password') {
                continue;
            }

            if (empty($properties[$key])) {
                $errors .= $key . ' is empty' . PHP_EOL;
            }
        }

        if (!($errors == '')) {
            throw new \Exception($errors);
        }
    }

    /**
     * create connect DB
     * @return \PDO
     */
    private function connectDB()
    {
        $dbName = $this->propertiesDB['db_name'];
        $dbHost = $this->propertiesDB['db_host'];
        $dbPassword = $this->propertiesDB['db_password'];
        $dbUser = $this->propertiesDB['db_user'];

        $db = new \PDO("mysql:host={$dbHost};dbname=", $dbUser, $dbPassword);
        if (!$this->dbExists($db, $dbName)) {
            $this->createDB($db);
        }
        $db->query("use {$dbName}");
        return $db;
    }

    /**
     * check exist database
     * @param \PDO $db
     * @param string $dbName
     * @return bool
     */
    private function dbExists(\PDO  $db, string $dbName)
    {
        $lisResult = $db->query('show databases');
        foreach ($lisResult as $row) {
            if ($row['Database'] == $dbName) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param \PDO $db
     * @throws \Exception
     */
    private function createDB(\PDO $db)
    {
        $db->query($this->getSQLScript('createDB'));
    }

    /**
     * return sql script for execute
     * @param string $name
     * @return false|string
     * @throws \Exception
     */
    private function getSQLScript(string $name)
    {
        $path = __DIR__ . "/sql/{$name}.sql";
        if (file_exists($path)) {
            return file_get_contents($path);
        } else {
            throw new \Exception('File with sql for create database not found');
        }
    }

    /** create default authority in database
     * @throws \Exception
     */
    function createDefaultAuthority()
    {
        $query = $this->getSQLScript('insertAuthority');
        $this->db->query($query);
    }

    /**
     * check table exist in database
     * @param string $tableName
     * @return bool
     */
    function tableExist(string $tableName)
    {
        $tableList = $this->getTables();
        if (in_array($tableName, $tableList)) {
            return true;
        }
        return false;
    }

    /**
     * get all tables from database
     * @return array
     */
    function getTables()
    {
        $tables = $this->db->query('show tables');
        $listTable = [];
        while ($result = $tables->fetch(\PDO::FETCH_ASSOC)) {
            foreach ($result as $table) {
                $listTable[] = $table;
            }
        }
        return $listTable;
    }

    /**
     * begin \PDO transaction
     */
    function beginTransaction()
    {
        $this->db->beginTransaction();
    }

    /**
     * make commit \PDO
     */
    function commit()
    {
        $this->db->commit();
    }
}