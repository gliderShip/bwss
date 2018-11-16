<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 11/1/2018
 * Time: 5:45 PM
 */

namespace AppBundle\Service;

use AppBundle\Entity\CategorySnapshot;
use AppBundle\Entity\Extra;
use AppBundle\Service\CategoryManager;

class ExtraManager
{

    private $categoryManager;

    public function __construct(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    /**
     * @param Extra $extra
     * @return int version
     */
    public function getCurrentVersion(Extra $extra){

        $extraVersion = $extra->getUpdatedAt()->getTimestamp();
        $categoryVersion = $this->categoryManager->getCurrentVersion($extra->getCategory());

        return max($extraVersion, $categoryVersion);
    }

}