//External
import { useState, useEffect, memo } from '@wordpress/element';
import { useNavigate } from 'react-router-dom';

//Internal
import Layout from '../components/layout/Layout';

import { WizardSteps } from '../components/setup-wizard/SetupWizardSteps';
import useWizard from '../hooks/setup-wizard/useWizard';
import useActivationButton from '../hooks/setup-wizard/useActivation';
import {
	useLicenseInput,
	useNavigation,
	usePluginCheckboxStyling,
	useVideoToggle,
	useWizardNavigation,
} from '../hooks/setup-wizard/useWizardHelper';

/**
 * Component for managing the setup wizard interface.
 * Handles user interactions, state management, and wizard configuration.
 * @since 1.0.3
 */

const SetupWizard = () => {
	const navigate = useNavigate();
	const [ license, setLicense ] = useState( '' );
	const [ inputValue, setInputValue ] = useState( false );

	/**
	 * Initializes a wizard using the `useWizard` hook with the provided configuration.
	 *
	 * @param {Object} steps - The configuration object defining the steps of the wizard.
	 * @returns {Object} - The wizard object.
	 * @since 1.0.3
	 */
	const wizard = useWizard( WizardSteps );
	const handleNavigation = useNavigation( navigate );
	const { prevToggle, nextToggle } = useWizardNavigation(
		wizard,
		setInputValue
	);
	usePluginCheckboxStyling();
	const handleVideoToggle = useVideoToggle();

	/**
	 * Function definition to use in package.
	 * @since 1.0.3
	 */
	window.handleVideoToggle = handleVideoToggle;
	window.prevToggle = prevToggle;
	window.nextToggle = nextToggle;
	window.handleNavigation = handleNavigation;

	jQuery( document ).on( 'click', '.setup-wizard__pregress-step', function () {
		const clickedElement = jQuery(this);
		const index = clickedElement.index('.setup-wizard__pregress-step');
		const activeElement = jQuery('.setup-wizard__pregress-step.step-active');
		if (activeElement.length) {
			const activeIndex = activeElement.index('.setup-wizard__pregress-step');
			if(activeIndex == 0 && index == 1){
				window.nextToggle();
			}else if(activeIndex == 0 && index == 2){
				window.nextToggle();
				window.nextToggle();
			}else if(activeIndex == 1 && index == 0) {
				window.prevToggle();
			}else if(activeIndex == 1 && index == 2) {
				window.nextToggle();
			}else if(activeIndex == 2 && index == 1){
				window.prevToggle();
			}else if(activeIndex == 2 && index == 0){
				window.prevToggle();
				window.prevToggle();
			}
		}
	});

	jQuery(document).on('click', '#product-recommendation-toggle-button', function () {
		const toggleButton = jQuery('#product-recommendation-toggle-button');
		if (toggleButton.is(':checked')) {
			toggleButton.attr('checked', false);
		} else {
			toggleButton.attr('checked', true);
		}
	});

	jQuery(document).on('click', '.product-recommendation-create-contact', function () {
		const toggleButton = jQuery('#product-recommendation-toggle-button');
		if (toggleButton.is(':checked')) {
			if (window?.rexPrRecommendationAdmin?.user_information) {
				const email = window?.rexPrRecommendationAdmin?.user_information?.email;
				const name = window?.rexPrRecommendationAdmin?.user_information?.name;
				const ajaxNonce = window?.rexPrRecommendationAdmin?.ajaxNonce;

				jQuery.ajax({
					url: window?.rexPrRecommendationAdmin?.ajaxUrl,
					type: 'POST',
					data: {
						action: 'rexprr_create_contact',
						email: email,
						name: name,
						security: ajaxNonce
					},
					success: function(response) {
					},
					error: function(error) {
					}
				});
			}
		}
	});

	jQuery( document ).on('click', '.lets-create-first-recommendation-engine', function(e){
		e.preventDefault();
		window.location.href = window?.rexPrRecommendationAdmin?.createNewEngineURL;
	});




	return (
		<>
			<Layout
				title={ '' }
				slug={ `setup-wizard` }
				customClasses={ `max-w-[1138px] mx-auto` }
			>
				<div id="wizardContainer">

				</div>
			</Layout>
		</>
	);
};

export default memo( SetupWizard );
