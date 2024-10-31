
import {__} from "@wordpress/i18n";
const SaveButton = ( props ) => {
	const {
		title,
		onClickAction,
		isProcessing,
		showNotification,
		notificationType,
		notificationMessage
	} = props;

	const notificationTypeText = `Alert` === notificationType ? __(`Alert!`, 'product-recommendations-addon-for-woocommerce') : __(`Success!`, 'product-recommendations-addon-for-woocommerce');
	const notificationColor = `Alert` === notificationType ? `red` : `green`;
	const notificationOpacity = showNotification ? `opacity-100` : `opacity-0`;
	const rtlDirection = document.documentElement.getAttribute('dir') === 'rtl' ? 'rtl' : 'ltr';
	return (
		<>
			<div className="mt-6 flex items-center justify-end gap-x-6">
				<div
					className={`transition-opacity duration-1000 ease-in ${notificationOpacity} flex items-center rounded p-3 font-semibold text-sm text-${notificationColor}-700 bg-${notificationColor}-100 dark:text-${notificationColor}-700 text-right`}
					role="alert">
					<svg className="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true"
						 xmlns="http://www.w3.org/2000/svg"
						 fill="currentColor" viewBox="0 0 20 20">
						<path
							d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
					</svg>

					<div>
						{notificationTypeText} {notificationMessage}
					</div>
				</div>
				<button
					disabled={isProcessing}
					className={`flex border border-[#216DEF] rounded cursor-pointer bg-[#216DEF] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#2177ef] hover:border-[#2177ef] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2177ef] ${'rtl' === rtlDirection ? 'gap-2' : ''}`}
					onClick={onClickAction}
				>
					{isProcessing && (
						<svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
							<circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
							<path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
						</svg>
					)}
					{title}
				</button>
			</div>
		</>
	);
}

export default SaveButton;
