<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008  <>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH.'init.php');
require_once($BACK_PATH.'template.php');

$LANG->includeLLFile('EXT:bridge_extgui/mod2/locallang.xml');
require_once(PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
// DEFAULT initialization of a module [END]

$classpath = PATH_txbridge_lib.'lib/';

// INCLUDE configurator classes
require_once($classpath.'misc/class.tx_bridgelib_Configurator.php');

// INCLUDE controller classes
require_once($classpath.'controller/class.tx_bridgelib_AbstractController.php');

// INCLUDE model classes
require_once($classpath.'model/class.tx_bridgelib_AbstractDbmodel.php');
require_once($classpath.'model/class.tx_bridgelib_SelectionContentelementRelationModel.php');
require_once($classpath.'model/class.tx_bridgelib_SelectionModel.php');
require_once($classpath.'model/class.tx_bridgelib_ContentelementModel.php');

//INCLUDE export handling classes
require_once($classpath.'export/class.tx_bridgelib_AbstractExporthandler.php');
require_once($classpath.'export/class.tx_bridgelib_DirectExporthandler.php');
require_once($classpath.'export/class.tx_bridgelib_ExporthandlerFactory.php');
require_once($classpath.'export/class.tx_bridgelib_Tocbuilder.php');
require_once($classpath.'export/class.tx_bridgelib_Xmlexport.php');

//INCLUDE transformation handling classes
require_once($classpath.'trafo/class.tx_bridgelib_AbstractTransformer.php');
require_once($classpath.'trafo/class.tx_bridgelib_XslTransformer.php');
require_once($classpath.'trafo/class.tx_bridgelib_PdflatexTransformer.php');
require_once($classpath.'trafo/class.tx_bridgelib_ZipTransformer.php');

require_once($classpath.'trafo/class.tx_bridgelib_Transformationflow.php');
require_once($classpath.'trafo/class.tx_bridgelib_TransformationflowFactory.php');

//XML EXPORT
$classpath = PATH_txbridge_extgui.'lib/';

// INCLUDE model classes
require_once($classpath.'model/class.tx_bridgeextgui_TreeFactory.php');
require_once($classpath.'model/class.tx_bridgeextgui_TreeModel.php');
require_once($classpath.'model/class.tx_bridgeextgui_TreeNodeModel.php');
require_once($classpath.'controller/class.tx_bridgeextgui_Controller.php');
require_once($classpath.'view/class.tx_bridgeextgui_TreeView.php');

/**
 * Module 'Select and Order' for the 'bridge_extgui' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_bridgeextgui
 */
class  tx_bridgeextgui_module2 extends t3lib_SCbase {
	var $pageinfo;

	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		parent::init();
	}


	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BACK_PATH;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		$ajax = t3lib_div::_GP('ajax');

		if($ajax == 1){
			$this->handleAjaxCall();
		}else{
			/* @var $this->doc bigDoc */
			$this->doc = t3lib_div::makeInstance('bigDoc');
			$this->doc->getPageRenderer()->loadExtCore();
			$this->doc->getPageRenderer()->loadExtJS();
			
			$this->doc->backPath = $BACK_PATH;
			$this->handleNormalCall();
			$this->content.=$this->doc->endPage();
		}
	}

	private function handleAjaxCall(){

		$controller = t3lib_div::makeInstance('tx_bridgeextgui_Controller');
		$controller->setCelemView(t3lib_div::makeInstance('tx_bridgeextgui_TreeView'));
		$controller->setPagetreeView(t3lib_div::makeInstance('tx_bridgeextgui_TreeView'));
		$controller->setPrinttreeView(t3lib_div::makeInstance('tx_bridgeextgui_TreeView'));

		$action = mysql_real_escape_string(t3lib_div::_GP('action'));
		$controller->handleRequest($action);
	}

	private function handleNormalCall(){
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		// Draw the header.
		$this->doc->form='<form action="" method="POST">';

		// JavaScript
		$this->doc->JScode = '
   						    <script type="text/javascript" src="../res/js/abstracttree.js"></script>
						    <script type="text/javascript" src="../res/js/globals.js"></script>
						    <script type="text/javascript" src="../res/js/celemtree.js"></script>
						    <script type="text/javascript" src="../res/js/printree.js"></script>
						    <script type="text/javascript" src="../res/js/pagetree.js"></script>
						    <script type="text/javascript" src="../res/js/selectfactory.js"></script>
						    <script type="text/javascript" src="../res/js/ui.js"></script>
						    ';

		$this->doc->postCode='
							<script language="javascript" type="text/javascript">
								script_ended = 1;
								if (top.fsMod) top.fsMod.recentIds["web"] = 0;
							</script>
						';

		$this->content.=$this->doc->startPage($LANG->getLL('title'));

		// Render content:
		$this->moduleContent();
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()	{

		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	function moduleContent()	{
		$this->content .='<div id="pagetree" style="width: 300px; height: 300px;"></div>
						  <div id="celems" style="width: 300px; height: 300px;"></div>
						  <div id="printtree" style="width: 500px; height: 500px;">
						  </div>
						  <div id="south"></div>';
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bridge_extgui/mod2/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bridge_extgui/mod2/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_bridgeextgui_module2');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>