import React, { useState } from 'react';

function Pelanggan() {
  // State untuk data pelanggan
  const [dataPelanggan, setDataPelanggan] = useState([
    { id: 1, nama: 'Budi Santoso', alamat: 'Jl. Merdeka No. 10, Jakarta Pusat, DKI Jakarta', telepon: '081234567890' },
    { id: 2, nama: 'Siti Aminah', alamat: 'Jl. Pahlawan No. 5, Surabaya, Jawa Timur', telepon: '081345678901' },
    { id: 3, nama: 'Agus Wijaya', alamat: 'Jl. Sudirman Kav. 25, Bandung, Jawa Barat', telepon: '081567890123' },
    { id: 4, nama: 'Dewi Lestari', alamat: 'Jl. Gajah Mada No. 101, Semarang, Jawa Tengah', telepon: '081789012345' },
    { id: 5, nama: 'Rahmat Hidayat', alamat: 'Jl. Diponegoro No. 77, Medan, Sumatera Utara', telepon: '081901234567' },
  ]);

  // Fungsi untuk menambah pelanggan baru (contoh, bisa dikembangkan)
  const handleTambahPelanggan = () => {
    const idBaru = dataPelanggan.length > 0 ? Math.max(...dataPelanggan.map(p => p.id)) + 1 : 1;
    const pelangganBaru = {
      id: idBaru,
      nama: `Pelanggan Baru ${idBaru}`,
      alamat: 'Alamat Default',
      telepon: '08xxxxxxxxxx'
    };
    // setDataPelanggan([...dataPelanggan, pelangganBaru]);
    alert('Fungsi tambah pelanggan belum diimplementasikan sepenuhnya. Ini hanya contoh.');
  };

  // Fungsi untuk menghapus pelanggan
  const handleHapusPelanggan = (id) => {
    if (window.confirm(`Apakah Anda yakin ingin menghapus pelanggan dengan ID ${id}?`)) {
      setDataPelanggan(dataPelanggan.filter(p => p.id !== id));
      alert(`Pelanggan dengan ID ${id} berhasil dihapus.`);
    }
  };


  return (
    <div>
      <h1>Data Pelanggan</h1>
      <p>Ini adalah halaman untuk mengelola data pelanggan.</p>

      {/* Anda bisa menambahkan form input dan tombol submit di sini jika diperlukan */}
      {/* 
      <button onClick={handleTambahPelanggan} style={{ marginBottom: '20px', padding: '10px 15px', backgroundColor: '#28a745', color: 'white', border: 'none', borderRadius: '4px', cursor: 'pointer' }}>
        Tambah Pelanggan
      </button>
      */}

      <table className="kategori-table"> {/* Menggunakan class styling yang sama jika ada */}
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Pelanggan</th>
            <th>Alamat</th>
            <th>Telepon</th>
            <th>Aksi</th> {/* Kolom aksi diaktifkan */}
          </tr>
        </thead>
        <tbody>
          {dataPelanggan.length > 0 ? (
            dataPelanggan.map((pelanggan, index) => (
              <tr key={pelanggan.id}>
                <td>{index + 1}</td>
                <td>{pelanggan.nama}</td>
                <td>{pelanggan.alamat}</td>
                <td>{pelanggan.telepon}</td>
                <td>
                  {/* Tombol Edit bisa ditambahkan di sini jika diperlukan nanti
                  <button 
                    onClick={() => alert(`Edit pelanggan ID: ${pelanggan.id}`)} 
                    style={{ marginRight: '5px', padding: '5px 10px', backgroundColor: '#ffc107', color: 'black', border: 'none', borderRadius: '4px', cursor: 'pointer' }}
                  >
                    Edit
                  </button>
                  */}
                  <button 
                    onClick={() => handleHapusPelanggan(pelanggan.id)} 
                    style={{ padding: '5px 10px', backgroundColor: '#dc3545', color: 'white', border: 'none', borderRadius: '4px', cursor: 'pointer' }}
                  >
                    Hapus
                  </button>
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="5" style={{ textAlign: 'center', padding: '10px' }}>Belum ada data pelanggan.</td> {/* colSpan disesuaikan menjadi 5 */}
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
}

export default Pelanggan;