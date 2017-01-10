<?php
/**
 * Class PostTypeTest
 *
 * @package WP_Slack
 * @subpackage Tests
 */

/**
 * Test for post-type.php
 *
 * @since 0.6.0
 */
class PostTypeTest extends WP_UnitTestCase {
	/**
	 * Test Slack integration CPT is registered.
	 *
	 * @since 0.6.0
	 */
	public function test_registered_post_type() {
		$this->assertTrue( post_type_exists( 'slack_integration' ) );
	}

	/**
	 * Make sure expected hooks have expected callbacks.
	 *
	 * @since 0.6.0
	 */
	public function test_registered_callbacks() {
		$instance = $GLOBALS['wp_slack']->post_type;

		$this->assertEquals( 10, has_action( 'admin_menu', array( $instance, 'remove_submitdiv' ) ) );
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', array( $instance, 'enqueue_scripts' ) ) );
		$this->assertEquals( 10, has_action( 'post_updated_messages', array( $instance, 'post_updated_messages' ) ) );
		$this->assertEquals( 10, has_action( 'bulk_post_updated_messages', array( $instance, 'bulk_post_updated_messages' ) ) );
		$this->assertEquals( 10, has_action( 'bulk_actions-edit-slack_integration', array( $instance, 'custom_bulk_actions' ) ) );
		$this->assertEquals( 10, has_action( 'post_row_actions', array( $instance, 'custom_row_actions' ) ) );
		$this->assertEquals( 10, has_action( 'admin_action_activate', array( $instance, 'activate' ) ) );
		$this->assertEquals( 10, has_action( 'admin_action_deactivate', array( $instance, 'deactivate' ) ) );
		$this->assertEquals( 10, has_action( 'all_admin_notices', array( $instance, 'admin_notices' ) ) );
		$this->assertEquals( 10, has_action( 'manage_slack_integration_posts_columns', array( $instance, 'columns_header' ) ) );
		$this->assertEquals( 10, has_action( 'manage_slack_integration_posts_custom_column', array( $instance, 'custom_column_row' ) ) );
		$this->assertEquals( 10, has_action( 'post_class', array( $instance, 'post_class' ) ) );
		$this->assertEquals( 10, has_action( 'views_edit-slack_integration', array( $instance, 'hide_subsubsub' ) ) );
		$this->assertEquals( 10, has_action( 'enter_title_here', array( $instance, 'title_placeholder' ) ) );
	}
}
