import React, { useState } from 'react';
import { __ } from '@wordpress/i18n';

const StatusButton = ( props ) => {
	const { handleEngineStatus, engineEnabled } = props;

	return (
		<>
			<label className="relative inline-flex cursor-pointer select-none items-center justify-center rounded-md bg-white text-gray-700 text-sm font-bold">
				<input
					type="checkbox"
					className="sr-only"
					checked={ engineEnabled }
					onChange={ handleEngineStatus }
				/>
				<span className="mr-[18px]rtl:mr-0">
					{ __(
						`Engine Status`,
						`product-recommendations-addon-for-woocommerce`
					) }
				</span>
				<div
					className={ `bg-gray-50 rounded relative inline-flex mx-4` }
				>
					<span
						className={ `flex items-center gap-2 space-x-[6px] rounded py-2 px-[18px] text-xs font-medium ${
							! engineEnabled
								? 'text-primary bg-red-100'
								: 'text-body-color'
						}` }
					>
						<svg
							className="h-4 w-4 stroke-current"
							fill="none"
							viewBox="0 0 24 24"
							xmlns="http://www.w3.org/2000/svg"
						>
							<path
								strokeLinecap="round"
								strokeLinejoin="round"
								stroke="red"
								strokeWidth="2"
								d="M6 18L18 6M6 6l12 12"
							></path>
						</svg>
						{ engineEnabled
							? __(
									`Disable`,
									`product-recommendations-addon-for-woocommerce`
							  )
							: __(
									`Disabled`,
									`product-recommendations-addon-for-woocommerce`
							  ) }
					</span>
					<span
						className={ `flex items-center gap-2  space-x-[6px] rounded py-2 px-[18px] text-xs font-medium ${
							engineEnabled
								? 'text-primary bg-green-100 '
								: 'text-body-color'
						}` }
					>
						<svg
							width="20"
							height="9"
							viewBox="0 0 11 8"
							fill="none"
							xmlns="http://www.w3.org/2000/svg"
						>
							<path
								d="M10.0915 0.951972L10.0867 0.946075L10.0813 0.940568C9.90076 0.753564 9.61034 0.753146 9.42927 0.939309L4.16201 6.22962L1.58507 3.63469C1.40401 3.44841 1.11351 3.44879 0.932892 3.63584C0.755703 3.81933 0.755703 4.10875 0.932892 4.29224L0.932878 4.29225L0.934851 4.29424L3.58046 6.95832C3.73676 7.11955 3.94983 7.2 4.1473 7.2C4.36196 7.2 4.55963 7.11773 4.71406 6.9584L10.0468 1.60234C10.2436 1.4199 10.2421 1.1339 10.0915 0.951972ZM4.2327 6.30081L4.2317 6.2998C4.23206 6.30015 4.23237 6.30049 4.23269 6.30082L4.2327 6.30081Z"
								fill="green"
								stroke="green"
								strokeWidth="0.5"
							></path>
						</svg>
						{ engineEnabled
							? __(
									`Enabled`,
									`product-recommendations-addon-for-woocommerce`
							  )
							: __(
									`Enable`,
									`product-recommendations-addon-for-woocommerce`
							  ) }
					</span>
				</div>
			</label>
		</>
	);
};
export default StatusButton;
