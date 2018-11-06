<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 11/1/2018
 * Time: 5:45 PM
 */

namespace AppBundle\Service;

use AppBundle\Entity\Service;
use AppBundle\Entity\ServiceSnapshot;
use AppBundle\Repository\ServiceSnapshotRepository;
use Doctrine\ORM\EntityManagerInterface;

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
        $version = $this->getServiceCurrentVersion($service);

        $currentSnapshot = $this->repository->findOneByVersion($version);

        if (!$currentSnapshot) {
            $currentSnapshot = $this->createSnapshot($service);
        }

        return $currentSnapshot;

    }

    private function createSnapshot(Service $service)
    {
            $serviceSnapshot = new ServiceSnapshot($service);
            $serviceCategory = $service->getServiceCategory();
            $categorySnapshot = $this->categorySnapshotManager->getCurrentSnapshot($serviceCategory);
            $serviceSnapshot->setCategorySnapshot($categorySnapshot);

            return $serviceSnapshot;
    }


    private function getServiceCurrentVersion(Service $service){

        return $service->getUpdatedAt()->getTimestamp();
    }

}