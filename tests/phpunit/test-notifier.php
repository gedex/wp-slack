<?php
/**
 * Class NotifierTest.
 *
 * @package WP_Slack
 * @subpackage Tests
 */

/**
 * Test for notifier.php
 *
 * @since 0.6.0
 */
class NotifierTest extends WP_UnitTestCase {

	/**
	 * Test notify Slack.
	 *
	 * Similar to testing WP_Slack_Event_Manager::dispatch_events(), this involves
	 * filtering HTTP response.
	 *
	 * @since 0.6.0
	 */
	public function test_notify() {
		$notifier = $GLOBALS['wp_slack']->notifier;
		$self     = $this;

		$payload = new WP_Slack_Event_Payload( array(
			'service_url' => 'https://hooks.slack.com/services/a/b/c',
			'channel'     => '#general',
			'username'    => 'bot',
			'text'        => 'Test message',
			'icon_emoji'  => ':rocket:',
		) );

		// Assert all requests data when short-circuited the HTTP request.
		add_filter( 'pre_http_request', function( $preempt, $r, $url ) use ( $self, $payload ) {
			$self->assertEquals( 'POST', $r['method'] );
			$self->assertEquals( 'wp_slack/' . $GLOBALS['wp_slack']->version, $r['user-agent'] );
			$self->assertEquals( $payload->get_url(), $url );
			$self->assertEquals( $payload->get_json_string(), $r['body'] );

			// This response should be returned by notify().
			return array(
				'response' => array(
					'code' => 200,
				),
			);
		}, 10, 3 );
		$resp = $notifier->notify( $payload );
		$this->assertEquals( 200, wp_remote_retrieve_response_code( $resp ) );

		add_filter( 'pre_http_request', function( $preempt, $r, $url ) {
			return array(
				'response' => array(
					'code' => 400,
				),
			);
		}, 10, 3 );
		$resp = $notifier->notify( $payload );
		$this->assertTrue( is_wp_error( $resp ) );
		$this->assertEquals( 'slack_unexpected_response', $resp->get_error_code() );
	}
}
