import {__} from "@wordpress/i18n";
import Select, { components } from "react-select";
import {useState} from "@wordpress/element";
import {fetchProductCategories, fetchProductTags} from "../../services/WCProductServices";

const FilterRowData = ( props ) => {
	const {
		index,
		filter,
		updateEngineFilters,
		selectFieldClassNames,
		inputFieldClassNames,
		removeFilterRow,
		showFrontendValidation
	} = props;

	const [loadingText, setLoadingText] = useState(__( `Please enter 3 or more characters`, `product-recommendations-addon-for-woocommerce` ));
	const [wcTaxonomies, setWcTaxonomies] = useState([]);

	const filterIfs = {
		post_title: {
			value: __(`Product Title`, `product-recommendations-addon-for-woocommerce`),
			conditionsAllowed: [ `equal_to`, `nequal_to`, `contain`, `dn_contain` ]
		},
		post_content: {
			value: __(`Product Description`, `product-recommendations-addon-for-woocommerce`),
			conditionsAllowed: [ `equal_to`, `nequal_to`, `contain`, `dn_contain` ]
		},
		_stock_status: {
			value: __(`Stock Status`, `product-recommendations-addon-for-woocommerce`),
			conditionsAllowed: [ `equal_to`, `nequal_to` ]
		},
		_price: {
			value: __(`Price`, `product-recommendations-addon-for-woocommerce`),
			conditionsAllowed: [ `equal_to`, `nequal_to`, `greater_than`, `greater_than_equal`, `less_than`, `less_than_equal` ]
		},
		_regular_price: {
			value: __(`Regular Price`, `product-recommendations-addon-for-woocommerce`),
			conditionsAllowed: [ `equal_to`, `nequal_to`, `greater_than`, `greater_than_equal`, `less_than`, `less_than_equal` ]
		},
		_sale_price: {
			value: __(`Sale Price`, `product-recommendations-addon-for-woocommerce`),
			conditionsAllowed: [ `equal_to`, `nequal_to`, `greater_than`, `greater_than_equal`, `less_than`, `less_than_equal` ]
		},
		_wc_average_rating: {
			value: __(`Average Ratings`, `product-recommendations-addon-for-woocommerce`),
			conditionsAllowed: [ `equal_to`, `nequal_to`, `greater_than`, `greater_than_equal`, `less_than`, `less_than_equal` ]
		},
		_sale_price_dates_from: {
			value: __(`Sale Start From`, `product-recommendations-addon-for-woocommerce`),
			conditionsAllowed: [ `equal_to`, `nequal_to`, `greater_than`, `greater_than_equal`, `less_than`, `less_than_equal` ]
		},
		_sale_price_dates_to: {
			value: __(`Sale Ends At`, `product-recommendations-addon-for-woocommerce`),
			conditionsAllowed: [ `equal_to`, `nequal_to`, `greater_than`, `greater_than_equal`, `less_than`, `less_than_equal` ]
		},
		_wc_review_count: {
			value: __(`Total Review`, `product-recommendations-addon-for-woocommerce`),
			conditionsAllowed: [ `equal_to`, `nequal_to`, `greater_than`, `greater_than_equal`, `less_than`, `less_than_equal` ]
		},
		total_sales: {
			value: __(`Total Sales`, `product-recommendations-addon-for-woocommerce`),
			conditionsAllowed: [ `equal_to`, `nequal_to`, `greater_than`, `greater_than_equal`, `less_than`, `less_than_equal` ]
		},
		product_cats: {
			value: __(`Product Categories`, `product-recommendations-addon-for-woocommerce`),
			conditionsAllowed: [ `contain`, `dn_contain` ]
		},
		product_tags: {
			value: __(`Product Tags`, `product-recommendations-addon-for-woocommerce`),
			conditionsAllowed: [ `contain`, `dn_contain` ]
		}
	};

	const filterConditions = {
		equal_to: __('Equal to', 'product-recommendations-addon-for-woocommerce'),
		nequal_to: __('Not equal to', 'product-recommendations-addon-for-woocommerce'),
		contain: __('Contains', 'product-recommendations-addon-for-woocommerce'),
		dn_contain: __('Does not contain', 'product-recommendations-addon-for-woocommerce'),
		greater_than: __('Greater than', 'product-recommendations-addon-for-woocommerce'),
		greater_than_equal: __('Greater than or equal to', 'product-recommendations-addon-for-woocommerce'),
		less_than: __('Less than', 'product-recommendations-addon-for-woocommerce'),
		less_than_equal: __('Less than or equal to', 'product-recommendations-addon-for-woocommerce')
	};

	const NoOptionsMessage = ( props ) => {
		return (
			<components.NoOptionsMessage { ...props }>
				<span>{ loadingText }</span>
			</components.NoOptionsMessage>
		);
	};

	const handleTaxonomySearch = ( keywords, taxonomy ) => {
		if ( keywords?.length >= 3 ) {
			setLoadingText(__(`Loading...`,`product-recommendations-addon-for-woocommerce`));
			if ( 'product_cats' === taxonomy ) {
				fetchProductCategories(keywords)
					.then((response) => {
						if ( 0 >= response?.length ) {
							setLoadingText(__(`No category found`,`product-recommendations-addon-for-woocommerce`));
						}
						else {
							setWcTaxonomies(response);
						}
					});
			}
			else if ( 'product_tags' === taxonomy ) {
				fetchProductTags(keywords)
					.then((response) => {
						if ( 0 >= response?.length ) {
							setLoadingText(__(`No tag found`,`product-recommendations-addon-for-woocommerce`));
						}
						else {
							setWcTaxonomies(response);
						}
					});
			}
		} else {
			setLoadingText(__( `Please enter 3 or more characters`, `product-recommendations-addon-for-woocommerce` ));
		}
	};

	const rtlDirection = document.documentElement.getAttribute('dir') === 'rtl' ? 'rtl' : 'ltr';

	return (
		<>
			<div className={`grid grid-cols-3 gap-5 mb-5`} key={index}>
				<div className={``}>
					<select
						className={selectFieldClassNames + `${(showFrontendValidation && ('-1' === filter?.if || '' === filter?.if)) ? ` rex-warning !border-red-500` : ``}`}
						name="if" value={filter?.if}
						onChange={(event) => updateEngineFilters(event, index)}>
						<option key={Math.random()} value="-1">{__('Please select', `product-recommendations-addon-for-woocommerce`)}</option>

						{Object.entries(filterIfs).map(([key, value]) => (
							<option key={Math.random()} value={key}>{value?.value}</option>
						))}
					</select>
				</div>
				<div className={``}>
					<select
						className={selectFieldClassNames + `${(showFrontendValidation && ('-1' === filter?.condition || '' === filter?.condition)) ? ` rex-warning !border-red-500` : ``}`}
						name="condition" value={filter?.condition}
						onChange={(event) => updateEngineFilters(event, index)}>
						<option key={Math.random()} value="-1">{__('Please select', 'product-recommendations-addon-for-woocommerce')}</option>

						{(filter?.if in filterIfs) && filterIfs[filter?.if]?.conditionsAllowed.map((key) => (
							<option key={Math.random()} value={key}>{filterConditions[key]}</option>
						))}
					</select>
				</div>

				<div className={`inline-flex items-center justify-center`}>
					<div className={`w-[95%]`}>
						{(`product_cats` === filter?.if || `product_tags` === filter?.if || `_stock_status` === filter?.if) ? (
							`_stock_status` === filter?.if ? (
								<select
									className={selectFieldClassNames}
									name={`value`} value={filter?.value}
									onChange={(event) => updateEngineFilters(event, index)}>
									<option key={Math.random()} value="-1">{__('Please select', 'product-recommendations-addon-for-woocommerce')}</option>
									<option key={Math.random()} value="instock">{__('In stock', 'product-recommendations-addon-for-woocommerce')}</option>
									<option key={Math.random()} value="outofstock">{__('Out of stock', 'product-recommendations-addon-for-woocommerce')}</option>
									<option key={Math.random()} value="onbackorder">{__('On backorder', 'product-recommendations-addon-for-woocommerce')}</option>
								</select>
							) : (
								<Select
									className={`rex-product-recommendations-taxonomy-select2`}
									name={`value`}
									components={{NoOptionsMessage}}
									value={filter?.value}
									onChange={(event) => updateEngineFilters(event, index, )}
									onInputChange={(keywords) => handleTaxonomySearch(keywords, filter?.if)}
									options={wcTaxonomies}
									isMulti={`true`}
									placeholder={`Search ${(`product_cats` === filter?.if) ? `categories` : `tags`}...`}
									isSearchable
								/>
							)
						) : (
							<input
								type={`${`_sale_price_dates_from` === filter?.if || `_sale_price_dates_to` === filter?.if ? `date` : `text`}`}
								className={`${inputFieldClassNames.replace('!mb-3', '')}`}
								name="value" value={filter?.value}
								onChange={(event) => updateEngineFilters(event, index)}
								placeholder={__(`Filter value`, `product-recommendations-addon-for-woocommerce`)}
							/>
						)}
					</div>

					<div className={`w-[5%] mx-auto ${ "rtl" === rtlDirection ? 'mr-3' : 'ml-3'}`}>
						<button type="button" onClick={() => removeFilterRow(index)}>
							<span
								className="sr-only">{__(`Delete row`, `product-recommendations-addon-for-woocommerce`)}</span>
							<svg width={16} height={16} xmlns="http://www.w3.org/2000/svg" fill="none"
								 viewBox="0 0 20 20" stroke="#dc2627" aria-hidden="true">
								<path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"/>
							</svg>
						</button>
					</div>
				</div>
			</div>
		</>
	);
}

export default FilterRowData;
