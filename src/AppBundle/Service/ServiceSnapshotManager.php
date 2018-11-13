<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 11/1/2018
 * Time: 5:45 PM
 */

namespace AppBundle\Service;

use AppBundle\Entity\CategorySnapshot;
use AppBundle\Entity\Service;
use AppBundle\Entity\ServiceCategory;
use AppBundle\Entity\ServiceSnapshot;
use AppBundle\Repository\ServiceSnapshotRepository;
use Doctrine\ORM\EntityManagerInterface;

class ServiceSnapshotManager
{
    private $em;

    private $categorySnapshotManager;

    private $serviceManager;

    /**
     * @var ServiceSnapshotRepository
     */
    private $repository;

    public function __construct(EntityManagerInterface $em, CategorySnapshotManager $categorySnapshotManager, ServiceManager $serviceManager)
    {
        $this->em = $em;
        $this->categorySnapshotManager = $categorySnapshotManager;
        $this->serviceManager = $serviceManager;
        $this->repository = $em->getRepository(ServiceSnapshot::class);
    }

    public function getCurrentSnapshot(Service $service)
    {
        $version = $this->serviceManager->getCurrentVersion($service);

        $currentSnapshot = $this->repository->getCurrent($service, $version);

        return $currentSnapshot;

    }

    public function createSnapshot(Service $service, CategorySnapshot $categorySnapshot = null, int $version = null)
    {
            if(!$version){
                $version = $this->serviceManager->getCurrentVersion($service);
            }

            $serviceSnapshot = new ServiceSnapshot($service, $version);
            $serviceCategory = $service->getServiceCategory();
            $categorySnapshot = $categorySnapshot ?? $this->categorySnapshotManager->getCurrentSnapshot($serviceCategory);
            $serviceSnapshot->setCategorySnapshot($categorySnapshot);

            return $serviceSnapshot;
    }

}