/**
 * External dependencies
 */
import { HashRouter, Routes, Route } from 'react-router-dom';

/**
 * Internal dependencies
 */
import routes from './routes';

const App = () => {
	return (
		<HashRouter>
			<>
				<Routes>
					{routes.map((route, index) => (
						<Route
							key={index}
							path={route.path}
							element={<route.element />}
						/>
					))}
				</Routes>
			</>
		</HashRouter>
	);
};

export default App;
