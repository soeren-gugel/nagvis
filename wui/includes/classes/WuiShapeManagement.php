<?php
/*****************************************************************************
 *
 * WuiShapeManagement.php - Class for managing shapes in WUI
 *
 * Copyright (c) 2004-2008 NagVis Project (Contact: lars@vertical-visions.de)
 *
 * License:
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *
 *****************************************************************************/
 
/**
 * @author	Lars Michelsen <lars@vertical-visions.de>
 */
class WuiShapeManagement extends GlobalPage {
    var $MAINCFG;
    var $LANG;
    var $ADDFORM;
    var $DELFORM;
    var $propCount;
    
    /**
    * Class Constructor
    *
    * @param  GlobalMainCfg $MAINCFG
    * @author Lars Michelsen <lars@vertical-visions.de>
    */
    function WuiShapeManagement(&$MAINCFG) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method WuiShapeManagement::WuiShapeManagement(&$MAINCFG)');
        $this->MAINCFG = &$MAINCFG;
        $this->propCount = 0;
        
        // load the language file
        $this->LANG = new GlobalLanguage($MAINCFG,'wui:shapeManagement');
        
        $prop = Array('title'=>$MAINCFG->getValue('internal', 'title'),
                    'cssIncludes'=>Array('./includes/css/wui.css'),
                    'jsIncludes'=>Array('./includes/js/ShapeManagement.js',
                        './includes/js/ajax.js',
                        './includes/js/wui.js'),
                    'extHeader'=>Array(''),
                    'allowedUsers' => $this->MAINCFG->getValue('wui','allowedforconfig'),
                    'languageRoot' => 'wui:shapeManagement');
        parent::GlobalPage($MAINCFG,$prop);
        if (DEBUG&&DEBUGLEVEL&1) debug('End method WuiShapeManagement::WuiShapeManagement()');
    }
    
    /**
    * If enabled, the form is added to the page
    *
    * @author Lars Michelsen <lars@vertical-visions.de>
    */
    function getForm() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method WuiShapeManagement::getForm()');
        // Inititalize language for JS
        $this->addBodyLines($this->parseJs($this->getJsLang()));
        
        $this->ADDFORM = new GlobalForm(Array('name'=>'shape_add',
                                                'id'=>'shape_add',
                                                'method'=>'POST',
                                                'action'=>'./form_handler.php?myaction=mgt_shape_add',
                                                'onSubmit'=>'return check_image_add();',
                                                'enctype'=>'multipart/form-data',
                                                'cols'=>'2'));
        $this->addBodyLines($this->ADDFORM->initForm());
        $this->addBodyLines($this->ADDFORM->getCatLine(strtoupper($this->LANG->getLabel('uploadShape'))));
        $this->propCount++;
        $this->addBodyLines($this->getAddFields());
        $this->propCount++;
        $this->addBodyLines($this->ADDFORM->getSubmitLine($this->LANG->getLabel('upload')));
        $this->addBodyLines($this->ADDFORM->closeForm());
        
        $this->DELFORM = new GlobalForm(Array('name'=>'shape_delete',
                                                'id'=>'shape_delete',
                                                'method'=>'POST',
                                                'action'=>'./form_handler.php?myaction=mgt_shape_delete',
                                                'onSubmit'=>'return check_image_delete();',
                                                'cols'=>'2'));
        $this->addBodyLines($this->DELFORM->initForm());
        $this->addBodyLines($this->DELFORM->getCatLine(strtoupper($this->LANG->getLabel('deleteShape'))));
        $this->propCount++;
        $this->addBodyLines($this->getDelFields());
        $this->propCount++;
        $this->addBodyLines($this->ADDFORM->getSubmitLine($this->LANG->getLabel('delete')));
        $this->addBodyLines($this->ADDFORM->closeForm());
        
        // Resize the window
        $this->addBodyLines($this->parseJs($this->resizeWindow(540,$this->propCount*40+10)));
        if (DEBUG&&DEBUGLEVEL&1) debug('End method WuiShapeManagement::getForm()');
    }
    
    /**
    * Gets new image fields
    *
    * @return Array HTML Code
    * @author  Lars Michelsen <lars@vertical-visions.de>
    */
    function getAddFields() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method WuiShapeManagement::getAddFields()');
        $ret = Array();
        $ret = array_merge($ret,$this->ADDFORM->getHiddenField('MAX_FILE_SIZE','1000000'));
        $ret = array_merge($ret,$this->ADDFORM->getFileLine($this->LANG->getLabel('choosePngImage'),'shape_image',''));
        $this->propCount++;
        
        if (DEBUG&&DEBUGLEVEL&1) debug('End method WuiShapeManagement::getAddFields(): Array(HTML)');
        return $ret;
    }
    
    /**
    * Gets delete fields
    *
    * @return Array HTML Code
    * @author  Lars Michelsen <lars@vertical-visions.de>
    */
    function getDelFields() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method WuiShapeManagement::getDelFields()');
        $ret = Array();
        $ret = array_merge($ret,$this->DELFORM->getSelectLine($this->LANG->getLabel('choosePngImage'),'shape_image',$this->getMapImages(),''));
        $this->propCount++;
        
        if (DEBUG&&DEBUGLEVEL&1) debug('End method WuiShapeManagement::getDelFields(): Array(HTML)');
        return $ret;
    }
    
    /**
    * Reads all map images in shape path
    *
    * @return Array map images
    * @author  Lars Michelsen <lars@vertical-visions.de>
    */
    function getMapImages() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method WuiShapeManagement::getMapImages()');
        $files = Array();
        
        if ($handle = opendir($this->MAINCFG->getValue('paths', 'shape'))) {
            while (false !== ($file = readdir($handle))) {
                if($file != '.' && $file != '..' && substr($file,strlen($file)-4,4) == ".png") {
                    $files[] = $file;
                }
            }
    
            if ($files) {
                natcasesort($files);
            }
            closedir($handle);
        }
    
        if (DEBUG&&DEBUGLEVEL&1) debug('End method WuiShapeManagement::getMapImages(): Array(...)');
        return $files;
    }
    
    /**
    * Gets all needed messages
    *
    * @return Array JS
    * @author  Lars Michelsen <lars@vertical-visions.de>
    */
    function getJsLang() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method WuiShapeManagement::getJsLang()');
        $ret = Array();
        $ret[] = 'var lang = Array();';
        $ret[] = 'lang[\'firstMustChoosePngImage\'] = \''.$this->LANG->getMessageText('firstMustChoosePngImage').'\';';
        $ret[] = 'lang[\'mustChoosePngImage\'] = \''.$this->LANG->getMessageText('mustChoosePngImage').'\';';
        $ret[] = 'lang[\'foundNoShapeToDelete\'] = \''.$this->LANG->getMessageText('foundNoShapeToDelete').'\';';
				$ret[] = 'lang[\'shapeInUse\'] = \''.$this->LANG->getMessageText('shapeInUse').'\';';
        $ret[] = 'lang[\'confirmShapeDeletion\'] = \''.$this->LANG->getMessageText('confirmShapeDeletion').'\';';
        $ret[] = 'lang[\'unableToDeleteShape\'] = \''.$this->LANG->getMessageText('unableToDeleteShape').'\';';
        
        if (DEBUG&&DEBUGLEVEL&1) debug('End method WuiShapeManagement::getJsLang(): Array(JS)');
        return $ret;
    }
		
		/**
		 * Deletes the given shape image
		 *
		 * @param		String	Filename of the shape image
		 * @author	Lars Michelsen <lars@vertical-visions.de>
		 */
		function deleteShape($fileName) {
			if(file_exists($this->MAINCFG->getValue('paths', 'shape').$fileName)) {
				if(unlink($this->MAINCFG->getValue('paths', 'shape').$fileName)) {
					// Go back to last page
					print("<script>window.history.back();</script>");
				} else {
					// Error handling
					print("ERROR: ".$this->LANG->getLabel('failedToDeleteShape','IMAGE~'.$fileName));
				}
			} else {
				// Error handling
				print("ERROR: ".$this->LANG->getLabel('shapeDoesNotExist','IMAGE~'.$fileName));
			}
		}
		
		/**
		 * Uploads a new shape image
		 *
		 * @param		Array	Informations of the new image
		 * @author	Lars Michelsen <lars@vertical-visions.de>
		 */
		function uploadShape(&$arrFile) {
			// check the file (the map) is properly uploaded
			if(is_uploaded_file($arrFile['tmp_name'])) {
				if(preg_match('/\.png/i', $arrFile['name'])) {
					if(@move_uploaded_file($arrFile['tmp_name'], $this->MAINCFG->getValue('paths', 'shape').$arrFile['name'])) {
						// Change permissions of the file after the upload
						chmod($this->MAINCFG->getValue('paths', 'shape').$arrFile['name'],0666);
						
						// Go back to last page
						print("<script>window.history.back();</script>");
					} else {
						// Error handling
						print("ERROR: ".$this->LANG->getLabel('moveUploadedFileFailed','PATH~'.$this->MAINCFG->getValue('paths', 'shape')));
					}
				} else {
					// Error handling
					print("ERROR: ".$this->LANG->getLabel('mustBePngFile'));
				}
			} else {
				// Error handling
				print("ERROR: ".$this->LANG->getLabel('uploadFailed'));
			}
		}
}
