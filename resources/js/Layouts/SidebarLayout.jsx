import React from 'react';
import { Link } from 'react-router-dom';

export default function SidebarLayout({ children }) {
    return (
        <div className="sidebar-container">
            {/* Keep only one logo - the icon version */}
            <div className="logo-container">
                <Link href={route('dashboard')} className="sidebar-logo">
                    <i className="fas fa-school"></i>
                    <span>SMAN 1 Girsip</span>
                    <span className="e-learning-text">E-Learning System</span>
                </Link>
            </div>
            
            {/* Remove any other logo sections that might be here */}
            
            {/* ...existing code... */}
        </div>
    );
}