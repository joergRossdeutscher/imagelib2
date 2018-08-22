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


use App\Entity\FileEntity;
use App\Entity\FolderEntity;
use App\Entity\ThingEntity;
use App\Manager\ThingsManager;

class ThingsRepository
{
    /* @var \App\Manager\ThingsManager $thingsManager */
    public $thingsManager;

    /* @var $things ThingEntity[] */
    public $things = array();

    /* @var $folders FolderEntity[] */
    public $folders = array();
    public $folderIds = array();

    public function init(ThingsManager $thingsManager)
    {
        $this->thingsManager = $thingsManager;
        $this->logger = $thingsManager->getLogger();
        $this->configHelper = $thingsManager->getConfigHelper();
        $this->fileHelper = $thingsManager->getFileHelper();
    }

    public function initFromFileSystem($baseDir)
    {
        /* @var $files FileEntity[] */
        $files = $this->thingsManager->getFileHelper()->findThings($baseDir);
        foreach ($files as $file) {
            $thingEntity = $this->addThing(new ThingEntity($this->thingsManager, $file));
            $folderEntity = $this->registerFolder($thingEntity);
            $thingEntity->folderId = $folderEntity->id;
        }
    }

    public function registerFolder(ThingEntity $thingEntity)
    {
        $relPath = $thingEntity->masterFile->relDirName;

        if (array_key_exists($relPath, $this->folderIds)) {
            $id = $this->folderIds[$relPath];
            return $this->folders[$id];
        }
        $folderEntity = new FolderEntity($this->thingsManager, $thingEntity->masterFile->baseDir, $relPath);

        $id = count($this->folders)+1;
        $folderEntity->id = $id;
        $this->folders[$id] = $folderEntity;
        $this->folderIds[$relPath] = $id;
        return $folderEntity;
    }

    public function addThing(ThingEntity $thingEntity)
    {
        $id = count($this->things)+1;
        $thingEntity->id = $id;
        $this->things[$id] = $thingEntity;
        return $thingEntity;
    }

    public function save()
    {
        $fileName = $this->thingsManager->getConfigHelper()->filePathToRepo;
        file_put_contents($fileName, serialize($this));
    }

    public function getAll()
    {
        return $this->things;
    }

    public function getById($id)
    {
        return $this->things[$id];
    }

    public function getAllDerivedFiles($attribute = 'absFileName', $asKey = false)
    {
        $ret = array();
        foreach ($this->things as $thing) {
            if ($asKey) {
                $ret[$thing->posterFile->{$attribute}] = true;
                $ret[$thing->thumbnailFile->{$attribute}] = true;
            } else {
                $ret[] = $thing->posterFile->{$attribute};
                $ret[] = $thing->thumbnailFile->{$attribute};
            }
        }
        return $ret;
    }

    public function dump(){
        echo "----------------- FOLDER MAPPING ------------------\n";
        print_r($this->folderIds);

        echo "----------------- FOLDERS ------------------\n";
        foreach ($this->folders as $folder){
            echo $folder->relPath."\n";
            $folder->dump();
            echo "\n";
        }

        echo "----------------- THINGS ------------------\n";
        foreach ($this->things as $thing){
            echo "THING:\n".$thing->masterFile->relFileName."\n";
            $thing->dump();
            echo "\n";
        }
    }

}
