const PageHeading = ({ text, customClass = '', show = true }) => {
	const showHeading = typeof show !== 'undefined' ? show : true;
	return showHeading ? (
		<h1 className={`text-2xl text-black ${customClass}`}>{text}</h1>
	) : (
		<></>
	);
};

export default PageHeading;
