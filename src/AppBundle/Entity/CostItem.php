<?php

namespace AppBundle\Entity;

use AppBundle\Model\Billable;
use AppBundle\Model\Item;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Service;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CostItem
 *
 * @ORM\Table(name="cost_item")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CostItemRepository")
 */
class CostItem extends Item implements Billable
{
    /**
     * @var string
     * @Assert\GreaterThan(0)
     * @Assert\LessThanOrEqual(9999999, message="This value should be less than or equal to {{ compared_value }}.")
     * @ORM\Column(name="price", type="decimal", precision=9, scale=2, options={"unsigned"=true})
     */
    private $price;

    /**
     * @var string
     * @Assert\Choice(choices=Billable::BILLABLE_TYPES, message="Invalid price type:{{ value }}")
     * @ORM\Column(name="price_type", type="string", length=50, nullable=false)
     */
    private $priceType;

    /**
     * @var string 3-letter ISO 4217 currencies
     * @ORM\Column(name="currency", type="string", length=3)
     */
    private $currency = self::DEFAULT_CURRENCY;

    /**
     * @var string
     * @Assert\LessThan(1)
     * @Assert\GreaterThanOrEqual(0)
     * @ORM\Column(name="vat", type="decimal", precision=3, scale=2)
     */
    private $vat = self::VAT;

    /**
     * Set price
     *
     * @param string $price
     */
    public function setPrice($price, $vatIncluded = true)
    {
        if(!$vatIncluded){
            $this->setNetPrice($price);
        } else{
            $this->setGrossPrice($price);
        }
    }

    public function setGrossPrice($grossPrice){

        $this->price = $grossPrice;
    }

    public function setNetPrice($netPrice){
        $this->price = ($netPrice * (1+$this->getVat())) ;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice($vatIncluded = true)
    {
        if(!$vatIncluded){
            return $this->getNetPrice();
        }

        return $this->getGrossPrice();
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getGrossPrice()
    {
        return $this->price;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getNetPrice()
    {
        return ( $this->price / (1+$this->getVat()) );
    }

    /**
     * @return string
     */
    public function getPriceType(): ?string
    {
        return $this->priceType;
    }

    /**
     * @param mixed $priceType
     */
    public function setPriceType($priceType): void
    {
        $this->priceType = $priceType;
    }

    /**
     * Set currency
     * @param string $currency
     */
    public function setCurrency($currency = self::DEFAULT_CURRENCY)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set vat
     *
     * @param string $vat
     *
     * @return Price
     */
    public function setVat($vat = self::VAT)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Get vat
     *
     * @return string
     */
    public function getVat()
    {
        return $this->vat;
    }

    public function priceEquals(Billable $costItem){
        if( ($this->getPrice() == $costItem->getPrice()) and ($this->getVat() == $costItem->getVat()) and ($this->getCurrency() == $costItem->getCurrency()) ){
            return true;
        }

        return false;
    }

    public function priceEqualsNet(Billable $price){
        if( ($this->getNetPrice() == $price->getNetPrice()) and ($this->getCurrency() == $price->getCurrency()) ){
            return true;
        }

        return false;
    }


}

