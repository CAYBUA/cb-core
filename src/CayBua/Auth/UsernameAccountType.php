<?php

namespace CayBua\Auth;

use CayBua\Http\UserHttp;
use CayBua\User\Service;
use PhalconApi\Auth\AccountType;

class UsernameAccountType implements AccountType
{
    const NAME = 'username';


    public function login($data)
    {

    }

    /**
     * @param string $token
     * @return bool
     */
    public function authenticate($token)
    {
        $useHttp = new UserHttp();
        $userData = $useHttp->getUserInformationWithToken($token)->getParsingResponse();
        if(
            ($userData['statusCode'] == 200)
            && isset($userData['data']['item'])
            && ($userData['data']['item']['id'] > 0)
        ){
            Service::$detailsCache[$userData['data']['item']['id']] = $userData;
            return true;
        }
        return false;
    }
}