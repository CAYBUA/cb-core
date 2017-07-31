<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 6/26/17
 * Time: 16:54
 */

namespace CayBua\Model;

use CayBua\Constants\ConfigConstants;
use CayBua\Constants\Services;
use CayBua\Http\UserHttp;
use Phalcon\Di;
use Phalcon\Mvc\Model;

abstract class BaseModel extends Model
{
    public $id;
    public $ipaddress;
    public $datecreated;
    public $datemodified;

    /**
     * @return array
     */
    public function columnMap()
    {
        return [
            'id' => 'id',
            'ipaddress' => 'ipaddress',
            'datecreated' => 'datecreated',
            'datemodified' => 'datemodified',
        ];
    }

    /**
     * Add tracking date and IP address when create model
     */
    public function beforeValidationOnCreate()
    {
        $this->datecreated = time();
        $this->datemodified = $this->datecreated;
        $request = $this->getDI()->get(Services::REQUEST);
        $this->ipaddress = ip2long($request->getClientAddress());
    }

    /**
     * Add tracking date and IP address when update model
     */
    public function beforeUpdate()
    {
        $this->datemodified = time();
        $request = $this->getDI()->get(Services::REQUEST);
        $this->ipaddress = ip2long($request->getClientAddress());
    }

    /**
     * @return array
     */
    public function getMessagesArray(){
        $messages = $this->getMessages();
        $messagesResponse = [];
        /** @var \Phalcon\Mvc\Model\Message $message */
        foreach ($messages as $message) {
            $messagesResponse[] = $message->getMessage();
        }
        return $messagesResponse;
    }

    /**
     * @param $resourceServerNumber
     * @return string
     */
    public function getImageResourceServer($resourceServerNumber)
    {
        $config = Di::getDefault()->get(Services::CONFIG);
        $resourceServers = $config->get(ConfigConstants::RESOURCE_SERVER);
        $resourceServerPath = '';
        foreach ($resourceServers as $key => $resourceServerPathConfig) {
            if ($key == $resourceServerNumber) {
                $resourceServerPath = $resourceServerPathConfig;
            }
        }
        return $resourceServerPath;
    }

    /**
     * @param $resourceServerPath
     * @return int|string
     */
    public function setImageResourceServer($resourceServerPath){
        $config = Di::getDefault()->get(Services::CONFIG);
        $resourceServers = $config->get(ConfigConstants::RESOURCE_SERVER);
        $resourceServerNumber = 0;
        foreach ($resourceServers as $key => $resourceServerPathConfig) {
            if ($resourceServerPathConfig == $resourceServerPath) {
                $resourceServerNumber = $key;
            }
        }
        return $resourceServerNumber;
    }

    /**
     * @param $userID
     * @return array
     */
    public function getUser($userID){
        $userHttp = new UserHttp();
        $userProfileDataResponse = $userHttp->getUseProfileWithUserID($userID);
        $userProfileData = $userProfileDataResponse['data']['item'];
        if(isset($userProfileData) && !empty($userProfileData)){
            return $userProfileData;
        }
        return [];
    }
}