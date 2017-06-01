<?php

namespace CayBua\User;

use CayBua\Constants\AclRoles;
use App\Model\User;
use CayBua\Mvc\BaseModel as BaseModel;

class Service extends \PhalconApi\User\Service
{
    protected $detailsCache = [];

    public function getRole()
    {
        /** @var User $userModel */
        $userModel = $this->getDetails();

        $role = AclRoles::UNAUTHORIZED;

        if($userModel && in_array(ucfirst(strtolower($userModel->role)), AclRoles::ALL_ROLES)){
            $role = $userModel->role;
        }

        return $role;
    }

    protected function getDetailsForIdentity($identity)
    {
        if (array_key_exists($identity, $this->detailsCache)) {
            return $this->detailsCache[$identity];
        }
        $details = new BaseModel();
        $details->getData($identity);
        $this->detailsCache[$identity] = $details;

        return $details;
    }
}