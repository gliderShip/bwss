<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 11/1/2018
 * Time: 5:45 PM
 */

namespace AppBundle\Service;

use AppBundle\Entity\CostItem;

class ItemManager
{

    private $serviceManager;

    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }
    
    /**
     * @param CostItem $item
     * @return int version
     */
    public function getCurrentVersion(CostItem $item){

        $itemVersion = $item->getUpdatedAt()->getTimestamp();
        $serviceVersion = $item->getService()->getUpdatedAt()->getTimestamp();

        return $itemVersion > $serviceVersion ? $itemVersion : $serviceVersion;
    }

}