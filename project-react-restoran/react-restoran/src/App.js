import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import './App.css';
import Nav from './components/Nav';
import Kategori from './pages/Kategori';
import Menu from './pages/Menu';
import Pelanggan from './pages/Pelanggan';
import Order from './pages/Order';
import OrderDetail from './pages/OrderDetail';
import Admin from './pages/Admin';

function App() {
  return (
    <Router>
      <div className="app-container">
        <Nav />
        <div className="content">
          <Routes>
            <Route path="/" element={<Navigate to="/kategori" replace />} /> {/* Redirect root ke Kategori */}
            <Route path="/kategori" element={<Kategori />} />
            <Route path="/menu" element={<Menu />} />
            <Route path="/pelanggan" element={<Pelanggan />} />
            <Route path="/order" element={<Order />} />
            <Route path="/order-detail" element={<OrderDetail />} />
            <Route path="/admin" element={<Admin />} />
            {/* Anda bisa menambahkan rute lain di sini, misalnya untuk halaman 404 */}
            {/* <Route path="*" element={<div>Halaman Tidak Ditemukan</div>} /> */}
          </Routes>
        </div>
      </div>
    </Router>
  );
}

export default App;
