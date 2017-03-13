function Send(el) {
	var valuesArray = {};
	var alerts = "";
	var formId = $(el).attr('id');
	$(el).find('input, select, textarea').each(function(e,v)
	{

		if(v.placeholder==undefined) v.placeholder = $(v).attr('data-placeholder');
		valuesArray[e] = {'value':v.value,'name':v.name,'must':parseInt($(v).attr('must')),'title':v.placeholder};
		if (parseInt($(v).attr('must'))==1&&v.value.length==0) alerts+=", "+v.placeholder;

	});
	if (alerts.length>0)
	{
		obj = {
			notif : {
				title:$('#'+formId).attr('title'),
				type:'warning',
				text:alerts.substr(2)+" not entered",
				icon:'exclamation-circle'
			}
		};
		showNotification(obj);
		return false;
	}
	var url = $(el).attr('action');
    $.ajax({
		dataType: "json",
		url: url,
		data: {values:valuesArray,_token:$('#_token').val(),page:window.location.pathname},
		method: 'POST',
		success: function (data) {
			console.log(data);
			if (data.error.length>0){
				obj = {
					notif : {
						title:$('#'+formId).attr('title'),
						type:'warning',
						text:data.error,
						icon:'exclamation-circle'
					}
				};
				showNotification(obj);
				return false;
			}
			else {
				obj = {
					notif : {
						title:$('#'+formId).attr('title'),
						type:'success',
						text:data.html,
						icon:'check-circle-o '
					}
				};
				showNotification(obj);
				$('#'+formId)[0].reset();
				return false;
			}
			return false;
		}
	});
	return false;
}

$(document).ready(function() {
	$('a').click(function() {
		var url = $(this).attr('href');
		if (url=='#'||$(this).attr('data-toggle')=='dropdown'||$(this).attr('data-toggle')=='logout') return true;
		if ($(this).hasClass('list-group-item')){
			$('a.active').removeClass('active');
			$(this).addClass('active');
		}
		NProgress.start();
		$.ajax({
			url:     url,
			data:     {modal:1,_token:$('#_token').val()},
			method:     'POST',
			success: function(data){
				$('#mainContent').html(data);
				NProgress.done();
				$.getScript( HOME_URL+"/js/links.js", function( data, textStatus, jqxhr ) {
					console.log( "Links loaded" );
				});
			}
		});

		if(url != window.location){
			window.history.pushState(null, null, url);
		}

		return false;
	});

	$(window).bind('popstate', function() {
		NProgress.start();
		$.ajax({
			url:     location.pathname,
			data:     {modal:1,_token:$('#_token').val()},
			method:     'POST',
			success: function(data) {
				NProgress.done();
				$('#mainContent').html(data);
				$.getScript( HOME_URL+"/js/links.js", function( data, textStatus, jqxhr ) {
					console.log( "Links loaded" );
				});
			}
		});
	});
});

$.fn.extend({
	animateCss: function (animationName) {
		var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
		this.addClass('animated ' + animationName).one(animationEnd, function() {
			$(this).removeClass('animated ' + animationName);
		});
	}
});

function preloader(){
	NProgress.start();
	$('body').append('<preloader></preloader>');
}
Date.prototype.addTime = function(h) {
	this.setTime(this.getTime() + (h*1000));
	return this;
};

function timer(game,finish)
{
	time = parseInt((finish - new Date)/1000);
	if (time<=0) {
		$('#game_'+game).remove();
		clearInterval(Timeouts[game]);
		Timeouts[game] = 0;
	}
	minutes = parseInt(time/60);
	sec = time - minutes*60;
	if (sec<10) sec='0'+sec;
	$('#game_'+game+' font').html(minutes+':'+sec);
}
function setLeftGameTimer(game,time){

	finish = new Date().addTime(time);
	Timeouts[game] = setInterval(timer,1000,game,finish);
}

function setHTML(el,id){
	var val = $(el.type+id).html();
	$(el.type+id).html(el.value);
	if (typeof el.css !== 'undefined')	$(el.type+id).css(el.css);
	if (el.effect!=='') {
		if(!el.equal) $(el.type+id).animateCss(el.effect);
		if(el.equal&&(val!==$(el.type+id).html())) $(el.type+id).animateCss(el.effect);
	}
}

function notificationSeen(id){
	$.ajax({
		dataType: "json",
		url:     HOME_URL+'/notifications/seen',
		data:     {modal:1,_token:$('#_token').val(),id:id},
		method:     'POST',
		success: function (data) {
			return false;
		}
	});
}

function selectColor(e){
	$('#amountForm input[name="color"]').val($(e).attr('id'));
	obj = {
		notif : {
			title:'Color selected',
			type:'info',
			text:"You select "+$(e).attr('id'),
			icon:'exclamation-circle'
		}
	};
	showNotification(obj);
}

function showNotification(el){

	var count = parseInt($('body').html().split('class="notification"').length);
	var top = (count-1)*160+10;

	var HTML = '<div class="notification" style="bottom:'+top+'px">';
	HTML+='<div class="header '+el.notif.type+'">';
	HTML+='<i class="fa fa-'+el.notif.icon+'" aria-hidden="true"></i>';
	HTML+='<title>'+el.notif.title+'</title>';
	HTML+='<text>'+el.notif.text+'</text>';
	HTML+='</div></div>';
	$('body').append(HTML);
	$('.notification:eq('+count+')').animateCss('bounceInRight');

	setTimeout(function(){
		$('.notification:eq(0)').animateCss('bounceOutRight');
		setTimeout(function(){
			$('.notification:eq(0)').remove();
		},1000);
	},5000);
}

function setNoClickable(el){
	$(el).off("click");
	$(el).attr('onclick','');
	$(el).on('click',function(){
		obj = {
			notif : {
				title:'Error',
				type:'warning',
				text:"Sorry cannot change selected color",
				icon:'exclamation-circle'
			}
		};
		showNotification(obj);
	});
}

function myObserver(){
	$.ajax({
		dataType: "json",
		url:     HOME_URL+'/observer',
		data:     {modal:1,_token:$('#_token').val(),page:window.location.pathname},
		method:     'POST',
		success: function (data) {
			//console.log(data);
			$.each(data, function(i, val) {
				if (val.type=='#'||val.type=='.') {
					if (val.action=='set') setHTML(val,i);
					if (val.action=='no_click') setNoClickable(val.type+i);
				}
				if (val.type=='notification') showNotification(val);
			});
			return false;
		}
	});
}

$(document).ready(function (){
	NProgress.done();
	$('preloader').remove();
	var myObs = setInterval(myObserver,1000);
});
