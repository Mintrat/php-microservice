<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 03.12.2018
 * Time: 19:51
 */

namespace MicroService\Database;


class Tables
{
    private $table;
    private $db;
    private $lastInsertId;

    function __construct($table, \PDO $db)
    {
        $this->table = $table;
        $this->db = $db;
    }

    function insertInto(array $list)
    {
        $data = $this->formatDataForWriting($list);
        $query = "INSERT INTO $this->table {$data[0]} VALUES {$data[1]}";
        $this->db->query($query);
        $lastInsertId = $this->db->lastInsertId();
        $this->lastInsertId = $lastInsertId;
    }

    function getLastInsertId()
    {
        return $this->lastInsertId;
    }

    private function formatDataForWriting(array $list)
    {
        $columns = array_keys($list);
        $columns = '(' . implode(', ', $columns) . ')';
        $values = "('" .implode("', '", $list) . "')";

        return array($columns, $values);
    }

    function getData($columns = '*')
    {
        if (is_array($columns)) {
            $columns = implode(', ', $columns);
        }
        $data = $this->db->query("SELECT $columns FROM $this->table");
        $result = [];
        while ($currentElement = $data->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = $currentElement;
        }
        return $result;
    }

    function getColumns()
    {
        $result = $this->db->query('SHOW COLUMNS FROM ' . $this->table);
        $columns = [];
        while ($column = $result->fetch(\PDO::FETCH_ASSOC)) {
            $columns[] = $column['Field'];
        }
        return $columns;
    }
}