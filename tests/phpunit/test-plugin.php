<?php
/**
 * Class PluginTest.
 *
 * @package WP_Slack
 * @subpackage Tests
 */

/**
 * Test for plugin.php
 *
 * @since 0.6.0
 */
class PluginTest extends WP_UnitTestCase {
	/**
	 * Test run().
	 *
	 * @since 0.6.0
	 */
	public function test_run() {
		$path   = $GLOBALS['wp_slack']->plugin_path;
		$plugin = new WP_Slack_Plugin();
		$plugin->run( $path );

		$this->assertEquals( 'wp_slack', $plugin->name );
		$this->assertEquals( trailingslashit( plugin_dir_path( $path ) ), $plugin->plugin_path );
		$this->assertEquals( trailingslashit( plugin_dir_url( $path ) ), $plugin->plugin_url );
		$this->assertEquals( $plugin->plugin_path . trailingslashit( 'includes' ), $plugin->includes_path );
		$this->assertTrue( is_a( $plugin->post_type, 'WP_Slack_Post_Type' ) );
		$this->assertTrue( is_a( $plugin->notifier, 'WP_Slack_Notifier' ) );
		$this->assertTrue( is_a( $plugin->post_meta_box, 'WP_Slack_Post_Meta_Box' ) );
		$this->assertTrue( is_a( $plugin->submit_meta_box, 'WP_Slack_Submit_Meta_Box' ) );
		$this->assertTrue( is_a( $plugin->event_manager, 'WP_Slack_Event_Manager' ) );
	}
}
