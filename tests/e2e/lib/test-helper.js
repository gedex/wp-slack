/**
 * External dependencies
 */
import config from 'config';
import request from 'request-promise';
import { WPAdminPostEdit } from 'wp-e2e-page-objects';

/**
 * Internal dependencies
 */
import { PAGE } from './page-map';

export const integrationTitle = 'Test integration ' + new Date().getTime();

export function createIntegration() {
	return global.__USER__.createIntegration( integrationTitle, getIntegrationSettings() );
}

export function activateIntegration() {
	const page = global.__USER__.openSlackIntegrations();
	page.activate( integrationTitle );

	return page;
}

export function deactivateIntegration() {
	const page = global.__USER__.openSlackIntegrations();
	page.deactivate( integrationTitle );

	return page;
}

export function trashIntegrationViaList() {
	const page = global.__USER__.openSlackIntegrations();
	page.trashIntegrationWithTitle( integrationTitle );

	return page;
}

export function trashIntegrationViaEdit() {
	const page = global.__USER__.openSlackIntegrations().editIntegrationWithTitle( integrationTitle );
	page.moveToTrash();

	return page;
}

export function testSendNotification() {
	const page = global.__USER__.openSlackIntegrations().editIntegrationWithTitle( integrationTitle );
	page.testSendNotification();

	return page;
}

export function editIntegration() {
	return global.__USER__.editIntegration( integrationTitle, getIntegrationSettings() );
}

export function enablePostIsPublished() {
	const settings = Object.assign(
		getIntegrationSettings(),
		{
			events: {
				postPublished: true,
				postNeedsReview: false,
				newComment: false
			}
		}
	);

	return global.__USER__.editIntegration( integrationTitle, settings );
}

export function enablePostNeedsReview() {
	const settings = Object.assign(
		getIntegrationSettings(),
		{
			events: {
				postPublished: false,
				postNeedsReview: true,
				newComment: false
			}
		}
	);

	return global.__USER__.editIntegration( integrationTitle, settings );
}

export function enableNewComment() {
	const settings = Object.assign(
		getIntegrationSettings(),
		{
			events: {
				postPublished: false,
				postNeedsReview: false,
				newComment: true
			}
		}
	);

	return global.__USER__.editIntegration( integrationTitle, settings );
}

export function getIntegrationSettings() {
	return Object.assign( {
		events: {
			postPublished: true
		},
		active: true
	}, config.get( 'slack.settings' ) );
}

export function publishPost( postTitle ) {
	global.__USER__.createPost( { title: postTitle } );
	return new WPAdminPostEdit( global.__DRIVER__, { visit: false } );
}

export function createPendingReviewPost( postTitle ) {
	global.__USER__.createPost( { title: postTitle, status: 'Pending Review' } );
	return new WPAdminPostEdit( global.__DRIVER__, { visit: false } );
}

export function trashPost( postTitle ) {
	return global.__USER__.
		open( PAGE.WP_ADMIN_POSTS ).
		trashPostWithTitle( postTitle );
}

export function addComment( postTitle, comment ) {
	return global.__USER__.addComment( postTitle, comment );
}

export function getLatestSlackMessage() {
	const token = config.get( 'slack.token' );
	const ch = config.get( 'slack.channel' );
	const url = `https://slack.com/api/channels.history?token=${ token }&channel=${ ch }&count=1`;
	return request( { url: url } ).then( resp => {
		return JSON.parse( resp ).messages.pop().text;
	} ).catch( err => err );
}
