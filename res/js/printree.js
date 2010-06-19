function Printtree(){
    var self = this;
   	var node_queue = new Array();

    // shorthand
    var Tree = Ext.tree;

	/**
	* Build Pagetree with loader and assign listeners.
	*
	* @param void
	* @access public
	*/
	this.init = function(){
		this.loader = new Ext.tree.TreeLoader({ dataUrl:'index.php', baseParams: {ajax: '1', action: 'getprinttree'}, waitMsg:'Loading', preloadChildren: true});
        this.loader.addListener('load', _LoadPrintNode);

        this.thetree = new Tree.TreePanel({el:'printtree',animate:true,autoScroll:true, loader: this.loader, containerScroll: true, enableDD:true});
		this.thetree.setRootNode(new Tree.AsyncTreeNode({text: 'Printtree',draggable:false, id:'0'}));
	    this.thetree.addListener('nodedrop', _NodeDropAjax);
	    this.thetree.addListener('contextmenu', _PrepareMenu);
		this.thetree.addListener('movenode', _NodeMoveAjax);

		
	}

	/**
	* Adds a node to the queue if the node is not exported. 
	*
	* @param node
	* @access private
	*/
	var queueUnexportedNode = function(node){
		if(!node.attributes.isExported){
			node_queue.push(node);
		}
	}

	/**
	* Queues all Childnodes of a node.
	*
	* @access private
	*/
	var queueChildren = function(node){
		node.cascade(queueUnexportedNode);
	}

	/**
	* Set the color of the node depending on his export state.
	*
	* @access private
	*/
  	var setNodeExportState = function(node){
		if(node.attributes.isExported){
			node.getUI().addClass('exported');
			node.getUI().removeClass('not-exported');
		}else{
			node.getUI().addClass('not-exported');
			node.getUI().removeClass('exported');
		}
	}

	/**
	* Builds Kontextmenu for a node in the tree.
	*
	* @param node
	* @access private
	*/
	var _PrepareMenu = function(node, e){
		//KONTEXT MENU
		var Menu = new Ext.menu.Menu({
			id:'menu',
			items: [
			{ id:'moveup',handler:_MoveUpSelectedNode, cls:'moveup',text:'move up'},
			{ id:'movedown', handler:_MoveDownSelectedNode, cls:'movedown', text:'move down'},
			'-',
			{ id:'remove', handler:_RemoveSelectedNode, cls:'remove', text: 'remove'},
			'-',
			{ id:'export', handler:_ExportSelectedNode, cls:'export', text: 'export node'},
			{ id:'export-selection', handler:_ExportBranch, cls:'export', text: 'export branch'},
			{ id:'unexport', handler:_ExportSelectedNode, cls:'unexport', text: 'delete export'}]
		});

		node.select();
		Menu.items.get('remove')[node.attributes.allowDelete ? 'enable' : 'disable']();
		Menu.showAt(e.getXY());
	}

	/**
	* Method to commit an update of a node to the server.
	*
	* @access private
	* @param node node that should be updated
	* @param string controller action string
	* @param function callback function in case of a successfull update
	* @param function callback function in case of a non-successfull update
	*/
	var nodeUpdateAjax = function(node,action,success_callback,error_callback){
  		Ext.Ajax.request({
			url: 'index.php',
			success: success_callback,
			failure: error_callback,
			//make ajax call, set uid (id of the droppend node), pid (uid of the relation from the parent node), selection (selection id, same as parent node)
			params: {ajax: '1', action: action, uid: node.id  }
	});}

	/**
	* Recusive function to export all nodes in the queue.
	*/
	var processNodeQueue = function(final_callback,i,initial_value){

		updateLoader(i/initial_value, i+' of '+ initial_value +' Contentelements exported');
		i++;
		if(node_queue.length > 0){
			//get last element of the queue
			n = node_queue.shift();

			//add the element to the export with an ajax call
			nodeUpdateAjax(n,'add-to-export',function(res){

				//set the node attribute as exported
				n.attributes.isExported = true;
	        	appendLog(res.responseText);

				//set the color of the node to green and mark it as exported
				setNodeExportState(n);
				n.getUI().show();

				//_ recusive call to process the next node in the queue
				processNodeQueue(final_callback,i,initial_value);
			},function(){
				appendLog("error in export");

				//in case of an error the next node of the queue will be processed
				processNodeQueue(final_callback,i,initial_value);
			});
		}else{
			//if the queue is empty call the final callback
			final_callback();
		}
	}

	/**
	* Method to get the selected node of the printtree.
	*
	* @access private
	* @param void
	* @return node
	*/
	var getSelectedNode = function(){
		var sm = self.thetree.getSelectionModel();
		var n = sm.getSelectedNode();
		return n;
	}

	var preparetree = function(){

		self.thetree.expand(true, true);
		self.thetree.expandAll();		
		self.thetree.getRootNode().setText('Printtree of '+self.name);

		self.thetree.render();		
		self.thetree.show();
	}
	
	/**
	* Reload the content of the tree.
	*
	* @access private
	* @param void
	* @return void
	*/
    var _reload = function(){
    	self.thetree.getLoader().on("beforeload", 
    			function(treeLoader, node) {
    				treeLoader.baseParams.selection = self.id;
    			}, this);

    	self.thetree.getLoader().load(self.thetree.getRootNode(),preparetree);
    }

	/**
	* Drag and Drop Listenerfunction for the tree.
	*
	* @param Event e
	* @access private
	*/
	var _NodeDropAjax = function(e){
         //we only need to insert nodes that are not in the selection
         if(!e.dropNode.attributes.in_selection){
          		showLoader();

          		Ext.Ajax.request({
 					url: 'index.php',
 					success: function(res){
 						//overwrite id of the droppen node with the primary key of the created relation
 						e.dropNode.id = res.responseText;
 						e.dropNode.draggable = true;
 						e.dropNode.allowDrop = true;
 						e.dropNode.allowDelete = true;
 						e.dropNode.allowChildren = true;
 						e.dropNode.attributes.in_selection = true;
 						e.dropNode.leaf = false;
 						setNodeExportState(e.dropNode);
 						hideLoader();
 					},

 					failure: function(res){
 						alert('error during node drop '+res.toSource());
 					},
 					//make ajax call, set uid (id of the droppend node), pid (uid of the relation from the parent node))
 					params: {ajax: '1', action: 'add-to-selection', foreign_uid: e.dropNode.id,pid: e.dropNode.parentNode.id, index: e.dropNode.parentNode.indexOf(e.dropNode)}
			});
		}
	}

	/**
	* Function to handler the move of a node inside the tree.
	*
	* @access private
	* @param tree tree to move
	* @param node the moved node
	* @param node the old parent node
	* @param node the new parent node
	* @param int the index on the level where the node should be inserted
	*/
	var _NodeMoveAjax = function(tree,node,old_parent,new_parent,index){
		if(node.attributes.in_selection){
			//if a dropped not is allready in the selection it only
			//has to be updated, because it has maybe been moved
			showLoader();

			Ext.Ajax.request({
				url: 'index.php',
				success: function(res){
					hideLoader();
				},
				//make ajax call, set uid (id of the droppend node), pid (uid of the relation from the parent node))
				params: {ajax: '1', action: 'move-in-selection', uid: node.id,pid: new_parent.id, index: index }
			});
		}
	}

	/**
	* Moves the current selected node up.
	*
	* @access private
	* @param void
	* @return void
	*/
	var _MoveUpSelectedNode = function(){
		var n = getSelectedNode();
			nodeUpdateAjax(n,'move-up',_reload,function(){
			appendLog("error movig up");
		});
	}

	/**
	* Moves the current selected node.
	*
	* @access private
	* @param void
	* @return void
	*/
	var _MoveDownSelectedNode = function(){
		var n = getSelectedNode();
		nodeUpdateAjax(n,'move-down', _reload ,function(){
			appendLog("error moving down");
		});
	}

	/**
	* Exports a node.
	*
	* @param node node to export
	* @return void
	* @access private
	*/
	var _ExportNode = function(n){
		nodeUpdateAjax(n,'add-to-export',function(res){
			n.attributes.isExported = true;
		    appendLog(res.responseText);
			setNodeExportState(n);
			n.getUI().show();
		},function(){
			appendLog("error adding to export");
		});
	}

	/**
	* Exports the current selected node.
	*
	* @param void
	* @return void
	* @access private
	*/
	var _ExportSelectedNode = function(){
		var n = getSelectedNode();
		_ExportNode(n);
	}

	/**
	* This method will be applyed on each node that will be loaded.
	* The method internally sets the exportstate of a node.
	*
	* @access private
	*/
	var _LoadPrintNode = function(obj,node,callback){
		//alert(node.toString);
		setNodeExportState(node);
	}

	/**
	* This method will unexport a given node.
	*
	* @access private
	* @param node node that should be unexported
	*
	*/
	var _UnexportNode = function(n){
		nodeUpdateAjax(n,'remove-from-export',function(res){
			n.attributes.isExported = false;
			setNodeExportState(n);
			n.getUI().show();
		});
	}

	/**
	* This method is used to unexport ther currently selected node.
	*
	* @access private
	* @param void
	*/
	var _UnexportSelectedNode = function(){
		var n = getSelectedNode();
		_UnexportNode(n);
	}

	/**
	* Method to remove a node from the printtree
	*
	* @access private
	* @param node n
	* @return void
	*/
	var _RemoveNode = function(n){
		var sm = self.thetree.getSelectionModel();
		//start ajax call to remove the celem from the selection
		nodeUpdateAjax(n,'remove-from-selection', function(){});
		if(n && n.attributes.allowDelete){
			sm.selectPrevious();
			n.parentNode.removeChild(n);
		}
	}

	/**
	* Method to remove the current selected node
	*
	* @access private
	* @param void
	* @return void
	*/
	var _RemoveSelectedNode = function(){
		var n = getSelectedNode();
		_RemoveNode(n);

		//reload the celem tree of the current selected page because, the deleted node is maybe form this page
		_reload();
	}

	/**
	* Method to export a complete branch
	*
	* @access private
	* @param function callback function that should be called if all nodes have been exported
	*/
    var _ExportBranch = function(){
		var n = getSelectedNode();
		n.eachChild(queueChildren);
		processNodeQueue(function(){},0,node_queue.length);
	}

	/**
	* Method to export the complted Printree of the current selected selection
	*
	* @access private
	* @param function callback that should be called if the export is completed
	*/
	var _ExportSelection = function(callback){
		n = self.thetree.getRootNode();
		n.eachChild(queueChildren);
		processNodeQueue(callback,0,node_queue.length);
	}

	//INTERFACES METHODS
	this.exportSelection = function(fn){
		_ExportSelection(fn);
	}

	this.reload = function (){
		_reload();
	}		
}

Printtree.prototype = new AbstractTree();