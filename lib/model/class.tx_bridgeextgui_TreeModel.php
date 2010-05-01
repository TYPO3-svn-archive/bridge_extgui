<?php
/**
 * Presentationmodel for an extjs-treewidget
 *
 * @author Timo Schmidt
 */

class tx_bridgeextgui_TreeModel{
	private $root_node;
	private $tree_array;
	private $node_found = false;

	public function __construct(){
		$this->tree_array = array();
	}
	
	/**
	 * Method to set a node as a root node of a tree.
	 *
	 * @param tx_bridge_extgui_treenodemodel $the_root_node
	 */
	public function setRoot($the_root_node){
		$this->root_node = $the_root_node;
	}

	/**
	 * Returns the rootnode of the tree
	 *
	 * @return tx_bridge_extgui_treenodemodel
	 */
	public function getRoot(){
		return $this->root_node;
	}

	/**
	 * Returns the complete tree an an array data structure
	 *
	 * @return array
	 */
	public function getTreeArray(){
		$this->tree_array = $this->buildTreeArray($this->root_node,0,$this->tree_array);

		return $this->tree_array['children'];
	}

	/**
	 * Method to build the internal tree array datastructure
	 *
	 * @param unknown_type $base
	 * @param unknown_type $cur
	 * @return unknown
	 */
	private function buildTreeArray($base, $cur){
		$cur = $this->buildNodeArray($base);
		$i = 0;
		foreach($base->getChilds() as $child_node){	
			if(!$child_node->isLeaf()){
				//if the child node is not a leaf call the metod recursive
				$this->buildTreeArray($child_node, &$cur['children'][$i]);
			}else{
				$cur['children'][$i] = $this->buildNodeArray($child_node);
			}
			$i++;
		}

		return $cur;
	}
	
	
	/**
	 * Returns a child node as array, by a given id
	 *
	 * @param int $id
	 * @return array
	 */
	public function getChildNodeArray($id){
		$res = array();
		$node = $this->getNodeById($id);
		if(!$node->isLeaf()){
			foreach($node->getChilds() as $child_node){
				$res[] = $this->buildNodeArray($child_node);
			}
		}

		return $res;
	}

	/**
	 * Builds the array representation of a node.
	 *
	 * @param tx_bridge_extgui_treenodemodel $node
	 * @return array
	 */
	private function buildNodeArray($node){

		$res = array(	'text' => (trim($node->getLabel()) == "") ? '...' : utf8_encode(trim($node->getLabel())) ,
						'id' =>$node->getId(),
						'cls'=>$node->isLeaf() ? 'file' : 'folder',
						'draggable' => true,
						'allowDrop' => true,
						'allowChildren' =>  true,
						'leaf' => $node->isLeaf() ? true : false
		);

		$custom_attributes = $node->getAttributes();

		if(is_array($custom_attributes)){ return array_merge($res,$custom_attributes); }else{ return $res;};
	}

	/**
	 * Adds a node as child to a node by a given id.
	 *
	 * @param int $parent_id
	 * @param tx_bridge_extgui_treenodemodel $child
	 */
	public function addNodeByParentId($parent_id,$child){
		$parent = $this->getNodeById($parent_id);
		$parent->addChild($child);
	}

	/**
	 * Returns a node by its id
	 *
	 * @param int $id
	 * @param tx_bridge_extgui_treenodemodel $act_node starting node
	 * @return tx_bridge_extgui_treenodemodel
	 */
	public function getNodeById($id,$act_node = false){
		$this->node_found = false;

		if(!$act_node){
			$act_node = $this->getRoot();
		}

		if((int)$act_node->getId() == (int)$id){
			$this->node_found = true;
			return $act_node;
		}elseif(!$act_node->isLeaf()){
			foreach($act_node->getChilds() as $child_node){
				$res = $this->getNodeById($id,$child_node);
				if($this->node_found) break;
			}
		}

		return $res;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bridge_extgui/lib/model/class.tx_bridgeextgui_TreeModel.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bridge_extgui/lib/model/class.tx_bridgeextgui_TreeModel.php']);
}
?>