<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * dashboard widget actions.
 *
 * @package    policat
 * @subpackage d_media_files
 * @author     Martin
 */
class d_media_filesActions extends policatActions {

    public function executeIndex(sfWebRequest $request) {        
        $this->petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
        $this->form = new MediaUploadForm();

        $this->includeChosen();
    }

    public function executePager(sfWebRequest $request) {
        $page = $request->getParameter('page', 1);
        if ($request->hasParameter('id')) {
            $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
            /* @var $petition Petition */
            if (!$petition)
                return $this->notFound();

            if (!$this->getGuardUser()->isPetitionMember($petition, true))
                return $this->noAccess();

            return $this->ajax()->replaceWithComponent('#media_files_list', 'd_media_files', 'list', array('page' => $page, 'petition' => $petition, 'no_filter' => true))->render();
        }
        return $this->ajax()->replaceWithComponent('#media_files_list', 'd_media_files', 'list', array('page' => $page, 'no_filter' => true))->render();
    }

    public function executeDelete(sfWebRequest $request) {
        $this->forward404Unless($this->petition = $request->getParameter("petition_id"));
        $this->forward404Unless($this->id = $request->getParameter("id"));

        if (!($file = MediaFilesTable::getInstance()->queryByPetitionAndId($this->petition, $this->id)))
            $this->redirect($this->generateUrl('media_files_list', array('id' => $this->petition)));


        $filePath = \sfConfig::get('sf_root_dir') . MediaFiles::FILE_PATH . $this->petition . '/' . $file->getFilename();

        if (file_exists($filePath)) {
            !unlink($filePath);
        }
        
        $file->delete();

        $this->redirect($this->generateUrl('media_files_list', array('id' => $this->petition)));
    }
    
    
      public function executeRename(sfWebRequest $request) {
        $this->forward404Unless($this->petitionId = $request->getParameter("petition_id"));
        $this->forward404Unless($this->id = $request->getParameter("id"));
        $this->petition = PetitionTable::getInstance()->findById($this->petitionId , $this->userIsAdmin());

        if (!($file = MediaFilesTable::getInstance()->queryByPetitionAndId($this->petitionId, $this->id)))
            $this->redirect($this->generateUrl('media_files_list', array('id' => $this->petitionId)));
                
        $this->form = new MediaFilesForm($file);
        
        if($request->isMethod(sfRequest::POST)){
            $data = $request->getParameter($this->form->getName());
            $data["petition_id"] = $this->petitionId;
            $data["filename"] = $file->filename;
            $data["size"] = $file->size;
            $data["extention"] = $file->extention;
            $data["mimetype"] = $file->mimetype;
            $data["path"] = $file->path;
            $data["created_at"] = $file->created_at;
            $data["updated_at"] = date("now");
                        
            $this->form->bind($data);        
            if ($this->form->isValid()){
                                
                $object = $this->form->save();                
                $object->setFilename($object->getName().".".$data["extention"]);                                
                $object->save();
                
                $filePath = \sfConfig::get('sf_root_dir') . $data["path"] . '/' . $data["filename"];
                $filePathNew = \sfConfig::get('sf_root_dir') . $data["path"] . '/' . $object->getFilename();                
                                                
                if (file_exists($filePath)) {
                        rename( $filePath, $filePathNew );
                }
                 
                $this->redirect($this->generateUrl('media_files_list', array('id' => $this->petitionId)));
           }                                                      
        }      
    }

    public function executeUpload(sfWebRequest $request) {
        $spaceLimit = MediaFiles::SPACE_LIMIT;
        $limit = MediaFiles::IMAGE_LIMIT;
        $this->forward404Unless($this->petition = $request->getParameter("petition_id"));

        if (!$request->isMethod(sfRequest::POST))
            $this->redirect($this->generateUrl('media_files_list', array('id' => $this->petition)));

        $this->form = new MediaFilesForm();
        $files = $request->getFiles($this->form->getName());

        $path = MediaFiles::FILE_PATH . $this->petition;

        $config = array(
            'path' => \sfConfig::get('sf_root_dir') . $path,
            'auto_rename'     => false,
            'extension' => '',
            'randomize' => false,
            'max_size' => $limit, // 200kb            
            'ext_whitelist' => array('svg','img', 'jpg', 'jpeg', 'gif', 'png'),
            'overwrite'       => false,
        );

        $upload = new \Fuel\Upload\Upload($config);
        $upload->processFiles();
        $upload->validate();

        if ($upload->isValid()) {

            $files = $upload->getAllFiles();
            $file = isset($files[0]) ? $files[0] : null;

            $filesPetition = MediaFilesTable::getInstance()->queryByPetition($this->petition)->execute();

            $totalSize = 0;
            if ($filesPetition) {
                $size = 0;
                foreach ($filesPetition as $key => $value) {
                    $size += (int) $value->getSize();
                }
                $totalSize = $size + $file->size;
            }

            if ($totalSize > $spaceLimit) {
                $space = $spaceLimit - $size;
                $errorData[] = "Limit has been  exceeded. Max 2MB for all images. Space left: " . $space;
            }

            if ($file) {
                /*
                 *  Upload image
                 */
                $upload->save();

                $mediaFiles = new MediaFiles();
                $mediaFiles->set("petition_id", $this->petition);
                $mediaFiles->set("filename", $file->filename);
                $mediaFiles->set("name", $file->basename);
                $mediaFiles->set("size", $file->size);
                $mediaFiles->set("extention", $file->extension);
                $mediaFiles->set("mimetype", $file->mimetype);
                $mediaFiles->set("path", $path);

                $mediaFiles->save();

                $mediaFiles->set("sort_order", $mediaFiles->getId());
                $mediaFiles->save();

                $this->getUser()->setFlash('success', "Image has been uploaded correctly");
            }
        }

        $errorData = array();
        foreach ($upload->getInvalidFiles() as $file) {
            $errorsArray = $file->getErrors();
            foreach ($errorsArray as $error) {
                if ($error->getError() !== 4) { // 4 is 'No File Uploaded'
                    $errorData[] = $file['name'] . ': ' . $error->getMessage();
                }
            }
        }

        if ($errorData) {
            $this->getUser()->setFlash('errors', $errorData);
        }

        $this->redirect($this->generateUrl('media_files_list', array('id' => $this->petition)));
    }

}
