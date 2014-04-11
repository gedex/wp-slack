<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="slack_setting[service_url]"><?php _e( 'Service URL', 'slack' ); ?></label>
			</th>
			<td>
				<input type="text" class="regular-text" name="slack_setting[service_url]" id="slack_setting[service_url]" value="<?php echo ! empty( $setting['service_url'] ) ? esc_url( $setting['service_url'] ) : ''; ?>">
				<p class="description">
					<?php _e( 'Your incoming webhooks URL plugin the token parameter. The format is <code>https://SUBDOMAIN.slack.com/services/hooks/incoming-webhook?token=YOUR_TOKEN</code>.', 'slack' ); ?>
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="slack_setting[channel]"><?php _e( 'Channel', 'slack' ); ?></label>
			</th>
			<td>
				<input type="text" class="regular-text" name="slack_setting[channel]" id="slack_setting[channel]" value="<?php echo ! empty( $setting['channel'] ) ? esc_attr( $setting['channel'] ) : ''; ?>">
				<p class="description">
					<?php _e( 'Channel in which notification will be sent to. For example <code>#general</code>.', 'slack' ); ?>
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="slack_setting[username]"><?php _e( 'Username', 'slack' ); ?></label>
			</th>
			<td>
				<input type="text" class="regular-text" name="slack_setting[username]" id="slack_setting[username]" value="<?php echo ! empty( $setting['username'] ) ? esc_attr( $setting['username'] ) : ''; ?>">
				<p class="description">
					<?php _e( 'Name of the bot that deliver the notification.', 'slack' ); ?>
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<?php _e( 'Events to Notify', 'slack' ); ?>
			</th>
			<td>
				<?php foreach ( $events as $event => $e ) : ?>
					<?php
					$field         = "slack_setting[events][$event]";
					$default_value = ! empty( $e['default'] ) ? $e['default'] : false;
					$value         = isset( $setting['events'][ $event ] ) ? $setting['events'][ $event ] : $default_value;
					?>
					<label>
						<input type="checkbox" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" value="1" <?php checked( $value ); ?>>
						<?php echo esc_html( $e['description'] ); ?>
					</label>
					<br>
			<?php endforeach; ?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="slack_setting[active]"><?php _e( 'Active', 'slack' ); ?></label>
			</th>
			<td>
				<input type="checkbox" name="slack_setting[active]" id="slack_setting[username]" <?php checked( ! empty( $setting['active'] ) ? $setting['active'] : false ); ?>>
				<p class="description">
					<?php _e( 'Notification will not be sent if not checked.', 'slack' ); ?>
				</p>
			</td>
		</tr>

		<?php if ( 'publish' === $post->post_status ) : ?>
		<tr valign="top">
			<th scope="row"></th>
			<td>
				<div id="slack-test-notify">
					<input id="slack-test-notify-nonce" type="hidden" value="<?php echo esc_attr( wp_create_nonce( 'test_notify_nonce' ) ); ?>">
					<button class="button" id="slack-test-notify-button"><?php _e( 'Test send notification with this setting.', 'slack' ); ?></button>
					<div class="spinner"></div>
				</div>
				<div id="slack-test-notify-response"></div>
			</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>
