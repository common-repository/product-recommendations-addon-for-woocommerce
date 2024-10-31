// External
import { __ } from '@wordpress/i18n';
import { memo } from '@wordpress/element';

// Internal
import TableLoader from '../loader/TableLoader';

/**
 * Component for displaying analytics card.
 * @param {Object} props - The props object.
 * @param {JSX.Element} props.source - The icon or image source for the card.
 * @param {string} props.title - The title of the card.
 * @param {string} props.total - The total value displayed on the card.
 * @param {boolean} props.loader - Indicates whether loader should be displayed.
 * @since 1.0.3
 */

const AnalyticsCard = ( props ) => {
	const { source = '', title = '', total = '', loader = '' } = props;

	return (
		<div className="bg-white rounded-md px-9 py-6">
			{ loader && <TableLoader rows={ 3 } /> }

			{ ! loader && total && (
				<>
					<div className="flex items-center flex-row flex-wrap mb-3">
						<span className="h-[38px] w-[38px] flex items-center justify-center rounded-full">
							{ source }
						</span>
						<p className="mx-2 text-[#9398a5] font-semibold text-base">
							{ title }
						</p>
					</div>
					<h3 className=" text-[#181f38] text-[24px] font-medium">
						{ total
							.toString()
							.replace( /\B(?=(\d{3})+(?!\d))/g, ',' ) }
					</h3>
				</>
			) }
		</div>
	);
};

export default memo( AnalyticsCard );
