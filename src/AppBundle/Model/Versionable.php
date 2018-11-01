<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 11/1/2018
 * Time: 6:08 PM
 */

namespace AppBundle\Model;

interface Versionable
{
    public function getUpdatedAt(): ?\DateTime;
}