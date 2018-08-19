<?php
/**
 * Class ThingsRepository
 *
 * This Software is the property of superReal and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @link      http://www.superreal.de
 * @package App\Repositories
 * @copyright (C) superReal 2018
 * @author    Joerg Rossdeutscher <j.rossdeutscher _AT_ superreal.de>
 */

namespace App\Repository;


use App\Manager\ThingsManager;

class ThingsRepository
{
    /* @var \App\Manager\ThingsManager $thingsManager */
    public $thingsManager;

    public function initFromFileSystem($baseDir)
    {
        $files=$this->thingsManager->getFileHelper()->find($baseDir);
    }

    public function buildIndexFromFileSystem($baseDir)
    {
        $this->initFromFileSystem($baseDir);
    }

}