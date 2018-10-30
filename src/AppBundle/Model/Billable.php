<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 10/30/2018
 * Time: 1:49 PM
 */

namespace AppBundle\Model;


interface Billable
{
    const VAT = 0.21;
    const DEFAULT_CURRENCY = 'EUR';

    public function setPrice($price, $vatIncluded = true);
    public function getPrice($vatIncluded = true);
    public function getGrossPrice();
    public function getNetPrice();
    public function setCurrency($currency = self::DEFAULT_CURRENCY);
    public function getCurrency();
    public function setVat($vat = self::VAT);
    public function getVat();
    public function priceEquals(Billable $billable);
    public function priceEqualsNet(Billable $billable);


}