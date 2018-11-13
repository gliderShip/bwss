<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 11/1/2018
 * Time: 5:45 PM
 */

namespace AppBundle\Service;

use AppBundle\Entity\Service;

class ServiceManager
{

    private $categoryManager;

    public function __construct(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    /**
     * @param Service $service
     * @return int version
     */
    public function getCurrentVersion(Service $service){

        $serviceVersion = $service->getUpdatedAt()->getTimestamp();
        $categoryVersion = $this->categoryManager->getCurrentVersion($service->getServiceCategory());
        $versionDependence = [$serviceVersion, $categoryVersion];
        foreach ($service->getItems() as $item){
            $versionDependence[] = $item->getUpdatedAt()->getTimestamp();
        }

        return max($versionDependence);
    }

}