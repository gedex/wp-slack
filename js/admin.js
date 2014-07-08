/* global jQuery */
(function($){

	var self = {
		testNotifySpinner: null,
		testNotifyResponse: null
	};

	self.init = function() {
		self.testNotifySpinner  = $( '#slack-test-notify .spinner' );
		self.testNotifyResponse = $( '#slack-test-notify-response' );

		$('#slack-test-notify-button').on( 'click', self.testNotifyClickHandler );
	};

	self.testNotifyClickHandler = function(e) {
		self.testNotifyResponse.html('');
		self.testNotifySpinner.show();

		var xhr = $.ajax( {
			url: ajaxurl,
			type: 'POST',
			data: {
				'action'           : 'slack_test_notify',
				'service_url'      : $('[name="slack_setting[service_url]"]').val(),
				'channel'          : $('[name="slack_setting[channel]"]').val(),
				'username'         : $('[name="slack_setting[username]"]').val(),
				'icon_emoji'       : $('[name="slack_setting[icon_emoji]"]').val(),
				'test_notify_nonce': $('#slack-test-notify-nonce').val()
			}
		} );

		xhr.done( function( r ) {
			self.testNotifyResponse.html( '<span style="color: green">OK</span>' );
			self.testNotifySpinner.hide();
		} );

		xhr.fail( function( xhr, textStatus ) {
			var message = textStatus;
			if ( typeof xhr.responseJSON === 'object' ) {
				if ( 'data' in xhr.responseJSON && typeof xhr.responseJSON.data === 'string' ) {
					message = xhr.responseJSON.data;
				}
			} else if ( typeof xhr.statusText === 'string' ) {
				message = xhr.statusText;
			}
			self.testNotifyResponse.html( '<span style="color: red">' + message + '</span>' );
			self.testNotifySpinner.hide();
		} );

		e.preventDefault();
	};

	// Init.
	$(function(){
		self.init();
	});

}(jQuery));
