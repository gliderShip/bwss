<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 10/30/2018
 * Time: 1:49 PM
 */

namespace AppBundle\Model;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


trait Priceable
{
    /**
     * @var boolean
     * @ORM\Column(name="price_includes_vat", type="boolean", nullable=false)
     */
    private $priceIncludesVat = true;

    /**
     * @var float
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
     * @return boolean
     */
    public function getPriceIncludesVat(): bool
    {
        return $this->priceIncludesVat;
    }

    /**
     * @param boolean $priceIncludesVat
     */
    public function setPriceIncludesVat(bool $priceIncludesVat = true): void
    {
        $this->priceIncludesVat = $priceIncludesVat;
    }

    /**
     * Set price
     *
     * @param float $price
     */
    public function setPrice(float $price, $vatIncluded = true)
    {
        if(!$vatIncluded){
            $this->setNetPrice($price);
        } else{
            $this->setGrossPrice($price);
        }
    }

    /**
     * Set price
     *
     * @param float $grossPrice
     */
    public function setGrossPrice(float $grossPrice){
        if($this->priceIncludesVat){
            $this->price = $grossPrice;
        } else{
            $this->price = $grossPrice / (1+$this->getVat() );
        }
    }

    /**
     * Set price
     *
     * @param float $netPrice
     */
    public function setNetPrice(float $netPrice){
        if(!$this->priceIncludesVat){
            $this->price = $netPrice;
        } else{
            $this->price = ($netPrice * (1+$this->getVat())) ;
        }
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice($vatIncluded = true): ?float
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
    public function getGrossPrice(): ?float
    {
        if($this->priceIncludesVat) {
            return $this->price;
        }else{
            return ($this->price * (1+$this->getVat())) ;
        }
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getNetPrice(): ?float
    {
        if(!$this->priceIncludesVat){
            return $this->price;
        }
        else{
            return ( $this->price / (1+$this->getVat()) );
        }

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
    public function setCurrency($currency = Billable::DEFAULT_CURRENCY)
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
     * @param float $vat
     *
     */
    public function setVat(float $vat = Billable::VAT)
    {
        $this->vat = $vat;
    }

    /**
     * Get vat
     *
     * @return float
     */
    public function getVat(): ?float
    {
        return $this->vat;
    }

    /**
     * Get vat
     *
     * @return float
     */
    public function getVatAmount(): ?float
    {
        if($this->priceIncludesVat){
            $this->price - ($this->price / (1+$this->vat));
        } else{
            return $this->price * $this->vat;
        }
    }


    public function priceEqualsGross(Billable $costItem){
        if( ($this->getGrossPrice() == $costItem->getGrossPrice())  and ($this->getCurrency() == $costItem->getCurrency()) ){
            return true;
        }

        return false;
    }

    public function priceEqualsNet(Billable $costItem){
        if( ($this->getNetPrice() == $costItem->getNetPrice()) and ($this->getCurrency() == $costItem->getCurrency()) ){
            return true;
        }

        return false;
    }

    public function priceEquals(Billable $costItem){
        if( ($this->getNetPrice() == $costItem->getNetPrice())  and ($this->getVat() == $costItem->getVat()) and ($this->getCurrency() == $costItem->getCurrency()) ){
            return true;
        }

        return false;
    }





}