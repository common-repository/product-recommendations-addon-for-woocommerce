import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';

import { adminVariable } from '../../setupWizardData/setupWizardData';

const useLicenseValidation = ( {
	license,
	validateLicense,
	licenseAction,
	setLicenseAction,
	nextToggle,
	errorElementId,
	errorElementStrongId,
	loadingElementIds,
	successElementId,
	loadingOpacity = 0.5,
	loadingDisabled = true,
	successDisplay = 'block',
	successTimeout = 2000,
} ) => {
	const [ isProcessing, setIsProcessing ] = useState( false );

	const handleLicenseValidation = () => {
		if ( ! license ) {
			showError(
				__(
					'Please enter license key',
					'product-recommendations-addon-for-woocommerce'
				)
			);
		} else {
			setLicenseAction( 'Loading' );
			handleLoadingState();
			const security = adminVariable?.ajaxNonce;
			validateLicense( licenseAction, license, security )
				.then( ( response ) => {
					if ( response?.status && response?.message ) {
						if ( response?.status === 'Alert' ) {
							showError( response?.message );
						} else if ( response?.status === 'Success' ) {
							showSuccess( response?.message );
							setLicenseAction(
								licenseAction !== 'deactivate'
									? 'deactivate'
									: 'activate'
							);

							if ( licenseAction === 'activate' ) {
								setTimeout( () => {
									nextToggle();
								}, successTimeout );
							}
						}
					}
				} )
				.finally( () => {
					handleLoadedState();
					setLicenseAction(
						licenseAction === 'deactivate'
							? 'deactivate'
							: 'activate'
					);
				} );
		}
	};

	const showError = ( message ) => {
		const errorElement = document.getElementById( errorElementId );
		const successElement = document.getElementById( successElementId );
		let isNotSuccess = '';

		if ( successElement ) {
			isNotSuccess = successElement.innerText === 'undefined ';
		}
		if ( errorElement && isNotSuccess ) {
			errorElement.style.display = 'block';
			const errorStrongElement =
				document.getElementById( errorElementStrongId );
			if ( errorStrongElement ) {
				errorStrongElement.innerText = message;
			}
		}
	};

	const handleLoadingState = () => {
		loadingElementIds.forEach( ( elementId ) => {
			const element = document.getElementById( elementId );
			if ( element ) {
				element.disabled = loadingDisabled;
				element.style.opacity = loadingOpacity;
			}
		} );
		setIsProcessing( true );
	};

	const handleLoadedState = () => {
		loadingElementIds.forEach( ( elementId ) => {
			const element = document.getElementById( elementId );
			if ( element ) {
				element.disabled = false;
				element.style.opacity = 1;
			}
		} );
		setIsProcessing( false );
	};

	const showSuccess = ( message ) => {
		const errorElement = document.getElementById( errorElementId );
		if ( errorElement ) {
			errorElement.style.display = 'none';
		}
		const successElement = document.getElementById( successElementId );
		if ( successElement ) {
			successElement.innerText = message;
			successElement.style.display = successDisplay;
		}
	};

	return {
		handleLicenseValidation,
		isProcessing,
	};
};

export default useLicenseValidation;
