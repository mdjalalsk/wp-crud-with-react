import React, {useState} from 'react';
import AddForm from "./components/AddForm";
import './style.css';
import UserTable from "./components/UserTable";
const App = () => {
    const [showForm, setShowForm] = useState(false);
    const handleAddNew = () => {
        setShowForm(true);
    };
    return (
        <>
            <h1>CRUD Operations </h1>
            <button onClick={handleAddNew}>Add New</button>
             {showForm && <AddForm />}
             <UserTable></UserTable>
        </>
    );
};

export default App;