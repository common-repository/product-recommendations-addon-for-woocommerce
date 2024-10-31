/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { memo } from '@wordpress/element';
import { Link } from 'react-router-dom';
import useMenuFix from '../../hooks/useMenuFix';
import NavMenu from './NavMenu';

function Header( props ) {
    const { onClickAction } = props;

    useMenuFix();

    return (
        <header className="sticky top-0 md:top-6 bg-white z-30 mb-2">
            <div className="container bg-white mx-auto md:mx-auto mb-2 rounded-[5px] md:w-[90%] lg:w-[90%] xl:w-[90%]">
                <div className="flex items-center justify-between h-16 -mb-px">
                    <div className="flex lg:block">
                        <span
                            onClick={ () => onClickAction( `/` ) }
                            className="text-gray-900 hover:text-gray-900 font-medium text-lg focus:outline-none focus:shadow-none hover:cursor-pointer"
                        >
                            <span className="text-primary">
                                { __( 'Product Recommendations', 'product-recommendations-addon-for-woocommerce' ) }
                            </span>
                        </span>
                    </div>
                    <div className="flex lg:block">
                        <NavMenu onClickAction={ onClickAction } />
                    </div>
                </div>
            </div>
            <hr className="wp-header-end" />
        </header>
    );
}

export default memo( Header );
