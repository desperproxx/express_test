var $ = require("jquery");
var data1  = new Array(); 
function json_get(path) {
	$.getJSON(path, function(data) { 
		$('#news').html('<a href="/">Назад</a>');
		$.each(data, function (ind, value) {
		$('#news').append('<div class=blog><b>Шапка: </b>'+value.header+'<b> URL: </b>'+value.slug+' <img src='+value.preview+' width=150 height=100><b>Дата создания: </b>'+value.createdAt.date.substring(0, 10)+' '+htmlspecialchars_decode(value.content)+'</div>');
		});
	});
}
function formatDate(date) {

	var dd = date.getDate();
	if (dd < 10) dd = '0' + dd;

	var mm = date.getMonth() + 1;
	if (mm < 10) mm = '0' + mm;

	var yy = date.getFullYear() % 100;
	if (yy < 10) yy = '0' + yy;

	return dd + '.' + mm + '.' + yy;
}
function htmlspecialchars_decode(string, quote_style) {
	var optTemp = 0,
	i = 0,
	noquotes = false;
	if (typeof quote_style === 'undefined') {
		quote_style = 2;
	}
	string = string.toString()
	.replace(/&lt;/g, '<')
	.replace(/&gt;/g, '>');
	var OPTS = {
		'ENT_NOQUOTES': 0,
		'ENT_HTML_QUOTE_SINGLE': 1,
		'ENT_HTML_QUOTE_DOUBLE': 2,
		'ENT_COMPAT': 2,
		'ENT_QUOTES': 3,
		'ENT_IGNORE': 4
	};
	if (quote_style === 0) {
		noquotes = true;
	}
	if (typeof quote_style !== 'number') { 
		quote_style = [].concat(quote_style);
		for (i = 0; i < quote_style.length; i++) {
			if (OPTS[quote_style[i]] === 0) {
				noquotes = true;
			} else if (OPTS[quote_style[i]]) {
				optTemp = optTemp | OPTS[quote_style[i]];
			}
		}
		quote_style = optTemp;
	}
	if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
		string = string.replace(/&#0*39;/g, "'"); 
	}
	if (!noquotes) {
		string = string.replace(/&quot;/g, '"');
	}
	string = string.replace(/&amp;/g, '&');
	return string;
}

$.getJSON('json_pictures', function(data) { 
	$.each(data, function (ind, value) {
		$.each(value.tags, function (index, val) {
			data1.push(String(val.name)); 
		});
		var select_tag_tofilter='';
		for(var i=0; i < data1.length; i++) {
			select_tag_tofilter += '<div class="tag_down">'+data1[i]+'</div> ';   
		} 
		$('.pictures').append('<tr><td><img src="'+value.img+'" width=250 height=150></td><td><b>'+value.description+'</b></td><td>'+data1+'</td><td><a href="admin_update_pictures/'+value.id+'">Изменить</a></td><td><a href="admin_clear_teg/'+value.id+'">очистить теги</a></td></tr>');	
		$('#pictures').append('<div class=picture><img src="'+value.img+'" width=250 height=150><p>'+value.description+'</p>'+select_tag_tofilter+'</div>');	
		data1=[];
	});
	var data_filter_tag = new Array();
	$(".tag_down").click(function(){
		$("#pictures").html('<a href="/">Назад</a>');
		$.getJSON('search_with_tags/'+$(this).text(), function(da) { 
			$.each(da, function (ind, valu) {
				$.each(valu.tags, function (index, val) {
					data_filter_tag.push(String(val.name)); 
				});
				var select_tag_filter='';
				for(var i=0; i < data_filter_tag.length; i++) {
					select_tag_filter += '<div class="tag_down">'+data_filter_tag[i]+'</div> ';   
				}
				$('#pictures').append('<div class=picture><img src="'+valu.img+'" width=250 height=150><p>'+valu.description+'</p>'+select_tag_filter+'</div>');	
				data_filter_tag=[];
			});
		});
	});
	$("#month_filter").click(function(){
		json_get('json_news_filter/'+$('.date_from option:selected').val()+'/'+$('.date_to option:selected').val()+'');
	});
	
	$("#header_filter").click(function(){
		json_get('json_news_filter_header');	

	});

});
$.getJSON('json_news', function(data) { 
	$.each(data, function (ind, value) {

		$('#news').append('<div class=blog><b>Шапка: </b>'+value.header+'<b> URL: </b>'+value.slug+' <img src='+value.preview+' width=150 height=100><b>Дата создания: </b>'+value.createdAt.date.substring(0, 10)+' '+htmlspecialchars_decode(value.content)+'</div>');	
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