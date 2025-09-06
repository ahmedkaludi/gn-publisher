jQuery(document).ready(function($) {
	/** Apple news publisher code */
	$(document).on('change', '#gnpub-apple-news', function(e){
		if( $(this).is(':checked') ) {
		  $('.gnpub-apple-news-options').show();
		}else{
		  $('.gnpub-apple-news-options').hide();
		}
	});

	$(document).on('click', '#gnpub-apple-news-publish-btn', function(e){
		$(this).addClass('update-message');
	});
});