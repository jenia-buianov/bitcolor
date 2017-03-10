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
		$('#'+formId+' #alerts').removeClass().addClass('bg-danger');
		$('#'+formId+' #alerts').html(alerts.substr(2)+" not entered");
		return false;
	}
	var url = $(el).attr('action');
    $.ajax({
		dataType: "json",
		url: url,
		data: {values:valuesArray},
		method: 'POST',
		success: function (data) {
			console.log(data);
			console.log(formId);
			if (data.error.length>0){
				$('#'+formId+' #alerts').removeClass().addClass('bg-danger');
				$('#'+formId+' #alerts').html(data.error);
				return false;
			}
			else {
				$('#'+formId+' #alerts').removeClass().addClass('bg-success');
				$('#'+formId+' #alerts').html(data.html);
				$('#'+formId)[0].reset();
				return false;
			}
			return false;
		}
	});
	return false;
}

$.fn.extend({
	animateCss: function (animationName) {
		var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
		this.addClass('animated ' + animationName).one(animationEnd, function() {
			$(this).removeClass('animated ' + animationName);
		});
	}
});

function setHTML(el,id){
	var val = $(el.type+id).html();
	$(el.type+id).html(el.value);
	if (el.effect!=='') {
		if(!el.equal) $(el.type+id).animateCss(el.effect);
		if(el.equal&&(val!==$(el.type+id).html())) $(el.type+id).animateCss(el.effect);
	}
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
	},4000);
}

function myObserver(){
	$.ajax({
		dataType: "json",
		url: HOME_URL+'/observer/',
		data: {'modal':1},
		method: 'post',
		success: function (data) {
			//console.log(data);
			$.each(data, function(i, val) {
				if (val.type=='#'||val.type=='.') {
					if (val.action=='set') setHTML(val,i);
				}
				if (val.type=='notification') showNotification(val);
			});
			return false;
		}
	});
}

$(document).ready(function (){
	var myObs = setInterval(myObserver,1000);
});
