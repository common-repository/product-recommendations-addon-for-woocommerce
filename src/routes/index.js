/**
 * Internal dependencies
 */
import HomePage from '../pages/Home';
import RecommendationEngine from "../pages/RecommendationEngine";
import SettingsPage from "../pages/Settings";
import SetupWizard from '../pages/SetupWizard';
import Analytics from '../pages/Analytics';

const routes = [
	{
		path: '/',
		element: HomePage,
	},
	{
		path: '/engines',
		element: HomePage,
	},
	{
		path: '/create-new',
		element: RecommendationEngine,
	},
	{
		path: '/engine/edit/:id',
		element: RecommendationEngine,
	},
	{
		path: '/settings',
		element: SettingsPage,
	},
	{
		path: '/setup-wizard',
		element: SetupWizard,
	}
	,
	{
		path: '/analytics',
		element: Analytics,
	}
];

export default routes;
