'use strict';

/*global window, document, console, Modernizr, jQuery, App:true, google, _gaq, debug */

/*jslint plusplus: true, nomen: true */

/**
 * @desc Specific application page autoload methods
 */
App = (function (App, $) {

	App.routes = $.extend({}, App.routes, {
		common: {
			initialize: function () {},
			finalize: function () {
				var elements	= {
						show: {
							'page-two-column.php':		$('#page_secondary_content')
						},
						hide: {
							'front-page.php':			$('#postexcerpt'),
							'page-parent.php':			$('#subtitle, #postimagediv, #postdivrich, #postexcerpt')
						}
					}

				$('#page_template').bind('change', function (event) {
					var $select	= $(event.target).closest('select'),
						value	= $select.val();

					$.each(elements.show, function (i, element) {
						$(element).hide();
					});
					$.each(elements.hide, function (i, element) {
						$(element).show();
					});
					
					if (typeof elements.show[value] !== 'undefined') {
						elements.show[value].show();
					}
					if (typeof elements.hide[value] !== 'undefined') {
						elements.hide[value].hide();
					}

				}).trigger('change');
			}
		}
	});

	return App;

}(typeof App === 'object' ? App : {}, jQuery));

/**
 * @desc Start the application
 */
if (typeof jQuery !== 'undefined') {
	(function (App, $) {
		$(document).ready(function () {
			App.util.route.start(App.routes);
		});
	}(typeof App === 'object' ? App : {}, jQuery));
}