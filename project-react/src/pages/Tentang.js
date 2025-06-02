import React, { useState } from 'react';

function Tentang() {
  const [counter, setCounter] = useState(0);
  
  const handleIncrement = () => {
    setCounter(counter + 1);
  };
  
  const handleDecrement = () => {
    setCounter(counter - 1);
  };
  
  return (
    <div>
      <h1 style={{ fontSize: '2.5rem', color: '#0000ff', marginBottom: '20px' }}>Tentang React</h1>
      <p style={{ fontSize: '1.2rem', marginBottom: '20px' }}>
        React adalah library JavaScript untuk membangun antarmuka pengguna (UI). React memungkinkan pengembang untuk membuat komponen UI yang dapat digunakan kembali.
      </p>
      <p style={{ fontSize: '1.2rem', marginBottom: '30px' }}>
        Dengan React, Anda dapat membuat aplikasi web yang cepat dan responsif dengan mudah.
      </p>
      
      <div style={{ 
        border: '1px solid #ddd', 
        borderRadius: '8px', 
        padding: '20px', 
        marginBottom: '30px',
        backgroundColor: '#f8f9fa'
      }}>
        <h3 style={{ marginBottom: '15px' }}>Counter Sederhana</h3>
        <p style={{ fontSize: '1.2rem', marginBottom: '15px' }}>Nilai saat ini: {counter}</p>
        <div style={{ display: 'flex', gap: '10px' }}>
          <button 
            onClick={handleDecrement} 
            style={{ 
              padding: '10px 20px', 
              backgroundColor: '#dc3545', 
              color: 'white', 
              border: 'none', 
              borderRadius: '5px',
              cursor: 'pointer'
            }}
          >
            Kurang
          </button>
          
          <button 
            onClick={handleIncrement} 
            style={{ 
              padding: '10px 20px', 
              backgroundColor: '#198754', 
              color: 'white', 
              border: 'none', 
              borderRadius: '5px',
              cursor: 'pointer'
            }}
          >
            Tambah
          </button>
        </div>
      </div>
    </div>
  );
}

export default Tentang;