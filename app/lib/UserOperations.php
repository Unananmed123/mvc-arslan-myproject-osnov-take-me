<?php
namespace app\lib;

class UserOperations
{
    public static function getRoleUser()
    {
        $result = 'guest';
        if (isset($_SESSION['user']['id']) && isset($_SESSION['user']['is_admin']))
        {
            $result = 'admin';
        } elseif (isset($_SESSION['user']['id'])){
            $result = 'user';
        }
        return $result;
    }
}