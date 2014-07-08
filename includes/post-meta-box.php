<?php

class WP_Slack_Post_Meta_Box {

	/**
	 * @var WP_Slack_Plugin
	 */
	private $plugin;

	public function __construct( WP_Slack_Plugin $plugin ) {
		$this->plugin = $plugin;

		add_action( 'add_meta_boxes_' . $this->plugin->post_type->name, array( $this, 'add_meta_box' ) );

		add_action( 'save_post', array( $this, 'save_post' ) );

		// AJAX handler to test sending notification.
		add_action( 'wp_ajax_slack_test_notify', array( $this, 'ajax_test_notify' ) );
	}

	public function add_meta_box() {
		add_meta_box(
			// ID.
			'slack_setting_metabox',

			// Title.
			__( 'Integration Setting', 'slack' ),

			// Callback.
			array( $this, 'render_meta_box' ),

			// Screen.
			$this->plugin->post_type->name,

			// Context.
			'advanced',

			// Priority.
			'high'
		);
	}

	/**
	 * Display the meta box.
	 *
	 * @param object $post
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field(
			// Action
			$this->plugin->post_type->name,

			// Name.
			$this->plugin->post_type->name . '_nonce'
		);

		// Get existing setting.
		$setting = get_post_meta( $post->ID, 'slack_integration_setting', true );

		// Available events.
		$events = $this->plugin->event_manager->get_events();

		require_once $this->plugin->plugin_path . 'views/post-meta-box.php';
	}

	/**
	 * Saves data in meta box to post meta.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save_post( $post_id ) {
		if ( $this->plugin->post_type->name !== get_post_type( $post_id ) ) {
			return;
		}

		// Check nonce.
		if ( empty( $_POST[ $this->plugin->post_type->name . '_nonce' ] ) ) {
			return;
		}

		// Verify nonce.
		if ( ! wp_verify_nonce( $_POST[ $this->plugin->post_type->name . '_nonce' ], $this->plugin->post_type->name ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( empty( $_POST['slack_setting'] ) ) {
			return;
		}

		$events = array_keys( $this->plugin->event_manager->get_events() );
		$fields = array(
			'service_url' => 'esc_url',
			'channel'     => 'sanitize_text_field',
			'username'    => 'sanitize_text_field',
			'icon_emoji'  => 'sanitize_text_field',
			'active'      => function( $val ) {
				if ( $val ) {
					return true;
				} else {
					return false;
				}
			},
			'events' => function( $val ) use( $events ) {
				$saved = array_fill_keys( $events , 0 );

				foreach ( $events as $event ) {
					if ( ! empty( $val[ $event ] ) ) {
						$saved[ $event ] = absint( $val[ $event ] );
					}
				}

				return $saved;
			}
		);

		$cleaned = array();

		$previous_setting = get_post_meta( $post_id, 'slack_integration_setting', true );
		foreach ( $fields as $field => $sanitizer ) {
			if ( is_callable( $sanitizer ) ) {
				$cleaned[ $field ] = call_user_func(
					$sanitizer,
					! empty( $_POST['slack_setting'][ $field ] ) ? $_POST['slack_setting'][ $field ] : null,
					! empty( $previous_setting[ $field ] ) ? $previous_setting[ $field ] : null
				);
			}
		}

		update_post_meta( $post_id, 'slack_integration_setting', $cleaned );
	}

	public function ajax_test_notify() {
		try {
			$expected_params = array(
				'service_url',
				'channel',
				'username',
				'test_notify_nonce',
			);
			foreach ( $expected_params as $param ) {
				if ( ! isset( $_REQUEST[ $param ] ) ) {
					throw new Exception( sprintf( __( 'Missing param %s', 'slack' ), $param ) );
				}
			}

			if ( ! wp_verify_nonce( $_REQUEST['test_notify_nonce'], 'test_notify_nonce' ) ) {
				throw new Exception( __( 'Malformed value for nonce', 'slack' ) );
			}

			$payload = array(
				'service_url' => esc_url( $_REQUEST['service_url'] ),
				'channel'     => $_REQUEST['channel'],
				'username'    => $_REQUEST['username'],
				'icon_emoji'  => $_REQUEST['icon_emoji'],
				'text'        => __( 'Test sending payload!', 'slack' ),
			);

			$resp = $this->plugin->notifier->notify( new WP_Slack_Event_Payload( $payload ) );

			if ( is_wp_error( $resp ) ) {
				throw new Exception( $resp->get_error_message() );
			}

			$body = trim( wp_remote_retrieve_body( $resp ) );
			if ( 200 !== intval( wp_remote_retrieve_response_code( $resp ) ) ) {
				throw new Exception( $body );
			}

			if ( 'ok' !== strtolower( $body ) ) {
				throw new Exception( $body );
			}
			wp_send_json_success();

		} catch ( Exception $e ) {
			$status_code = 500;
			$message     = $e->getMessage();

			if ( ! $message ) {
				$message = __( 'Unexpected response', 'slack' );
			}

			status_header( 500 );
			wp_send_json_error( $message );
		}
	}
}
