import React, { useState } from 'react';

const AddForm = () => {
    const [user, setUser] = useState([]);

    const handleSubmit = (e) => {
        e.preventDefault();
        alert('from submit');

    };

    return (
        <>
        <form onSubmit={handleSubmit}>

            <table className="form-table">
                <tr>
                    <th scope="row">
                        <label htmlFor="name">Name</label>
                    </th>
                    <td>
                        <input type="text" name="name" id="name" placeholder="Enter your name " required/>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label htmlFor="email">Id</label>
                    </th>
                    <td>
                        <input type="email" name="email" id="email" placeholder="Enater Your Email" required/>
                    </td>
                </tr> <tr>
                    <th scope="row">
                    </th>
                    <td><button type="submit" className="button button-secondary">Submit</button>
                    </td>
            </tr>
            </table>

            </form>
        </>
    );
};

export default AddForm;
