import React, { useState, useEffect } from 'react';
import axios from 'axios';
import SweetAlert from 'sweetalert2';

const AddEditModal = ({ user, onClose, onRefreshData }) => {
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');

    useEffect(() => {
        if (user) {
            setName(user.name);
            setEmail(user.email);
        } else {
            setName('');
            setEmail('');
        }
    }, [user]);

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            if (user) {
                await axios.put(`${crudApi.root}crud/v1/item/${user.id}`, { name, email });
                SweetAlert.fire('Success', 'User updated successfully', 'success');
            } else {
                await axios.post(`${crudApi.root}crud/v1/item`, { name, email });
                SweetAlert.fire('Success', 'User added successfully', 'success');
            }
            onRefreshData();
            onClose();
        } catch (error) {
            SweetAlert.fire('Error', 'Something went wrong', 'error');
        }
    };

    return (
        <div className="modal">
            <form onSubmit={handleSubmit}>
                <h2>{user ? 'Edit User' : 'Add New User'}</h2>
                <input
                    type="text"
                    placeholder="Enter your name"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    required
                />
                <input
                    type="email"
                    placeholder="Enter your email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    required
                />
                 <div id="modal-button-group">
                    <button type="submit" className="button button-primary">{user ? 'Update User' : 'Add User'}</button>
                    <button type="button" className="button button-cancel" onClick={onClose}>Close</button>
                </div>
            </form>
        </div>
    );
};

export default AddEditModal;
