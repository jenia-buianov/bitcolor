function Send(el) {
	var valuesArray = {};
	var alerts = "";
	var formId = $(el).attr('id');
	$(el).find('input, select, textarea').each(function(e,v)
	{

		if(v.placeholder==undefined) v.placeholder = $(v).attr('data-placeholder');
		if (parseInt($(v).attr('must'))==1&&v.value.length==0) alerts+=", "+v.placeholder;
	});
	if (alerts.length>0)
	{
		obj = {
			notif : {
				title:$('#'+formId).attr('title'),
				type:'warning',
				text:alerts.substr(2)+Lang.notEntered,
				icon:'exclamation-circle'
			}
		};
		showNotification(obj);
		return false;
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
	},5000);
}


$.fn.extend({
	animateCss: function (animationName) {
		var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
		this.addClass('animated ' + animationName).one(animationEnd, function() {
			$(this).removeClass('animated ' + animationName);
		});
	}
});