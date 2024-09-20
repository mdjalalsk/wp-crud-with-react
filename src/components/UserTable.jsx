import React, { useState, useEffect } from 'react';
import axios from 'axios';
import Swal from 'sweetalert2';
import { IoTrashBinOutline } from "react-icons/io5";
import { FaEdit } from "react-icons/fa";



const UserTable = ({ data, onEditUser, onRefreshData }) => {
    const [currentPageData, setCurrentPageData] = useState([]);
    const [page, setPage] = useState(1);
    const perPage = 5;

    useEffect(() => {
        const startIdx = (page - 1) * perPage;
        const endIdx = startIdx + perPage;
        setCurrentPageData(data.slice(startIdx, endIdx));
    }, [page, data]);

    const totalPages = Math.ceil(data.length / perPage);

    const handleNext = () => {
        if (page < totalPages) {
            setPage(page + 1);
        }
    };

    const handlePrevious = () => {
        if (page > 1) {
            setPage(page - 1);
        }
    };

    const handleDelete = async (id) => {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
        });

        if (result.isConfirmed) {
            try {
                await axios.delete(`${crudApi.root}crud/v1/item/${id}`, {
                    headers: {
                        'X-WP-Nonce': crudApi.nonce,
                    },
                });
                Swal.fire('Deleted!', 'Your file has been deleted.', 'success');
                 onRefreshData();
            } catch (error) {
                console.error('Error deleting item:', error);
                Swal.fire('Error!', 'There was an error deleting the item.', 'error');
            }
        }
    };

    return (
        <div id="show-item">
            <table className="wp-list-table widefat fixed striped table-view-list">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                {currentPageData.length > 0 ? (
                    currentPageData.map((item) => (
                        <tr key={item.id}>
                            <td>{item.id}</td>
                            <td>{item.name}</td>
                            <td>{item.email}</td>
                            <td>
                                <div id="table-actions-button">
                                    <button id="action-edit-button" onClick={() => onEditUser(item)}><FaEdit/></button>
                                    <button id="action-delete-button" onClick={() => handleDelete(item.id)}><IoTrashBinOutline/></button>
                                </div>

                            </td>
                        </tr>
                    ))
                ) : (
                    <tr>
                    <td colSpan="4">No data found</td>
                    </tr>
                )}
                </tbody>
            </table>
            <div className="pagination">
                <p>{data.length}</p>
                <button disabled={page === 1} className="button button-secondary" onClick={handlePrevious}>Previous</button>
                <span>Page {page} of {totalPages}</span>
                <button disabled={page === totalPages} className="button button-secondary" onClick={handleNext}>Next</button>
            </div>
        </div>
    );
};

export default UserTable;
