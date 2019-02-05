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

    /**
     * create new user
     * @param string $name
     * @param string $password
     * @param string $authority
     * @return bool
     */
    function createUser(string $name, string $password, $authority = 'user')
    {
        if ($this->authorityExist($authority)) {
            $data = array(
                'name' => htmlspecialchars($name),
                'password' => password_hash($password, PASSWORD_BCRYPT),
            );

            try {
                $this->db->beginTransaction();
                $this->db->users->insertInto($data);
                $userId = $this->db->users->getLastInsertId();
                $authorityId = $this->getAuthorityId($authority);
                $this->writeAuthorityUser($authorityId, $userId);
                $this->db->commit();
                return true;
            } catch (\Exception $e) {

                $this->db->rollBack();
                return false;
            }

        }
    }

    /** get list authority from database
     * @return array
     */
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

    /**
     * get id authority by name
     * @param string $authorityName
     * @return int|string
     */
    function getAuthorityId(string $authorityName)
    {
        $authorityList = $this->getAuthorities();

        foreach ($authorityList as $key => $val) {
            if ($val == $authorityName) {
                return $key;
            }
        }
    }

    /**
     * check exist authority
     * @param $authority
     * @return bool
     */
    function authorityExist($authority)
    {
        $allAuthorityList = $this->getAuthorities();
        return in_array($authority, $allAuthorityList);
    }

    /**
     * write authority user into database
     * @param $authorityId
     * @param $userId
     */
    function writeAuthorityUser($authorityId, $userId)
    {
        $data = [
            'authority_id' => $authorityId,
            'user_id' => $userId
        ];

        $this->db->user_authority->insertInto($data);
    }

    /**
     * check exist user
     * if user exist return 200 else return 401
     * @param $name
     * @param $password
     * @return int
     */
    function checkUser($name, $password)
    {
        $data = [
            'name' => $name,
            'password' => ''
        ];
        $requirement = "WHERE name = :name";
        $data = $this->db->users->getData($data, $requirement);


        return !empty($data) && password_verify($password, $data[0]['password']) ? 200 : 401;

        /*if(empty($data[0])){
            return 401;
        }

        return 200;*/
    }
}