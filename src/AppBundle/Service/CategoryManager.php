<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 11/1/2018
 * Time: 5:45 PM
 */

namespace AppBundle\Service;

use AppBundle\Entity\ServiceCategory;

class CategoryManager
{
    /**
     * @param ServiceCategory $category
     * @return int version
     */
    public function getCurrentVersion(ServiceCategory $category){

        $categoryVersion = $category->getUpdatedAt()->getTimestamp();

        $versionDependence = [$categoryVersion];

        foreach ($category->getServices() as $service){
            $versionDependence[] = $service->getUpdatedAt()->getTimestamp();
        }

        foreach ($category->getExtras() as $extra){
            $versionDependence[] = $extra->getUpdatedAt()->getTimestamp();
        }

        return max($versionDependence);
    }

}