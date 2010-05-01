<?php
/**
 * Factoryclass to build the instances of the tree-presentation-models
 *
 * @author Timo Schmidt
 */
require_once (PATH_t3lib.'class.t3lib_browsetree.php');

class tx_bridgeextgui_Treefactory{
	private $result_tree;
	private $source_tree;

	public function __construct(){
	}

	/**
	 * Method to build a tree from a printselection
	 *
	 * @param int $selection_uid
	 */
	public function buildTreeFromSelection($selection_uid){
		$selection = t3lib_div::makeInstance('tx_bridgelib_SelectionModel');
		$selection->loadFromUid($selection_uid);

		$this->result_tree = t3lib_div::makeInstance('tx_bridgeextgui_TreeModel');

		$root = t3lib_div::makeInstance('tx_bridgeextgui_TreeNodeModel');
		$root->setId(0);
		$this->result_tree->setRoot($root);
		$this->traversCelemRelationTree((int)0,$selection);
	}

	/**
	 * Method to build the nodes for the printtree from the contentelement models.
	 *
	 * @param int $pid
	 * @param tx_bridge_lib_selection_model $selection
	 */
	private function traversCelemRelationTree($pid,$selection){
		$relations = $selection->getRelations((int)$pid, true);
		if(is_array($relations)){
			foreach($relations as $relation){
				$cur_celem = $relation->getContentElement();
				$new_node = t3lib_div::makeInstance('tx_bridgeextgui_TreeNodeModel');
				$new_node->setId($relation->getUid());
				$new_node->setLabel($cur_celem->getHeader());
				$new_node->addAttribute('selection',$selection);
				$new_node->addAttribute('in_selection',true);
				$new_node->addAttribute('allowDelete',true);
				$new_node->addAttribute('allowChildren',true);
				$new_node->addAttribute('isExported',$relation->isExported());
				$new_node->setIsMoveable(true);
				$new_node->setAllowDrop(true);
			//	$new_node->makeToLeaf();
			
				$this->result_tree->addNodeByParentId($relation->getPid(),$new_node);
				$this->traversCelemRelationTree($relation->getUid(),$selection);
			}
		}
	}

	/**
	 * Builds a tree with only one level just for contentelements
	 * of one page
	 */
	public function buildTreeFromSinglePage($page_id, $selection_id){
		$celems_onpage 		= tx_bridgelib_ContentelementModel::loadAll($page_id);
		$current_selection 	= t3lib_div::makeInstance('tx_bridgelib_SelectionModel');
		$current_selection->loadFromUid($selection_id);

		$this->result_tree = t3lib_div::makeInstance('tx_bridgeextgui_TreeModel');
		$root = t3lib_div::makeInstance('tx_bridgeextgui_TreeNodeModel');
		$root->setId(0);
		$this->result_tree->setRoot($root);

		if(is_array($celems_onpage)){
			foreach($celems_onpage as $celem){
				if(!$current_selection->isContentElementInSelection($celem->getUid())){
					$new_node = t3lib_div::makeInstance('tx_bridgeextgui_TreeNodeModel');
					$new_node->setIsMoveable(true);
					$new_node->setId($celem->getUid());
					$new_node->setLabel($celem->getHeader());
					$this->result_tree->addNodeByParentId(0,$new_node);
				}
			}
		}
	}

	/**
	 * Method to buid the tree-presentation-model from the pagetree.
	 *
	 */
	public function buildTreeFromPagetreeObject(){
		$this->result_tree = t3lib_div::makeInstance('tx_bridgeextgui_TreeModel');
		$root = t3lib_div::makeInstance('tx_bridgeextgui_TreeNodeModel');
		$root->setId(0);

		$this->result_tree->setRoot($root);
		$this->loadTypo3PagetreeObject();

		foreach($this->source_tree->MOUNTS as $mount){
			$this->traversBranchAndAddToTree($mount,$root);
		}
	}

	/**
	 * Returns the an configured instance of the presentation-treemodel
	 *
	 * @return tx_bridge_extgui_treemodel
	 */
	public function getTree(){
		return $this->result_tree;
	}

	/**
	 * Traverses the pagetreenodes and adds them to the presentation-tree-model.
	 *
	 * @param unknown_type $branch_uid
	 * @param unknown_type $parent_node
	 */
	private function traversBranchAndAddToTree($branch_uid,$parent_node){
		$data = $this->source_tree->getDataInit($branch_uid);

		while($row = $this->source_tree->getDataNext($data)){
			$new_node = t3lib_div::makeInstance('tx_bridgeextgui_TreeNodeModel');
			$new_node->setId($row['uid']);
			$new_node->setLabel($row['title']);
			$parent_node->addChild($new_node);
			$this->traversBranchAndAddToTree($row['uid'],$new_node);
		}
	}

	/**
	 * Loads an instance from the typo3 core pagetree class (t3lib_browseTree)
	 *
	 */
	private function loadTypo3PagetreeObject(){
		/**
		 * Init Sourcetree (TYPO3 Pagetree)
		 */
		if(!isset($this->source_tree)){
			$this->source_tree = new t3lib_browseTree();
			$this->source_tree->table = 'pages';
			$perms_clause = $GLOBALS['BE_USER']->getPagePermsClause(1);
			$this->source_tree->init('AND '.$perms_clause);
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bridge_extgui/lib/model/class.tx_bridgeextgui_TreeFactory.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bridge_extgui/lib/model/class.tx_bridgeextgui_TreeFactory.php']);
}
?>