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

    public function getRequestSextras(string $jsonIds, CategorySnapshot $categorySnapshot){

        $selectedSextras = array();

        $extrasArrayIds = json_decode($jsonIds, false, 2 );
        $snapshotExtras = $categorySnapshot->getExtraSnapshots();
        foreach ($snapshotExtras as $snapshotExtra) {
            if(in_array($snapshotExtra->getId(), $extrasArrayIds)){
                $selectedSextras[] = $snapshotExtra;
            }
        }

        return $selectedSextras;
    }

    /**
     * @return ExtraSnapshot|null
     */
    public function getCurrentSnapshot(Extra $extra, CategorySnapshot $categorySnapshot)
    {
        $version = $this->extraManager->getCurrentVersion($extra);
        $currentSnapshot = $this->repository->getCurrent($extra, $version);

        if (!$currentSnapshot) {
            $currentSnapshot = $this->createSnapshot($extra, $categorySnapshot);
            $this->em->persist($currentSnapshot);
        }

        return $currentSnapshot;

    }


    public function createSnapshot(Extra $extra, CategorySnapshot $categorySnapshot, int $version = null )
    {
        if(!$version){
            $version = $this->extraManager->getCurrentVersion($extra);
        }

        $version = $this->extraManager->getCurrentVersion($extra);
        $extraSnapshot = new ExtraSnapshot($extra, $version);
        $extraSnapshot->setCategorySnapshot($categorySnapshot);

        return $extraSnapshot;
    }
}