<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 03.12.2018
 * Time: 19:51
 */

namespace MicroService\database;


class Tables
{
    private $table;
    private $db;

    function __construct($table, \PDO $db)
    {
        $this->table = $table;
        $this->db = $db;
    }

    function insertInto(array $list)
    {
        $data = $this->formatDataForWriting($list);
        $query = "INSERT INTO $this->table {$data[0]} VALUES {$data[1]}";
        return $this->db->query($query);


    }

    private function formatDataForWriting(array $list)
    {
        $columns = array_shift($list);
        $columns = '(' . implode(', ', $columns) . ')';

        for ($values = '', $length = count($list), $count = 1; $count <= $length; ++$count) {
            $elements = array_shift($list);
            $elements = array_map(function ($element){
                return "'{$element}'";
            },$elements);
            $values .= '(' . implode(', ', $elements) . ')';

            if ($count != $length) {
                $values .= ', ';
            }
        }

        return array($columns, $values);
    }

    private function getColumns()
    {
        $result = $this->db->query('SHOW COLUMNS FROM ' . $this->table);
        $columns = [];
        while ($column = $result->fetch(\PDO::FETCH_ASSOC)) {
            $columns[] = $column['Field'];
        }
        return $columns;
    }
}