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
            $query = "INSERT INTO $this->table {$data['columns']} VALUES {$data['parameters']}";

            $statement = $this->db->prepare($query);
            $statement->execute($data['relatedValues']);

            $lastInsertId = $this->db->lastInsertId();
            $this->lastInsertId = $lastInsertId;
    }

    function getLastInsertId()
    {
        return $this->lastInsertId;
    }

    private function formatDataForWriting(array $list)
    {
        $keys = array_keys($list);
        $parameters = '(:' . implode(', :', $keys) . ')';
        $columns = '(' . implode(', ', $keys) . ')';
        $relatedValues = $this->bindParameters($list);

        return array('columns' => $columns, 'parameters' => $parameters, 'relatedValues' => $relatedValues);
    }

    private function bindParameters(array $parameters)
    {
        $bindParam = [];
        foreach ($parameters as $key => $value) {
            $key = ':' . $key;
            $bindParam[$key] = $value;
        }

        return $bindParam;
    }

    function getData($columns = '*',  $requirement = '')
    {
        if (is_array($columns)) {
            $columns = implode(', ', $columns);
        }
        $query = "SELECT $columns FROM $this->table";

        if($requirement){
            $query .= " $requirement";
        }

        $data = $this->db->query($query);

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