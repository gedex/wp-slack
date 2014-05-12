<?php

class WP_Slack_Event_Manager {

	/**
	 * @var WP_Slack_Plugin
	 */
	private $plugin;

	public function __construct( WP_Slack_Plugin $plugin ) {
		$this->plugin = $plugin;

		$this->dispatch_events();
	}

	private function dispatch_events() {

		$events = $this->get_events();

		// Get all integration settings.
		// @todo Adds get_posts method into post type
		// that caches the results.
		$integrations = get_posts( array(
			'post_type'      => $this->plugin->post_type->name,
			'nopaging'       => true,
			'posts_per_page' => -1,
		) );

		foreach ( $integrations as $integration ) {
			$setting = get_post_meta( $integration->ID, 'slack_integration_setting', true );

			// Skip if inactive.
			if ( empty( $setting['active'] ) ) {
				continue;
			}
			if ( ! $setting['active'] ) {
				continue;
			}

			if ( empty( $setting['events'] ) ) {
				continue;
			}

			// For each checked event calls the callback, that's,
			// hooking into event's action-name to let notifier
			// deliver notification based on current integration
			// setting.
			foreach ( $setting['events'] as $event => $is_enabled ) {
				if ( ! empty( $events[ $event ] ) && $is_enabled ) {
					$this->notifiy_via_action( $events[ $event ], $setting );
				}
			}

		}
	}

	/**
	 * Get list of events. There's filter `slack_get_events`
	 * to extend available events that can be notified to
	 * Slack.
	 */
	public function get_events() {
		return apply_filters( 'slack_get_events', array(
			'post_published' => array(
				'action'      => 'transition_post_status',
				'description' => __( 'When a post is published', 'slack' ),
				'default'     => true,
				'message'     => function( $new_status, $old_status, $post ) {
					$notified_post_types = apply_filters( 'slack_event_transition_post_status_post_types', array(
						'post',
					) );

					if ( ! in_array( $post->post_type, $notified_post_types ) ) {
						return false;
					}

					if ( 'publish' !== $old_status && 'publish' === $new_status ) {
						return sprintf(
							'New post published: *<%1$s|%2$s>* by *%3$s*',

							get_permalink( $post->ID ),
							get_the_title( $post->ID ),
							get_the_author_meta( 'display_name', $post->post_author )
						);
					}
				},
			),

			'new_comment' => array(
				'action'      => 'wp_insert_comment',
				'description' => __( 'When there is a new comment', 'slack' ),
				'default'     => false,
				'message'     => function( $comment_id, $comment ) {
					$comment = is_object( $comment ) ? $comment : get_comment( absint( $comment ) );
					$post_id = $comment->comment_post_ID;

					$notified_post_types = apply_filters( 'slack_event_wp_insert_comment_post_types', array(
						'post',
					) );

					if ( ! in_array( get_post_type( $post_id ), $notified_post_types ) ) {
						return false;
					}

					$post_title = get_the_title( $post_id );
					return sprintf(
						'New comment by *%1$s* on *<%2$s|%3$s>* (_%4$s_)',

						$comment->comment_author,
						get_permalink( $post_id ),
						$post_title,
						$comment->comment_approved ? 'approved' : 'pending'
					);
				},
			),
		) );
	}

	public function notifiy_via_action( array $event, array $setting ) {
		$notifier = $this->plugin->notifier;

		$callback = function() use( $event, $setting, $notifier ) {
			$message = '';
			if ( is_string( $event['message'] ) ) {
				$message = $event['message'];
			} else if ( is_callable( $event['message'] ) ) {
				$message = call_user_func_array( $event['message'], func_get_args() );
			}

			if ( ! empty( $message ) ) {
				$setting = wp_parse_args(
					array(
						'text' => $message,
					),
					$setting
				);

				$notifier->notify( new WP_Slack_Event_Payload( $setting ) );
			}
		};
		add_action( $event['action'], $callback, null, 5 );
	}
}
