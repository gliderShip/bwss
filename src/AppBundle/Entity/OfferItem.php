<?php

namespace AppBundle\Entity;

use AppBundle\Model\Billable;
use AppBundle\Model\Item;
use AppBundle\Model\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Service;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Offer;

/**
 * OfferItem
 *
 * @ORM\Table(name="offer_item")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OfferItemRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class OfferItem implements Billable
{
    use Timestampable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Offer", inversedBy="items")
     * @ORM\JoinColumn(name="offer_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $offer;

    /**
     * @var integer
     * @Assert\GreaterThanOrEqual(0, message="This value should be positive")
     * @ORM\Column(name="hours", type="integer", options={"unsigned"=true}, nullable=false)
     */
    protected $hours = 0;

    /**
     * @var ItemSnapshot
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ItemSnapshot", cascade={"persist"})
     * @ORM\JoinColumn(name="snapshot_id", referencedColumnName="id", nullable=false)
     */
    protected $itemSnapshot;

    public function __construct(ItemSnapshot $itemSnapshot)
    {
        $this->itemSnapshot = $itemSnapshot;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Offer
     */
    public function getOffer(): ?Offer
    {
        return $this->offer;
    }

    /**
     * @param Offer $offer
     */
    public function setOffer(Offer $offer): void
    {
        $this->offer = $offer;
    }


    /**
     * @return string
     */
    public function getHours(): ?int
    {
        return $this->hours;
    }

    /**
     * @param string $hours
     */
    public function setHours(int $hours)
    {
        $this->hours = $hours;
    }

    /**
     * @return ItemSnapshot
     */
    public function getItemSnapshot(): ?ItemSnapshot
    {
        return $this->itemSnapshot;
    }

    /**
     * @param mixed $itemSnapshot
     */
    public function setItemSnapshot(ItemSnapshot $itemSnapshot = null): void
    {
        $this->itemSnapshot = $itemSnapshot;
    }


    public function getCost($vatIncluded = true)
    {
        if ($this->getPriceType() == Billable::BILLABLE_TYPES['SINGLE AMOUNT']) {
            return $this->getPrice($vatIncluded);
        } elseif ($this->getPriceType() == Billable::BILLABLE_TYPES['HOURLY AMOUNT']) {
            return $this->getPrice($vatIncluded) * $this->getHours();
        } else {
            throw new \Exception('Undefined cost for this price type');
        }
    }

    public function __toString()
    {

        return $this->itemSnapshot->getName() ?? '';
    }


    /* Billable Interface */
    public function getPrice($vatIncluded = true)
    {

        if ($vatIncluded) {
            return $this->getGrossPrice();
        }

        return $this->getNetPrice();
    }

    public function getGrossPrice()
    {

        $itemSnapshotGrossPrice = $this->itemSnapshot->getGrossPrice();
        if ($this->isRentable()) {
            return $this->getHours() * $itemSnapshotGrossPrice;
        }

        return $itemSnapshotGrossPrice;
    }

    public function getNetPrice()
    {
        $itemSnapshotNetPrice = $this->itemSnapshot->getNetPrice();
        if ($this->isRentable()) {
            return $this->getHours() * $itemSnapshotNetPrice;
        }

        return $itemSnapshotNetPrice;
    }

    public function getPriceType()
    {
        return $this->itemSnapshot->getPriceType();
    }

    public function getCurrency()
    {
        return $this->itemSnapshot->getCurrency();
    }

    public function getVat(){
        return $this->itemSnapshot->getVat();
    }

    public function getVatAmount(){

        $itemSnapshotVatAmount = $this->itemSnapshot->getVatAmount();
        if ($this->isRentable()) {
            return $this->getHours() * $itemSnapshotVatAmount;
        }

        return $itemSnapshotVatAmount;
    }

    public function priceEquals($thatItem){
        $thatItemSnapshot = $thatItem->getItemSnapshot();
        if($this->itemSnapshot->priceEquals($thatItemSnapshot) and ($this->getHours() == $thatItem->getHours())){
            return true;
        }

        return false;
    }

    public function priceEqualsNet($thatItem){
        $thatItemSnapshot = $thatItem->getItemSnapshot();
        if($this->itemSnapshot->priceEqualsNet($thatItemSnapshot) and ($this->getHours() == $thatItem->getHours())){
            return true;
        }

        return false;
    }

    public function priceEqualsGross($thatItem){
        $thatItemSnapshot = $thatItem->getItemSnapshot();
        if($this->itemSnapshot->priceEqualsGross($thatItemSnapshot) and ($this->getHours() == $thatItem->getHours())){
            return true;
        }

        return false;
    }

    public function isRentable()
    {
        if ($this->itemSnapshot->isRentable()) {
            return true;
        }

        return false;
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

