/*
 *
 */
function idle(){}

var BridgeGUI = function(){
    // shorthand
    var Tree = Ext.tree;

    return {
        init : function(){
        	var printtree = new Printtree();
        	var pagetree = new Pagetree();
        	var celemtree = new Celemtree();

			var factory = new SelectorFactory();
        	var selection_selector = factory.get('getselections',['id','name'],'selections','id','name','Click to load a Printselection....');
			var trafo_selector = factory.get('gettransformationflows',['key','name'],'flows','key','name','Apply transformation....');

        	var current_selection_id;
        	var current_page_id;
        	var current_selection_name;

        	pagetree.init();
        	celemtree.init();
        	printtree.init();

			trafo_button = new Ext.Button({text:'Run ', width: 100});
			selection_button = new Ext.Button({text:'Load', width: 80});

            /**
            * Eventlistern to load the Celementstree with Contentelements of
            * a leaf of the pagetree
            **/
        	pagetree.addListener('click', function(node,y){
        			celemtree.setId(node.id);
        			celemtree.reload(current_page_id);
        		}
            );

			function transformExport(key){
			     updateLoader(1.0, 'Applying transformationflow: '+key);
			     Ext.Ajax.request({
			   			url: 'index.php',
			   			success: function(res){
			  				hideLoader();
			  				Ext.Msg.alert('Status', 'The Printree has been transformed');
			   			},
			   			params: {ajax: '1', action: 'transform-export', flow_key: key }
				});
			}

			trafo_button.addListener('click', function(){
			    showLoader();
        		var fn = function(){ transformExport(trafo_selector.getValue()); };
        		printtree.exportSelection(fn);
			});
			trafo_selector.disable();
			trafo_button.disable();

            selection_button.addListener('click', function(){
       			showLoader();
                //update the selection
				Ext.Ajax.request({
					url: 'index.php',
					success: function(res){
					     current_selection_id 	= selection_selector.getValue();
					     current_selection_name 	= selection_selector.getRawValue();
					     printtree.setName(current_selection_name);
					     printtree.setId(current_selection_id);
					     printtree.reload();
					     //the current page may contain allready assign content elemts
					     celemtree.setId(current_selection_id);
					     celemtree.reload();
					     trafo_selector.enable();
					     trafo_button.enable();
					     hideLoader();
					},
					params: {ajax: '1', action: 'set-selection-uid', uid: selection_selector.getValue()}
				});

            });

		    /***
		    * Build the main window with the Ext.Viewport
		    */
		    var viewport = new Ext.Viewport({
		            layout:'border',
		            items:[
						{
                		    region:'south',
                    		contentEl: 'south',
                    		split:true,
                    		height: 100,
                    		minSize: 100,
                    		maxSize: 800,
                   			collapsible: true,
                    		title:'Status',
                    		margins:'0 0 0 0'
                		},
		                {
		                    region:'west',
		                    id:'west-panel',
		                    title:'Pages and Contentelements',
		                    split:true,
		                    width: 350,
		                    minSize: 175,
		                    maxSize: 400,
		                    collapsible: true,
		                    margins:'0 0 0 5',

		                    items: [
		              		{
		              			layout: 'border',
		              			height: 50,
		              			width: 350,
  		              			border:false,
		              			items:[
		              				{
		              					region: 'west',
		              					height: 50,
		              					width: 250,
		              					border:false,
		              					items:[
		              						selection_selector,
		              						trafo_selector
		              					]
		              				},
		              				{
		              					region: 'center',
										border:false,
		              				    items:[
		              						selection_button,
		              						trafo_button
		              					]
		              				}
		              			]
		              		}
		                    ,{
		                        contentEl: 'pagetree',
		                        title:'Pagetree',
		                        border:false,
		                        layout: 'form',
		                        iconCls:'nav'
		                    },{
		                        contentEl: 'celems',
		                        title:'Contentelements',
		                        border:false,
		                        layout: 'form',
		                        iconCls:'nav'
		                    }]
		                },
		                new Ext.Panel({
		                    region:'center',
		                    items:[{
		                        contentEl:'printtree',
		                        title: 'Printtree',
		                        autoScroll:true,
		                        layout: 'fit'
		                    }]
		                })
		             ]
		       });
		 }
    };
}();

Ext.EventManager.onDocumentReady(BridgeGUI.init, BridgeGUI, true);