<?php
/**
 * Class SlackTest
 *
 * @package WP_Slack
 * @subpackage Tests
 */

/**
 * Test for slack.php
 *
 * @since 0.6.0
 */
class SlackTest extends WP_UnitTestCase {

	/**
	 * Test classes are loaded by autoloader.
	 *
	 * @since 0.6.0
	 */
	public function test_classes_are_available_from_autoloader() {
		$this->assertTrue( class_exists( 'WP_Slack_Event_Manager' ) );
		$this->assertTrue( class_exists( 'WP_Slack_Event_Payload' ) );
		$this->assertTrue( class_exists( 'WP_Slack_Notifier' ) );
		$this->assertTrue( class_exists( 'WP_Slack_Plugin' ) );
		$this->assertTrue( class_exists( 'WP_Slack_Post_Meta_Box' ) );
		$this->assertTrue( class_exists( 'WP_Slack_Post_Type' ) );
		$this->assertTrue( class_exists( 'WP_Slack_Submit_Meta_Box' ) );
	}

	/**
	 * Test instance WP_Slack_Plugin is available globally.
	 *
	 * @since 0.6.0
	 */
	public function test_slack_plugin_instance_available_globally() {
		$this->assertTrue( is_a( $GLOBALS['wp_slack'], 'WP_Slack_Plugin' ) );
	}
}
