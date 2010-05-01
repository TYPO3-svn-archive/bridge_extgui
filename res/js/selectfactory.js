/**
* Creates a ExtJS SelectCombo Box with an AJAX Loader
*/
function SelectorFactory(){
	var store;
	var selector;

	/**
	* Create the selector combobox
	*
	* @param string controller action to get the data from the server
	* @param array array with fieldnames delivered by the server
	* @param string name of the field for the value for an option of the combofield
	* @param string name of the field for the label for an option of the combofield
	* @param string label of the initial state of the combofield
	*/
	this.get = function(action, fields,root,valueField,displayField,text){
		store = new Ext.data.JsonStore({
		   url: 'index.php',
		   baseParams: {ajax:'1',action: action},
		  	   root: root,
		  	   fields: fields
		});

		selector = new Ext.form.ComboBox({
			store: store,
		    valueField: valueField,
		    displayField: displayField,
		    typeAhead: true,
		    mode: 'remote',
		    triggerAction: 'all',
		    width: 250,
		    emptyText: text,
		    selectOnFocus:true
		});

		return selector;
	}
}