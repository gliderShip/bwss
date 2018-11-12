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

    public function getCurrentSnapshot(CostItem $costItem, ServiceSnapshot $serviceSnapshot = null)
    {

        $version = $this->itemManager->getCurrentVersion($costItem);
        $currentSnapshot = $this->repository->getCurrent($costItem, $version);

        if (!$currentSnapshot) {
            $currentSnapshot = $this->createSnapshot($costItem, $version, $serviceSnapshot);
        }

        return $currentSnapshot;

    }

    public function getServiceSnapshots(ServiceSnapshot $serviceSnapshot){

        return $this->repository->findByServiceSnapshot($serviceSnapshot);
    }

    private function createSnapshot(CostItem $costItem, int $version, ServiceSnapshot $serviceSnapshot = null)
    {
        $itemSnapshot = new ItemSnapshot($costItem, $version);
        $service = $costItem->getService();
        $serviceSnapshot = $serviceSnapshot ?? $this->serviceSnapshotManager->getCurrentSnapshot($service);
        $itemSnapshot->setServiceSnapshot($serviceSnapshot);

        return $itemSnapshot;
    }
}