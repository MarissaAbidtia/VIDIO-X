import React, { useState } from 'react';

function Kontak() {
  const [formData, setFormData] = useState({
    nama: '',
    email: '',
    pesan: ''
  });
  
  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({
      ...formData,
      [name]: value
    });
  };
  
  const handleSubmit = (e) => {
    e.preventDefault();
    alert(`Pesan dari ${formData.nama} telah dikirim!`);
    setFormData({ nama: '', email: '', pesan: '' });
  };
  
  return (
    <div>
      <h1 style={{ fontSize: '2.5rem', color: '#0000ff', marginBottom: '20px' }}>Hubungi Kami</h1>
      <p style={{ fontSize: '1.2rem', marginBottom: '30px' }}>
        Silakan isi formulir di bawah ini untuk menghubungi kami.
      </p>
      
      <form onSubmit={handleSubmit} style={{ width: '100%', maxWidth: '500px' }}>
        <div style={{ marginBottom: '15px' }}>
          <label 
            htmlFor="nama" 
            style={{ 
              display: 'block', 
              marginBottom: '5px', 
              fontSize: '1rem', 
              fontWeight: 'bold' 
            }}
          >
            Nama
          </label>
          <input 
            type="text" 
            id="nama" 
            name="nama" 
            value={formData.nama} 
            onChange={handleChange} 
            required 
            style={{ 
              width: '100%', 
              padding: '10px', 
              borderRadius: '5px', 
              border: '1px solid #ddd' 
            }} 
          />
        </div>
        
        <div style={{ marginBottom: '15px' }}>
          <label 
            htmlFor="email" 
            style={{ 
              display: 'block', 
              marginBottom: '5px', 
              fontSize: '1rem', 
              fontWeight: 'bold' 
            }}
          >
            Email
          </label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            value={formData.email} 
            onChange={handleChange} 
            required 
            style={{ 
              width: '100%', 
              padding: '10px', 
              borderRadius: '5px', 
              border: '1px solid #ddd' 
            }} 
          />
        </div>
        
        <div style={{ marginBottom: '20px' }}>
          <label 
            htmlFor="pesan" 
            style={{ 
              display: 'block', 
              marginBottom: '5px', 
              fontSize: '1rem', 
              fontWeight: 'bold' 
            }}
          >
            Pesan
          </label>
          <textarea 
            id="pesan" 
            name="pesan" 
            value={formData.pesan} 
            onChange={handleChange} 
            required 
            rows="5" 
            style={{ 
              width: '100%', 
              padding: '10px', 
              borderRadius: '5px', 
              border: '1px solid #ddd' 
            }}
          ></textarea>
        </div>
        
        <button 
          type="submit" 
          style={{ 
            padding: '10px 20px', 
            backgroundColor: '#0d6efd', 
            color: 'white', 
            border: 'none', 
            borderRadius: '5px',
            cursor: 'pointer',
            fontSize: '1rem'
          }}
        >
          Kirim Pesan
        </button>
      </form>
    </div>
  );
}

export default Kontak;