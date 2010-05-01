function Celemtree(){
    var Tree = Ext.tree;
	
	this.init = function(){
       	this.loader = new Ext.tree.TreeLoader({ dataUrl:'index.php', baseParams: {ajax:'1',action:'getcelemtree'}, waitMsg:'Loading'});
        this.thetree = new Tree.TreePanel({el:'celems',animate:true,autoScroll:true,rootVisible: false,loader: this.loader, containerScroll: true, enableDD: true});

        // add the root node
        this.root = new Tree.AsyncTreeNode({ text: 'Contentelements', draggable:false, id:0});

        this.thetree.setRootNode(this.root);
        this.thetree.render();
        this.thetree.expand(true, true);
	}
	
	this.reload = function(){
        this.thetree.getLoader().on("beforeload", function(treeLoader, node) {
        	treeLoader.baseParams.pid = this.id;
    	}, this);
    	this.thetree.getLoader().load(this.root,function(){});
    }
}
Celemtree.prototype = new AbstractTree();