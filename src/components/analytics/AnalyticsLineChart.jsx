import {
	CategoryScale,
	Chart as ChartJS,
	Legend,
	LinearScale,
	LineElement,
	PointElement,
	Title,
	Tooltip,
} from 'chart.js';
import { Line } from 'react-chartjs-2';
import { memo } from '@wordpress/element';


ChartJS.register(
	CategoryScale,
	LinearScale,
	PointElement,
	LineElement,
	Title,
	Tooltip,
	Legend
);

/**
 * Component for rendering a line chart for analytics data.
 * @param {Object} props - The props object.
 * @param {Array} props.labels - The labels for the chart.
 * @param {Array} props.values - The values for the chart.
 * @param {number} props.stepSize - The step size for the y-axis.
 * @param {number} props.maxStep - The maximum step for the y-axis.
 * @since 1.0.3
 */

const AnalyticsLineChart = ( props ) => {
	const { labels = [], values = [], stepSize = 2, maxStep = 10 } = props;
	
	const direction = document.documentElement.dir;

	const options = {
		responsive: true,
		plugins: {
			legend: {
				display: false,
			},
			title: {
				display: false,
				text: 'Chart.js Line Chart',
			},
		},
		scales: {
			x: {
				grid: {
					display: false,
				},
				ticks: {
					color: '#9398A5',
					padding: 10,
					reverse: 'rtl' === direction ? true : false,
				},

				scaleLabel: {
					display: true,
					rtl: 'rtl' === direction ? true : false,
				},
			},
			y: {
				min: 0,
				max: maxStep,
				ticks: {
					padding: 20,
					color: '#9398A5',
					stepSize: stepSize,
					callback: ( value ) => value,
				},
				grid: {
					tickBorderDash: [ 10 ],
				},

				position: 'rtl' === direction ? 'right' : 'left',
				scaleLabel: {
					display: true,
					rtl: 'rtl' === direction ? true : false,
				},
			},
		},
	};

	const data = {
		labels,
		datasets: [
			{
				data: values,
				borderColor: '#216DEF',
				backgroundColor: '#ffffff',
				tension: 0.5,
			},
		],
	};

	return (
		<Line
			options={ options }
			data={ data }
			width={ 9 }
			height={ 4 }
		/>
	);
};
export default memo(AnalyticsLineChart);
