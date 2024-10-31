const AlertMessage = (props) => {
	const {classNames, heading, message} = props;
	return (
		<>
			<div className={classNames} role={`alert`}>
				<p className={`font-bold`}>{heading}</p>
				<p>{message}</p>
			</div>
		</>
	);
}
export default AlertMessage;
