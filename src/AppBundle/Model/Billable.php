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

    const BILLABLE_TYPES = [
        'SINGLE AMOUNT' => 'SINGLE AMOUNT',
        'HOURLY AMOUNT' => 'HOURLY AMOUNT'
    ];

    public function setPrice($price, $vatIncluded = true);
    public function getPrice($vatIncluded = true);
    public function getPriceType();
    public function setPriceType($priceType);
    public function getGrossPrice();
    public function getNetPrice();

//    public function getCost($vatIncluded = true);
//    public function getNetCost();
//    public function getGrossCost();

    public function setCurrency($currency = self::DEFAULT_CURRENCY);
    public function getCurrency();
    public function setVat($vat = self::VAT);
    public function getVat();
    public function priceEquals(Billable $billable);
    public function priceEqualsNet(Billable $billable);


}