<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Service;

/**
 * ServiceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ServiceSnapshotRepository extends SnapshotRepository
{
    public function getCurrent(Service $service, int $version){

        return $this->findOneBy(['service'=>$service, 'version' => $version]);
    }
}
