<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 28.11.2018
 * Time: 22:21
 */

namespace MicroService\Database;

use MicroService\UserServiceDB;

class UserController
{
    private $db;

    function __construct(MicroService\UserServiceDB $db)
    {
        $this->db = $db;
    }

    function createUser($name, $pass, $authority = 'user')
    {
        $this->db->prepare();
    }
}