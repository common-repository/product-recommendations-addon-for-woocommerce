import { __ } from '@wordpress/i18n';
import FilterRowData from '../components/filters/FilterRowData';
import { useEffect, useState } from 'react';
import { createEngine, getEngineData } from '../services/EngineServices';
import { useNavigate, useParams } from 'react-router-dom';
import Layout from '../components/layout/Layout';
import PageHeading from '../components/layout/PageHeading';
import SaveButton from '../components/button/SaveButton';
import StatusButton from '../components/button/StatusButton';
import TableLoader from '../components/loader/TableLoader';
import Tooltip from '../components/tooltips/Tooltip';
import ConfirmationPopup from '../components/modal/ConfirmationPopup';
import Header from '../components/layout/Header';
import useUnload from '../hooks/useUnload';
import useDocumentTitle from '../hooks/useDocumentTitle';

const RecommendationEngine = () => {
	const navigate = useNavigate();
	const urlParams = useParams();
	const engineId = urlParams?.id;
	const pageHeading = engineId ? __('Edit Recommendation Engine', `product-recommendations-addon-for-woocommerce`) : __('Create Recommendation Engine', `product-recommendations-addon-for-woocommerce`);
	const registerText = __('Register', `product-recommendations-addon-for-woocommerce`);
	const updateText = __('Update', `product-recommendations-addon-for-woocommerce`);
	const [engineTitle, setEngineTitle] = useState( '' );
	const [engineType, setEngineType] = useState( {title: __('Please select', `product-recommendations-addon-for-woocommerce`), value: '-1'} );
	const [engineVisibilityLocation, setEngineVisibilityLocation] = useState( {title: __('Please select', `product-recommendations-addon-for-woocommerce`), value: '-1'} );
	const [engineFilters, setEngineFilters] = useState([]);
	const [loadData, setLoadData] = useState(false);
	const [showNotification, setShowNotification] = useState(false);
	const [saveButtonTitle, setSaveButtonTitle] = useState( registerText );
	const [isProcessing, setIsProcessing] = useState( false);
	const [isProcessed, setIsProcessed] = useState( false);
	const [notificationType, setNotificationType] = useState( '');
	const [notificationMessage, setNotificationMessage] = useState( '');
	const [engineEnabled, setEngineEnabled] = useState(true);
	const [filtersExists, setFiltersExists] = useState(false);
	const [showFrontendValidation, setShowFrontendValidation] = useState(false);
	const [showUnsavedConfirmation, setShowUnsavedConfirmation] = useState(false);
	const [unsavedPageLeaveLocation, setUnsavedPageLeaveLocation] = useState('');
	// Settings

	const [engineSettings, setEngineSettings] = useState( {
		rows: 1,
		columns: 4,
		customClass: ''
	} );

	const [initialValues, setInitialValues] = useState({
		engine_title: engineTitle,
		engine_type: engineType,
		engine_filters: engineFilters,
		engine_status: engineEnabled,
		metadata: {
			engine_visibility_location: engineVisibilityLocation,
			engine_settings: engineSettings
		}
	});

	const engineTypes = {
		frequently_bought_together: {
			title: __('Frequently Bought Together', `product-recommendations-addon-for-woocommerce`),
			types: [ 'product' ]
		},
		out_of_stock_product_alternatives: {
			title: __('Out of Stock Product Alternatives', `product-recommendations-addon-for-woocommerce`),
			types: [ 'product' ]
		},
		top_rated_products: {
			title: __('Top Rated Products', `product-recommendations-addon-for-woocommerce`),
			types: [ 'product', 'archive', 'cart', 'checkout', 'thankyou' ]
		},
		best_selling_products: {
			title: __('Best Selling Products', `product-recommendations-addon-for-woocommerce`),
			types: [ 'product', 'archive', 'cart', 'checkout', 'thankyou' ]
		},
		new_arrival_products: {
			title: __('New Arrivals', `product-recommendations-addon-for-woocommerce`),
			types: [ 'product', 'archive', 'cart', 'checkout', 'thankyou' ]
		},
		popular_on_sale_products: {
			title: __('On-sale Popular Products', `product-recommendations-addon-for-woocommerce`),
			types: [ 'product', 'archive', 'cart', 'checkout', 'thankyou' ]
		},
		recently_viewed_products: {
			title: __('Recently Viewed Products', `product-recommendations-addon-for-woocommerce`),
			types: [ 'recently_viewed_products', 'product']
		}
	};

	const visibilityLocations = {
		'Single Product Page': {
			woocommerce_after_single_product_summary: {
				title: __(
					'After Single Product Summary',
					`product-recommendations-addon-for-woocommerce`
				),
				type: 'product',
			},
			woocommerce_after_add_to_cart_form: {
				title: __(
					'After Add-To-Cart Button',
					`product-recommendations-addon-for-woocommerce`
				),
				type: 'product',
			},
			woocommerce_after_single_product: {
				title: __(
					'After Single Product',
					`product-recommendations-addon-for-woocommerce`
				),
				type: 'recently_viewed_products',
			},
		},
		'Product Archive Page': {
			woocommerce_before_shop_loop: {
				title: __(
					'Before Products',
					`product-recommendations-addon-for-woocommerce`
				),
				type: 'archive',
			},
			woocommerce_after_shop_loop: {
				title: __(
					'After Products',
					`product-recommendations-addon-for-woocommerce`
				),
				type: 'archive',
			},
			woocommerce_no_products_found: {
				title: __(
					'No Products Found',
					`product-recommendations-addon-for-woocommerce`
				),
				type: 'archive',
			},
		},
		'Cart Page': {
			woocommerce_before_cart_table: {
				title: __(
					'Before Cart Table',
					`product-recommendations-addon-for-woocommerce`
				),
				type: 'cart',
			},
			woocommerce_after_cart_table: {
				title: __(
					'After Cart Table',
					`product-recommendations-addon-for-woocommerce`
				),
				type: 'cart',
			},
			woocommerce_after_cart_totals: {
				title: __(
					'After Cart Totals',
					`product-recommendations-addon-for-woocommerce`
				),

				type: 'cart',
			},
			woocommerce_cart_collaterals: {
				title: __(
					'Cart Collaterals',
					`product-recommendations-addon-for-woocommerce`
				),

				type: 'cart',
			},
			woocommerce_cart_is_empty: {
				title: __(
					'Empty Cart',
					`product-recommendations-addon-for-woocommerce`
				),

				type: 'cart',
			},
		},
		'Checkout Page': {
			woocommerce_before_checkout_form: {
				title: __(
					'Before Checkout Form',
					`product-recommendations-addon-for-woocommerce`
				),
				type: 'checkout',
			},
			woocommerce_review_order_after_submit: {
				title: __(
					'After Place Order Button',
					`product-recommendations-addon-for-woocommerce`
				),
				type: 'checkout',
			},
			woocommerce_after_checkout_form: {
				title: __(
					'After Checkout Form',
					`product-recommendations-addon-for-woocommerce`
				),
				type: 'checkout',
			},
		},
		'Thank You Page': {
			woocommerce_order_details_before_order_table: {
				title: __(
					'Before Order Details',
					`product-recommendations-addon-for-woocommerce`
				),
				type: 'thankyou',
			},
			woocommerce_order_details_after_order_table: {
				title: __(
					'After Order Details',
					`product-recommendations-addon-for-woocommerce`
				),
				type: 'thankyou',
			},
			woocommerce_thankyou: {
				title: __(
					'After Customer Details',
					`product-recommendations-addon-for-woocommerce`
				),
				type: 'thankyou',
			},
		},
	};


	// set document title
	useDocumentTitle(pageHeading);

	const updateEngineFilters = async (e, index) => {
		await setEngineFilters(prevFilters => {
			let value;
			let name;
			const filters = [...prevFilters];

			if ( undefined !== e?.target ) {
				name = e?.target?.name;
				value = e?.target?.value;
			}
			else {
				value = e;
				name = `value`;
			}

			filters[index] = { ...filters[index], [name]: value };
			return filters;
		});
	}

	const inputFieldClassNames = "appearance-none block w-full !bg-gray-50 text-gray-600 border !border-gray-200 rounded !py-3 !px-4 !mb-3 !focus:bg-white ";
	const selectFieldClassNames = "block appearance-none w-full !bg-gray-50 border !border-gray-200 !text-gray-600 !py-3 !px-4 !pr-8 rounded !focus:bg-white ";

	const validateFilters = () => {
		if ( filtersExists ) {
			for ( const filter of engineFilters ) {
				if ( '-1' === filter?.if || '' === filter?.if || '-1' === filter?.condition || '' === filter?.condition ) {
					return false;
				}
			}
		}
		return true;
	}

	const validateData = () => {
		if ( '' === engineTitle || '-1' === engineType?.value || '-1' === engineVisibilityLocation?.value || !validateFilters() ) {
			setShowFrontendValidation(true);
			setIsProcessing(false);
			setNotificationType('Alert');
			setNotificationMessage(__(`Please fill in all the required field(s).`, `product-recommendations-addon-for-woocommerce`));
			setShowNotification(true);
			setIsProcessed(true);
			return false;
		}
		return true;
	}

	const saveEngine = () => {
		setIsProcessing(true);
		if ( !validateData() ) {
			return;
		}
		const engineData = {
			engine_title: engineTitle,
			engine_type: engineType,
			engine_filters: engineFilters,
			engine_status: engineEnabled,
			metadata: {
				engine_visibility_location: engineVisibilityLocation,
				engine_settings: engineSettings
			}
		};

		setInitialValues(engineData);

		createEngine(engineData, engineId)
			.then((response) => {
				if ( response?.engine_id ) {
					navigate(`/engine/edit/${response?.engine_id}`);
				}
				setNotificationType(response?.notification?.type);
				setNotificationMessage(response?.notification?.message);
			})
			.then( async () => {
				await setIsProcessing(false);
				setShowNotification(true);
				setIsProcessed(true);
			});
	}

	const handleUnsavedChanges = () => {
		return (
			engineTitle !== initialValues?.engine_title ||
			engineType?.value !== initialValues?.engine_type?.value ||
			engineVisibilityLocation?.value !== initialValues?.metadata?.engine_visibility_location?.value ||
			engineEnabled !== initialValues?.engine_status ||
			JSON.stringify(engineFilters) !== JSON.stringify(initialValues?.engine_filters) ||
			JSON.stringify(engineSettings) !== JSON.stringify(initialValues?.metadata?.engine_settings)
		);
	}

	const handlePageLeave = (location) => {
		setUnsavedPageLeaveLocation(location);
		if ( handleUnsavedChanges() ) {
			setShowUnsavedConfirmation(true)
		}
		else {
			navigate(`${location}`);
		}
	}

	const handleEngineTitle = (e) => {
		const title = e?.target?.value;
		setEngineTitle( title );
	}

	const handleEngineType = (e) => {
		const type = { title: e?.target?.options[ e?.target?.selectedIndex ].text, value: e?.target?.value };
		setEngineType( type );
	}

	const handleVisibilityLocation = (e) => {
		const location = { title: e?.target?.options[ e?.target?.selectedIndex ].text, value: e?.target?.value };
		setEngineVisibilityLocation( location );
	}

	const handleEngineStatus = () => {
		const status = !engineEnabled;
		setEngineEnabled(status);
	}

	const pageTitleContent = (
		<div className={``}>
			<div className="mr-3 mb-4">
				<span
					className={`text-gray-dark border-none focus:shadow-none hover:cursor-pointer hover:text-gray-600`}
					onClick={() => handlePageLeave(`/`)}
				>
					← {__('Back to list', `product-recommendations-addon-for-woocommerce`)}
				</span>
			</div>
			<div className="text-left">
				<PageHeading text={pageHeading}/>
			</div>

			<div className={`relative  mt-[2rem] text-right bottom-[0.6rem]`}>
				<span
					id={`rex-product-recommendations-engine-settings-btn`}
					className={`px-5 py-3 rounded-tl-[10px] font-bold hover:cursor-pointer bg-white `}
					onClick={() => handleTabs(`rex-product-recommendations-engine-settings`)}
				>
					{__('Settings', `product-recommendations-addon-for-woocommerce`)}
				</span>
				<span
					id={`rex-product-recommendations-engine-configs-btn`}
					className={`px-5 py-3 rounded-tr-[10px] text-white font-bold hover:cursor-pointer bg-[#216DEF]`}
					onClick={() => handleTabs(`rex-product-recommendations-engine-configs`)}
				>
					{__('Configurations', `product-recommendations-addon-for-woocommerce`)}
				</span>
			</div>
		</div>
	);

	const handleTabs = (tabId) => {
		const $ = jQuery;

		switch (tabId) {
			case `rex-product-recommendations-engine-configs`:
				$(`#rex-product-recommendations-engine-settings`).addClass(`hidden`);
				$(`#rex-product-recommendations-engine-settings-btn`).removeClass(`bg-[#216DEF]`).removeClass(`text-white` ).addClass( `bg-white` );
				break;
			case `rex-product-recommendations-engine-settings`:
				$(`#rex-product-recommendations-engine-configs`).addClass( `hidden` );
				$(`#rex-product-recommendations-engine-configs-btn`).removeClass( `bg-[#216DEF]` ).removeClass( `text-white` ).addClass( `bg-white` );
				break;
			default:
				break;
		}

		$(`#${tabId}` ).removeClass( `hidden` );
		$(`#${tabId}-btn` ).removeClass( `bg-white` ).addClass( `text-white bg-[#216DEF]` );
	}

	const addFilterRow = () => {
		setEngineFilters((prevFilters) => {
			return [
				...prevFilters,
				{if: '-1', condition: '-1', value: ''}
			];
		});
	}

	const removeFilterRow = (rowIndex) => {
		setEngineFilters(prevFilters => {
			setFiltersExists( 1 < prevFilters?.length );
			return prevFilters.filter((_, index) => index !== rowIndex);
		});
	}

	const handleEngineFilterSection = async () => {
		await setEngineFilters((prevFilters) => {
			const filters = [...prevFilters];
			filters[0] = { if: '', condition: '', value: '' };
			return filters;
		});
		setFiltersExists(true);
	}

	// Settings
	const handleEngineSettings = (e) => {
		setEngineSettings((prevSettings) => {
			const settings = {...prevSettings};
			settings[e?.target?.name] = e?.target?.value;
			return settings;
		})
	}

	useEffect(() => {
		let isMounted = true;

		setLoadData(false);
		const fetchInitEngineData = () => {
			if ( engineId) {
				getEngineData(engineId)
					.then((response) => {
						setEngineTitle(response?.engine_title);
						setEngineType(response?.engine_type);
						setEngineEnabled( 'publish' === response?.engine_status);
						setEngineVisibilityLocation(response?.visibility_location);

						setInitialValues((prevState) => {
							prevState['engine_status'] = 'publish' === response?.engine_status;
							prevState['engine_title'] = response?.engine_title;
							prevState['engine_type'] = response?.engine_type;
							prevState['metadata']['engine_visibility_location'] = response?.visibility_location;
							return prevState;
						});

						if ( response?.engine_filters ) {
							setEngineFilters( response?.engine_filters );
							setFiltersExists( true );
							setInitialValues((prevState) => {
								prevState['engine_filters'] = response?.engine_filters;
								return prevState;
							});
						}

						if ( response?.engine_settings ) {
							setEngineSettings( () => {
								const settings = response?.engine_settings ?? engineSettings;
								setInitialValues((prevState) => {
									prevState['metadata']['engine_settings'] = settings;
									return prevState;
								});
								return settings;
							});
						}
					})
					.then(() => setLoadData(true))
			} else {
				setLoadData(true);
			}
		}
		fetchInitEngineData();

		const handleSaveButtonTitle = () => {
			if ( engineId ) {
				setSaveButtonTitle(updateText);
			}
			else {
				setSaveButtonTitle(registerText)
			}
		}
		handleSaveButtonTitle();

		return () => { isMounted = false; };

	}, [engineId] );

	useEffect(() => {
		let isMounted = true;

		const handleNotification = () => {
			if ( showNotification ) {
				setTimeout(() => {
					setShowNotification(false);
					setIsProcessed(false);
				}, 3000);
			}
		}
		handleNotification();

		return () => { isMounted = false };
	}, [showNotification]);

	useEffect(() => {
		let isMounted = true;

		if ( isProcessing ) {
			setSaveButtonTitle(__('Processing...', `product-recommendations-addon-for-woocommerce`));
		}
		else if ( isProcessed ) {
			setSaveButtonTitle(notificationType);
		}
		else {
			setSaveButtonTitle(engineId ? updateText : registerText);
		}

		return () => { isMounted = false };
	}, [isProcessing, isProcessed]);

	useUnload((e) => {
		if (handleUnsavedChanges()) {
			e.preventDefault();
			e.returnValue = "";
		}
	});


	return (
		<>
			<Header onClickAction={handlePageLeave}/>

			<Layout
				title={pageTitleContent}
				slug={`engine-edit-create`}
				customClasses={`bg-white px-40 py-16 rounded-tl-[10px] rounded-bl-[10px] rounded-br-[10px] w-full`}
			>
				<div id={`rex-product-recommendations-engine-configs`} className={`flex justify-center`}>
					<div className={`w-full max-w-screen-xl`}>

						{!loadData ? (
							<TableLoader rows={10}/>
						) : (
							<div className={`rex-product-recommendation-engine-contents relative`}>
								<div className={`flex flex-wrap -mx-3 mb-9`}>
									<div className={`w-full px-3`}>
										<StatusButton
											handleEngineStatus={handleEngineStatus}
											engineEnabled={engineEnabled}
										/>
									</div>
								</div>

								<div className={`flex flex-wrap -mx-3 mb-6`}>
									<div className={`w-full px-3`}>
										<div className={`inline-flex mb-2 items-center font-bold gap-1`}>
											<label className={`tracking-wide text-gray-700 text-sm`}
												   htmlFor={`rex-prr-grid-engine-title`}>
												{__(`Engine Title`, `product-recommendations-addon-for-woocommerce`)}
											</label>
											<span
												className={`text-[#dc2627] font-50 lowercase relative top-0.5`}>*</span>
											<Tooltip
												text={__(`Assign a title to the recommended products section when displayed to customers.`, `product-recommendations-addon-for-woocommerce`)}
											/>
										</div>
										<input
											className={inputFieldClassNames + `${(showFrontendValidation && '' === engineTitle) ? ` rex-warning !border-red-500` : ``}`}
											id={`rex-prr-grid-engine-title`} type={`text`}
											placeholder={ __(`Engine Title`, 'product-recommendations-addon-for-woocommerce')}
											onChange={handleEngineTitle}
											value={engineTitle}
											required={true}
										/>
									</div>
								</div>

								<div className={`grid grid-cols-2 gap-4`}>
									<div className={`col-span-1`}>
										<div className={`inline-flex mb-2 items-center font-bold gap-1`}>
											<label className={`tracking-wide text-gray-700 text-sm`}
												   htmlFor={`rex-prr-grid-engine-type`}>
												{__('Engine Type', `product-recommendations-addon-for-woocommerce`)}
											</label>
											<span
												className={`text-[#dc2627] font-50 lowercase relative top-0.5`}>*</span>
											<Tooltip
												text={__(`Choose the type of products you want to recommend using this engine.`, `product-recommendations-addon-for-woocommerce`)}
											/>
										</div>
										<select
											className={selectFieldClassNames + `${(showFrontendValidation && '-1' === engineType?.value) ? ` rex-warning !border-red-500` : ``} min-w-full`}
											id={`rex-prr-grid-engine-type`}
											value={engineType?.value} onChange={handleEngineType}
											required={true}
										>
											<option key={Math.random()}
													value={`-1`}>{__(`Please select`, `product-recommendations-addon-for-woocommerce`)}</option>
											{Object.entries(engineTypes).map(([key, value]) => (
												<option key={Math.random()} value={key}>{value?.title}</option>
											))}
										</select>
									</div>
									<div className={`col-span-1`}>
										<div className={`inline-flex mb-2 items-center font-bold gap-1`}>
											<label className={`tracking-wide text-gray-700 text-sm`}
												   htmlFor={`rex-prr-grid-engine-visibility-location`}>
												{__('Placement/Visibility Location', `product-recommendations-addon-for-woocommerce`)}
											</label>
											<span
												className={`text-[#dc2627] font-50 lowercase relative top-0.5`}>*</span>
											<Tooltip
												text={__(`Choose where you want to display the recommended products in your store.`, `product-recommendations-addon-for-woocommerce`)}
											/>
										</div>
										<select
											className={selectFieldClassNames + `${(showFrontendValidation && '-1' === engineVisibilityLocation?.value) ? ` rex-warning !border-red-500` : ``} min-w-full`}
											id={`rex-prr-grid-engine-visibility-location`}
											value={engineVisibilityLocation?.value}
											onChange={handleVisibilityLocation}
											required={true}
										>
											<option key={Math.random()}
													value={`-1`}>{__('Please select', `product-recommendations-addon-for-woocommerce`)}</option>
											{
												Object.entries(visibilityLocations)
													.filter(([groupKey, locations]) => {
														return Object.entries(locations).some(([locationKey, locationValue]) => {
															return (engineType?.value &&
																engineType?.value in engineTypes &&
																engineTypes[engineType?.value]?.types &&
																engineTypes[engineType?.value]?.types.includes(locationValue?.type));
														});
													})
													.map(([groupKey, locations]) => (
														<optgroup key={groupKey} label={groupKey}>
															{Object.entries(locations)
																.filter(([locationKey, locationValue]) => {
																	return (engineType?.value &&
																		engineType?.value in engineTypes &&
																		engineTypes[engineType?.value]?.types &&
																		engineTypes[engineType?.value]?.types.includes(locationValue?.type));
																})
																.map(([locationKey, locationValue]) => (
																	<option key={locationKey} value={locationKey}>
																		{locationValue?.title}
																	</option>
																))}
														</optgroup>
													))
											}
										</select>
									</div>
								</div>

								{!filtersExists ? (
									'recently_viewed_products' !== engineType.value ? (
										<div
											className={`rex-product-recommendation-filter-section-add-btn mt-8 inline-flex items-center gap-1`}>
										<span
											className={`underline text-sm text-[#216DEF] font-medium hover:text-[#2177ef] hover:cursor-pointer`}
											onClick={handleEngineFilterSection}>
											{__('Advanced Product Filter Options', `product-recommendations-addon-for-woocommerce`)}
										</span>
											<Tooltip
												text={__(`Set conditions to control what products to display as recommendations. By default the products displayed will be random based on the engine type. With filters, you will be able to specify conditions which will help to display more appropriate product recommendations.`, `product-recommendations-addon-for-woocommerce`)}
											/>
										</div>
									) : ''
								) : (
									'recently_viewed_products' !== engineType.value ? (
									<div className={`rex-product-recommendation-engine-fitlers-area mt-8`}>
										<div className={`inline-flex mb-2 items-center font-bold gap-1`}>
										<span className={`block tracking-wide text-gray-700 text-sm font-bold`}>
											{__('Filter Products', `product-recommendations-addon-for-woocommerce`)}
										</span>
											<Tooltip
												text={__(`Set conditions to control what products to display as recommendatons. In case you set multiple conditions, it will mean that a product has to meet all the conditions to be eligible for recommendation.`, `product-recommendations-addon-for-woocommerce`)}
											/>
										</div>
										<div className={`rex-engine-configuration-filters border rounded p-5`}>
											<div className={`grid grid-cols-3 gap-5`}>
												<div className={`inline-flex items-center font-bold mb-2 gap-1`}>
												<span
													className={`block tracking-wide text-gray-700 text-sm font-bold`}>
													{__('If', `product-recommendations-addon-for-woocommerce`)}
												</span>
													<span
														className={`text-[#dc2627] font-50 lowercase relative top-0.5`}>*</span>
												</div>
												<div className={`inline-flex items-center font-bold mb-2 gap-1`}>
												<span
													className={`block tracking-wide text-gray-700 text-sm font-bold`}>
													{__('Condition', `product-recommendations-addon-for-woocommerce`)}
												</span>
													<span
														className={`text-[#dc2627] font-50 lowercase relative top-0.5`}>*</span>
												</div>
												<div className={`inline-flex items-center font-bold mb-2`}>
												<span
													className={`block tracking-wide text-gray-700 text-sm font-bold`}>
													{__('Value', `product-recommendations-addon-for-woocommerce`)}
												</span>
												</div>
											</div>

											{loadData && 0 < engineFilters?.length && engineFilters.map((filter, index) => {
												return (
													<FilterRowData
														key={index}
														index={index}
														filter={filter}
														updateEngineFilters={updateEngineFilters}
														selectFieldClassNames={selectFieldClassNames}
														inputFieldClassNames={inputFieldClassNames}
														removeFilterRow={removeFilterRow}
														showFrontendValidation={showFrontendValidation}
													/>
												);
											})}

											<span
												className={`text-[#216DEF] font-medium hover:text-[#2177ef] cursor-pointer pr-2`}
												onClick={addFilterRow}
											>
											+ {__('New row', `product-recommendations-addon-for-woocommerce`)}
										</span>
										</div>
									</div>) : ''
								)}
							</div>
						)}
					</div>
				</div>

				<div id={`rex-product-recommendations-engine-settings`} className={`flex justify-center hidden`}>
					<div className={`w-full max-w-screen-xl`}>

						{!loadData ? (
							<TableLoader rows={10}/>
						) : (
							<div className={`rex-product-recommendation-engine-settings mb-6 relative`}>
								<div className={`grid grid-cols-2 gap-4 w-full`}>
									<div className={`inline-flex col-span-1 mb-2 items-center font-bold gap-1`}>
										<label className={`tracking-wide text-gray-700 text-sm`}
											   htmlFor={`rex-prr-grid-engine-product-rows`}>
											{__(`Product Rows`, `product-recommendations-addon-for-woocommerce`)}
										</label>
										<span className={`text-red-500 font-50 lowercase relative top-0.5`}>*</span>
										<Tooltip
											text={__(`Assign a value for how many rows you want in your product recommendations block.`, `product-recommendations-addon-for-woocommerce`)}
										/>
									</div>

									<input
										className={inputFieldClassNames + `${(showFrontendValidation && (!engineSettings?.rows || (1 > engineSettings?.rows))) ? ` rex-warning !border-red-500` : ``}`}
										id={`rex-prr-grid-engine-product-rows`} type={`number`}
										name={`rows`}
										placeholder={__(`Product rows`, `product-recommendations-addon-for-woocommerce`)}
										onChange={handleEngineSettings}
										value={engineSettings?.rows >= 1 ? engineSettings?.rows : 1}
										required={true}
										min={1}
									/>
								</div>

								<div className={`grid grid-cols-2 gap-4 w-full`}>
									<div className={`inline-flex col-span-1 mb-2 items-center font-bold gap-1`}>
										<label className={`tracking-wide text-gray-700 text-sm`}
											   htmlFor={`rex-prr-grid-engine-product-columns`}>
											{__(`Product Columns`, `product-recommendations-addon-for-woocommerce`)}
										</label>
										<span className={`text-red-500 font-50 lowercase relative top-0.5`}>*</span>
										<Tooltip
											text={__( `Assign a value for how many columns you want in your product recommendations block.`, `product-recommendations-addon-for-woocommerce`)}
										/>
									</div>
									<input
										className={inputFieldClassNames + `${(showFrontendValidation && (!engineSettings?.columns || (1 > engineSettings?.columns))) ? ` rex-warning !border-red-500` : ``}`}
										id={`rex-prr-grid-engine-product-columns`} type={`number`}
										name={`columns`}
										placeholder={__(`Product columns`, `product-recommendations-addon-for-woocommerce`)}
										onChange={handleEngineSettings}
										value={engineSettings?.columns >= 1 ? engineSettings?.columns : 1}
										required={true}
										min={1}
									/>
								</div>

								<div className={`grid grid-cols-2 gap-4 w-full`}>
									<div className={`inline-flex col-span-1 mb-2 items-center font-bold gap-1`}>
										<label className={`tracking-wide text-gray-700 text-sm`}
											   htmlFor={`rex-prr-grid-engine-product-block-custom-class`}>
											{__(`Custom Class`, `product-recommendations-addon-for-woocommerce`)}
										</label>
										<Tooltip
											text={__(`Assign space separated custom classes for you recommendations product block.`, `product-recommendations-addon-for-woocommerce`)}
										/>
									</div>
									<input
										className={inputFieldClassNames + `col-span-1`}
										id={`rex-prr-grid-engine-product-block-custom-class`} type={`text`}
										name={`customClass`}
										placeholder={__(`Product block custom class`, `product-recommendations-addon-for-woocommerce`)}
										onChange={handleEngineSettings}
										value={engineSettings?.customClass}
										required={true}
									/>
								</div>
							</div>
						)}
					</div>
				</div>

				<SaveButton
					title={saveButtonTitle}
					onClickAction={saveEngine}
					isProcessing={isProcessing}
					showNotification={showNotification}
					notificationType={notificationType}
					notificationMessage={notificationMessage}
				/>

				{showUnsavedConfirmation && (
					<ConfirmationPopup
						onConfirmation={() => navigate(unsavedPageLeaveLocation)}
						onCancellation={() => setShowUnsavedConfirmation(false)}
						message={__(`You have some unsaved changes. Do you want to leave without saving?`, `product-recommendations-addon-for-woocommerce`)}
						confirmButtonText={__(`Yes, leave`, `product-recommendations-addon-for-woocommerce`)}
						cancelButtonText={__(`No, cancel`, `product-recommendations-addon-for-woocommerce`)}
						confirmButtonClass={`text-white bg-gray-500 hover:bg-gray-400 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center`}
						cancelButtonClass={`mr-4 text-white bg-[#2177ef] hover:bg-pruple-400 focus:outline-none font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center`}
					/>
				)}
			</Layout>
		</>
	);
}

export default RecommendationEngine;
