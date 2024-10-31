import { __ } from '@wordpress/i18n';
import { useNavigate, Link } from 'react-router-dom';
import { useEffect, useState } from 'react';
import apiFetch from '@wordpress/api-fetch';
import { memo } from '@wordpress/element';

//Internal
import Header from '../components/layout/Header';
import Layout from '../components/layout/Layout';
import PageHeading from '../components/layout/PageHeading';
import AnalyticsCard from '../components/analytics/AnalyticsCard';
import FilterWithRange from '../components/filters/FilterWithRange';
import { DateFilter } from '../components/analytics/DataFilter';
import TableLoader from '../components/loader/TableLoader';
import AnalyticsLineChart from '../components/analytics/AnalyticsLineChart';
import RevenueIcon from '../components/icons/Revenue';
import ProductIcon from '../components/icons/Products';
import OrderIcon from '../components/icons/Order';

/**
 * Analytics component for displaying various analytics data and charts.
 * @since 1.0.3
 */
const Analytics = () => {
	const navigate = useNavigate();

	const [ cardTotalLoader, setCardTotalLoader ] = useState( false );
	const [ cardsProductLoader, setCardProductLoader ] = useState( false );
	const [ cardRemainingLoader, setTopCardRemainingLoader ] =
		useState( false );
	const [ order, setOrders ] = useState( '' );
	const [ sells, setSells ] = useState( '' );
	const [ product, setProducts ] = useState( '' );
	const [ filter, setFilter ] = useState( 'monthly' );
	const [ engine, setEngine ] = useState( 'all' );
	const [ engineData, setEngineData ] = useState( {
		'All Engines': __(
			'All Engines',
			'product-recommendations-addon-for-woocommerce'
		),
	} );
	const [ chartLoader, setChartLoader ] = useState( false );
	const [ labels, setLabels ] = useState( [] );
	const [ amount, setAmount ] = useState( [] );
	const [ maxStep, setMaxStep ] = useState( 10 );
	const [ stepSize, setStepSize ] = useState( 2 );

	/**
	 * Direction for right-to-left (RTL) support.
	 * Retrieves the directionality of the document's root element.
	 * @since 1.0.3
	 */
	const direction = document.documentElement.dir;

	const handleNavigation = ( path ) => {
		navigate( path );
	};

	useEffect( () => {
		/**
		 * Fetches data for all available engines.
		 * @since 1.0.3
		 */
		const endpoint = `rex-pr-recommendation/v1/engines-data/`;
		apiFetch( { path: endpoint } ).then( ( reports ) => {
			const engines = reports.data;
			engines.all = __(
				'All Engines',
				'product-recommendations-addon-for-woocommerce'
			);
			setEngineData( engines );
		} );
	}, [] );

	useEffect( () => {
		let isMounted = true;
		getAnalyticsReport( isMounted );
		return () => {
			isMounted = false;
		};
	}, [ filter, engine ] );

	/**
	 * Fetches and handles the analytics report.
	 * @param {boolean} isMounted - Indicates whether the component is mounted or not.
	 * @since 1.0.3
	 */
	const getAnalyticsReport = async ( isMounted ) => {
		setCardTotalLoader( true );
		setCardProductLoader( true );
		setTopCardRemainingLoader( true );
		setChartLoader( true );

		try {
			/**
			 * Fetches analytics data.
			 * @since 1.0.3
			 */
			const endpoint = `rex-pr-recommendation/v1/analytics/?engine_id=${
				engine || 'all'
			}&filter=${ filter }`;
			const jsonResponse = await apiFetch( { path: endpoint } );

			if ( isMounted && jsonResponse ) {
				setOrders( jsonResponse?.total_orders );
				setSells( jsonResponse?.total_sales );
				setProducts( jsonResponse?.total_products );

				setLabels(
					'rtl' === direction
						? jsonResponse?.line_chart_data?.amount?.label?.reverse()
						: jsonResponse?.line_chart_data?.amount?.label
				);
				setAmount(
					'rtl' === direction
						? jsonResponse?.line_chart_data?.amount?.values?.reverse()
						: jsonResponse?.line_chart_data?.amount?.values
				);

				const maxToday = jsonResponse?.line_chart_data?.amount?.max;
				const size = Math.ceil( parseInt( maxToday ) / 10 );
				const step = 10 * Math.ceil( parseInt( maxToday ) / 10 );
				setMaxStep( step );
				setStepSize( size );
			}
		} catch ( e ) {
		} finally {
			setCardTotalLoader( false );
			setCardProductLoader( false );
			setTopCardRemainingLoader( false );
			setChartLoader( false );
		}
	};

	const pageTitleContent = (
		<>
			<div className="mr-3 mb-4">
				<Link
					to="/"
					className="text-gray-dark border-none focus:shadow-none"
				>
					←{ ' ' }
					{ __(
						'Back to list',
						'product-recommendations-addon-for-woocommerce'
					) }
				</Link>
			</div>

			<div className="flex align-center justify-between">
				<PageHeading
					text={ __(
						'Analytics',
						'product-recommendations-addon-for-woocommerce'
					) }
				/>

				<div className="flex align-center gap-4">
					<FilterWithRange
						options={ engineData }
						value={ engine }
						setSearch={ setEngine }
					/>
					<FilterWithRange
						options={ DateFilter }
						value={ filter }
						setSearch={ setFilter }
					/>
				</div>
			</div>
		</>
	);

	return (
		<>
			<Header onClickAction={ handleNavigation } />
			<Layout
				title={ pageTitleContent }
				slug={ `analytics` }
				customClasses={ `mt-[3rem]` }
			>
				<div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 md:gap-10">
					<AnalyticsCard
						title={ __(
							`Total Revenue`,
							'product-recommendations-addon-for-woocommerce'
						) }
						source={ <RevenueIcon /> }
						loader={ cardTotalLoader }
						total={ sells }
					/>
					<AnalyticsCard
						title={ __(
							`Number of Products Sold`,
							'product-recommendations-addon-for-woocommerce'
						) }
						source={ <ProductIcon /> }
						loader={ cardsProductLoader }
						total={ product }
					/>
					<AnalyticsCard
						title={ __(
							`Number of Orders Placed`,
							'product-recommendations-addon-for-woocommerce'
						) }
						source={ <OrderIcon /> }
						loader={ cardRemainingLoader }
						total={ order }
					/>
				</div>

				<div>
					<div className="bg-white rounded-md mt-5 px-9 py-6">
						<header className="mb-6">
							<h4 className="text-[18px] text-[#9398a5] font-semibold ">
								{ __(
									'Engine Performance',
									'product-recommendations-addon-for-woocommerce'
								) }
							</h4>
						</header>

						{ chartLoader ? (
							<TableLoader rows={ 6 } />
						) : (
							<>
								{ maxStep <= 0 ? (
									<div className="min-h-44 flex items-center justify-center">
										<p className="text-[20px] text-[#bfc3ca] font-medium ">
											{ __(
												`No Data Available`,
												'product-recommendations-addon-for-woocommerce'
											) }
										</p>
									</div>
								) : (
									<AnalyticsLineChart
										labels={ labels }
										values={ amount }
										maxStep={ maxStep }
										stepSize={ stepSize }
									/>
								) }
							</>
						) }
					</div>
				</div>
			</Layout>
		</>
	);
};

export default memo(Analytics);
