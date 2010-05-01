<?php
class tx_bridgeextgui_ListView{
	//private
	private $list;

	/**
	 * Method to set the treenodemodel for this treeview
	 *
	 * @param unknown_type $tree
	 */
	public function setList($list){
		$this->list = $list;
	}

	/**
	 * Method to render the tree as json string for the frontend
	 *
	 */
	public function render(){
		$nodearr = $this->tree->getChildNodeArray(t3lib_div::GPVar('node'));
		print(trim(json_encode($nodearr)));
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bridge_extgui/lib/view/class.tx_bridgeextgui_ListView.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bridge_extgui/lib/view/class.tx_bridgeextgui_ListView.php']);
}
?>