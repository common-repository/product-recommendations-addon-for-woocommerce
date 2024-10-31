/**
 * External dependencies
 */
import { Link, useLocation } from 'react-router-dom';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import useMenuFix from '../../hooks/useMenuFix';

function NavMenu(props) {
	const { onClickAction } = props;

	const location = useLocation();

	// Fix admin menu sidebar links.
	useMenuFix();

	const navRoutes = location.pathname.split('/');

	const isActive = (path) => {
		const routeName = typeof navRoutes[1] !== 'undefined' ? navRoutes[1] : path;

		if ('/' + routeName === path) {
			return true;
		}

		return false;
	};

	return (
		<div className="flex justify-center align-baseline gap-8">
			<span
				onClick={() => onClickAction(`/`)}
				className={`flex-grow font-bold text-gray-900 focus:text-gray-800 border-b-2 hover:text-gray-700 hover:border-gray-300 sm:py-0.5 max-w-[9rem] focus:shadow-none hover:cursor-pointer ${
					isActive('/')
						? `text-gray-900 border-gray-300`
						: `border-transparent`
				}`}
			>
				<span className="sm:inline hidden float-left">
                    {__('Engines', 'product-recommendations-addon-for-woocommerce')}
                </span>
			</span>

			<span
				onClick={() => onClickAction(`/analytics`)}
				className={`flex-grow font-bold text-gray-900 focus:text-gray-800 border-b-2 hover:text-gray-700 hover:border-gray-300 sm:py-0.5 max-w-[9rem] focus:shadow-none hover:cursor-pointer ${
					isActive('/analytics')
						? `text-gray-900 border-gray-300`
						: `border-transparent`
				}`}
			>
				<span className="sm:inline hidden float-left">
                    {__('Analytics', 'product-recommendations-addon-for-woocommerce')}
                </span>
			</span>
			<span
				onClick={() => onClickAction(`/setup-wizard`)}
				className={`flex-grow font-bold text-gray-900 focus:text-gray-800 border-b-2 hover:text-gray-700 hover:border-gray-300 sm:py-0.5 max-w-[9rem] focus:shadow-none hover:cursor-pointer ${
					isActive('//setup-wizard')
						? `text-gray-900 border-gray-300`
						: `border-transparent`
				}`}
			>
				<span className="sm:inline hidden float-left">
                    {__('Setup Wizard', 'product-recommendations-addon-for-woocommerce')}
                </span>
			</span>
		</div>
	);
}

export default NavMenu;
