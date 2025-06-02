import React, { useState } from 'react';
// Jika Anda menggunakan react-router-dom untuk navigasi setelah login:
// import { useNavigate } from 'react-router-dom';

// Daftar pengguna contoh dengan peran dan kata sandi
const sampleUsers = [
  { email: 'admin@example.com', password: 'adminpassword', role: 'Admin' },
  { email: 'kasir@example.com', password: 'kasirpassword', role: 'Kasir' },
  { email: 'koki@example.com', password: 'kokipassword', role: 'Koki' },
  { email: 'pelanggan@example.com', password: 'pelangganpassword', role: 'Pelanggan' },
  // Tambahkan pengguna lain sesuai kebutuhan
];

function Login() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loggedInUser, setLoggedInUser] = useState(null); // Untuk menyimpan info user yang login
  // const navigate = useNavigate(); // Untuk navigasi setelah login berhasil

  const handleSubmit = (event) => {
    event.preventDefault();
    
    const user = sampleUsers.find(
      (u) => u.email === email && u.password === password
    );

    if (user) {
      setLoggedInUser(user); // Simpan informasi pengguna yang berhasil login
      alert(`Login berhasil sebagai ${user.role}: ${user.email}`);
      
      // Simulasi akses berdasarkan peran
      let accessiblePages = '';
      if (user.role === 'Admin') {
        accessiblePages = 'Semua halaman (Menu, Order, Order Detail, Manajemen User, dll).';
      } else if (user.role === 'Kasir' || user.role === 'Pelanggan') {
        accessiblePages = 'Menu, Order, Order Detail.';
      } else if (user.role === 'Koki') {
        accessiblePages = 'Daftar Pesanan (untuk melihat apa yang diorder).';
      }
      alert(`Anda dapat mengakses: ${accessiblePages}`);
      
      // Contoh navigasi setelah login "berhasil" (placeholder)
      // if (user.role === 'Admin') navigate('/admin-dashboard');
      // else if (user.role === 'Kasir') navigate('/kasir-dashboard');
      // else if (user.role === 'Koki') navigate('/koki-dashboard');
      // else if (user.role === 'Pelanggan') navigate('/menu');
      // else navigate('/dashboard'); 
    } else {
      alert('Email atau Password salah!');
      setLoggedInUser(null);
    }
  };

  const handleLogout = () => {
    alert(`Pengguna ${loggedInUser?.email} telah logout.`);
    setLoggedInUser(null); // Hapus informasi pengguna yang login
    setEmail(''); 
    setPassword(''); 
    // Contoh: navigate('/login'); 
  };

  // Jika pengguna sudah login, tampilkan pesan selamat datang dan tombol logout
  if (loggedInUser) {
    return (
      <div style={{ display: 'flex', flexDirection: 'column', justifyContent: 'center', alignItems: 'center', minHeight: '80vh', backgroundColor: '#f0f2f5', padding: '20px' }}>
        <div style={{ padding: '40px', borderRadius: '8px', boxShadow: '0 4px 12px rgba(0,0,0,0.1)', backgroundColor: 'white', width: '100%', maxWidth: '400px', textAlign: 'center' }}>
          <h1 style={{ marginBottom: '20px', color: '#333' }}>Selamat Datang, {loggedInUser.role}!</h1>
          <p style={{ marginBottom: '10px', color: '#555' }}>Anda login sebagai: {loggedInUser.email}</p>
          <p style={{ marginBottom: '30px', color: '#555', fontSize: '0.9em' }}>
            {loggedInUser.role === 'Admin' && 'Anda memiliki akses ke semua fitur.'}
            {(loggedInUser.role === 'Kasir' || loggedInUser.role === 'Pelanggan') && 'Anda dapat mengakses Menu, Order, dan Order Detail.'}
            {loggedInUser.role === 'Koki' && 'Anda dapat melihat pesanan yang masuk.'}
          </p>
          <button
            onClick={handleLogout}
            style={{
              width: '100%',
              padding: '12px',
              backgroundColor: '#dc3545',
              color: 'white',
              border: 'none',
              borderRadius: '4px',
              cursor: 'pointer',
              fontSize: '16px',
              fontWeight: 'bold',
            }}
            onMouseOver={(e) => e.currentTarget.style.backgroundColor = '#c82333'}
            onMouseOut={(e) => e.currentTarget.style.backgroundColor = '#dc3545'}
          >
            Logout
          </button>
        </div>
      </div>
    );
  }

  // Jika belum login, tampilkan form login
  return (
    <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '80vh', backgroundColor: '#f0f2f5' }}>
      <div style={{ padding: '40px', borderRadius: '8px', boxShadow: '0 4px 12px rgba(0,0,0,0.1)', backgroundColor: 'white', width: '100%', maxWidth: '400px' }}>
        <h1 style={{ textAlign: 'center', marginBottom: '30px', color: '#333' }}>Login Pengguna</h1>
        <form onSubmit={handleSubmit}>
          <div style={{ marginBottom: '20px' }}>
            <label htmlFor="email" style={{ display: 'block', marginBottom: '8px', fontWeight: 'bold', color: '#555' }}>Email:</label>
            <input
              type="email"
              id="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              style={{ width: 'calc(100% - 24px)', padding: '12px', border: '1px solid #ccc', borderRadius: '4px', fontSize: '16px' }}
              placeholder="Masukkan email Anda"
              required
            />
          </div>
          <div style={{ marginBottom: '30px' }}>
            <label htmlFor="password" style={{ display: 'block', marginBottom: '8px', fontWeight: 'bold', color: '#555' }}>Password:</label>
            <input
              type="password"
              id="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              style={{ width: 'calc(100% - 24px)', padding: '12px', border: '1px solid #ccc', borderRadius: '4px', fontSize: '16px' }}
              placeholder="Masukkan password Anda"
              required
            />
          </div>
          <button
            type="submit"
            style={{
              width: '100%',
              padding: '12px',
              backgroundColor: '#007bff',
              color: 'white',
              border: 'none',
              borderRadius: '4px',
              cursor: 'pointer',
              fontSize: '16px',
              fontWeight: 'bold',
              transition: 'background-color 0.2s'
            }}
            onMouseOver={(e) => e.currentTarget.style.backgroundColor = '#0056b3'}
            onMouseOut={(e) => e.currentTarget.style.backgroundColor = '#007bff'}
          >
            Login
          </button>
        </form>
      </div>
    </div>
  );
}

export default Login;