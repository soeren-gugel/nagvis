<?php
/*******************************************************************************
 *
 * CoreAuthorisationModSession.php - Authorsiation module based on sessions
 *
 * Copyright (c) 2004-2009 NagVis Project (Contact: info@nagvis.org)
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
 ******************************************************************************/

/**
 * Checks if there are authorisation information stored in a session
 * If so it tries to reuse the stored information
 *
 * @author Lars Michelsen <lars@vertical-visions.de>
 */
class CoreAuthorisationModSession extends CoreAuthorisationModule {
	private $CORE;
	private $SHANDLER;
	
	public function __construct(GlobalCore $CORE, CoreAuthHandler $AUTHENTICATION) {
		$this->CORE = $CORE;
		$this->SHANDLER = new CoreSessionHandler($this->CORE->getMainCfg()->getValue('global', 'sesscookiedomain'), 
		                                         $this->CORE->getMainCfg()->getValue('global', 'sesscookiepath'),
		                                         $this->CORE->getMainCfg()->getValue('global', 'sesscookieduration'));
	}
	
	public function parsePermissions() {
		if($this->SHANDLER->isSetAndNotEmpty('userPermissions')) {
			return $this->SHANDLER->get('userPermissions');
		} else {
			return false;
		}
	}
}
?>
