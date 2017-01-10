<?php
/**
 * Class EventPayloadTest.
 *
 * @package WP_Slack
 * @subpackage Tests
 */

/**
 * Test for event-payload.php
 *
 * @since 0.6.0
 */
class EventPayloadTest extends WP_UnitTestCase {

	/**
	 * Test retrieving service URL.
	 *
	 * @since 0.6.0
	 */
	public function test_get_url() {
		$payload = new WP_Slack_Event_Payload( array(
			'service_url' => 'https://hooks.slack.com/services/a/b/c',
		) );

		$this->assertEquals(
			'https://hooks.slack.com/services/a/b/c',
			$payload->get_url()
		);
	}

	/**
	 * Test retrieving JSON string of setting.
	 *
	 * @since 0.6.0
	 */
	public function test_get_json_string() {
		$payload = new WP_Slack_Event_Payload( array(
			'channel'    => '#general',
			'username'   => 'bot',
			'text'       => 'Message to send',
			'icon_emoji' => ':rocket:',
		) );

		$this->assertEquals(
			'{"channel":"#general","username":"bot","text":"Message to send","icon_emoji":":rocket:"}',
			$payload->get_json_string()
		);
	}
}
