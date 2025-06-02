import React from 'react';
import { NavLink } from 'react-router-dom';

function Nav() {
  return (
    <div className="sidebar">
      <h2>Nav</h2>
      <h3>Menu Aplikasi</h3>
      <ul>
        <li><NavLink to="/kategori" className={({ isActive }) => isActive ? "active" : ""}>Kategori</NavLink></li>
        <li><NavLink to="/menu" className={({ isActive }) => isActive ? "active" : ""}>Menu</NavLink></li>
        <li><NavLink to="/pelanggan" className={({ isActive }) => isActive ? "active" : ""}>Pelanggan</NavLink></li>
        <li><NavLink to="/order" className={({ isActive }) => isActive ? "active" : ""}>Order</NavLink></li>
        <li><NavLink to="/order-detail" className={({ isActive }) => isActive ? "active" : ""}>Order Detail</NavLink></li>
        <li><NavLink to="/admin" className={({ isActive }) => isActive ? "active" : ""}>Admin</NavLink></li>
      </ul>
    </div>
  );
}

export default Nav;