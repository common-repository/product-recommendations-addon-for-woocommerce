import apiFetch from "@wordpress/api-fetch";

export async function createEngine( engineData, engineId = null ) {
	let queryParams = '';
	if ( null !== engineId ) {
		queryParams += `?id=${engineId}`;
	}

	const endpoint = `rex-pr-recommendation/v1/engine/${queryParams}`;
	const options = {
		method: 'POST',
		headers: {
			"Content-type": "application/json",
		},
		body: JSON.stringify({
			engine_data: engineData
		})
	};

	return await apiFetch({path: endpoint, ...options})
		.then((response) => {
			return response;
		})
		.then((data) => {
			return data;
		});
}

export async function getEngineData( engineId ) {
	const endpoint = `rex-pr-recommendation/v1/engine/?id=${engineId}`;
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

export async function getAllEngineData( perPage = 10, offset = 0 ) {
	const endpoint = `rex-pr-recommendation/v1/engines/?per_page=${perPage}&offset=${offset}`;
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

export async function deleteEngineData(id) {
	const endpoint = `rex-pr-recommendation/v1/engine/?id=${id}`;
	const options = {
		method: 'DELETE',
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
