import { useEffect,useState } from "react";

export default function Layout({
								   title = <></>,
								   slug = '',
								   customClasses = '',
								   children,
							   }) {

	const getClassNames = () => {
		let leftSideClassNames = 'flex-1';
		const rightSideClassNames = 'flex-end text-right';

		// For overview/dashboard page, remove right side flex for mobile devices.
		if ('overview' === slug || 'email-template-create' === slug) {
			leftSideClassNames = 'flex-none';
		}

		return {
			leftSideClassNames,
			rightSideClassNames,
		};
	};

	return (
		<div className={`rex-product-recommendation-${slug}-page mt-[10px]` }>
			 <div className={`container mx-auto md:mx-auto mb-2 py-6 md:w-[90%] lg:w-[90%] xl:w-[90%]`}>
				{title}
				<div className={`rex-product-recommendation-content-area ${customClasses}`}>{children}</div>
			 </div>

		</div>
	);
}
