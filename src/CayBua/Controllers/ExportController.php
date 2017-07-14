<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 7/13/17
 * Time: 09:48
 */

namespace CayBua\Controllers;

use CayBua\Constants\Services;

use CayBua\Export\Documentation;
use CayBua\Transformers\DocumentationTransformer;

use CayBua\Export\Postman\ApiCollection;
use CayBua\Transformers\Postman\ApiCollectionTransformer;

use PhalconRest\Mvc\Controllers\CollectionController;

class ExportController extends CollectionController
{
    public function documentation()
    {
        /** @var \Phalcon\Config $config */
        $config = $this->di->get(Services::CONFIG);
        $documentation = new Documentation(
            $config->get('application')->title,
            $config->get('hostName')
        );
        $documentation->addManyCollections($this->application->getCollections());
        $documentation->addManyRoutes($this->application->getRouter()->getRoutes());
        return $this->createItemResponse(
            $documentation,
            new DocumentationTransformer(),
            'documentation'
        );
    }

    public function postman()
    {
        /** @var \Phalcon\Config $config */
        $config = $this->di->get(Services::CONFIG);
        $postmanCollection = new ApiCollection(
            $config->get('application')->application->title,
            $config->get('hostName')
        );
        $postmanCollection->addManyCollections($this->application->getCollections());
        $postmanCollection->addManyRoutes($this->application->getRouter()->getRoutes());
        return $this->createItemResponse($postmanCollection, new ApiCollectionTransformer());
    }
}