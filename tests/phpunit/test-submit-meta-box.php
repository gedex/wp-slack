<?php
/**
 * Class SubmitMetaBoxTest
 *
 * @package WP_Slack
 * @subpackage Tests
 */

/**
 * Test for submit-meta-box.php
 *
 * @since 0.6.0
 */
class SubmitMetaBoxTest extends WP_UnitTestCase {
	/**
	 * Make sure expected hooks have expected callbacks.
	 *
	 * @since 0.6.0
	 */
	public function test_registered_callbacks() {
		$instance = $GLOBALS['wp_slack']->submit_meta_box;

		$this->assertEquals( 10, has_action( 'add_meta_boxes', array( $instance, 'register_meta_box' ) ) );
	}
}
