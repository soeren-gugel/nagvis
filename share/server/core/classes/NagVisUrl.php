<?php
/*****************************************************************************
 *
 * NagVisUrl.php - This class handles urls which should be shown in NagVis
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
class NagVisUrl {
	private $CORE;
	
	private $strUrl;
	private $strContents;
	
	/**
	 * Class Constructor
	 *
	 * @param   GlobalCore 	$CORE
	 * @param   String      URL
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	public function __construct(GlobalCore $CORE, $strUrl) {
		$this->CORE = $CORE;
		
		$this->strUrl = $strUrl;
		$this->strContents = '';
	}
	
	/**
	 * Fetches the contets of the specified URL
	 *
	 * @param 	GlobalCore 	$CORE
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	private function fetchContents() {
		// Suppress error messages from file_get_contents
		$oldLevel = error_reporting(0);

		// Only allow urls not paths for security reasons
		// Reported here: http://news.gmane.org/find-root.php?message_id=%3cf60c42280909021938s7f36c0edhd66d3e9156a5d081%40mail.gmail.com%3e
		$aUrl = parse_url($this->strUrl);
		if(!isset($aUrl['scheme']) || $aUrl['scheme'] == '') {
			echo new GlobalMessage('ERROR', $this->CORE->getLang()->getText('problemReadingUrl', Array('URL' => htmlentities($this->strUrl), 'MSG' => 'Not allowed url')), null, 'error');
			exit(1);
		}
				
		if(false == ($this->strContents = file_get_contents($this->strUrl))) {
			$aError = error_get_last();
			
			echo new GlobalMessage('ERROR', $this->CORE->getLang()->getText('problemReadingUrl', Array('URL' => htmlentities($this->strUrl), 'MSG' => $aError['message'])), null, 'error');
		}
		
		// set the old level of reporting back
		error_reporting($oldLevel);
	}
	
	/**
	 * Gets the contents of the URL
	 *
	 * @return  String  Contents of the URL
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getContents() {
		if($this->strContents == '') {
			$this->fetchContents();
		}
		
		return $this->strContents;
	}
	
	/**
	 * Parses the url options in json format
	 *
	 * @return	String 	String with JSON Code
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function parsePropertiesJson() {
		$arr = Array();
		$arr['url'] = $this->strUrl;
		/*$arr['name'] = $this->MAPCFG->getName();
		$arr['alias'] = $this->MAPCFG->getValue('global', 0, 'alias');
		$arr['background_image'] = $this->getBackgroundJson();
		$arr['background_color'] = $this->MAPCFG->getValue('global', 0, 'background_color');
		$arr['favicon_image'] = $this->getFavicon();
		$arr['page_title'] = $this->MAPCFG->getValue('global', 0, 'alias').' ('.$this->MAPOBJ->getSummaryState().') :: '.$this->CORE->getMainCfg()->getValue('internal', 'title');*/
		
		return json_encode($arr);
	}
}
?>
