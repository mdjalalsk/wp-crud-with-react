import React, {useEffect, useState} from 'react';
import UserTable from "./components/UserTable";
import AddEditModal from "./components/AddEditModal";
import './style.css';
import axios from 'axios';

const App = () => {
    const [showModal, setShowModal] = useState(false);
    const [currentUser, setCurrentUser] = useState(null);
    const [userData, setUserData] = useState([]);

    const handleAddNew = () => {
        setCurrentUser(null);
        setShowModal(true);
    };

    const handleEditUser = (user) => {
        setCurrentUser(user);
        setShowModal(true);
    };

    const fetchData = async () => {
        try {
            const response = await axios.get(`${crudApi.root}crud/v1/items`, {
                headers: {
                    'X-WP-Nonce': crudApi.nonce,
                },
            });
            setUserData(response.data.items);
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    };

    const handleRefreshData = () => {
        fetchData();
    };

    useEffect(() => {
        fetchData();
    }, []);

    return (
        <>
            <h1 className="wp-heading-inline">CRUD</h1>
            <button onClick={handleAddNew} className="page-title-action">Add New</button>
            <UserTable data={userData} onEditUser={handleEditUser} onRefreshData={handleRefreshData} />
            {showModal && (
                <AddEditModal
                    user={currentUser}
                    onClose={() => setShowModal(false)}
                    onRefreshData={handleRefreshData}
                />
            )}
        </>
    );
};

export default App;
