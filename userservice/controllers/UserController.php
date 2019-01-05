<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 08.12.2018
 * Time: 12:57
 */

namespace MicroService\controllers;

use MicroService\Database\UserServiceDB;

class UserController
{
    private $db;
    private $authority = [];

    function __construct()
    {
        $this->db = new UserServiceDB();
    }

    function createUser($name, $password, $authority = 'user')
    {
        if ($this->authorityExist($authority)) {
            $data = array(
                'name' => htmlspecialchars($name),
                'password' => htmlspecialchars($password),
            );
            $this->db->users->insertInto($data);
            $userId = $this->db->users->getLastInsertId();

            $authorityId = $this->getAuthorityId($authority);
            $this->setAuthorityUser($authorityId, $userId);
        }
    }

    function getAuthorities()
    {
        if (empty($this->authority)) {
            $authorities = [];
            //$authorityList = $this->db->authorities->getData();

            foreach ($this->db->authorities->getData() as $val) {
                $authorities[$val['authority_id']] = $val['authority'];
            }

            $this->authority = $authorities;
        } else {
            $authorities = $this->authority;
        }

        return $authorities;
    }

    function getAuthorityId($authorityName)
    {
        $authorityList = $this->getAuthorities();

        foreach ($authorityList as $key => $val) {
            if ($val == $authorityName) {
                return $key;
            }
        }
    }

    function authorityExist($authority)
    {
        $allAuthorityList = $this->getAuthorities();
        return in_array($authority, $allAuthorityList);
    }

    function setAuthorityUser($authorityId, $userId)
    {
        $data = [
            'authority_id' => $authorityId,
            'user_id' => $userId
        ];

        $this->db->user_authority->insertInto($data);
    }
}