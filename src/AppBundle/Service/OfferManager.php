<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 11/1/2018
 * Time: 5:45 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\OfferItem;
use AppBundle\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;

class OfferManager
{

    /**
     * @var ItemSnapshotManager
     */
    private $itemSnapshotManager;


    public function __construct(ItemSnapshotManager $itemSnapshotManager)
    {
        $this->itemSnapshotManager = $itemSnapshotManager;
    }

    public function getItemsSnapshots(Service $service)
    {
        $snapshots = array();

        foreach ($service->getItems() as $item) {
            $snapshots[] = $this->itemSnapshotManager->getCurrentSnapshot($item);
        }

        return $snapshots;
    }


    public function getOfferItems($service)
    {
        $snapshots = $this->getItemsSnapshots($service);

        foreach ($snapshots as $snap) {
            $offerItems[] = new OfferItem($snap);
        }

        return $offerItems;
    }

}