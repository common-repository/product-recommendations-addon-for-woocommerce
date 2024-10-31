/**
 * External dependencies
 */
import { render } from '@wordpress/element';
import { createRoot } from 'react-dom/client';

/**
 * Internal dependencies
 */
import App from './App';

// Import the stylesheet for the plugin.
import './style/tailwind.css';
import './style/main.scss';

// Render the App component into the DOM
const rexProductRecommendations = document.getElementById('rex-product-recommendations-for-woocommerce');
if (rexProductRecommendations) {
	const root = createRoot(rexProductRecommendations);
	root.render(<App />);
}
