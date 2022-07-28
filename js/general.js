$(document).ready(function(){
	var urlPath = window.location.pathname,
    urlPathArray = urlPath.split('.'),
    tabId = urlPathArray[0].split('/').pop();
	$('#dashboard, #invoices, #items, #tax, #users').removeClass('active');	
	$('#'+tabId).addClass('active');		
});