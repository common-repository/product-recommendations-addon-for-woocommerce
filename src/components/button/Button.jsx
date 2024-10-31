const defaultButtonProps = {
	text: '',
	icon: undefined,
	iconPosition: 'left',
	type: 'default',
	outline: false,
	onClick: () => {},
	buttonCustomClass: '',
	iconCustomClass: '',
	textClassName: '',
	smTextHidden: false,
	disabled: false,
	style: {},
};

const Button = (props) => {
	const {
		text,
		type,
		outline,
		onClick,
		buttonCustomClass,
		disabled,
		textClassName,
		style,
	} = { ...defaultButtonProps, ...props };

	/**
	 * Get class Name for button from props.
	 *
	 * @return string
	 */
	const getClassName = () => {
		let className = `transition px-4 pl-4 py-2 leading-5 rounded-md font-medium text-sm`;
		let textColor = 'white';
		let bgColor = '';
		let borderColor = '';
		let bgActiveColor = '';
		let hoverTextColor = 'white';

		switch (type) {
			case 'primary':
				textColor = outline ? 'bg-green-900' : 'bg-white';
				bgColor = outline ? 'bg-white' : 'bg-green-900';
				bgActiveColor = outline ? 'primary-dark' : 'primary-dark';
				borderColor = outline ? 'blue-800' : 'transparent';
				break;

			case 'warning':
				textColor = outline ? 'yellow-500' : 'white';
				bgColor = outline ? 'white' : 'yellow-500';
				bgActiveColor = outline ? 'yellow-600' : 'yellow-600';
				borderColor = outline ? 'yellow-500' : 'transparent';
				break;

			case 'error':
				textColor = outline ? 'error' : 'white';
				bgColor = outline ? 'white' : 'bg-red-500';
				bgActiveColor = outline ? 'error-dark' : 'error';
				borderColor = outline ? 'error' : 'transparent';
				break;

			case 'success':
				textColor = outline ? 'success' : 'white';
				bgColor = outline ? 'white' : 'success-dark';
				bgActiveColor = outline ? 'success-dark' : 'success';
				borderColor = outline ? 'success' : 'transparent';
				break;

			case 'default':
				textColor = 'black';
				bgColor = outline ? 'white' : 'gray-liter';
				bgActiveColor = outline ? 'gray-liter' : 'gray-liter';
				borderColor = outline ? 'transparent' : 'gray-dark';
				hoverTextColor = 'black'; // outline ? 'black' : 'white';
				break;

			default:
				break;
		}

		// Add background and text colors
		className += ` !bg-${bgColor} hover:!bg-${bgActiveColor} hover:!bg-opacity-80 !focus:bg-${bgActiveColor} text-${textColor} hover:text-${hoverTextColor} focus:text-${textColor} hover:rounded-md focus:rounded-md focus:outline-none`;

		// Add border with color
		className += ` border ${
			outline ? ' border-solid ' : ''
		} border-${borderColor} hover:border-${borderColor} focus:border-${borderColor}`;

		// Add custom class name if provided
		if (
			typeof buttonCustomClass !== 'undefined' &&
			buttonCustomClass !== null
		) {
			className = `${buttonCustomClass}`;
		}

		// Add opacity for disabled button
		if (disabled) {
			className += ' opacity-80 cursor-not-allowed';
		}

		return className;
	};
	return (
		<button
			className={getClassName()}
			style={{ ...style }}
			onClick={onClick}
			disabled={disabled}
			type="button"
		>
			<span className={textClassName}>{text}</span>

			<br />
		</button>
	);
};

export default Button;
