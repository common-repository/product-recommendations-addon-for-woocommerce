(function($) {
    $(document).ready(function() {
		$(document).on('click','.product_recommendation_add_to_cart_button',function(e){


			let product_id = $(this).data('product_id');
			let href = $(this).attr('href');
			let engine_id = getUrlParameter('rexprr_engine_id', href);
			let engine_position = getUrlParameter('rexprr_engine_position', href);


			setTimeout(function(){
				$.ajax({
					url: window.rexPrRecommendationFrontend.ajaxAdminUrl,
					type: 'POST',
					data: {
						action: 'add_custom_metadata_to_cart_item',
						product_id: product_id,
						engine_id: engine_id,
						engine_position: engine_position,
						security: window?.rexPrRecommendationFrontend?.ajaxPublicNonce
					},
					success: function(response) {
					}
				});
			}, 1000);

			setTimeout(function(){
				$('body').trigger('update_checkout');
			}, 1000);

		});
		function getUrlParameter(name, url) {
			name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
			var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
			var results = regex.exec(url);
			return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
		}
    })
})(jQuery);
