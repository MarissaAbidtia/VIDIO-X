import React, { useState } from 'react';

function Kategori() {
  // State untuk input form
  const [namaKategoriInput, setNamaKategoriInput] = useState('');
  const [keteranganKategoriInput, setKeteranganKategoriInput] = useState('');

  // Data awal untuk tabel kategori sesuai permintaan Anda
  const [dataKategori, setDataKategori] = useState([
    { id: 1, kategori: 'Makanan Ringan', keterangan: 'Makanan Ringan' },
    { id: 2, kategori: 'Minuman', keterangan: 'Minuman' },
    { id: 3, kategori: 'Gorengan', keterangan: 'Gorengan' },
    { id: 4, kategori: 'Snack', keterangan: 'Snack' },
  ]);

  const handleSubmit = (event) => {
    event.preventDefault();
    if (namaKategoriInput.trim() === '' || keteranganKategoriInput.trim() === '') {
      alert('Nama Kategori dan Keterangan harus diisi!');
      return;
    }
    const newItem = {
      id: dataKategori.length > 0 ? Math.max(...dataKategori.map(item => item.id)) + 1 : 1, // simple ID generation
      kategori: namaKategoriInput,
      keterangan: keteranganKategoriInput,
    };
    setDataKategori([...dataKategori, newItem]);
    console.log('Data kategori baru ditambahkan:', newItem);
    alert(`Kategori "${namaKategoriInput}" berhasil ditambahkan!`);
    setNamaKategoriInput(''); 
    setKeteranganKategoriInput('');
  };

  const handleHapus = (id) => {
    // Konfirmasi sebelum menghapus
    if (window.confirm(`Apakah Anda yakin ingin menghapus kategori dengan ID ${id}?`)) {
      setDataKategori(dataKategori.filter(item => item.id !== id));
      console.log('Hapus item kategori dengan ID:', id);
      alert(`Item kategori dengan ID ${id} dihapus!`);
    }
  };

  const handleUbah = (id) => {
    // Logika untuk mengubah item
    // Ini bisa melibatkan mengisi form dengan data yang ada untuk diedit
    console.log('Ubah item kategori dengan ID:', id);
    const itemToEdit = dataKategori.find(item => item.id === id);
    if (itemToEdit) {
      setNamaKategoriInput(itemToEdit.kategori);
      setKeteranganKategoriInput(itemToEdit.keterangan);
      // Anda mungkin perlu state tambahan untuk menandai mode edit dan ID item yang diedit
      // Untuk saat ini, kita akan menghapus item lama dan pengguna bisa submit sebagai item baru
      // atau Anda bisa implementasikan logika update yang lebih canggih.
      alert(`Data untuk ID ${id} telah dimuat ke form. Edit lalu submit lagi atau implementasikan fungsi update.`);
    } else {
      alert(`Fitur ubah untuk kategori ID ${id} belum diimplementasikan sepenuhnya atau item tidak ditemukan.`);
    }
  };

  return (
    <div>
      <h1>Kelola Kategori</h1>
      <p>Ini adalah halaman untuk menambah, melihat, mengubah, dan menghapus data kategori.</p>

      <form onSubmit={handleSubmit} style={{ marginBottom: '20px', padding: '15px', border: '1px solid #eee', borderRadius: '5px', backgroundColor: '#f9f9f9' }}>
        <h3 style={{marginTop: 0}}>Tambah/Ubah Kategori</h3>
        <div style={{ marginBottom: '10px' }}>
          <label htmlFor="namaKategoriInput" style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>
            Kategori:
          </label>
          <input
            type="text"
            id="namaKategoriInput"
            value={namaKategoriInput}
            onChange={(e) => setNamaKategoriInput(e.target.value)}
            placeholder="Masukkan nama kategori (e.g., Makanan Ringan)"
            style={{ width: 'calc(100% - 22px)', padding: '10px', border: '1px solid #ccc', borderRadius: '4px' }}
            required
          />
        </div>
        <div style={{ marginBottom: '15px' }}>
          <label htmlFor="keteranganKategoriInput" style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>
            Keterangan:
          </label>
          <input
            type="text"
            id="keteranganKategoriInput"
            value={keteranganKategoriInput}
            onChange={(e) => setKeteranganKategoriInput(e.target.value)}
            placeholder="Masukkan keterangan (e.g., Makanan Ringan)"
            style={{ width: 'calc(100% - 22px)', padding: '10px', border: '1px solid #ccc', borderRadius: '4px' }}
            required
          />
        </div>
        <button type="submit" style={{ padding: '10px 20px', backgroundColor: '#28a745', color: 'white', border: 'none', borderRadius: '4px', cursor: 'pointer', fontSize: '16px' }}>
          Submit Kategori
        </button>
      </form>

      <h2>Daftar Kategori</h2>
      <table className="kategori-table"> {/* Pastikan class ini ada di App.css */}
        <thead>
          <tr>
            <th>No</th>
            <th>Kategori</th>
            <th>Keterangan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {dataKategori.length > 0 ? (
            dataKategori.map((item, index) => (
              <tr key={item.id}>
                <td>{index + 1}</td>
                <td>{item.kategori}</td>
                <td>{item.keterangan}</td>
                <td>
                  <button 
                    onClick={() => handleUbah(item.id)} 
                    style={{ marginRight: '8px', padding: '6px 12px', backgroundColor: '#ffc107', color: 'black', border: 'none', borderRadius: '4px', cursor: 'pointer' }}
                  >
                    Ubah
                  </button>
                  <button 
                    onClick={() => handleHapus(item.id)} 
                    style={{ padding: '6px 12px', backgroundColor: '#dc3545', color: 'white', border: 'none', borderRadius: '4px', cursor: 'pointer' }}
                  >
                    Hapus
                  </button>
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="4" style={{ textAlign: 'center', padding: '10px' }}>Tidak ada data kategori. Silakan tambahkan melalui form di atas.</td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
}

export default Kategori;