<?php

//
// Open Web Analytics - An Open Source Web Analytics Framework
//
// Copyright 2006 Peter Adams. All rights reserved.
//
// Licensed under GPL v2.0 http://www.gnu.org/copyleft/gpl.html
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
//
// $Id$
//

require_once(OWA_DIR.'owa_lib.php');
require_once(OWA_DIR.'owa_view.php');
require_once(OWA_DIR.'owa_adminController.php');
/**
 * Options View
 * 
 * @author      Peter Adams <peter@openwebanalytics.com>
 * @copyright   Copyright &copy; 2006 Peter Adams <peter@openwebanalytics.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GPL v2.0
 * @category    owa
 * @package     owa
 * @version		$Revision$	      
 * @since		owa 1.0.0
 */

class owa_optionsGeneralView extends owa_view {
	
	function owa_optionsGeneralView($params) {
		
		$this->owa_view($params);
		//set priviledge level
		$this->_setPriviledgeLevel('admin');
		//set page type
		$this->_setPageType('Administration Page');
		
		return;
	}
	
	function construct($data) {
		
		// load template
		$this->body->set_template('options_general.tpl');
		// fetch admin links from all modules
		$this->body->set('headline', 'General Configuration Options');
		
		//print_r($data['config']);
		// assign config data
		$this->body->set('config', $data['configuration']);
		
		return;
	}
	
	
}

/**
 * Admin Settings/Options Controller
 * 
 * @author      Peter Adams <peter@openwebanalytics.com>
 * @copyright   Copyright &copy; 2006 Peter Adams <peter@openwebanalytics.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GPL v2.0
 * @category    owa
 * @package     owa
 * @version		$Revision$	      
 * @since		owa 1.0.0
 */

class owa_optionsGeneralController extends owa_adminController {
	
	function __construct($params) {
	
		parent::__construct($params);
		$this->priviledge_level = 'admin';
		$this->type = 'options';
		$this->_setCapability('alter_config');
		
		return;
	}
	
	/**
	 * PHP 4 Style Constructor
	 */
	function owa_optionsGeneralController($params) {
		
		return owa_optionsGeneralController::__construct($params);
	}
	

	function action() {
		
		$this->data['configuration'] = $this->c->fetch('base');
			
		// add data to container
		$this->data['view'] = 'base.options';
		$this->data['subview'] = 'base.optionsGeneral';
		$this->data['view_method'] = 'delegate';
		
		return $this->data;
	
	}
	
}



?>