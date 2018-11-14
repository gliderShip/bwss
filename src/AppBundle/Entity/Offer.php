<?php

namespace AppBundle\Entity;

use AppBundle\Model\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Service;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Offer
 *
 * @ORM\Table(name="offer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OfferRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Offer
{
    use Timestampable;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ServiceSnapshot", cascade={"persist"})
     * @ORM\JoinColumn(name="snapshot_id", referencedColumnName="id", nullable=false)
     */
    protected $serviceSnapshot;

    /**
     * @var OfferItem[] $offerItems
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\OfferItem", mappedBy="offer", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    protected $offerItems;

    public function __construct(ServiceSnapshot $serviceSnapshot, array $offerItems)
    {

        $this->serviceSnapshot = $serviceSnapshot;
        $this->setOfferItems($offerItems);
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return ServiceSnapshot
     */
    public function getServiceSnapshot(): ?ServiceSnapshot
    {
        return $this->serviceSnapshot;
    }

    /**
     * @param ServiceSnapshot $serviceSnapshot
     */
    public function setServiceSnapshot(ServiceSnapshot $serviceSnapshot): void
    {
        $this->serviceSnapshot = $serviceSnapshot;
    }

    /**
     * @return OfferItem[]
     */
    public function getOfferItems()
    {
        return $this->offerItems;
    }

    /**
     * @param mixed $offerItems
     */
    public function setOfferItems($offerItems): void
    {
        $this->offerItems = new ArrayCollection();

        foreach ($offerItems as $item) {
            $this->offerItems->add($item);
            $item->setOffer($this);
        }
    }


    public function getSubTotal($vatIncluded = true)
    {
        $subTotal = 0;
        foreach ($this->offerItems as $offerItem) {
            $subTotal += $offerItem->getPrice($vatIncluded);
        }

        return $subTotal;
    }

    public function getGrandTotal($vatIncluded = true)
    {
        return $this->getSubTotal($vatIncluded);
    }

    public function getVatAmount()
    {
        $vatAmount = 0;

        foreach ($this->offerItems as $offerItem) {
            $vatAmount += $offerItem->getVatAmount();
        }

        return $vatAmount;
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

    public function __toString()
    {
        return 'Offer '.$this->serviceSnapshot->getName() . ' ['.$this->getId().']';
    }

}
