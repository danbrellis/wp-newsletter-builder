// JavaScript Document

jQuery(document).ready(function($) {
	
	//hide 'read online' link
	var f=$('#newsletterframe');
	f.load(function(){ 
		f.contents().find('div').css('visibility', 'hidden'); 
	})
});