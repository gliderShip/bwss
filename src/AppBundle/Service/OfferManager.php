<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 11/1/2018
 * Time: 5:45 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\ExtraSnapshot;
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
    public function createOffer(ServiceSnapshot $serviceSnapshot, array $sExtras){

        $offerItems = $this->getOfferItems($serviceSnapshot);

        return new Offer($serviceSnapshot, $offerItems, $sExtras);
    }

    public function getRentableOfferItems(Offer $offer){

        $offerItems = $offer->getOfferItems();

        $rentableItems = $offerItems->filter(function($offerItem){
            return $offerItem->getItemSnapshot()->isRentable();
        })->toArray();

        return $rentableItems;

    }

    public function getSinglePriceOfferItems(Offer $offer){
        $offerItems = $offer->getOfferItems();

        $spItems =  $offerItems->filter(function($offerItem){
            return !$offerItem->getItemSnapshot()->isRentable();
        })->toArray();

        return $spItems;
    }


    /**
     * @param Service $service
     * @return OfferItem[]
     */
    private function getOfferItems(ServiceSnapshot $serviceSnapshot)
    {
        $snapshots = $this->getItemsSnapshots($serviceSnapshot);

        $offerItems = array();

        foreach ($snapshots as $snap) {
            $offerItems[] = new OfferItem($snap);
        }

        return $offerItems;
    }

    private function getItemsSnapshots(ServiceSnapshot $serviceSnapshot)
    {
        return $this->itemSnapshotManager->getServiceSnapshots($serviceSnapshot);
    }

}