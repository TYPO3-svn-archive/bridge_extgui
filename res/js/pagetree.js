function Pagetree(){
    // shorthand
    var Tree = Ext.tree;

	/**
	* Initializes the printtree with data from the loader.
	*
	*/
	this.init = function(){
		/*
		* The pagetree
		*/
        this.thetree = new Tree.TreePanel({
        	el:'pagetree',
        	animate:true,
            autoScroll:true,
            //rootVisible: false,
            loader: new Ext.tree.TreeLoader({
            	dataUrl:'index.php',
            	baseParams: {ajax:'1',action:'getpagetree'} // custom http params
            }),
            containerScroll: true,
            enableDD: false
        });

        // add the root node
        this.root = new Tree.AsyncTreeNode({
            text: 'Pagetree',
            draggable:false,
            id:'0'
        });
        this.thetree.setRootNode(this.root);
        this.thetree.render();
        this.thetree.expand(false, true);
	}
}
Pagetree.prototype = new AbstractTree();
