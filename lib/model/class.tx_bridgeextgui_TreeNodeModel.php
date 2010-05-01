<?php
class tx_bridgeextgui_TreeNodeModel{
	private $label;
	private $id;
	private $childs;
	private $ismoveable;
	private $allowdrop;
	private $attributes;

	private $isleaf;

	public function __construct(){
		$this->childs = array();
	}

	/**
	 * Method to set the label for the node.
	 *
	 * @param string label
	 */
	public function setLabel($the_label){
		$this->label = $the_label;
	}

	/**
	 * Returns the node label.
	 *
	 * @return string label
	 */
	public function getLabel(){
		return $this->label;
	}

	/**
	 * Returns the unique identifier.
	 *
	 * @return int id
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Method to set the identifier for this node.
	 *
	 * @param string $the_id
	 */
	public function setId($the_id){
		$this->id = $the_id;
	}

	/**
	 * Mark the node as moveable. This property will be evaluated by the extjs frontend.
	 *
	 * @param boolean $boolean
	 */
	public function setIsMoveable($boolean){
		$this->ismoveable = $boolean;
	}

	/**
	 * Method to check is this node is moveable or not
	 *
	 * @return boolean
	 */
	public function isMoveable(){
		return $this->ismoveable;
	}

	/**
	 * Mark this node to accept drops.
	 *
	 * @param unknown_type $boolean
	 */
	public function setAllowDrop($boolean){
		$this->allowdrop = $boolean;
	}

	/**
	 * Method to check if this node accepts drops.
	 *
	 * @return boolean
	 */
	public function getAllowDrop(){
		return $this->allowdrop;
	}

	/**
	 * Returns an array with all childnodes of this node
	 *
	 * @return array array with treenodemodel instances
	 */
	public function getChilds(){
		return $this->childs;
	}

	/**
	 * Determines the number of childnodes.
	 *
	 * @return int number of child nodes
	 */
	public function countChilds(){
		return count($this->childs);
	}

	/**
	 * Add a childnode to this node.
	 *
	 * @param tx_bridge_extgui_treenodemodel $child_node
	 */
	public function addChild($child_node){
		$this->childs[]= $child_node;
	}

	/**
	 * Determines if this node is a leaf or not.
	 * Nodes without childs are leafs or nodes which haven been set as leaf.
	 *
	 * @return boolean
	 */
	public function isLeaf(){
		if((count($this->childs) > 0) || $this->isleaf ){ return false; }else{ return true;}
	}

	/**
	 * Make a node to a leaf without childs
	 *
	 */
	public function makeToLeaf(){
		$this->isleaf = true;
	}

	/**
	 * Add a attribute to the node.
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function addAttribute($name, $value){
		$this->attributes[$name] = $value;
	}

	public function getAttributes(){
		return $this->attributes;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bridge_extgui/lib/model/class.tx_bridgeextgui_TreeNodeModel.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bridge_extgui/lib/model/class.tx_bridge_extgui_TreeNodeModel.php']);
}
?>