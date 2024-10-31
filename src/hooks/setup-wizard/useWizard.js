import { useEffect, useState } from '@wordpress/element';
import rexWizard from 'rex-setup-wizard-manager';

const useWizard = ( WizardSteps ) => {
	const [ wizard, setWizard ] = useState( null );

	useEffect( () => {
		const initializeWizard = () => {
			setWizard(
				rexWizard( {
					general: {
						title: 'Welcome to the Wizard',
						currentStep: WizardSteps?.currentStep,
						logo: WizardSteps?.logoUrl,
						targetElement: 'wizardContainer',
						logoStyles: WizardSteps?.logoClass,
					},
					steps: WizardSteps?.steps,
				} )
			);
		};

		initializeWizard();

		// Clean up function to destroy the wizard when the component unmounts
		return () => {
			if ( wizard ) {
				wizard.destroy();
			}
		};
	}, [ WizardSteps ] );

	return wizard;
};

export default useWizard;
