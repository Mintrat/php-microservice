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

            try{
                $this->db->beginTransaction();
                $this->db->users->insertInto($data);
                $userId = $this->db->users->getLastInsertId();
                $authorityId = $this->getAuthorityId($authority);
                $this->writeAuthorityUser($authorityId, $userId);
                $this->db->commit();
                return true;
            }catch (\Exception $e){

                $this->db->rollBack();
                return false;
            }

        }
    }

    function getAuthorities()
    {
        if (empty($this->authority)) {
            $authorities = [];
            $authorityList = $this->db->authorities->getData();

            foreach ($authorityList as $val) {
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

    function writeAuthorityUser($authorityId, $userId)
    {
        $data = [
            'authority_id' => $authorityId,
            'user_id' => $userId
        ];

        $this->db->user_authority->insertInto($data);
    }

    function checkUser($name, $password)
    {
        $data = [
            'name' => $name,
            'password' => $password
        ];

        $requirement = "WHERE name = '{$data['name']}' AND password = '{$data['password']}'";
        $data = $this->db->users->getData('name, password', $requirement);

        if(empty($data[0])){
            return 401;
        }

        return 200;
    }
}