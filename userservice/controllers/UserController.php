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

            $authorityId = $this->getIdAuthority($authority);
            $this->setAuthorityUser($authorityId, $userId);
        }
    }

    function getAllAuthority()
    {
        if (empty($this->authority)) {
            $authority = [];
            $listAuthority = $this->db->authories->getData();

            foreach ($listAuthority as $val) {
                $authority[
                $val['id_authories']
                ] = $val['authority'];
            }
        } else {
            $authority = $this->authority;
        }

        return $authority;
    }

    function getIdAuthority($authority)
    {
        $listAuthority = $this->getAllAuthority();

        foreach ($listAuthority as $key => $val) {
            if ($val == $authority){
                break;
            }
        }

        return $key;
    }

    function authorityExist($authority)
    {
        $listAllAuthority = $this->getAllAuthority();
        return in_array($authority, $listAllAuthority);
    }

    function setAuthorityUser($authorityId, $userId)
    {
        $data = [
            'id_authories' => $authorityId,
            'id_user' => $userId
        ];

        $this->db->user_authority->insertInto($data);
    }
}