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
use AppBundle\Model\Timestampable;
use AppBundle\Model\Versionable;
use AppBundle\Repository\CostItemRepository;
use AppBundle\Repository\ItemSnapshotRepository;
use AppBundle\Repository\SnapshotRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

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


        $version = $costItem->getUpdatedAt()->getTimestamp();
        $currentSnapshot = $this->repository->findOneByVersion($version);

        if (!$currentSnapshot) {
            $currentSnapshot = $this->createSnapshot($costItem, $version);
        }

        return $currentSnapshot;

    }

    private function createSnapshot(CostItem $costItem, $version)
    {

        $itemSnapshot = new ItemSnapshot($costItem, $version);

        $itemSnapshot->setName($costItem->getName());
        $itemSnapshot->setPrice($costItem->getPrice());
        $itemSnapshot->setPriceType($costItem->getPriceType());
        $itemSnapshot->setCurrency($costItem->getCurrency());
        $itemSnapshot->setVat($costItem->getVat());

        $service = $costItem->getService();
        $serviceSnapshot = $this->serviceSnapshotManager->getCurrentSnapshot($service);
        $itemSnapshot->setServiceSnapshot($serviceSnapshot);

        return $itemSnapshot;
    }

}