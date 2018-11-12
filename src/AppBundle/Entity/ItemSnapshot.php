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
 * @ORM\Table(name="item_snapshot", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="version_unique", columns={"costItem_id", "version"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ItemSnapshotRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ServiceSnapshot", inversedBy="itemSnapshots", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="serviceSnapshot_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $serviceSnapshot;


    public function __construct(CostItem $costItem, int $version)
    {
        parent::__construct($version);

        $this->costItem = $costItem;

        $this->setName($costItem->getName());
        $this->setPrice($costItem->getPrice());
        $this->setPriceType($costItem->getPriceType());
        $this->setCurrency($costItem->getCurrency());
        $this->setVat($costItem->getVat());
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

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $now = new \DateTime();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

}

