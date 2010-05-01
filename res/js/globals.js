/**
* THIS FILE CONTAINS SOME GLOBAL FUNCTIONS
*/

/**
* Method to append a string to the logwindow
*/
function appendLog(string){
	Ext.get('south').insertHtml("beforeEnd","<p>"+string+"</p>");
	Ext.get('south').repaint();
}

/**
* Function to show the Loaderwindow
*/
function showLoader(){
	Ext.MessageBox.show({
           title: 'Please wait',
           msg: 'Starting',
           progressText: 'Working...',
           width:500,
           height: 200,
           progress:true,
           closable:false
   });
}

/**
* Function to update the Loader
*/
function updateLoader(procress, text){
	Ext.MessageBox.updateProgress(procress, text);
}

/**
* Function to hide the Loaderwindow
*/
function hideLoader(){
	Ext.MessageBox.hide();
}