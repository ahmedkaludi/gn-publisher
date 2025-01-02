/**
 * JS file for setup wizard
 * @since 1.5.19
 * */
jQuery(document).ready(function($){

	$(document).on( 'click', '#gnpub_enable_gnsitemap', function( e ) {

		if ( $(this).is(':checked') ) {
			$('#gnpub-sitemap-setting-wrapper').show();
			$('#gnpub-sitemap-setting-wrapper').removeClass('gnpub-d-none');
		}else{
			$('#gnpub-sitemap-setting-wrapper').hide();
			$('#gnpub-sitemap-setting-wrapper').removeClass('gnpub-show');
		}

	});

	// Change the opacity of the table row is checklist checkbox is selected
	$(document).on( 'click', '.gnpub-setup-wizard-chklist-chkbox', function(e) {
		let thisClass 	=	$(this).attr('data-dont-hide');

		if ( $(this).is(':checked') ){
			$(this).parent().addClass('gnpub-setup-wizard-chklist-tr-checked');
		
			$.each($('.gnpub-setup-wizard-add-opacity '), function( index, element ) {
				let siblings 	=	$(this).attr('data-hide');
				if ( thisClass == siblings ) {
					$(this).addClass('gnpub-setup-wizard-chklist-opacity');
				}
			});
			
		}else{
			$(this).parent().removeClass('gnpub-setup-wizard-chklist-tr-checked');
			
			$.each($('.gnpub-setup-wizard-add-opacity '), function( index, element ) {
				let siblings 	=	$(this).attr('data-hide');
				if ( thisClass == siblings ) {
					$(this).removeClass('gnpub-setup-wizard-chklist-opacity');
				}
			});
		}
	});

});