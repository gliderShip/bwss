<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use AppBundle\Entity\CategorySnapshot;
use AppBundle\Entity\Service;

/**
 * Service
 *
 * @ORM\Table(name="service_snapshot")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ServiceSnapshotRepository")
 */
class ServiceSnapshot extends Service
{
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Service")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $service;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CategorySnapshot", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="categorySnapshot_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $categorySnapshot;


    static function create(Service $service){

        $serviceSnapshot = new ServiceSnapshot();
        $serviceSnapshot->service = $service;
        $serviceSnapshot->setName($service->getName());

        $serviceCategory = $service->getServiceCategory();
        $serviceSnapshot->setServiceCategory($serviceCategory);
        $serviceSnapshot->categorySnapshot = CategorySnapshot::create($serviceCategory);

        return $serviceSnapshot;
    }

    /**
     * @return Service|null
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @return CategorySnapshot
     */
    public function getCategorySnapshot()
    {
        return $this->categorySnapshot;
    }





}

