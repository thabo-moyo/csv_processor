import React, { useState, useEffect } from 'react';
import CsvUploader from './CsvUploader';
import PersonsTable from './PersonsTable';

function App() {
    const [persons, setPersons] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchPersons();
    }, []);

    const fetchPersons = async () => {
        setLoading(true);
        try {
            const response = await fetch('/api/persons');
            const data = await response.json();
            if (response.ok) {
                setPersons(data.data || []);
            }
        } catch (err) {
            console.error('Failed to fetch persons:', err);
        } finally {
            setLoading(false);
        }
    };

    const handleUploadSuccess = () => {
        fetchPersons();
    };

    return (
        <div className="min-h-screen bg-gray-100">
            <div className="container mx-auto py-8 px-4">
                <h1 className="text-4xl font-bold text-center text-gray-800 mb-8">
                    Homeowner CSV Processor
                </h1>

                <CsvUploader onUploadSuccess={handleUploadSuccess} />

                {loading ? (
                    <div className="bg-white rounded-lg shadow-md p-6 max-w-6xl mx-auto">
                        <p className="text-gray-500 text-center">Loading persons...</p>
                    </div>
                ) : (
                    <PersonsTable persons={persons} />
                )}
            </div>
        </div>
    );
}

export default App;