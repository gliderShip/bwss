<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 11/1/2018
 * Time: 5:45 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\CostItem;
use AppBundle\Entity\ItemSnapshot;
use AppBundle\Entity\Service;
use AppBundle\Entity\ServiceSnapshot;
use AppBundle\Repository\ItemSnapshotRepository;
use Doctrine\ORM\EntityManagerInterface;

class ItemSnapshotManager
{
    private $em;

    private $serviceSnapshotManager;

    private $itemManager;

    /**
     * @var ItemSnapshotRepository
     */
    private $repository;


    public function __construct(EntityManagerInterface $em, ItemManager $itemManager, ServiceSnapshotManager $serviceSnapshotManager)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(ItemSnapshot::class);
        $this->serviceSnapshotManager = $serviceSnapshotManager;
        $this->itemManager = $itemManager;
    }

    public function generateItemSnapshots(Service $service, $serviceSnapshot){
        foreach ($service->getItems() as $costItem) {
            $itemSnapShot = $this->getCurrentSnapshot($costItem);
            if (!$itemSnapShot or $itemSnapShot->getServiceSnapshot() != $serviceSnapshot) {
                $itemSnapShot = $this->createSnapshot($costItem, $serviceSnapshot);
                $this->em->persist($itemSnapShot);
            }
        }
    }

    /**
     * @return ItemSnapshot|null
     */
    public function getCurrentSnapshot(CostItem $costItem)
    {

        $version = $this->itemManager->getCurrentVersion($costItem);
        $currentSnapshot = $this->repository->getCurrent($costItem, $version);

        return $currentSnapshot;

    }

    public function getServiceSnapshots(ServiceSnapshot $serviceSnapshot){

        return $this->repository->findByServiceSnapshot($serviceSnapshot);
    }

    public function createSnapshot(CostItem $costItem, ServiceSnapshot $serviceSnapshot = null )
    {

        if(!$serviceSnapshot){
            $serviceSnapshot = $this->serviceSnapshotManager->getCurrentSnapshot($costItem->getService());
        }

        $version = $this->itemManager->getCurrentVersion($costItem);
        $itemSnapshot = new ItemSnapshot($costItem, $version);
        $itemSnapshot->setServiceSnapshot($serviceSnapshot);

        return $itemSnapshot;
    }
}