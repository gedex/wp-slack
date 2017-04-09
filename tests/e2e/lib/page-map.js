/**
 * External dependencies
 */
import { PageMap } from 'wp-e2e-page-objects';

/**
 * Internal dependencies
 */
import WPAdminSlackIntegrations from './wp-admin-slack-integrations';
import WPAdminSlackIntegrationEdit from './wp-admin-slack-integration-edit';
import WPAdminSlackIntegrationNew from './wp-admin-slack-integration-new';

export const PAGE = Object.assign(
	PageMap.PAGE,
	{
		WP_ADMIN_SLACK_INTEGRATIONS: {
			object: WPAdminSlackIntegrations,
			path: '/wp-admin/edit.php?post_type=slack_integration'
		},
		WP_ADMIN_NEW_SLACK_INTEGRATION: {
			object: WPAdminSlackIntegrationNew,
			path: '/wp-admin/post-new.php?post_type=slack_integration'
		},
		WP_ADMIN_EDIT_SLACK_INTEGRATION: {
			object: WPAdminSlackIntegrationEdit,
			path: '/wp-admin/post.php?post=%s&action=edit'
		}
	}
);

export function getPageUrl( baseUrl, page, ...args ) {
	return PageMap.getPageUrl( baseUrl, page.path, ...args );
}
