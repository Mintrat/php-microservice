<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 22.11.2018
 * Time: 21:16
 */

namespace MicroService;


class UserServiceDB
{
    private $propertiesDB;
    public $db;

    function __construct()
    {
        $this->retrieveProperties();
        $this->db = $this->connectDB();
    }

    private function retrieveProperties()
    {
        $pathToproperties = __DIR__ . '/service.properties.php';
        if (file_exists($pathToproperties)) {
            $this->propertiesDB = include $pathToproperties;
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
        if ($this->dbExists($db, $dbName)) {
            $db->query("use {$dbName}");
            return $db;
        } else {
            $this->createDB($db);
            $db->query("use {$dbName}");
            return $db;
        }
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
        $db->query($this->getSQLScript());
    }

    private function getSQLScript()
    {
        $path = __DIR__ . '/services.sql';
        if (file_exists($path)) {
            return file_get_contents($path);
        } else {
            throw new \Exception('File with sql for create database not found');
        }
    }
}