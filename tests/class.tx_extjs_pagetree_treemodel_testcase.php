<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2005-2006 Robert Lemke (robert@typo3.org)
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

require_once (t3lib_extMgm::extPath('t3unit').'class.tx_t3unit_testcase.php');
require_once (t3lib_extMgm::extPath('bridge_extgui').'lib/model/class.tx_bridgeextgui_TreeModel.php');
require_once (t3lib_extMgm::extPath('bridge_extgui').'lib/model/class.tx_bridgeextgui_TreeNodeModel.php');
require_once (t3lib_extMgm::extPath('bridge_extgui').'lib/model/class.tx_bridgeextgui_TreeFactory.php');


class tx_bridge_extgui_treemodel_testcase extends tx_t3unit_testcase {
	private $node_rand_values;

	function testTreemodel_createTree(){
		$tree = self::getTreeWithOneNode();
		self::AssertTrue(is_array($tree->getTreeArray()));
	}

	function testTreemodel_countChilds(){
		$tree = self::getTreeWithOneNode();

		self::AssertTrue(count($tree->getTreeArray()) == 1);
	}

	function testTreeNodemodel_getLabel(){
		$tree = self::getTreeWithOneNode();
		$label = $tree->getRoot()->getLabel();
		self::AssertEquals('moc',$label);
	}

	function testTreeNodemodel_addChild(){
		$tree = self::getTreeWithOneNode();
		$root = $tree->getRoot();

		for($i = 0; $i < 10; $i++){
			$new_node = t3lib_div::makeInstance('tx_bridgeextgui_TreeNodeModel');
			$new_node->setLabel('Im Node '.$i);
			$root->addChild($new_node);
		}
		self::AssertEquals(11,$root->countChilds());
	}

	/**
	 * Test for the isLeaf Method
	 */
	function testTreeNodemodel_isLeaf(){
		$tree = self::getTreeWithOneNode();
		$root = $tree->getRoot();

		for($i = 0; $i < 10; $i++){
			$new_node = t3lib_div::makeInstance('tx_bridgeextgui_TreeNodeModel');
			$new_node->setLabel('Im Node '.$i);
			$root->addChild($new_node);
		}

		foreach($root->getChilds() as $child_node){
			self::AssertTrue($child_node->isLeaf());
		}
	}

	/**
	 * Test the getNodeById Method.
	 */
	function testTreemodel_findChild(){
		$tree = $this->getTreeWithMultipleNodes();
		$node = $tree->getNodeById($this->node_rand_values[2][1244]);
		self::assertTrue(is_object($node),'tree node should be found but wasn\'t found');
	}

	/**
	 * Test the addNode By Parent Id Method to insert  node into the tree.
	 */
	function testTreemodel_addNodeByParentId(){
		$tree = $this->getTreeWithMultipleNodes();
	}

	/**
	 * Test the pagetree adapter by creating a pagetree object.
	 */
	function testTreeFactory_buildPagetree(){
		$factory = new tx_bridge_extgui_treefactory();
		$factory->buildTreeFromPagetreeObject();

		self::assertTrue(is_a($factory->getTree(), 'tx_bridgeextgui_TreeModel'),'Creating tree from Pagetree Object failed');
	}

	/**
	 * Method to get an initialized three with multiple nodes
	 */
	private function getTreeWithMultipleNodes(){
		$tree = self::getTreeWithOneNode();
		$root = $tree->getRoot();

		$node_rand_values;
		for($i = 0; $i < 10; $i++){
				
			$new_node = new tx_bridge_extgui_treenodemodel();
			$new_node->setLabel('I am Node '.$i);

			for($j = 1000; $j < 2000; $j++){
				$sub_node = new tx_bridge_extgui_treenodemodel();

				$this->node_rand_values[$i][$j] = mt_rand(1,2000000);
				$sub_node->setId($this->node_rand_values[$i][$j]);

				$new_node->addChild($sub_node);
			}

			$root->addChild($new_node);
		}

		return $tree;
	}
	
	/**
	 * Private method to inizialize a tree with one root node.
	 */
	private function getTreeWithOneNode(){
		$tree = t3lib_div::makeInstance('tx_bridgeextgui_TreeModel');
		$root = t3lib_div::makeInstance('tx_bridgeextgui_TreeNodeModel');

		$root->setLabel('moc');
		$sub_node = t3lib_div::makeInstance('tx_bridgeextgui_TreeNodeModel');
		$sub_node->setLabel('test2');
		
		$root->addChild($sub_node);		
		$tree->setRoot($root);

		return $tree;
	}
}
?>