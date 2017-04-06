$(document).ready(function() {
	$('a').click(function() {
		var url = $(this).attr('href');
		if (url=='#'||$(this).attr('data-toggle')=='dropdown'||$(this).attr('data-toggle')=='logout') return true;
		if ($(this).hasClass('list-group-item')){
			$('a.active').removeClass('active');
			$(this).addClass('active');
		}
		preloader();
		$.ajax({
			url:     url,
			data:     {modal:1,_token:$('#_token').val()},
			method:     'POST',
			success: function(data){
				$('#mainContent').html(data);
				NProgress.done();
				$.getScript( HOME_URL+"/js/links.js", function( data, textStatus, jqxhr ) {
					$('preloader').remove();
					console.log( "Links loaded" );
				});
			}
		});

		if(url != window.location){
			window.history.pushState(null, null, url);
		}

		return false;
	});
});