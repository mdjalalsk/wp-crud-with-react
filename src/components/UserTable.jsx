import React, { useState, useEffect } from 'react';
import axios from 'axios';

const UserTable = () => {
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(false);
    const [page, setPage] = useState(1);
    const [totalPages, setTotalPages] = useState(0);
    const perPage = 5;
    const fetchData = async (page) => {
        setLoading(true); // Start loading

        try {
            await new Promise(resolve => setTimeout(resolve, 500)); // 500ms delay
            const response = await axios.get(`${crudApi.root}crud/v1/items`, {
                params: { page, per_page: perPage },
                headers: { 'X-WP-Nonce': crudApi.nonce },
            });

            setData(response.data.items);
            setTotalPages(response.data.total_pages);
        } catch (error) {
            console.error('Error fetching data:', error);
        } finally {
            setLoading(false);
        }
    };
    useEffect(() => {
        fetchData(page);
    }, [page]);
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
    return (
        <div id="show-item">
            {loading ? (
                <p>Loading data...</p> // Display this while loading
            ) : (
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
                    {data.map((item) => (
                        <tr key={item.id}>
                            <td>{item.id}</td>
                            <td>{item.name}</td>
                            <td>{item.email}</td>
                            <td>Edit | Delete</td>
                        </tr>
                    ))}
                    </tbody>
                </table>
            )}

            <div className="pagination">
                <button disabled={page === 1} onClick={handlePrevious}>
                    Previous
                </button>
                <span>Page {page} of {totalPages}</span>
                <button disabled={page === totalPages} onClick={handleNext}>
                    Next
                </button>
            </div>
        </div>
    );
};

export default UserTable;
