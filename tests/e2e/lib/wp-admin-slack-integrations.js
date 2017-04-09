/**
 * External dependencies
 */
import { WebDriverHelper as helper } from 'wp-e2e-webdriver';
import { WPAdminPosts } from 'wp-e2e-page-objects';

/**
 * Internal dependencies
 */
import WPAdminSlackIntegrationEdit from './wp-admin-slack-integration-edit';

export default class WPAdminSlackIntegrations extends WPAdminPosts {
	constructor( driver, args = {} ) {
		super( driver, args );
	}

	editIntegrationWithTitle( title ) {
		this.editPostWithTitle( title );
		return new WPAdminSlackIntegrationEdit( this.driver, { visit: false } );
	}

	trashIntegrationWithTitle( title ) {
		this.trashPostWithTitle( title );
		return this;
	}

	activate( title ) {
		const postsList = this.components.postsList;
		const activateSelector = postsList._getRowActionSelector( title, 'activate' );

		postsList._mouseOverPostTitle( title );
		return helper.clickWhenClickable( this.driver, activateSelector );
	}

	deactivate( title ) {
		const postsList = this.components.postsList;
		const deactivateSelector = postsList._getRowActionSelector( title, 'deactivate' );

		postsList._mouseOverPostTitle( title );
		return helper.clickWhenClickable( this.driver, deactivateSelector );
	}
}
