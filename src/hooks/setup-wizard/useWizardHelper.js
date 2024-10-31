import { useEffect } from 'react';

// Hook for handling license input
const useLicenseInput = (setLicense) => {
  const handleLicense = (e) => {
    setLicense(e.target.value);
  };
  return handleLicense;
};

// Hook for handling navigation
const useNavigation = (navigate) => {
  const handleNavigation = (path) => {
    navigate(path, { replace: true });
  };
  return handleNavigation;
};

// Hook for wizard navigation
const useWizardNavigation = (wizard, setInputValue) => {
  const prevToggle = () => {
    wizard.previousStep();
  };

  const nextToggle = () => {
    wizard.nextStep();
    setInputValue(wizard?.getCurrentStep() + 1);
  };

  return { prevToggle, nextToggle };
};

// Hook for plugin checkbox styling
const usePluginCheckboxStyling = () => {
    const isPluginInstalled = window.rexPrRecommendationAdmin.isWcActive;
	const pluginCheckbox = document?.getElementById(
		'plugin_require_checkbox'
	);
  useEffect(() => {
    if (pluginCheckbox) {
      if (isPluginInstalled === '1') {
        pluginCheckbox.style.backgroundColor = 'rgb(147 197 253)';
        const span = document.getElementById('plugin_require_span');
        if (span) {
          span.style.display = 'block';
        }
      } else {
        pluginCheckbox.style.backgroundColor = '#216DEF';
      }
    }
  }, [pluginCheckbox, isPluginInstalled]);
};

// Hook for handling video toggle
const useVideoToggle = () => {
  const handleVideoToggle = () => {
	  const videoElement = document.getElementById('recommendation-video');
	  const previewContent = document.getElementById('recommendation-preview');
	  const previewButton = document.getElementById('recommendation-button');
	  const videoIframe = document.getElementById('recommendation-video_set');

	  if (videoElement && previewContent && previewButton) {
		  videoIframe.setAttribute('src', 'https://www.youtube.com/embed/HkDFQyOmOLU?&autoplay=1');
		  videoElement.style.display = 'block';
		  previewContent.style.display = 'none';
		  previewButton.style.display = 'none';
	  }
  };
  return handleVideoToggle;
};



export {
  useLicenseInput,
  useNavigation,
  useWizardNavigation,
  usePluginCheckboxStyling,
  useVideoToggle,
};
