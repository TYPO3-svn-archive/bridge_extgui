<?php
class tx_bridgeextgui_Controller extends tx_bridgelib_AbstractController{
	private $printtree_view;
	private $pagetree_view;
	private $celem_view;

	public function setPrinttreeView($view){
		$this->printtree_view = $view;
	}

	public function setPagetreeView($view){
		$this->pagetree_view = $view;
	}

	public function setCelemView($view){
		$this->celem_view = $view;
	}

	/**
	 * Facademethod to handle the post request for the controller.
	 * It dispatches each call to the correct controller-method.
	 *
	 * @param string $action
	 */
	public function handleRequest($action){
		global $BE_USER;

		$foreign_uid = (int) mysql_real_escape_string(t3lib_div::_GP('foreign_uid'));
		//id of the celem which is the parent of the elem that should be inserted
		$pid =  (int) mysql_real_escape_string(t3lib_div::_GP('pid'));
		$sorting =  (int) mysql_real_escape_string(t3lib_div::_GP('index'));
		$uid 	= (int) mysql_real_escape_string(t3lib_div::_GP("uid"));
		$flowkey = t3lib_div::GPvar("flow_key");

		switch($action){
			case 'getpagetree':
				$factory = t3lib_div::makeInstance('tx_bridgeextgui_TreeFactory');
				$factory->buildTreeFromPagetreeObject();
				$tree = $factory->getTree();

				$this->pagetree_view->setTree($tree);
				$this->pagetree_view->render();
			break;

			case 'getprinttree':
				$selection_uid = $this->getCurrentSelectionUid();
				$factory = t3lib_div::makeInstance('tx_bridgeextgui_TreeFactory');
				$factory->buildTreeFromSelection(mysql_real_escape_string($selection_uid));
				$tree = $factory->getTree();

				$this->printtree_view->setTree($tree);
				$this->printtree_view->render();
			break;

			case 'getcelemtree':
				$factory = t3lib_div::makeInstance('tx_bridgeextgui_TreeFactory');
				$selection_uid = $this->getCurrentSelectionUid();

				if(isset($pid)){
					$factory->buildTreeFromSinglePage($pid,$selection_uid);
				}
				$tree = $factory->getTree();

				$this->celem_view->setTree($tree);
				$this->celem_view->render();
			break;

			case 'set-selection-uid':
				$this->setCurrentSelectionUid($uid);
			break;

			case 'getselections':
				$res = $this->getSelections();
				print(trim(json_encode($res)));
			break;

			case 'add-to-selection':
				//id of the celem
				$res = $this->addToSelection($foreign_uid,$pid,$sorting);
				print(trim(json_encode($res)));
			break;

			case 'move-in-selection':
				$this->moveInSelection($uid,$pid,$sorting);
			break;

			case 'remove-from-selection':
				//delete the export
				$this->handleRequest('remove-from-export');
				$this->removeFromSelection($uid);
			break;

			case 'move-up':
				$this->moveUp($uid);
			break;

			case 'move-down':
				$this->moveDown($uid);
			break;

			case 'add-to-export':
				$this->addToExport($uid);
			break;

			case 'remove-from-export':
				$this->removeFromExport($uid);
			break;

			case 'export-selection':
				$this->exportSelection();
			break;

			case 'gettransformationflows':
				$res = $this->getTransformationflows();
				print(trim(json_encode($res)));
			break;

			case 'transform-export':
				$this->transformExport($flowkey);
			break;

			default: break;
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bridge_extgui/lib/controller/class.tx_bridgeextgui_Controller.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bridge_extgui/lib/controller/class.tx_bridgeextgui_Controller.php']);
}
?>