//External
import { __ } from '@wordpress/i18n';

export const adminVariable=window?.rexPrRecommendationAdmin;

const assetsPath = adminVariable?.assetsPath;

// images
export const banner_img=`${ assetsPath }/icons/banner-img.png`;
export const wooCommerceIcon=`${ assetsPath }/icons/woo.webp`;
export const yt_preview_img=`${ assetsPath }/icons/preview-yt.png`;
export const wizard_logo=`${ assetsPath }/icons/PRFW_logo.svg`;
export const video_link="https://www.youtube.com/embed/HkDFQyOmOLU";

// Steps data
export const stepOne = {
    step_text:__('Welcome',  'product-recommendations-addon-for-woocommerce'),
	heading: __(
		'Hello, welcome to ',
		'product-recommendations-addon-for-woocommerce'
	),
	strong_heading: [
		__(
			'Product Recommendations',
			'product-recommendations-addon-for-woocommerce'
		),
	],
	description: __(
		"Upsell and cross-sell smoothly, and keep your customers hooked with personalized product suggestions to increase the order values.",
		'product-recommendations-addon-for-woocommerce'
	),
	img_alt: 'banner-img',
	button_text: [
		__( "Let’s create your recommendations", 'product-recommendations-addon-for-woocommerce' ),
		__('Check the guide', 'product-recommendations-addon-for-woocommerce' ),
	],

	// features section data

	feature_section_heading: __(
		'Product Recommendations',
		'product-recommendations-addon-for-woocommerce'
	),
	feature_section_strong_heading: __(
		'Features',
		'product-recommendations-addon-for-woocommerce'
	),

	feature_section_description: __(
		"Create intelligent upsells and cross-sells with rule-based recommendations, strategically place them across your store, and optimize their impact using in-depth analytics.",
		'product-recommendations-addon-for-woocommerce'
	),

	feature_section_button_text: [
		__("Check All Features", `product-recommendations-addon-for-woocommerce`),
        __("", `product-recommendations-addon-for-woocommerce`),
	],

	feature_1: [
		__("Suggest Purchased Together", `product-recommendations-addon-for-woocommerce`),
        __("Suggest Products Commonly Purchased Together.", `product-recommendations-addon-for-woocommerce`),
	],
	feature_2: [
		__("Out-of-Stock Backup Options", `product-recommendations-addon-for-woocommerce`),
        __("Never miss a sale due to stock issues. Always have a plan B for out-of-stock items.", `product-recommendations-addon-for-woocommerce`),
	],
	feature_3: [
		__("Showcase Top Rated Products", `product-recommendations-addon-for-woocommerce`),
        __("Leverage customer feedback by prominently suggesting your highest-rated products.", `product-recommendations-addon-for-woocommerce`),
	],
	feature_4: [
		__("Best Sellers Product", `product-recommendations-addon-for-woocommerce`),
        __("Highlight your best sellers right on product pages.", `product-recommendations-addon-for-woocommerce`),
	],
	feature_5: [
		__("New Arrival Alerts", `product-recommendations-addon-for-woocommerce`),
        __("Strategically showcase recent additions across relevant category pages.", `product-recommendations-addon-for-woocommerce`),
	],
	feature_6: [
		__("On Sale Popular Product", `product-recommendations-addon-for-woocommerce`),
        __("Cash in on shoppers' appetites for deals by spotlighting current discounts.", `product-recommendations-addon-for-woocommerce`),
	],


	// pro feature section
	pro_feature_section_button_text: [
		__("Purchase Now", `product-recommendations-addon-for-woocommerce`),
        __("", `product-recommendations-addon-for-woocommerce`),
	],
	// footer button
	footer_section_button_text: [
		__("Let’s create your recommendations", `product-recommendations-addon-for-woocommerce`),
        __("Next", `product-recommendations-addon-for-woocommerce`),
	],

};

export const stepTwo = {
    step_text:__('Required Plugins',  'product-recommendations-addon-for-woocommerce'),
	heading: [
		 __( 'Necessary', 'product-recommendations-addon-for-woocommerce' ),
		 __( 'Activate Your', 'product-recommendations-addon-for-woocommerce' ),
	],
	strong_heading: [
		__( 'Plugins', 'product-recommendations-addon-for-woocommerce' ),
	],
	card_heading: 'WooCommerce',
	card_text: __( 'Required for Product recommendations', 'product-recommendations-addon-for-woocommerce' ),
	tooltip_span_text: __( 'Installed', 'product-recommendations-addon-for-woocommerce' ),
	img_alt: 'woocommerce-icon',

	button_text: [
		__( "Let’s create your recommendations", 'product-recommendations-addon-for-woocommerce' ),
		__( 'Next', 'product-recommendations-addon-for-woocommerce' ),
	],
	required: __('Required', 'product-recommendations-addon-for-woocommerce'),
};

export const stepThree = {
    step_text:__('Done',  'product-recommendations-addon-for-woocommerce'),
	heading: __( 'You Are', 'product-recommendations-addon-for-woocommerce' ),
	strong_heading: [
        __( 'Done', 'product-recommendations-addon-for-woocommerce' ),
	],
	testimonials_heading: __( 'Testimonials', 'product-recommendations-addon-for-woocommerce' ),
	testimonials_description:[
		__('Working fine & nice support.','product-recommendations-addon-for-woocommerce'),
		__('Good plugin, good service.','product-recommendations-addon-for-woocommerce'),
	],
	testimonials_author_name: [ 'Francesco Marcuzzi', 'Aleks1180' ],
	checkbox_input_text: __( 'Opt-in to receive tips, discounts, and recommendations from the RexTheme team directly in your inbox.', 'product-recommendations-addon-for-woocommerce' ),
	button_text: [
		__( 'Let’s create your recommendations', 'product-recommendations-addon-for-woocommerce' ),
	],
};


