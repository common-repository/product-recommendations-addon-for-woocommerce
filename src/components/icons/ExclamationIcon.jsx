const ExclamationIcon = (props) => {
	const {height, width, color} = props;

	return (
		<>
			<svg xmlns="http://www.w3.org/2000/svg" clipRule="evenodd" fillRule="evenodd" fill={color} className={`mb-3.5 mx-auto`} height={`${height}px`} width={`${width}px`} strokeLinejoin="round" strokeMiterlimit="2" viewBox="0 0 24 24">
				<g id="Icon">
					<path d="m1.676 17.021c-.561.968-.568 2.168-.019 3.143.552.982 1.582 1.586 2.695 1.586h15.296c1.113 0 2.143-.604 2.695-1.586.549-.975.542-2.175-.019-3.143-2.159-3.732-5.51-9.523-7.647-13.218-.558-.964-1.577-1.553-2.677-1.553s-2.119.589-2.677 1.553l-7.647 13.218zm1.298.751 7.648-13.218c.287-.497.811-.804 1.378-.804s1.091.307 1.378.804l7.648 13.218c.295.51.299 1.142.01 1.656-.285.507-.814.822-1.388.822h-15.296c-.574 0-1.103-.315-1.388-.822-.289-.514-.285-1.146.01-1.656z"/>
					<circle cx="12" cy="17.375" r="1"/>
					<path d="m10.75 8.875.5 5.5c0 .414.336.75.75.75s.75-.336.75-.75l.5-5.5c0-.414-.25-1.25-1.25-1.25s-1.25.836-1.25 1.25z"/>
				</g>
			</svg>
		</>
	);
}
export default ExclamationIcon;
