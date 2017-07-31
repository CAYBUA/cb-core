<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 6/26/17
 * Time: 15:08
 */

namespace CayBua\Middleware;

use CayBua\Constants\AclRoles;
use CayBua\Constants\ConfigConstants;
use CayBua\Constants\Services;
use CayBua\Api;
use CayBua\Mvc\Plugin;

use CayBua\User\Service;
use Phalcon\Config;
use PhalconApi\Constants\ErrorCodes;
use PhalconApi\Exception;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

class RbacMiddleware extends Plugin implements MiddlewareInterface
{

    public function beforeExecuteRoute(Event $event, Api $api)
    {
        $URI = $api->request->getURI();
        $allowed = true;

        // Is API request
        if (
            $this->isApiRequest($URI) &&
            ($this->userService->getRole() != AclRoles::UNAUTHORIZED) &&
            ($this->userService->getRole() != AclRoles::LOCAL_SERVICE)
        ) {

            /** @var Config $config */
            $config = $this->di->get(Services::CONFIG);

            $activeHandler = $api->getActiveHandler();

            /** @var Micro\LazyLoader $activeControllerPath */
            $activeControllerPath = $activeHandler[0];

            // Call method whoAmI() current controller
            $activeControllerClassName = $activeControllerPath->callMethod('whoAmI', []);
            $activeController = $this->getControllerName($activeControllerClassName);
            $activeAction = $activeHandler[1];

            /** @var Service $userService */
            $userService = $this->di->get(Services::USER_SERVICE);

            $allowed = $userService->allowRbacPermission(
                $config->get(ConfigConstants::DOMAIN_NAME),
                $activeController,
                $activeAction
            );
        }

        if (!$allowed) {
            throw new Exception(ErrorCodes::ACCESS_DENIED);
        }
    }

    /**
     * Check Request is API request (not .html)
     * @param $URI
     * @return bool
     */
    private function isApiRequest($URI)
    {
        return count(explode('.html', $URI)) == 1;
    }

    /**
     * Process controller class name to controller name
     * @param $controllerPath
     * @return mixed
     */
    private function getControllerName($controllerPath)
    {
        $controllerNameArray = explode('\\', $controllerPath);
        $controllerName = explode('Controller', $controllerNameArray[2]);
        return strtolower($controllerName[0]);
    }

    /**
     * @param Micro $api
     * @return bool
     */
    public function call(Micro $api)
    {
        return true;
    }
}