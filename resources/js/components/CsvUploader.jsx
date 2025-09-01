import React, { useState } from 'react';

function CsvUploader({ onUploadSuccess }) {
    const [file, setFile] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [successMessage, setSuccessMessage] = useState('');

    const handleFileChange = (e) => {
        const selectedFile = e.target.files[0];
        if (selectedFile && selectedFile.type === 'text/csv') {
            setFile(selectedFile);
            setError('');
            setSuccessMessage('');
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
                const { total_processed, new_added, duplicates } = data.data;
                let message = `Processed ${total_processed} person(s): `;
                if (new_added > 0) {
                    message += `${new_added} new added`;
                }
                if (duplicates > 0) {
                    message += new_added > 0 ? `, ${duplicates} already existed` : `All ${duplicates} already existed`;
                }
                setSuccessMessage(message);
                setFile(null);
                document.getElementById('file-input').value = '';
                
                // Notify parent component to refresh the list
                if (onUploadSuccess) {
                    onUploadSuccess();
                }
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
        <div className="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto mb-8">
            <h2 className="text-2xl font-semibold mb-4">Upload Homeowner List</h2>
            
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
    );
}

export default CsvUploader;