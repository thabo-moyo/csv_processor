import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './components/App';
import '../css/app.css';

const container = document.getElementById('app');
if (container) {
    const root = createRoot(container);
    root.render(<App />);
}