function AbstractTree(){
    var self = this;

	var thetree;
	var loader;
	var root;
	var id = 'moc';
	var name;

	/**
	* Method to set the id of the current selection
	*
	* @param int id
	* @access public
	*/
	this.setId = function(current_page_id){
		this.id = current_page_id;
	}

	/**
	* Add a Listener to the internal printtree.
	*
	* @access public
	* @param string event
	* @param function eventhandler
	*/	
	this.addListener = function(string,fn){
		this.thetree.addListener(string,fn);
	}

	/**
	* Set a name of the Printree, will be visible on the root node
	*
	* @param string name of the tree
	*/
	this.setName = function(newname){
		this.name = newname;
	}
}