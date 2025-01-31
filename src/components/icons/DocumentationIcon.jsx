const DocumentationIcon =( props ) => {
	const {height, width} = props;

	return (
		<>
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 72 72" height={`${height}px`} width={`${width}px`}><path d="M 25 11 C 20.029 11 16 15.029 16 20 L 16 52 C 16 56.971 20.029 61 25 61 L 47 61 C 51.971 61 56 56.971 56 52 L 56 31 L 42 31 C 38.686 31 36 28.314 36 25 L 36 11 L 25 11 z M 40 11.34375 L 40 25 C 40 26.105 40.896 27 42 27 L 55.65625 27 L 40 11.34375 z M 29 38 L 43 38 C 44.104 38 45 38.895 45 40 C 45 41.105 44.104 42 43 42 L 29 42 C 27.896 42 27 41.105 27 40 C 27 38.895 27.896 38 29 38 z M 29 47 L 43 47 C 44.104 47 45 47.895 45 49 C 45 50.105 44.104 51 43 51 L 29 51 C 27.896 51 27 50.105 27 49 C 27 47.895 27.896 47 29 47 z"/></svg>
		</>
	);
}

export default DocumentationIcon;
