<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.4                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2011                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id$
 *
 */

/**
 * Page for displaying list of financial types
 */
class CRM_Grant_Page_GrantProgram extends CRM_Core_Page {

  protected $_id;
  /**
   * The action links that we need to display for the browse screen
   *
   * @var array
   */
  private static $_links;
  /**
   * Get action Links
   *
   * @return array (reference) of action links
   */
  function &links() {
    if (!(self::$_links)) {
      self::$_links = array(
        CRM_Core_Action::VIEW => array(
          'name' => ts('View'),
          'url' => 'civicrm/grant_program',
          'qs' => 'action=view&id=%%id%%&reset=1',
          'title' => ts('View Grant Program') 
        ),
        CRM_Core_Action::UPDATE => array(
          'name' => ts('Edit'),
          'url' => 'civicrm/grant_program',
          'qs' => 'action=update&id=%%id%%&reset=1',
          'title' => ts('Edit Grant Program') 
        ),
        CRM_Core_Action::DELETE => array(
          'name' => ts('Delete'),
          'url' => 'civicrm/grant_program',
          'qs' => 'action=delete&id=%%id%%',
          'title' => ts('Delete Grant Program') 
        )
      );
    }
    return self::$_links;
  }
    
  function browse() {
        
    $grantProgram = array();
    $dao = new CRM_Grant_DAO_GrantProgram();
        
    $dao->orderBy('label');
    $dao->find();
        
    while ($dao->fetch()) {
      $grantProgram[$dao->id] = array();
      CRM_Core_DAO::storeValues( $dao, $grantProgram[$dao->id]);
      $action = array_sum(array_keys($this->links()));

      $grantProgram[$dao->id]['action'] = CRM_Core_Action::formLink(
        self::links(), 
        $action, 
        array('id' => $dao->id)
      );
    }
    $grantType = CRM_Core_OptionGroup::values('grant_type');
    $grantStatus = CRM_Grant_BAO_GrantProgram::grantProgramStatus();
    foreach ($grantProgram as $key => $value) {
      $grantProgram[$key]['grant_type_id'] = $grantType[$value['grant_type_id']];
      $grantProgram[$key]['status_id'] = $grantStatus[CRM_Grant_BAO_GrantProgram::getOptionValue($value['status_id'])];
    }
    $this->assign('programs',$grantProgram);
  }
    
  function run() {
    $action = CRM_Utils_Request::retrieve(
      'action', 
      'String',
      $this, 
      FALSE, 
      0 
    );
    if ($action & CRM_Core_Action::VIEW) { 
      $this->view($action); 
    } 
    elseif ($action & (CRM_Core_Action::UPDATE | CRM_Core_Action::ADD | CRM_Core_Action::DELETE)) {
      $this->edit($action);
    } 
    else {
      $this->browse(); 
    }
    $this->assign('action', $action);
    if ($action & CRM_Core_Action::DELETE) {
      CRM_Core_Session::setStatus(ts('Deleting a grant program cannot be undone. Do you want to continue?'), NULL, 'no-popup');
    }
    return parent::run();
  }

  function edit($action) {
    $controller = new CRM_Core_Controller_Simple('CRM_Grant_Form_GrantProgram', ts(''), $action);
    $controller->setEmbedded(TRUE);
    $result = $controller->process();
    $result = $controller->run();
  }

  function view($action) {   
    $controller = new CRM_Core_Controller_Simple('CRM_Grant_Form_GrantProgramView', ts(''), $action);
    $controller->setEmbedded(TRUE);  
    $result = $controller->process();
    $result = $controller->run();
  }
}
