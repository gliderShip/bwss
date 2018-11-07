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
use AppBundle\Entity\Offer;
use AppBundle\Entity\ServiceSnapshot;

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

    /**
     * @return Offer
     */
    public function createOffer(ServiceSnapshot $serviceSnapshot){

        $service = $serviceSnapshot->getService();
        $offerItems = $this->getOfferItems($service, $serviceSnapshot);

        return new Offer($serviceSnapshot, $offerItems);
    }

    public function getRentableOfferItems(Offer $offer){

        $offerItems = $offer->getOfferItems();

        $items = $offerItems->filter(function($offerItem){
            return $offerItem->getItemSnapshot()->isRentable();
        })->toArray();

        return $items;

    }

    public function getSinglePriceOfferItems(Offer $offer){
        $offerItems = $offer->getOfferItems();

        $items =  $offerItems->filter(function($offerItem){
            return !$offerItem->getItemSnapshot()->isRentable();
        })->toArray();

        return $items;
    }


    /**
     * @param Service $service
     * @return OfferItem[]
     */
    private function getOfferItems(Service $service, ServiceSnapshot $serviceSnapshot = null)
    {
        $snapshots = $this->getItemsSnapshots($service, $serviceSnapshot);

        foreach ($snapshots as $snap) {
            $offerItems[] = new OfferItem($snap);
        }

        return $offerItems;
    }

    private function getItemsSnapshots(Service $service, ServiceSnapshot $serviceSnapshot)
    {
        $snapshots = array();

        foreach ($service->getItems() as $item) {
            $snapshots[] = $this->itemSnapshotManager->getCurrentSnapshot($item, $serviceSnapshot);
        }

        return $snapshots;
    }

}