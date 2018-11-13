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
    private $em;

    private $categoryManager;

    /**
     * @var CategorySnapshotRepository
     */
    private $repository;


    public function __construct(EntityManagerInterface $em, CategoryManager $categoryManager)
    {
        $this->em = $em;
        $this->categoryManager = $categoryManager;
        $this->repository = $em->getRepository(CategorySnapshot::class);
    }

    public function getCurrentSnapshot(ServiceCategory $category)
    {
        $version = $this->categoryManager->getCurrentVersion($category);

        $currentSnapshot = $this->repository->getCurrent($category, $version);

        if (!$currentSnapshot) {
            $currentSnapshot = $this->createSnapshot($category, $version);
        }

        return $currentSnapshot;

    }

    private function createSnapshot(ServiceCategory $category, int $version)
    {
            $categorySnapshot = new CategorySnapshot($category, $version);

            return $categorySnapshot;
    }

}