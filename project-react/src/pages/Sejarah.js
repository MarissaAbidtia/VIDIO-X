import React from 'react';

function Sejarah() {
  return (
    <div>
      <h1 style={{ fontSize: '2.5rem', color: '#0000ff', marginBottom: '20px' }}>Sejarah React</h1>
      <div style={{ display: 'flex', gap: '30px', alignItems: 'flex-start' }}>
        <div style={{ flex: '1' }}>
          <p style={{ fontSize: '1.2rem', marginBottom: '20px' }}>
            React dikembangkan oleh Facebook dan dirilis pada tahun 2013. Awalnya dibuat oleh Jordan Walke, seorang insinyur perangkat lunak di Facebook.
          </p>
          <p style={{ fontSize: '1.2rem', marginBottom: '20px' }}>
            React menjadi populer karena pendekatan komponennya yang memudahkan pengembangan aplikasi web yang kompleks dan interaktif.
          </p>
          <p style={{ fontSize: '1.2rem', marginBottom: '30px' }}>
            Saat ini, React adalah salah satu library JavaScript paling populer untuk membangun antarmuka pengguna.
          </p>
        </div>
        <div style={{ flex: '1' }}>
          <img 
            src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a7/React-icon.svg/1200px-React-icon.svg.png" 
            alt="Logo React" 
            style={{ width: '100%', maxWidth: '300px', borderRadius: '8px' }}
          />
        </div>
      </div>
      
      <div style={{ display: 'flex', gap: '10px', marginTop: '20px' }}>
        <button style={{ 
          padding: '10px 20px', 
          backgroundColor: '#0d6efd', 
          color: 'white', 
          border: 'none', 
          borderRadius: '5px',
          cursor: 'pointer'
        }}>
          Lihat Timeline
        </button>
      </div>
    </div>
  );
}

export default Sejarah;