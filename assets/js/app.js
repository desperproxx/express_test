var $ = require("jquery");
var data1  = new Array(); 
$.getJSON('json_pictures', function(data) { 
	$.each(data, function (ind, value) {
		$.each(value.tags, function (index, val) {
			data1.push(String(val.name)); 
		});
		var select_tag_tofilter='';
		for(var i=0; i < data1.length; i++) {
			select_tag_tofilter += '<div class="tag_down">'+data1[i]+'</div> ';   
		} 
		$('.pictures').append('<tr><td><img src="'+value.img+'" width=250 height=250></td><td>'+value.description+'</td><td>'+data1+'</td><td><a href="admin_update_pictures/'+value.id+'">Изменить</a></td><td><a href="admin_clear_teg/'+value.id+'">очистить теги</a></td></tr>');	
		$('#pictures').append('<div class=picture><img src="'+value.img+'" width=250 height=250><br><b>'+value.description+'</b><br>'+select_tag_tofilter+'</div>');	
		data1=[];
	});
	var data_filter_tag = new Array();
	$(".tag_down").click(function(){
		$("#pictures").html("");
		$.getJSON('search_with_tags/'+$(this).text(), function(da) { 
			$.each(da, function (ind, valu) {
				$.each(valu.tags, function (index, val) {
					data_filter_tag.push(String(val.name)); 
				});
				var select_tag_filter='';
				for(var i=0; i < data_filter_tag.length; i++) {
					select_tag_filter += '<div class="tag_down">'+data_filter_tag[i]+'</div> ';   
				}
				$('#pictures').append('<div class=picture><img src="'+valu.img+'" width=250 height=250><br><b>'+valu.description+'</b><br>'+select_tag_filter+'</div>');	
				data_filter_tag=[];
			});
		});
	});
});
$.getJSON('json_news', function(data) { 
	$.each(data, function (ind, value) {

		$('#news').append('<div class=blog>'+value.slug+' '+value.preview+' '+value.createdAt.date+' '+value.header+' '+value.content+'</div><br>');	
	});
});
$(document).ready(function(){
	$(".tabs").lightTabs();
});
(function($){				
	jQuery.fn.lightTabs = function(options){

		var createTabs = function(){
			tabs = this;
			i = 0;

			showPage = function(i){
				$(tabs).children("div").children("div").hide();
				$(tabs).children("div").children("div").eq(i).show();
				$(tabs).children("ul").children("li").removeClass("active");
				$(tabs).children("ul").children("li").eq(i).addClass("active");
			}
			showPage(0);				

			$(tabs).children("ul").children("li").each(function(index, element){
				$(element).attr("data-page", i);
				i++;                        
			});

			$(tabs).children("ul").children("li").click(function(){
				showPage(parseInt($(this).attr("data-page")));
			});				
		};		
		return this.each(createTabs);
	};	
})(jQuery);
$(document).ready(function(){
	$(".tabs").lightTabs();
});