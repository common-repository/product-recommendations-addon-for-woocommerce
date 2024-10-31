import apiFetch from "@wordpress/api-fetch";

/**
 * Asynchronously fetches product categories from the WooCommerce REST API.
 *
 * @returns {Promise} A Promise that resolves with the response from the API.
 * @since 1.0.0
 *
 */
export async function fetchProductCategories(keywords) {
	const endpoint = `rex-pr-recommendation/v1/products/categories?s=${keywords}`;
	const options = {
		method: 'GET',
		headers: {
			"Content-type": "application/json",
		}
	};

	return await apiFetch({path: endpoint, ...options})
		.then((response) => {
			return response;
		})
		.then((data) => {
			return data;
		});
}

/**
 * Asynchronously fetches product tags from the WooCommerce REST API.
 *
 * @returns {Promise} A Promise that resolves with the response from the API.
 * @since 1.0.0
 *
 */
export async function fetchProductTags(keywords) {
	const endpoint = `rex-pr-recommendation/v1/products/tags?s=${keywords}`;
	const options = {
		method: 'GET',
		headers: {
			"Content-type": "application/json",
		}
	};

	return await apiFetch({path: endpoint, ...options})
		.then((response) => {
			return response;
		})
		.then((data) => {
			return data;
		});
}
