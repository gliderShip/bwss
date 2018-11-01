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

    public function getPrice($vatIncluded = true);
    public function getPriceType();
    public function getGrossPrice();
    public function getNetPrice();

//    public function getCost($vatIncluded = true);
//    public function getNetCost();
//    public function getGrossCost();

    public function getCurrency();
    public function getVat();
    public function priceEquals(Billable $billable);
    public function priceEqualsNet(Billable $billable);
    public function priceEqualsGross(Billable $billable);


}