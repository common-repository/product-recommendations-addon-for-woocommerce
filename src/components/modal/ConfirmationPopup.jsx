import {__} from '@wordpress/i18n';
import ExclamationIcon from "../icons/ExclamationIcon";

const ConfirmationPopup = (props) => {
	const {
		onConfirmation,
		onCancellation,
		message='',
		confirmButtonText='',
		cancelButtonText='',
		confirmButtonClass='',
		cancelButtonClass=''
	} = props;
	return (
		<>
			<div
				id={`popup-modal`}
				tabIndex={`-1`}
				className={`fixed z-50 justify-center items-center md:inset-0 max-h-full bg-[#10052e4d] flex top-0 left-0 w-full h-full`}
			>
				<div className={`relative p-4 w-full max-w-md max-h-full`}>
					<div className={`relative bg-white rounded-lg shadow-md bg-gray`}>
						<button
							type="button"
							className={`absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-red-100 hover:text-gray-200 rounded-3xl text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-red-100 dark:hover:text-white`}
							data-modal-hide={`popup-modal`}
							onClick={onCancellation}
						>
							<svg
								className="w-3 h-3"
								aria-hidden="true"
								xmlns="http://www.w3.org/2000/svg"
								fill="none"
								viewBox="0 0 14 14"
							>
								<path
									stroke="currentColor"
									strokeLinecap="round"
									strokeLinejoin="round"
									strokeWidth="2"
									d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
								/>
							</svg>
							<span className="sr-only">{__( `Close modal`, `product-recommendations-addon-for-woocommerce` )}</span>
						</button>
						<div className="p-4 md:p-5 text-center">
							<ExclamationIcon
								height={50}
								width={50}
								color={`red`}
							/>
							<h3 className={`mb-5 text-lg font-semibold font-normal text-gray-600 dark:text-gray-600`}>
								{message}
							</h3>
							<button
								data-modal-hide={`popup-modal`}
								type={`button`}
								className={`inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-[#216DEF] rounded-lg hover:bg-[#2177ef] m-2`}
								onClick={onCancellation}
							>
								{cancelButtonText}
							</button>
							<button
								data-modal-hide={`popup-modal`}
								type={`button`}
								className={`inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-red-500 rounded-lg hover:bg-red-600 m-2`}
								onClick={onConfirmation}
							>
								{confirmButtonText}
							</button>
						</div>
					</div>
				</div>
			</div>
		</>
	);
}

export default ConfirmationPopup;
