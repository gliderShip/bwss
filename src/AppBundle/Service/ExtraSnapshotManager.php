<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 11/1/2018
 * Time: 5:45 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\Extra;
use AppBundle\Entity\ExtraSnapshot;
use AppBundle\Entity\CategorySnapshot;
use AppBundle\Repository\ExtraSnapshotRepository;
use Doctrine\ORM\EntityManagerInterface;

class ExtraSnapshotManager
{
    private $em;

    private $categorySnapshotManager;

    private $extraManager;

    /**
     * @var ExtraSnapshotRepository
     */
    private $repository;


    public function __construct(EntityManagerInterface $em, ExtraManager $extraManager, CategorySnapshotManager $categorySnapshotManager)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(ExtraSnapshot::class);
        $this->categorySnapshotManager = $categorySnapshotManager;
        $this->extraManager = $extraManager;
    }

    /**
     * @return ExtraSnapshot|null
     */
    public function getCurrentSnapshot(Extra $extra)
    {
        $version = $this->extraManager->getCurrentVersion($extra);
        $currentSnapshot = $this->repository->getCurrent($extra, $version);

        return $currentSnapshot;

    }

    public function getCategorySnapshots(CategorySnapshot $categorySnapshot){

        return $this->repository->findByCategorySnapshot($categorySnapshot);
    }

    public function createSnapshot(Extra $extra, CategorySnapshot $categorySnapshot = null )
    {
        if(!$categorySnapshot){
            $categorySnapshot = $this->categorySnapshotManager->getCurrentSnapshot($extra->getCategory());
        }

        $version = $this->extraManager->getCurrentVersion($extra);
        $extraSnapshot = new ExtraSnapshot($extra, $version);
        $extraSnapshot->setCategorySnapshot($categorySnapshot);

        return $extraSnapshot;
    }
}