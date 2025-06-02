import React, { useState } from 'react';

function Admin() {
  const initialUsers = [
    { id: 1, username: 'chef_anton', level: 'Koki', status: 'Aktif' },
    { id: 2, username: 'cashier_bella', level: 'Kasir', status: 'Aktif' },
    { id: 3, username: 'admin_utama', level: 'Admin', status: 'Aktif' },
    { id: 4, username: 'chef_dina', level: 'Koki', status: 'Banned' },
  ];

  const [users, setUsers] = useState(initialUsers);
  const [newUsername, setNewUsername] = useState('');
  const [newLevel, setNewLevel] = useState('Kasir'); // Default level
  const [newStatus, setNewStatus] = useState('Aktif'); // Default status

  const levelOptions = ['Koki', 'Kasir', 'Admin'];
  const statusOptions = ['Aktif', 'Banned'];

  const handleAddUser = (e) => {
    e.preventDefault();
    if (!newUsername) {
      alert('Username tidak boleh kosong!');
      return;
    }
    const newUser = {
      id: users.length > 0 ? Math.max(...users.map(u => u.id)) + 1 : 1,
      username: newUsername,
      level: newLevel,
      status: newStatus,
    };
    setUsers([...users, newUser]);
    setNewUsername('');
    setNewLevel('Kasir');
    setNewStatus('Aktif');
    alert('Pengguna baru berhasil ditambahkan!');
  };

  const handleDeleteUser = (userId) => {
    if (window.confirm(`Apakah Anda yakin ingin menghapus pengguna dengan ID ${userId}?`)) {
      setUsers(users.filter(user => user.id !== userId));
      alert(`Pengguna dengan ID ${userId} berhasil dihapus.`);
    }
  };

  const handleUserUpdate = (userId, field, value) => {
    setUsers(users.map(user => 
      user.id === userId ? { ...user, [field]: value } : user
    ));
  };

  const handleToggleStatus = (userId) => {
    const userToToggle = users.find(user => user.id === userId);
    if (userToToggle) {
      const newStatus = userToToggle.status === 'Aktif' ? 'Banned' : 'Aktif';
      setUsers(users.map(user =>
        user.id === userId ? { ...user, status: newStatus } : user
      ));
      alert(`Status pengguna ${userToToggle.username} telah diubah menjadi ${newStatus}.`);
    }
  };

  return (
    <div>
      <h1>Manajemen Pengguna</h1>
      <p>Halaman ini untuk mengelola data pengguna sistem.</p>

      {/* Form Tambah Pengguna Baru */}
      <div style={{ marginBottom: '30px', padding: '20px', border: '1px solid #ddd', borderRadius: '8px', backgroundColor: '#f9f9f9' }}>
        <h3 style={{ marginTop: 0, marginBottom: '15px' }}>Tambah Pengguna Baru</h3>
        <form onSubmit={handleAddUser}>
          <div style={{ display: 'flex', gap: '15px', marginBottom: '15px', alignItems: 'flex-end' }}>
            <div style={{ flex: 2 }}>
              <label htmlFor="username" style={{ display: 'block', marginBottom: '5px' }}>Username:</label>
              <input
                type="text"
                id="username"
                value={newUsername}
                onChange={(e) => setNewUsername(e.target.value)}
                style={{ width: 'calc(100% - 12px)', padding: '8px', border: '1px solid #ccc', borderRadius: '4px' }}
                required
              />
            </div>
            <div style={{ flex: 1 }}>
              <label htmlFor="level" style={{ display: 'block', marginBottom: '5px' }}>Level:</label>
              <select
                id="level"
                value={newLevel}
                onChange={(e) => setNewLevel(e.target.value)}
                style={{ width: '100%', padding: '8px', border: '1px solid #ccc', borderRadius: '4px', height: '38px' }}
              >
                {levelOptions.map(level => <option key={level} value={level}>{level}</option>)}
              </select>
            </div>
            <div style={{ flex: 1 }}>
              <label htmlFor="status" style={{ display: 'block', marginBottom: '5px' }}>Status:</label>
              <select
                id="status"
                value={newStatus}
                onChange={(e) => setNewStatus(e.target.value)}
                style={{ width: '100%', padding: '8px', border: '1px solid #ccc', borderRadius: '4px', height: '38px' }}
              >
                {statusOptions.map(status => <option key={status} value={status}>{status}</option>)}
              </select>
            </div>
            <button type="submit" style={{ padding: '8px 20px', backgroundColor: '#007bff', color: 'white', border: 'none', borderRadius: '5px', cursor: 'pointer', height: '38px' }}>
              Tambah User
            </button>
          </div>
        </form>
      </div>

      {/* Tabel Daftar Pengguna */}
      <table className="kategori-table"> {/* Anda bisa menggunakan class styling yang sama */}
        <thead>
          <tr>
            <th>No</th>
            <th>User</th>
            <th>Level</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {users.length > 0 ? (
            users.map((user, index) => (
              <tr key={user.id}>
                <td>{index + 1}</td>
                <td>
                  <input 
                    type="text" 
                    value={user.username} 
                    onChange={(e) => handleUserUpdate(user.id, 'username', e.target.value)}
                    style={{ width: 'calc(100% - 10px)', padding: '5px', border: '1px solid #eee', borderRadius: '3px' }}
                  />
                </td>
                <td>
                  <select 
                    value={user.level} 
                    onChange={(e) => handleUserUpdate(user.id, 'level', e.target.value)}
                    style={{ width: '100%', padding: '5px', border: '1px solid #eee', borderRadius: '3px' }}
                  >
                    {levelOptions.map(level => <option key={level} value={level}>{level}</option>)}
                  </select>
                </td>
                <td>
                  {/* Dropdown status masih ada, bisa dipertimbangkan untuk dihapus jika tombol toggle lebih disukai */}
                  <select 
                    value={user.status} 
                    onChange={(e) => handleUserUpdate(user.id, 'status', e.target.value)}
                    style={{ width: '100%', padding: '5px', border: '1px solid #eee', borderRadius: '3px' }}
                  >
                    {statusOptions.map(status => <option key={status} value={status}>{status}</option>)}
                  </select>
                </td>
                <td style={{ display: 'flex', gap: '5px', alignItems: 'center' }}>
                  <button 
                    onClick={() => handleToggleStatus(user.id)}
                    style={{ 
                      padding: '5px 10px', 
                      backgroundColor: user.status === 'Aktif' ? '#ffc107' : '#28a745', 
                      color: user.status === 'Aktif' ? 'black' : 'white', 
                      border: 'none', 
                      borderRadius: '4px', 
                      cursor: 'pointer', 
                      fontSize: '12px' 
                    }}
                  >
                    {user.status === 'Aktif' ? 'Ban' : 'Aktifkan'}
                  </button>
                  <button 
                    onClick={() => handleDeleteUser(user.id)}
                    style={{ padding: '5px 10px', backgroundColor: '#dc3545', color: 'white', border: 'none', borderRadius: '4px', cursor: 'pointer', fontSize: '12px' }}
                  >
                    Hapus
                  </button>
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="5" style={{ textAlign: 'center', padding: '20px' }}>
                Belum ada data pengguna.
              </td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
}

export default Admin;