import React, { useState } from 'react';

function Menu() {
  // State untuk data menu
  const [dataMenuItems, setDataMenuItems] = useState([
    { id: 1, kategori: 'Makanan Utama', namaMenu: 'Bakso', gambar: 'https://via.placeholder.com/100x75/E8117F/FFFFFF?text=Bakso', harga: 15000 },
    { id: 2, kategori: 'Minuman', namaMenu: 'Es Jeruk', gambar: 'https://via.placeholder.com/100x75/FF8C00/FFFFFF?text=Es+Jeruk', harga: 5000 },
    { id: 3, kategori: 'Minuman', namaMenu: 'Es Teh', gambar: 'https://via.placeholder.com/100x75/A52A2A/FFFFFF?text=Es+Teh', harga: 3000 },
    { id: 4, kategori: 'Makanan Utama', namaMenu: 'Nasi Goreng', gambar: 'https://via.placeholder.com/100x75/008000/FFFFFF?text=Nasi+Goreng', harga: 12000 },
  ]);

  // State untuk form input
  const [kategoriInput, setKategoriInput] = useState('');
  const [namaMenuInput, setNamaMenuInput] = useState('');
  const [gambarInput, setGambarInput] = useState(''); // Tetap sebagai URL untuk saat ini
  const [hargaInput, setHargaInput] = useState('');

  // State untuk mode edit
  const [isEditing, setIsEditing] = useState(false);
  const [editingId, setEditingId] = useState(null);

  // Daftar kategori (bisa juga diambil dari state/API lain)
  const daftarKategori = ['Makanan Utama', 'Minuman', 'Cemilan', 'Dessert', 'Makanan Ringan']; // Menambahkan 'Makanan Ringan'


  const resetForm = () => {
    setKategoriInput('');
    setNamaMenuInput('');
    setGambarInput('');
    setHargaInput('');
    setIsEditing(false);
    setEditingId(null);
  };

  const handleSubmit = (event) => {
    event.preventDefault();
    if (!kategoriInput || !namaMenuInput || !gambarInput || !hargaInput) {
      alert('Semua field harus diisi!');
      return;
    }

    const harga = parseInt(hargaInput, 10);
    if (isNaN(harga) || harga <= 0) {
        alert('Harga harus berupa angka positif!');
        return;
    }

    if (isEditing) {
      // Logika Update
      setDataMenuItems(
        dataMenuItems.map((item) =>
          item.id === editingId
            ? { ...item, kategori: kategoriInput, namaMenu: namaMenuInput, gambar: gambarInput, harga: harga }
            : item
        )
      );
      alert('Menu berhasil diperbarui!');
    } else {
      // Logika Tambah Baru
      const newItem = {
        id: dataMenuItems.length > 0 ? Math.max(...dataMenuItems.map(item => item.id)) + 1 : 1,
        kategori: kategoriInput,
        namaMenu: namaMenuInput,
        gambar: gambarInput,
        harga: harga,
      };
      setDataMenuItems([...dataMenuItems, newItem]);
      alert('Menu baru berhasil ditambahkan!');
    }
    resetForm();
  };

  const handleEdit = (item) => {
    setIsEditing(true);
    setEditingId(item.id);
    setKategoriInput(item.kategori);
    setNamaMenuInput(item.namaMenu);
    setGambarInput(item.gambar);
    setHargaInput(item.harga.toString());
  };

  const handleDelete = (id) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus menu ini?')) {
      setDataMenuItems(dataMenuItems.filter((item) => item.id !== id));
      alert('Menu berhasil dihapus!');
      if (id === editingId) { // Jika item yang dihapus sedang diedit, reset form
        resetForm();
      }
    }
  };

  return (
    <div>
      <h1>Manajemen Data Menu</h1>
      <p>Kelola daftar menu restoran Anda di sini.</p>

      <form onSubmit={handleSubmit} style={{ marginBottom: '30px', padding: '20px', border: '1px solid #ddd', borderRadius: '8px', backgroundColor: '#f9f9f9' }}>
        <h3 style={{ marginTop: 0, marginBottom: '15px' }}>{isEditing ? 'Edit Menu' : 'Tambah Menu Baru'}</h3>
        
        <div style={{ marginBottom: '10px' }}>
          <label htmlFor="kategoriMenu" style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Kategori:</label>
          <select 
            id="kategoriMenu" 
            value={kategoriInput} 
            onChange={(e) => setKategoriInput(e.target.value)}
            style={{ width: '100%', padding: '10px', border: '1px solid #ccc', borderRadius: '4px' }}
            required
          >
            <option value="">Pilih Kategori</option>
            {daftarKategori.map(kat => <option key={kat} value={kat}>{kat}</option>)}
          </select>
        </div>

        <div style={{ marginBottom: '10px' }}>
          <label htmlFor="namaMenu" style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Menu:</label>
          <input
            type="text"
            id="namaMenu"
            value={namaMenuInput}
            onChange={(e) => setNamaMenuInput(e.target.value)}
            placeholder="Contoh: Bakso Keju"
            style={{ width: 'calc(100% - 22px)', padding: '10px', border: '1px solid #ccc', borderRadius: '4px' }}
            required
          />
        </div>

        <div style={{ marginBottom: '10px' }}>
          <label htmlFor="hargaMenu" style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Harga (Rp):</label>
          <input
            type="number"
            id="hargaMenu"
            value={hargaInput}
            onChange={(e) => setHargaInput(e.target.value)}
            placeholder="Contoh: 2000"
            style={{ width: 'calc(100% - 22px)', padding: '10px', border: '1px solid #ccc', borderRadius: '4px' }}
            required
            min="0"
          />
        </div>

        <div style={{ marginBottom: '15px', display: 'flex', alignItems: 'flex-start' }}>
          <div style={{ flex: 1, marginRight: '15px' }}>
            <label htmlFor="gambarMenu" style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Gambar (URL):</label>
            <input
              type="text" 
              id="gambarMenu"
              value={gambarInput}
              onChange={(e) => setGambarInput(e.target.value)}
              placeholder="Masukkan URL gambar..."
              style={{ width: '100%', padding: '10px', border: '1px solid #ccc', borderRadius: '4px' }}
              required
            />
          </div>
          {gambarInput && (
            <div style={{ border: '1px solid #ddd', padding: '5px', borderRadius: '4px', backgroundColor: '#fff' }}>
              <img 
                src={gambarInput} 
                alt="Pratinjau" 
                style={{ width: '100px', height: '75px', objectFit: 'cover', display: 'block' }} 
                onError={(e) => { 
                  e.target.alt = 'Gagal memuat gambar'; 
                  // Anda bisa juga mengganti src ke gambar placeholder jika URL tidak valid
                  // e.target.src = 'https://via.placeholder.com/100x75?text=Error';
                }}
              />
            </div>
          )}
        </div>
        {/* Jika Anda ingin input file sebenarnya, ini memerlukan penanganan yang lebih kompleks:
        <input type="file" id="gambarMenuFile" onChange={handleFileChange} /> 
        */}


        <button type="submit" style={{ padding: '10px 20px', backgroundColor: '#007bff', color: 'white', border: 'none', borderRadius: '4px', cursor: 'pointer', fontSize: '16px', marginRight: '10px' }}>
          {isEditing ? 'Update Menu' : 'Submit'}
        </button>
        {isEditing && (
          <button type="button" onClick={resetForm} style={{ padding: '10px 20px', backgroundColor: '#6c757d', color: 'white', border: 'none', borderRadius: '4px', cursor: 'pointer', fontSize: '16px' }}>
            Batal Edit
          </button>
        )}
      </form>

      <h2>Daftar Menu Saat Ini</h2>
      <table className="kategori-table"> {/* Anda bisa menggunakan styling tabel yang sudah ada */}
        <thead>
          <tr>
            <th>No</th>
            <th>Kategori</th>
            <th>Nama Menu</th>
            <th>Gambar</th>
            <th>Harga (Rp)</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {dataMenuItems.length > 0 ? (
            dataMenuItems.map((item, index) => (
              <tr key={item.id}>
                <td>{index + 1}</td>
                <td>{item.kategori}</td>
                <td>{item.namaMenu}</td>
                <td>
                  <img src={item.gambar} alt={item.namaMenu} style={{ width: '100px', height: '75px', objectFit: 'cover', borderRadius: '4px' }} />
                </td>
                <td>{item.harga.toLocaleString('id-ID')}</td>
                <td>
                  <button 
                    onClick={() => handleEdit(item)} 
                    style={{ marginRight: '8px', padding: '6px 12px', backgroundColor: '#ffc107', color: 'black', border: 'none', borderRadius: '4px', cursor: 'pointer' }}
                  >
                    Edit
                  </button>
                  <button 
                    onClick={() => handleDelete(item.id)} 
                    style={{ padding: '6px 12px', backgroundColor: '#dc3545', color: 'white', border: 'none', borderRadius: '4px', cursor: 'pointer' }}
                  >
                    Hapus
                  </button>
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="6" style={{ textAlign: 'center', padding: '20px' }}>Belum ada data menu. Silakan tambahkan melalui form di atas.</td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
}

export default Menu;