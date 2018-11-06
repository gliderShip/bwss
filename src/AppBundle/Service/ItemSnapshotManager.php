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
use AppBundle\Repository\ItemSnapshotRepository;
use Doctrine\ORM\EntityManagerInterface;

class ItemSnapshotManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ItemSnapshotRepository
     */
    private $repository;

    /**
     * @var ServiceSnapshotManager
     */
    private $serviceSnapshotManager;


    public function __construct(EntityManagerInterface $em, ServiceSnapshotManager $serviceSnapshotManager)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(ItemSnapshot::class);
        $this->serviceSnapshotManager = $serviceSnapshotManager;
    }

    public function getCurrentSnapshot(CostItem $costItem)
    {

        $version = $this->getItemCurrentVersion($costItem);
        $currentSnapshot = $this->repository->findOneByVersion($version);

        if (!$currentSnapshot) {
            $currentSnapshot = $this->createSnapshot($costItem);
        }

        return $currentSnapshot;

    }

    private function createSnapshot(CostItem $costItem)
    {

        $itemSnapshot = new ItemSnapshot($costItem);

        $service = $costItem->getService();
        $serviceSnapshot = $this->serviceSnapshotManager->getCurrentSnapshot($service);
        $itemSnapshot->setServiceSnapshot($serviceSnapshot);

        return $itemSnapshot;
    }

    private function getItemCurrentVersion(CostItem $costItem){

        return $costItem->getUpdatedAt()->getTimestamp();
    }
}