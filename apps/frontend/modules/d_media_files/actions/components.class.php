<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class d_media_filesComponents extends policatComponents {

  public function executeList() {
    $page = isset($this->page) ? $this->page : 1;

    $filesPetition = MediaFileTable::getInstance()->queryByPetitionId($this->petition->getId())->execute();
    $size = 0;
    if (count($filesPetition) > 0) {
      foreach ($filesPetition as $file) {
        $size += (int) $file->getSize();
      }
    }

    $this->spaceLimit = MediaFile::SPACE_LIMIT;
    $this->totalSize = $size;
    $this->leftSize = $this->spaceLimit - $this->totalSize;

    $this->files = new policatPager(MediaFileTable::getInstance()->queryByPetitionId($this->petition->getId()), $page, 'media_files_pager', array('id' => $this->petition->getId()), true, 20);
  }

}
