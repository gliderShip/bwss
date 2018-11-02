<?php

namespace AppBundle\Entity;

use AppBundle\Model\Billable;
use AppBundle\Model\Item;
use AppBundle\Model\Priceable;
use AppBundle\Model\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Service;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\ServiceSnapshot;

/**
 * CostItem
 *
 * @ORM\Table(name="item_snapshot")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ItemSnapshotRepository")
 */
class ItemSnapshot extends AbstractSnapshot implements Billable
{
    use Priceable ;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CostItem")
     * @ORM\JoinColumn(name="costItem_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $costItem;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ServiceSnapshot", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="serviceSnapshot_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $serviceSnapshot;


    public function __construct(CostItem $costItem, int $version)
    {
        parent::__construct($version);

        $this->costItem = $costItem;
    }


    /**
     * @return CostItem|null
     */
    public function getCostItem()
    {
        return $this->costItem;
    }

    /**
     * @param ServiceSnapshot $serviceSnapshot
     */
    public function setServiceSnapshot(ServiceSnapshot $serviceSnapshot): void
    {
        $this->serviceSnapshot = $serviceSnapshot;
    }


    /**
     * @return Service|null
     */
    public function getServiceSnapshot(): ?ServiceSnapshot
    {
        return $this->serviceSnapshot;
    }

    public function __toString()
    {
        return $this->name;
    }

}

