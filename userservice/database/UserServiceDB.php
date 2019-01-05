<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 22.11.2018
 * Time: 21:16
 */

namespace MicroService\Database;

use MicroService\Database\Tables;


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

    function __get($name)
    {
        if (!$this->$name && $this->tableExist($name)) {
            $this->$name = new Tables($name, $this->db);
        }

        return $this->$name;
    }

    private function retrieveProperties()
    {
        $pathToProperties = self::$pathToProperties;
        if (file_exists($pathToProperties)) {
            $this->propertiesDB = include $pathToProperties;
            $this->checkPropertiesDB($this->propertiesDB);
        } else {
            throw new \Exception('File with properties for database not found');
        }
    }

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

        if (!$errors == '') {
            throw new \Exception($errors);
        }
    }

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

    private function dbExists($db, $dbName)
    {
        $lisResult = $db->query('show databases');
        foreach ($lisResult as $row) {
            if ($row['Database'] == $dbName) {
                return true;
            }
        }
        return false;
    }

    private function createDB(\PDO $db)
    {
        $db->query($this->getSQLScript('createDB'));
    }

    private function getSQLScript($name)
    {
        $path = __DIR__ . "/sql/{$name}.sql";
        if (file_exists($path)) {
            return file_get_contents($path);
        } else {
            throw new \Exception('File with sql for create database not found');
        }
    }

    function createDefaultAuthority()
    {
        $query = $this->getSQLScript('insertAuthority');
        $this->db->query($query);
    }

    function tableExist($tableName)
    {
        $tableList = $this->getTables();
        if (in_array($tableName, $tableList)) {
            return true;
        }
        return false;
    }

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
}