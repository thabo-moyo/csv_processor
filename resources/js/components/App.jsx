import React, { useState } from 'react';

function App() {
    const [file, setFile] = useState(null);
    const [persons, setPersons] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [successMessage, setSuccessMessage] = useState('');

    const handleFileChange = (e) => {
        const selectedFile = e.target.files[0];
        if (selectedFile && selectedFile.type === 'text/csv') {
            setFile(selectedFile);
            setError('');
        } else {
            setError('Please select a valid CSV file');
            setFile(null);
        }
    };

    const handleUpload = async () => {
        if (!file) {
            setError('Please select a file first');
            return;
        }

        setLoading(true);
        setError('');
        setSuccessMessage('');

        const formData = new FormData();
        formData.append('file', file);

        try {
            const response = await fetch('/api/process-csv', {
                method: 'POST',
                body: formData,
            });

            const data = await response.json();

            if (response.ok) {
                setPersons(data.data.persons || []);
                setSuccessMessage(`Successfully processed ${data.data.persons.length} persons`);
                setFile(null);
                document.getElementById('file-input').value = '';
            } else {
                setError(data.error || 'Failed to process CSV');
            }
        } catch (err) {
            setError('Network error: ' + err.message);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen bg-gray-100">
            <div className="container mx-auto py-8 px-4">
                <h1 className="text-4xl font-bold text-center text-gray-800 mb-8">
                    CSV Person Parser
                </h1>

                <div className="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto mb-8">
                    <h2 className="text-2xl font-semibold mb-4">Homeowner list</h2>
                    
                    <div className="space-y-4">
                        <div>
                            <label htmlFor="file-input" className="block text-sm font-medium text-gray-700 mb-2">
                                Select CSV File
                            </label>
                            <input
                                id="file-input"
                                type="file"
                                accept=".csv"
                                onChange={handleFileChange}
                                className="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                            />
                        </div>

                        {file && (
                            <p className="text-sm text-gray-600">
                                Selected: {file.name}
                            </p>
                        )}

                        {error && (
                            <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                                {error}
                            </div>
                        )}

                        {successMessage && (
                            <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                                {successMessage}
                            </div>
                        )}

                        <button
                            onClick={handleUpload}
                            disabled={!file || loading}
                            className={`w-full py-2 px-4 rounded font-medium text-white ${
                                !file || loading
                                    ? 'bg-gray-400 cursor-not-allowed'
                                    : 'bg-blue-600 hover:bg-blue-700'
                            }`}
                        >
                            {loading ? 'Processing...' : 'Upload and Process'}
                        </button>
                    </div>
                </div>

                {persons.length > 0 && (
                    <div className="bg-white rounded-lg shadow-md p-6 max-w-6xl mx-auto">
                        <h2 className="text-2xl font-semibold mb-4">
                            Parsed Persons ({persons.length})
                        </h2>
                        
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            #
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Title
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            First Name
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Initial
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Last Name
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {persons.map((person, index) => (
                                        <tr key={index} className="hover:bg-gray-50">
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {index + 1}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {person.title || '-'}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {person.first_name || '-'}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {person.initial || '-'}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {person.last_name || '-'}
                                            </td>
                                            <td className="px-6 py-4 text-sm text-gray-500">
                                                {person.original_name || '-'}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}

export default App;