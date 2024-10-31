/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState, Fragment } from '@wordpress/element';
import useWindowWidth from '../../hooks/useWindowWidth';

/**
 * Internal dependencies
 */
import Pagination from '../pagination/Pagination';
import { findIndex } from 'lodash';
import LoaderCircle from "../loader/LoaderCircle";

/**
 * Generate Default Props for Table component.
 */
export const defaultTableProps = {
	headers: [],
	rows: [],
	showPagination: true,
	totalItems: 0,
	perPage: 10,
	currentPage: 1,
	onChangePage: () => {},
	onCheckAll: () => {},
	noDataMessage: __(`Sorry ! No data found.`, `product-recommendations-addon-for-woocommerce`),
};

/**
 * Get Header Row class name.
 *
 * @param  header  Table Header Definition
 * @param  index   Table Header Index
 * @param  headers
 *
 * @return string
 */
export const getHeaderRowClassName = (
	header,
	index,
	headers
) => {
	let className = `bg-[#f8fafd] border-y border-y-[.0625rem]  border-[rgba(231,234,243,.7)] text-[#606060] p-3 font-bold capitalize   ${header.className}`;

	// Add style for first header
	className += `${index === 0 ? ' pl-6 w-0 ' : ' '}`;

	// Add style for last header
	className += headers.length === index + 1 ? 'rtl:text-right text-left pr-7' : 'text-left rtl:text-right';

	// Add custom style
	className += ` ${header.className ? header.className : ''}`;

	return className;
};

/**
 * Get Body Row class name.
 *
 * @param  cells
 * @param  index       int
 * @param  customClass string|undefined
 *
 * @return string
 */
export const getBodyCellClassName = (
	cells,
	index,
	customClass
) => {
	let className = `border-b border-gray-lite text-gray-500 p-3 text-left rtl:text-right`;

	// Add style for first cell
	className += `${index === 0 ? ' pl-6 w-0 ' : ' '}`;

	// Add style for last cell
	className +=
		cells.length === index + 1
			? 'pr-7 '
			: 'text-center md:text-left ';

	// Add custom class
	className += typeof customClass !== 'undefined' ? customClass : '';

	return className;
};

/**
 * Table Component.
 *
 * Handles table component rendering.
 *
 * @param  props
 */
const Table = (props) => {
	const {
		headers,
		rows,
		showPagination,
		totalItems,
		perPage,
		onChangePage,
		currentPage,
		noDataMessage,
		responsiveColumns = [],
		loadData
	} = props;

	const width = useWindowWidth();
	const isMobile = width < 600;

	const tableHeaders =
		isMobile && responsiveColumns.length
			? headers.filter((header) => responsiveColumns.includes(header.key))
			: headers;

	const rowCellsForMobile = (cells) => {
		return isMobile && responsiveColumns.length
			? cells.filter((cell) => responsiveColumns.includes(cell.key))
			: cells;
	};

	const [expandedRows, setExpandedRows] = useState([]);
	const toggleRow = (index) => {
		if (expandedRows.includes(index)) {
			const updatedRows = [...expandedRows];
			updatedRows.splice(findIndex(expandedRows, index), 1);
			setExpandedRows(updatedRows);
		} else {
			setExpandedRows([...expandedRows, index]);
		}
	};

	const rtlDirection = document.documentElement.getAttribute('dir') === 'rtl' ? 'rtl' : 'ltr';


	return (
		<>
			<div className="table-outer min-h-[700px] py-6 relative">
				{loadData ? <LoaderCircle/> : (
					<table className={`w-full text-sm text-left ${ 'rtl' === rtlDirection ? 'text-right' : ''}  text-gray-500 dark:text-gray-400`}>
						<thead>
						<tr className="h-16">
							{tableHeaders.map((header, index) => (
								<th
									key={header?.key}
									className={getHeaderRowClassName(
										header,
										index,
										headers
									) }
								>
									{header?.title}
								</th>
							))}
						</tr>
						</thead>
						<tbody>

						{totalItems === 0 && (
							<tr>
								<td colSpan={headers?.length}>
									<div className={`text-center text-gray-dark p-3`}>
										<p className={`text-lg mt-4`}>{noDataMessage}</p>
									</div>
								</td>
							</tr>
						)}

						{rows.map((row, index) => (
							<Fragment key={index}>
								<tr
									key={index}
									className={`h-16 ${
										isMobile ? 'cursor-pointer' : ''
									}`}
									onClick={() => {
										if (isMobile) {
											toggleRow(index);
										}
									}}
								>
									{rowCellsForMobile(row?.cells).map(
										(
											cell,
											indexCell
										) => (
											<td
												key={indexCell}
												className={getBodyCellClassName(
													row?.cells,
													indexCell,
													cell?.className
												)}
											>
												{cell?.value}
											</td>
										)
									)}
								</tr>

								{expandedRows.includes(index) && (
									<tr key={'expand-row-' + index}>
										<td colSpan={responsiveColumns?.length}>
											{row?.cells.map(
												(cell, indexCell) => {
													if (
														!responsiveColumns.includes(
															cell?.key
														)
													) {
														return (
															<div
																key={indexCell}
																className="p-1.5 border-b border-solid border-slate-50 ml-5"
															>
																<p>
																	<b>
																		{
																			headers.filter(
																				(
																					header
																				) =>
																					header?.key ===
																					cell?.key
																			)[0]?.title
																		}{' '}
																		&nbsp;
																	</b>
																</p>
																<div>
																	{cell?.value}
																</div>
															</div>
														);
													}
												}
											)}
										</td>
									</tr>
								)}
							</Fragment>
						))}
						</tbody>
					</table>
				)}
			</div>
			{showPagination && (
				<Pagination
					perPage={
						typeof perPage !== 'undefined'
							? perPage
							: defaultTableProps.perPage
					}
					currentPage={
						typeof currentPage !== 'undefined'
							? currentPage
							: defaultTableProps.currentPage
					}
					total={
						typeof totalItems !== 'undefined'
							? totalItems
							: defaultTableProps.totalItems
					}
					paginate={(page) => {
						typeof onChangePage === 'function'
							? onChangePage(page)
							: '';
					}}
				/>
			)}
		</>
	);
};

export default Table;
