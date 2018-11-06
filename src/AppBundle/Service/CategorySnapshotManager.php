<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 11/1/2018
 * Time: 5:45 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\CategorySnapshot;
use AppBundle\Entity\ServiceCategory;
use AppBundle\Repository\CategorySnapshotRepository;
use Doctrine\ORM\EntityManagerInterface;

class CategorySnapshotManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CategorySnapshotRepository
     */
    private $repository;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(CategorySnapshot::class);
    }

    public function getCurrentSnapshot(ServiceCategory $category)
    {
        $version = $this->getCategoryCurrentVersion($category);
        $currentSnapshot = $this->repository->findOneByVersion($version);

        if (!$currentSnapshot) {
            $currentSnapshot = $this->createSnapshot($category);
        }

        return $currentSnapshot;

    }

    private function createSnapshot(ServiceCategory $category)
    {
            $categorySnapshot = new CategorySnapshot($category);

            return $categorySnapshot;
    }

    private function getCategoryCurrentVersion(ServiceCategory $category){

        return $category->getUpdatedAt()->getTimestamp();
    }

}