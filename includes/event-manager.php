<?php
/**
 * Event manager.
 *
 * @package WP_Slack
 * @subpackage Event
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dispatch registered events.
 *
 * Event registered in WP_Slack is an array with following structure:
 *
 * {
 *
 *     @type string   $action      WordPress hook.
 *     @type string   $description Description for the event. Appears in integration
 *                                 setting.
 *     @type bool     $default     Default value for integration setting. True
 *                                 means it's checked by default.
 *     @type function $message     The callback for $action. This function must
 *                                 returns a string to notify to Slack.
 *
 * }
 */
class WP_Slack_Event_Manager {

	/**
	 * Plugin's instance.
	 *
	 * @var WP_Slack_Plugin
	 */
	private $plugin;

	/**
	 * Constructor.
	 *
	 * @param WP_Slack_Plugin $plugin Plugin's instance.
	 */
	public function __construct( WP_Slack_Plugin $plugin ) {
		$this->plugin = $plugin;

		$this->dispatch_events();
	}

	/**
	 * Dispatch events.
	 */
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

			// For each checked event calls the callback, that's, hooking into
			// event's action-name to let notifier deliver notification based on
			// current integration setting.
			foreach ( $setting['events'] as $event => $is_enabled ) {
				if ( ! empty( $events[ $event ] ) && $is_enabled ) {
					$this->notifiy_via_action( $events[ $event ], $setting );
				}
			}
		}
	}

	/**
	 * Get list of events. There's filter `slack_get_events` to extend available
	 * events that can be notified to Slack.
	 *
	 * @return array List of events
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
						$excerpt = has_excerpt( $post->ID ) ?
							apply_filters( 'get_the_excerpt', $post->post_excerpt )
							:
							wp_trim_words( strip_shortcodes( $post->post_content ), 55, '&hellip;' );

						return sprintf(
							/* translators: 1) URL, 2) post title, and 3) post author. */
							__( 'New post published: *<%1$s|%2$s>* by *%3$s*', 'slack' ) . "\n" .
							'> %4$s',
							get_permalink( $post->ID ),
							html_entity_decode( get_the_title( $post->ID ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
							get_the_author_meta( 'display_name', $post->post_author ),
							html_entity_decode( $excerpt, ENT_QUOTES, get_bloginfo( 'charset' ) )
						);
					}
				},
			),

			'post_pending_review' => array(
				'action'      => 'transition_post_status',
				'description' => __( 'When a post needs review', 'slack' ),
				'default'     => false,
				'message'     => function( $new_status, $old_status, $post ) {
					$notified_post_types = apply_filters( 'slack_event_transition_post_status_post_types', array(
						'post',
					) );

					if ( ! in_array( $post->post_type, $notified_post_types ) ) {
						return false;
					}

					if ( 'pending' !== $old_status && 'pending' === $new_status ) {
						$excerpt = has_excerpt( $post->ID ) ?
							apply_filters( 'get_the_excerpt', $post->post_excerpt )
							:
							wp_trim_words( strip_shortcodes( $post->post_content ), 55, '&hellip;' );

						return sprintf(
							/* translators: 1) URL, 2) post title and 3) post author. */
							__( 'New post needs review: *<%1$s|%2$s>* by *%3$s*', 'slack' ) . "\n" .
							'> %4$s',
							admin_url( sprintf( 'post.php?post=%d&action=edit', $post->ID ) ),
							html_entity_decode( get_the_title( $post->ID ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
							get_the_author_meta( 'display_name', $post->post_author ),
							html_entity_decode( $excerpt, ENT_QUOTES, get_bloginfo( 'charset' ) )
						);
					}
				},
			),

			'new_comment' => array(
				'action'      => 'wp_insert_comment',
				'priority'    => 999,
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

					$post_title     = get_the_title( $post_id );
					$comment_status = wp_get_comment_status( $comment_id );

					// Ignore spam.
					if ( 'spam' === $comment_status ) {
						return false;
					}

					return sprintf(
						/* translators: 1) edit URL, 2) comment author, 3) post URL, 4) post title, and 5) comment status. */
						__( '<%1$s|New comment> by *%2$s* on *<%3$s|%4$s>* (_%5$s_)', 'slack' ) . "\n" .
						'>%6$s',
						admin_url( "comment.php?c=$comment_id&action=editcomment" ),
						$comment->comment_author,
						get_permalink( $post_id ),
						html_entity_decode( $post_title, ENT_QUOTES, get_bloginfo( 'charset' ) ),
						$comment_status,
						preg_replace( "/\n/", "\n>", get_comment_text( $comment_id ) )
					);
				},
			),
		) );
	}

	/**
	 * Notify Slack from invoked action callback.
	 *
	 * @param array $event   Event.
	 * @param array $setting Integration setting.
	 */
	public function notifiy_via_action( array $event, array $setting ) {
		$priority = 10;
		if ( ! empty( $event['priority'] ) ) {
			$priority = intval( $event['priority'] );
		}

		$callback = $this->get_event_callback( $event, $setting );

		add_action( $event['action'], $callback, $priority, 5 );
	}

	/**
	 * Get event callback for a given event and setting.
	 *
	 * @since 0.6.0
	 *
	 * @param array $event   Event.
	 * @param array $setting Integration setting.
	 *
	 * @return function Callback for a given event and setting
	 */
	public function get_event_callback( array $event, array $setting ) {
		$notifier = $this->plugin->notifier;

		return function() use ( $event, $setting, $notifier ) {
			$callback_args = array();
			$message       = '';

			if ( is_string( $event['message'] ) ) {
				$message = $event['message'];
			} elseif ( is_callable( $event['message'] ) ) {
				$callback_args = func_get_args();
				$message       = call_user_func_array( $event['message'], $callback_args );
			}

			if ( ! empty( $message ) ) {
				$setting = wp_parse_args(
					array(
						'text' => $message,
					),
					$setting
				);

				$resp = $notifier->notify( new WP_Slack_Event_Payload( $setting ) );

				/**
				 * Fires after notify an event to Slack.
				 *
				 * @since 0.6.0
				 *
				 * @param array|WP_Error $resp          Results from wp_remote_post.
				 * @param array          $event         Event.
				 * @param array          $setting       Integration setting.
				 * @param array          $callback_args Callback arguments.
				 */
				do_action( 'slack_after_notify', $resp, $event, $setting, $callback_args );
			}
		};
	}
}
