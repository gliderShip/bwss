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
use AppBundle\Model\Timestampable;
use AppBundle\Model\Versionable;
use AppBundle\Repository\CostItemRepository;
use AppBundle\Repository\ItemSnapshotRepository;
use AppBundle\Repository\ServiceSnapshotRepository;
use AppBundle\Repository\SnapshotRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class ServiceSnapshotManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ServiceSnapshotRepository
     */
    private $repository;

    /**
     * @var CategorySnapshotManager
     */
    private $categorySnapshotManager;


    public function __construct(EntityManagerInterface $em, CategorySnapshotManager $categorySnapshotManager)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(ServiceSnapshot::class);
        $this->categorySnapshotManager = $categorySnapshotManager;
    }

    public function getCurrentSnapshot(Service $service)
    {

        $version = $service->getUpdatedAt()->getTimestamp();
        $currentSnapshot = $this->repository->findOneByVersion($version);

        if (!$currentSnapshot) {
            $currentSnapshot = $this->createSnapshot($service, $version);
        }

        return $currentSnapshot;

    }

    private function createSnapshot(Service $service, $version)
    {
            $serviceSnapshot = new ServiceSnapshot($service, $version);
            $serviceSnapshot->setName($service->getName());

            $serviceCategory = $service->getServiceCategory();
            $categorySnapshot = $this->categorySnapshotManager->getCurrentSnapshot($serviceCategory);
            $serviceSnapshot->setCategorySnapshot($categorySnapshot);

            return $serviceSnapshot;
    }

}