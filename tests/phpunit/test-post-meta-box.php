<?php
/**
 * Class PostMetaBoxTest
 *
 * @package WP_Slack
 * @subpackage Tests
 */

/**
 * Test for post-meta-box.php
 *
 * @since 0.6.0
 */
class PostMetaBoxTest extends WP_UnitTestCase {
	/**
	 * Make sure expected hooks have expected callbacks.
	 *
	 * @since 0.6.0
	 */
	public function test_registered_callbacks() {
		$instance = $GLOBALS['wp_slack']->post_meta_box;
		$this->assertEquals( 10, has_action( 'add_meta_boxes_slack_integration', array( $instance, 'add_meta_box' ) ) );
		$this->assertEquals( 10, has_action( 'save_post', array( $instance, 'save_post' ) ) );
		$this->assertEquals( 10, has_action( 'wp_ajax_slack_test_notify', array( $instance, 'ajax_test_notify' ) ) );
	}
}
