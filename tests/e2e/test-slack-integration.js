/**
 * External dependencies
 */
import assert from 'selenium-webdriver/testing/assert';
import config from 'config';
import test from 'selenium-webdriver/testing';

/**
 * Internal dependencies
 */
import * as testHelper from './lib/test-helper';

test.describe( 'slack integrations', function() {
	this.timeout( config.get( 'mochaTimeoutMs' ) );

	test.it( 'allows user to create slack integrations', () => {
		assert( testHelper.createIntegration().hasNotice( 'Setting updated.' ) ).
			isTrue( 'expect notice of "settings updated" to be displayed' );
	} );

	test.it( 'allows user to activate and deactivate integration from integrations list', () => {
		assert( testHelper.deactivateIntegration().hasNotice( '1 integration deactivated.' ) ).
			isTrue( 'expect notice of "1 integration deactivated" to be displayed' );

		assert( testHelper.activateIntegration().hasNotice( '1 integration activated.' ) ).
			isTrue( 'expect notice of "1 integration activated" to be displayed' );
	} );

	test.it( 'allows user to trash integration from integrations list', () => {
		assert( testHelper.trashIntegrationViaList().hasNotice( '1 integration moved to the Trash.' ) ).
			isTrue( 'expect notice of "1 integration moved to the Trash" to be displayed' );
	} );

	test.it( 'allows user to test integration via edit integration', () => {
		testHelper.createIntegration();
		assert( testHelper.testSendNotification().getTestNotficationResponse() ).
			equals( 'OK', 'expect test notification get "OK" response' );
	} );

	test.it( 'allows user to edit and trash via edit integration', () => {
		assert( testHelper.editIntegration().hasNotice( 'Setting updated.' ) ).
			isTrue( 'expect notice of "settings updated" to be displayed' );

		assert( testHelper.trashIntegrationViaEdit().hasNotice( '1 integration moved to the Trash.' ) ).
			isTrue( 'expect notice of "1 integration moved to the Trash" to be displayed' );
	} );
} );
