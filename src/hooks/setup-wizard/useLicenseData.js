import { useEffect } from 'react';

const useLicenseData = ({
  isProcessing,
  inputValue,
  fetchLicenseData,
  setLicense,
  setLicenseAction,
}) => {
  useEffect(() => {
    let isMounted = true;

    const getLicenseData = async () => {
      if (!isProcessing) {
        try {
          const response = await fetchLicenseData();
          if (isMounted) {
            const licenseInput = document.getElementById('licenseData');
            if (licenseInput) {
              document.getElementById('activate-button-loader').style.display = 'none';
              document.getElementById('activate_input').style.display = 'block';
              document.getElementById('activate_button').style.display = 'block';

              setLicense(response?.license || '');
              licenseInput.value = response?.license || '';

              setLicenseAction(
                response?.license_status === 'valid' ? 'deactivate' : 'activate'
              );
            }
          }
        } catch (error) {
          console.error('Error fetching license data:', error);
        }
      }
    };

    getLicenseData();

    return () => {
      isMounted = false;
    };
  }, [isProcessing, inputValue, fetchLicenseData, setLicense, setLicenseAction]);
};

export default useLicenseData;
