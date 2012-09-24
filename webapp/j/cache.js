// JavaScript Document
$(function(){
	$.ajax({
		 url: "cache.php"
	  });
	var timer = $.timer(function() {
		$.ajax({
			 url: "cache.php"
		  });
	});
	timer.set({ time : 60000, autostart : true });
})();