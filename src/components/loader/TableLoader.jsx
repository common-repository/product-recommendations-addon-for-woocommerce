const TableLoader = ( props ) => {
	const {rows} = props;
	return (
		<>
			<div className={`animate-pulse rounded-[10px]`}>
				{Array.from({ length: rows }, (_, index) => <div key={index} className="h-4 bg-gray-200 mb-6 rounded"></div>)}
			</div>
		</>
	);
}

export default TableLoader;
