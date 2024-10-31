//External
import { __ } from '@wordpress/i18n';
import { useEffect } from 'react';

const useActivationButton = ({ licenseAction, license }) => {
  useEffect(() => {
    const activationButton = document.getElementById('activate_button');
    if (!activationButton) return;

    if (licenseAction === 'Loading') {
      activationButton.innerText = __('Loading...',  'product-recommendations-addon-for-woocommerce');
    } else if (license) {
      activationButton.innerText = licenseAction === 'activate' ? __('Activate License',  'product-recommendations-addon-for-woocommerce') : __('Deactivate License',  'product-recommendations-addon-for-woocommerce');
    }
  }, [licenseAction, license]);
};

export default useActivationButton;
