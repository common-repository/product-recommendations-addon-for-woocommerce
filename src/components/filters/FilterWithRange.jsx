//External
import { __ } from '@wordpress/i18n';


const FilterWithRange = ( props ) => {
	const { options = {}, value = '', setSearch = ''} = props;

	const handleSelect = ( event ) => {
		setSearch( event.target.value );
	};

	const capitalizeString=( str )=> {
		return str.replace( /\b\w/g, function ( char ) {
			return char.toUpperCase();
		} );
	}

	return (
		<select
			className="min-w-30 !border-none !focus:border-none rounded-md"
			value={ value }
			onChange={ handleSelect }
		>
			{ Object.keys( options ).length > 0 ? (
				Object.entries( options )?.map( ( [ key, val ], index ) => (
					<option
						key={ val + index }
						value={ key }
					>
						{ __(
							`${ capitalizeString(val) }`,
							`product-recommendations-addon-for-woocommerce`
						) }
					</option>
				) )
			) : (
				<p className="text-center font-medium text-blue-500">
					{ __(
						`No data available`,
						`product-recommendations-addon-for-woocommerce`
					) }
				</p>
			) }
		</select>
	);
};

export default FilterWithRange;
