import {useEffect, useRef, useState} from "react";
import {Link, useNavigate} from "react-router-dom";
import {getAllEngineData, deleteEngineData} from "../services/EngineServices";
import Layout from "../components/layout/Layout";
import {__} from '@wordpress/i18n'
import PageHeading from "../components/layout/PageHeading";
import Button from "../components/button/Button";
import Table from "../components/Table/Table";
import ConfirmationPopup from "../components/modal/ConfirmationPopup";
import Header from "../components/layout/Header";
import useDocumentTitle from "../hooks/useDocumentTitle";

const HomePage = () => {
	const navigate = useNavigate();
	const tableHeaders = [
		{
			key: 'sl',
			title: '#',
			className: '',
		},
		{
			key: 'engine_title',
			title: __(`Engine Title`, `product-recommendations-addon-for-woocommerce`),
			className: '',
		},
		{
			key: 'engine_type',
			title: __(`Engine Type`, `product-recommendations-addon-for-woocommerce`),
			className: '',
		},
		{
			key: 'engine_visibility_location',
			title: __(`Visibility Location`, `product-recommendations-addon-for-woocommerce`),
			className: '',
		},
		{
			key: 'status',
			title: __(`Status`, `product-recommendations-addon-for-woocommerce`),
			className: '',
		},
		{
			key: 'action',
			title: __(`Actions`, `product-recommendations-addon-for-woocommerce`),
			className: '',
		}
	];
	const [ loadData, setLoadData ] = useState(true);
	const [ totalEngine, setTotalEngine ] = useState(0);
	const [ engineListData, setEngineListData ] = useState( [{id: '', cells: []}] );
	const [ perPage, setPerPage ] = useState(10);
	const [ offset, setOffset ] = useState(0);
	const [ page, setPage ] = useState(1);
	const [ showConfirmation, setShowConfirmation ] = useState(false);
	const [ deleteEngineId, setDeleteEngineId ] = useState(null);

	// set document title
	useDocumentTitle(`Engines - Product Recommendations for WooCommerce`);

	/**
	 * Get Page Content
	 * Get Page Content
	 *
	 * @return JSX.Element
	 */
	const pageTitleContent = (
		<>
			<div className="flex items-center justify-between ms-8 me-[3rem]">
				<div>
					<PageHeading text={__(`All Recommendation Engines`, `product-recommendations-addon-for-woocommerce`)}/>
				</div>
				<div>
					<Button
						text={__(` + Create New`, `product-recommendations-addon-for-woocommerce`)}
						type="primary"
						onClick={() => navigate('/create-new')}
						buttonCustomClass={`inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-[#216DEF] rounded-lg hover:bg-[#2177ef]`}
					/>
				</div>
			</div>
		</>
	);

	const deleteEngine = ( id ) => {
		if ( id ) {
			deleteEngineData(id)
				.then(() => {
					handleConfirmationPopup(null, false);
				})
				.then(() => {
					setLoadData(true);
				})
		}
	}

	const updatePageData = (newPage) => {
		setCurrentPage(newPage)
			.then((offsetDirection) => updateOffset(offsetDirection) )
			.then(() => setLoadData(true))
	}

	const setCurrentPage = async (newPage) => {
		let offsetDirection = 1;
		await setPage((prevPage) => {
			offsetDirection = newPage - prevPage;
			return newPage;
		});
		return offsetDirection;
	}

	const updateOffset = (offsetDirection) => {
		setOffset( (prevOffset) => prevOffset + (perPage * offsetDirection) )
	}

	const handleConfirmationPopup = (id, state) => {
		setShowConfirmation( state );
		setDeleteEngineId( id )
	}

	const handleNavigation = (path) => {
		navigate(path);
	}
	useEffect( () => {
		let isMounted = true;
		if ( loadData ) {
			getAllEngineData( perPage, offset )
				.then((response) => {
					const listRows = [{id: '', cells: []}];
					if ( 0 < response?.data?.length && 0 < response?.total ) {
						let status = '';
						response?.data.map((data, index) => {
							status = 'publish' === data?.engine_status;
							listRows[index] = {id: data?.id, cells: []};

							const rowData = [];
							rowData[0] = {className: '', key: 'sl', value: index + 1}
							rowData[1] = {className: '', key: 'engine_title', value: (
									<Link
										to={`/engine/edit/${data?.id}`}
										className={`text-indigo-700 hover:text-indigo-500 font-medium focus:shadow-none`}
									>
										{data?.engine_title}
									</Link>
								)
							}
							rowData[2] = {
								className: '',
								key: 'engine_type',
								value: data?.engine_type?.title
							}
							rowData[3] = {
								className: '',
								key: 'engine_visibility_location',
								value: data?.visibility_location?.title
							}
							rowData[4] = {
								className: status ? `font-medium text-green-700` : `font-medium text-red-400`, key: 'status', value: status ? __( `Active`, `product-recommendations-addon-for-woocommerce` ) : __( `Inactive`, `product-recommendations-addon-for-woocommerce` )
							}
							rowData[5] = {
								className: '', key: 'action', value: (
									<div className="flex items-center gap-1">
										<Link
											to={`/engine/edit/${data?.id}`}
											className={`font-medium text-indigo-700 hover:text-indigo-500 focus:shadow-none`}
										>
											<span>{__(`Edit`, `product-recommendations-addon-for-woocommerce`)}</span>
										</Link>
										|
										<button
											onClick={() => handleConfirmationPopup(data?.id, true)}
											className={`font-medium text-red-500 hover:text-red-400 focus:shadow-none`}
										>
											{__(`Delete`, `product-recommendations-addon-for-woocommerce`)}
										</button>
									</div>
								)
							}

							listRows[index].cells = rowData;
						})
					}
					if ( response?.total ) {
						setTotalEngine(parseInt(response?.total));
					}
					return listRows;
				})
				.then((response) => {
					setEngineListData(response);
				})
				.then(() => {
					setLoadData(false);
				})
		}
		return () => { isMounted = false; };
	}, [loadData] );

	return (
		<>
			<Header onClickAction={handleNavigation}/>
			<Layout
				title={pageTitleContent}
				slug={`engine-listing`}
				customClasses={`bg-white mt-[3rem] rounded-[10px]`}
			>
				<Table
					headers={tableHeaders}
					rows={engineListData}
					totalItems={totalEngine}
					perPage={perPage}
					showPagination={totalEngine > 0}
					responsiveColumns={[]}
					noDataMessage={__(`No engine found!`, `product-recommendations-addon-for-woocommerce`)}
					currentPage={page}
					onChangePage={(page) => updatePageData(page)}
					loadData={loadData}
				/>
				{showConfirmation && (
					<ConfirmationPopup
						onConfirmation={() => deleteEngine(deleteEngineId)}
						onCancellation={() => handleConfirmationPopup(null, false)}
						message={__(`Are you sure you want to delete this engine?`, `product-recommendations-addon-for-woocommerce`)}
						confirmButtonText={__(`Yes, delete`, `product-recommendations-addon-for-woocommerce`)}
						cancelButtonText={__(`No, cancel`, `product-recommendations-addon-for-woocommerce`)}
						confirmButtonBg={`red`}
						cancelButtonBg={`gray`}

					/>
				)}
			</Layout>
		</>
	);
};

export default HomePage;
