import React, { useState, useEffect } from 'react';

function OrderDetail() {
  // Data contoh untuk beberapa pesanan yang sudah dibayar (dan satu yang belum)
  const samplePaidOrdersData = [
    {
      faktur: 'INV-20231115-001',
      tglOrder: '2023-11-15',
      pelanggan: 'Budi Santoso',
      items: [
        { id: 'i1', menu: 'Nasi Goreng Spesial', harga: 25000, jumlah: 2, total: 50000 },
        { id: 'i2', menu: 'Es Teh Manis', harga: 5000, jumlah: 2, total: 10000 },
      ],
      grandTotal: 60000,
      statusPembayaran: 'Lunas'
    },
    {
      faktur: 'INV-20231116-002',
      tglOrder: '2023-11-16',
      pelanggan: 'Siti Aminah',
      items: [
        { id: 'i3', menu: 'Ayam Bakar Madu', harga: 35000, jumlah: 1, total: 35000 },
      ],
      grandTotal: 35000,
      statusPembayaran: 'Lunas'
    },
    {
      faktur: 'INV-20231110-003', // Tanggal lebih awal
      tglOrder: '2023-11-10',
      pelanggan: 'Agus Wijaya',
      items: [
        { id: 'i4', menu: 'Mie Ayam', harga: 15000, jumlah: 1, total: 15000 },
      ],
      grandTotal: 15000,
      statusPembayaran: 'Lunas'
    },
    {
      faktur: 'INV-20231117-004', // Pesanan belum lunas
      tglOrder: '2023-11-17',
      pelanggan: 'Dewi Lestari',
      items: [
        { id: 'i5', menu: 'Soto Ayam', harga: 20000, jumlah: 1, total: 20000 },
      ],
      grandTotal: 20000,
      statusPembayaran: 'Belum Bayar'
    },
    {
      faktur: 'INV-20231118-005', // Tanggal lebih akhir
      tglOrder: '2023-11-18',
      pelanggan: 'Rina Amelia',
      items: [
        { id: 'i6', menu: 'Gado-Gado', harga: 18000, jumlah: 1, total: 18000 },
        { id: 'i7', menu: 'Jus Alpukat', harga: 12000, jumlah: 1, total: 12000 },
      ],
      grandTotal: 30000,
      statusPembayaran: 'Lunas'
    }
  ];

  const [allOrders] = useState(samplePaidOrdersData); // Data master
  const [displayedItems, setDisplayedItems] = useState([]);
  const [filterTglAwal, setFilterTglAwal] = useState('');
  const [filterTglAkhir, setFilterTglAkhir] = useState('');
  const [grandTotalFiltered, setGrandTotalFiltered] = useState(0);

  const processAndFilterOrders = (tglAwal, tglAkhir) => {
    let itemsToShow = [];
    let currentGrandTotal = 0;

    allOrders.forEach(order => {
      // Hanya proses order yang sudah Lunas
      if (order.statusPembayaran === 'Lunas') {
        // Cek apakah order masuk dalam rentang tanggal jika filter tanggal ada
        const isDateInRange = (!tglAwal || order.tglOrder >= tglAwal) &&
                              (!tglAkhir || order.tglOrder <= tglAkhir);

        if (isDateInRange) {
          order.items.forEach(item => {
            itemsToShow.push({
              ...item, // item fields: id, menu, harga, jumlah, total
              faktur: order.faktur, // tambahkan faktur dari parent order
              tglOrder: order.tglOrder, // tambahkan tglOrder dari parent order
            });
            currentGrandTotal += item.total;
          });
        }
      }
    });
    setDisplayedItems(itemsToShow);
    setGrandTotalFiltered(currentGrandTotal);
  };

  useEffect(() => {
    // Tampilkan semua item dari order lunas saat komponen pertama kali dimuat (tanpa filter tanggal)
    processAndFilterOrders('', '');
  }, [allOrders]); // Jalankan saat allOrders berubah (meskipun di sini konstan)

  const handleFilterSubmit = (event) => {
    event.preventDefault();
    if (filterTglAwal && filterTglAkhir && filterTglAwal > filterTglAkhir) {
      alert('Tanggal Awal tidak boleh melebihi Tanggal Akhir.');
      return;
    }
    processAndFilterOrders(filterTglAwal, filterTglAkhir);
  };

  const handleResetFilter = () => {
    setFilterTglAwal('');
    setFilterTglAkhir('');
    processAndFilterOrders('', ''); // Proses ulang tanpa filter tanggal
  };

  return (
    <div>
      <h1>Detail Penjualan (Lunas)</h1>
      <p>Halaman ini menampilkan semua item dari pesanan yang telah lunas.</p>

      {/* Form untuk Filter Tanggal */}
      <div style={{ marginBottom: '30px', padding: '20px', border: '1px solid #ddd', borderRadius: '8px', backgroundColor: '#f0f0f0' }}>
        <h3 style={{ marginTop: 0, marginBottom: '15px' }}>Filter Detail Penjualan</h3>
        <form onSubmit={handleFilterSubmit}>
          <div style={{ display: 'flex', gap: '15px', alignItems: 'flex-end', marginBottom: '15px' }}>
            <div style={{ flex: 1 }}>
              <label htmlFor="filterTglAwal" style={{ display: 'block', marginBottom: '5px' }}>Tanggal Awal:</label>
              <input type="date" id="filterTglAwal" value={filterTglAwal} onChange={(e) => setFilterTglAwal(e.target.value)} style={{ width: 'calc(100% - 22px)', padding: '8px', border: '1px solid #ccc', borderRadius: '4px' }} />
            </div>
            <div style={{ flex: 1 }}>
              <label htmlFor="filterTglAkhir" style={{ display: 'block', marginBottom: '5px' }}>Tanggal Akhir:</label>
              <input type="date" id="filterTglAkhir" value={filterTglAkhir} onChange={(e) => setFilterTglAkhir(e.target.value)} style={{ width: 'calc(100% - 22px)', padding: '8px', border: '1px solid #ccc', borderRadius: '4px' }} />
            </div>
            <button type="submit" style={{ padding: '8px 20px', backgroundColor: '#17a2b8', color: 'white', border: 'none', borderRadius: '5px', cursor: 'pointer', height: '38px' }}>
              Cek Pesanan
            </button>
            <button type="button" onClick={handleResetFilter} style={{ padding: '8px 20px', backgroundColor: '#6c757d', color: 'white', border: 'none', borderRadius: '5px', cursor: 'pointer', height: '38px' }}>
              Reset Filter
            </button>
          </div>
        </form>
      </div>

      <table className="kategori-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Faktur</th>
            <th>Tgl Order</th>
            <th>Menu</th>
            <th>Harga (Rp)</th>
            <th>Jumlah</th>
            <th>Total (Rp)</th>
          </tr>
        </thead>
        <tbody>
          {displayedItems.length > 0 ? (
            displayedItems.map((item, index) => (
              <tr key={`${item.faktur}-${item.id}`}> {/* Kunci unik gabungan */}
                <td>{index + 1}</td>
                <td>{item.faktur}</td>
                <td>{item.tglOrder}</td>
                <td>{item.menu}</td>
                <td style={{ textAlign: 'right' }}>{item.harga.toLocaleString('id-ID')}</td>
                <td style={{ textAlign: 'center' }}>{item.jumlah}</td>
                <td style={{ textAlign: 'right' }}>{item.total.toLocaleString('id-ID')}</td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="7" style={{ textAlign: 'center', padding: '20px' }}>
                Tidak ada item penjualan yang sesuai dengan filter atau belum ada penjualan lunas.
              </td>
            </tr>
          )}
        </tbody>
        {displayedItems.length > 0 && (
          <tfoot>
            <tr>
              <td colSpan="6" style={{ textAlign: 'right', fontWeight: 'bold', padding: '10px' }}>Grand Total (Filtered):</td>
              <td style={{ textAlign: 'right', fontWeight: 'bold' }}>{grandTotalFiltered.toLocaleString('id-ID')}</td>
            </tr>
          </tfoot>
        )}
      </table>
    </div>
  );
}

export default OrderDetail;