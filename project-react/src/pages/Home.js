import React from 'react';
import { useNavigate } from 'react-router-dom';

function Home() {
  const navigate = useNavigate();
  
  return (
    <div>
      <h1 style={{ fontSize: '2.5rem', color: '#0000ff', marginBottom: '20px' }}>Selamat Datang di React</h1>
      <p style={{ fontSize: '1.2rem', marginBottom: '30px' }}>Ini adalah website React pertama saya.</p>
      
      <div style={{ display: 'flex', gap: '10px' }}>
        <button 
          onClick={() => navigate('/tentang')} 
          style={{ 
            padding: '10px 20px', 
            backgroundColor: '#0d6efd', 
            color: 'white', 
            border: 'none', 
            borderRadius: '5px',
            cursor: 'pointer'
          }}
        >
          Mulai Jelajahi
        </button>
      </div>
    </div>
  );
}

export default Home;