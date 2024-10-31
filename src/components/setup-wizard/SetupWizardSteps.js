// Importing required data and components for the setup wizard.
import {
	stepOne,         // Data related to Step One of the wizard
	stepThree,       // Data related to Step Three of the wizard
	stepTwo,         // Data related to Step Two of the wizard
	wizard_logo,     // Logo URL for the wizard
} from '../../setupWizardData/setupWizardData';

import { StepOneHTML } from './StepOne';     // HTML content for Step One
import { StepThreeHTML } from './StepThree'; // HTML content for Step Three
import { StepTwoHTML } from './StepTwo';     // HTML content for Step Two

// WizardSteps object contains the configuration for the setup wizard, including each step's content and navigation logic.
export const WizardSteps = {
	steps: [
		{
			// Configuration for Step One
			stepText: `${ stepOne?.step_text }`, // Text to display for Step One
			html: StepOneHTML,                  // HTML content for Step One
			isNextStep: true,                   // Indicates whether the 'Next' button is available
			isPreviousStep: false,              // Indicates whether the 'Previous' button is available
			isSkip: false,                      // Indicates whether the step can be skipped
		},
		{
			// Configuration for Step Two
			stepText: `${ stepTwo?.step_text }`, // Text to display for Step Two
			html: StepTwoHTML,                  // HTML content for Step Two
			isNextStep: true,                   // 'Next' button is available
			isPreviousStep: true,               // 'Previous' button is available
			isSkip: true,                       // This step can be skipped
		},
		{
			// Configuration for Step Three
			stepText: `${ stepThree?.step_text }`, // Text to display for Step Three
			html: StepThreeHTML,                  // HTML content for Step Three
			isNextStep: false,                    // 'Next' button is not available (last step)
			isPreviousStep: true,                 // 'Previous' button is available
			isSkip: false,                        // This step cannot be skipped
		}
	],
	currentStep: 0,                           // Index of the current step in the wizard
	logoUrl: `${ wizard_logo }`,              // URL for the wizard's logo
	logoClass: 'setup-wizard__logo',          // CSS class for styling the wizard's logo
};
