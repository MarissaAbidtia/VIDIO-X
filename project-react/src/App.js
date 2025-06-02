import React from 'react';
import { BrowserRouter as Router, Routes, Route, Link, useNavigate } from 'react-router-dom';
import './App.css';
import Home from './pages/Home';
import Sejarah from './pages/Sejarah';
import Tentang from './pages/Tentang';
import Kontak from './pages/Kontak';
import Siswa from './pages/Siswa';
import Menu from './pages/Menu';

function Navbar() {
  const navigate = useNavigate();
  return (
    <nav style={{
      width: '200px',
      background: '#7fffd4',
      padding: '20px',
      height: '100vh',
      position: 'fixed',
      left: 0,
      top: 0,
      display: 'flex',
      flexDirection: 'column',
      alignItems: 'flex-start'
    }}>
      <ul style={{
        listStyle: 'none',
        margin: 0,
        padding: 0,
        width: '100%',
        display: 'flex',
        flexDirection: 'column',
        gap: '15px',
        fontSize: '18px'
      }}>
        <li><Link to="/" style={{ textDecoration: 'none', color: '#0000ff', fontWeight: 'bold' }}>Home</Link></li>
        <li><Link to="/sejarah" style={{ textDecoration: 'none', color: '#0000ff', fontWeight: 'bold' }}>Sejarah</Link></li>
        <li><Link to="/tentang" style={{ textDecoration: 'none', color: '#0000ff', fontWeight: 'bold' }}>Tentang</Link></li>
        <li><Link to="/kontak" style={{ textDecoration: 'none', color: '#0000ff', fontWeight: 'bold' }}>Kontak</Link></li>
        <li><Link to="/siswa" style={{ textDecoration: 'none', color: '#0000ff', fontWeight: 'bold' }}>Siswa</Link></li>
        <li><Link to="/menu" style={{ textDecoration: 'none', color: '#0000ff', fontWeight: 'bold' }}>Menu</Link></li>
      </ul>
    </nav>
  );
}

function App() {
  return (
    <Router>
      <div className="App" style={{ minHeight: '100vh', display: 'flex', background: '#7fffd4' }}>
        <Navbar />
        <main style={{ 
          flex: 1, 
          marginLeft: '200px', 
          padding: '40px', 
          display: 'flex', 
          flexDirection: 'column', 
          alignItems: 'flex-start',
          justifyContent: 'flex-start'
        }}>
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/sejarah" element={<Sejarah />} />
            <Route path="/tentang" element={<Tentang />} />
            <Route path="/kontak" element={<Kontak />} />
            <Route path="/siswa" element={<Siswa />} />
            <Route path="/menu" element={<Menu />} />
          </Routes>
        </main>
        <footer style={{
          width: '100%',
          background: '#7fffd4',
          borderTop: '1px solid #eee',
          padding: '16px 0',
          textAlign: 'center',
          fontSize: '16px',
          position: 'fixed',
          bottom: 0,
          left: 0
        }}>
          © 2024 Website React Saya. Hak Cipta Dilindungi.
        </footer>
      </div>
    </Router>
  );
}

export default App;
