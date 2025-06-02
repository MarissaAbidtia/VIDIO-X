import React, { useState, useEffect } from 'react';

function Order() {
  const initialDataOrder = [ // Menyimpan data asli
    { id: 1, pelanggan: 'Budi Santoso', tglOrder: '2023-10-26', total: 75000, bayar: 75000, kembalian: 0, status: 'Lunas' },
    { id: 2, pelanggan: 'Siti Aminah', tglOrder: '2023-10-27', total: 50000, bayar: 0, kembalian: 0, status: 'Belum Bayar' },
    { id: 3, pelanggan: 'Agus Wijaya', tglOrder: '2023-10-28', total: 120000, bayar: 120000, kembalian: 0, status: 'Menunggu Pesanan' },
    { id: 4, pelanggan: 'Dewi Lestari', tglOrder: '2023-10-28', total: 35000, bayar: 50000, kembalian: 15000, status: 'Lunas' },
    { id: 5, pelanggan: 'Rahmat Hidayat', tglOrder: '2023-11-01', total: 60000, bayar: 60000, kembalian: 0, status: 'Lunas' },
    { id: 6, pelanggan: 'Budi Santoso', tglOrder: '2023-11-02', total: 25000, bayar: 0, kembalian: 0, status: 'Belum Bayar' },
  ];

  const [dataOrder, setDataOrder] = useState(initialDataOrder); // Data yang bisa berubah karena penambahan
  const [ordersToDisplay, setOrdersToDisplay] = useState(initialDataOrder); // Data yang ditampilkan di tabel

  const statusOptions = ['Belum Bayar', 'Lunas', 'Menunggu Pesanan'];

  // State untuk form tambah pesanan baru
  const [inputPelanggan, setInputPelanggan] = useState('');
  const [inputTglOrder, setInputTglOrder] = useState(new Date().toISOString().slice(0, 10));
  const [inputTotal, setInputTotal] = useState('');
  const [inputBayarForm, setInputBayarForm] = useState(''); // Renamed to avoid conflict

  const [filterTglAwal, setFilterTglAwal] = useState('');
  const [filterTglAkhir, setFilterTglAkhir] = useState('');

  // State untuk Modal Pembayaran
  const [isPaymentModalOpen, setIsPaymentModalOpen] = useState(false);
  const [currentOrderForPayment, setCurrentOrderForPayment] = useState(null);
  const [modalBayarAmount, setModalBayarAmount] = useState('');
  const [modalKembalian, setModalKembalian] = useState(0);

  useEffect(() => {
    if (!filterTglAwal && !filterTglAkhir) {
      setOrdersToDisplay(dataOrder);
    }
  }, [dataOrder, filterTglAwal, filterTglAkhir]);

  useEffect(() => {
    if (currentOrderForPayment) {
      const total = parseFloat(currentOrderForPayment.total) || 0;
      const bayar = parseFloat(modalBayarAmount) || 0;
      setModalKembalian(calculateKembalian(total, bayar));
    }
  }, [modalBayarAmount, currentOrderForPayment]);


  const handleUpdateStatus = (orderId, newStatus) => {
    setDataOrder(
      dataOrder.map((order) =>
        order.id === orderId ? { ...order, status: newStatus } : order
      )
    );
    alert(`Status pesanan ID ${orderId} diubah menjadi ${newStatus}`);
  };

  // Fungsi untuk menghitung kembalian secara otomatis
  const calculateKembalian = (total, bayar) => {
    const numTotal = parseFloat(total) || 0;
    const numBayar = parseFloat(bayar) || 0;
    if (numBayar >= numTotal) {
      return numBayar - numTotal;
    }
    return 0; // Jika bayar kurang dari total, kembalian 0
  };
  
  // Fungsi untuk menangani perubahan input bayar di tabel dan update kembalian
  const handleBayarChangeInTable = (orderId, nilaiBayar) => {
    const updateData = (currentData) => 
      currentData.map((order) => {
        if (order.id === orderId) {
          const bayarInput = parseFloat(nilaiBayar) || 0;
          const bayarValid = Math.max(0, bayarInput);
          const kembalian = calculateKembalian(order.total, bayarValid);
          return { ...order, bayar: bayarValid, kembalian: kembalian };
        }
        return order;
      });
    
    setDataOrder(updateData(dataOrder));
    // Jika filter aktif, perbarui juga data yang ditampilkan agar perubahan bayar terlihat
    if (filterTglAwal || filterTglAkhir) {
        setOrdersToDisplay(updateData(ordersToDisplay));
    }
  };

  const resetFormTambahOrder = () => {
    setInputPelanggan('');
    setInputTglOrder(new Date().toISOString().slice(0, 10));
    setInputTotal('');
    setInputBayarForm(''); // Use renamed state
  };

  const handleSubmitNewOrder = (event) => {
    event.preventDefault();
    if (!inputPelanggan || !inputTglOrder || !inputTotal) {
      alert('Pelanggan, Tanggal Order, dan Total harus diisi!');
      return;
    }
    const totalNum = parseFloat(inputTotal) || 0;
    const bayarNum = parseFloat(inputBayarForm) || 0; // Use renamed state

    if (totalNum <= 0) {
        alert('Total harus bernilai positif!');
        return;
    }

    const newId = dataOrder.length > 0 ? Math.max(...dataOrder.map(o => o.id)) + 1 : 1;
    const kembalianNewOrder = calculateKembalian(totalNum, bayarNum);

    const newOrder = {
      id: newId,
      pelanggan: inputPelanggan,
      tglOrder: inputTglOrder,
      total: totalNum,
      bayar: bayarNum,
      kembalian: kembalianNewOrder,
      status: bayarNum >= totalNum ? 'Lunas' : 'Belum Bayar',
    };

    setDataOrder([...dataOrder, newOrder]); // Ini akan memicu useEffect
    alert('Pesanan baru berhasil ditambahkan!');
    resetFormTambahOrder();
  };

  const handleFilterSubmit = (event) => {
    event.preventDefault();
    if (!filterTglAwal || !filterTglAkhir) {
      alert('Mohon isi Tanggal Awal dan Tanggal Akhir untuk filter.');
      return;
    }
    if (filterTglAwal > filterTglAkhir) {
      alert('Tanggal Awal tidak boleh melebihi Tanggal Akhir.');
      return;
    }

    const filtered = dataOrder.filter(order => {
      return order.tglOrder >= filterTglAwal && order.tglOrder <= filterTglAkhir;
    });
    setOrdersToDisplay(filtered);
    alert(`Menampilkan ${filtered.length} pesanan dari ${filterTglAwal} sampai ${filterTglAkhir}.`);
  };

  const handleResetFilter = () => {
    setFilterTglAwal('');
    setFilterTglAkhir('');
    setOrdersToDisplay(dataOrder); // Kembali tampilkan semua data dari state dataOrder
    alert('Filter tanggal direset. Menampilkan semua pesanan.');
  };

  // Define openPaymentModal function
  const openPaymentModal = (order) => {
    setCurrentOrderForPayment(order);
    setModalBayarAmount(order.bayar > 0 ? order.bayar.toString() : ''); // Pre-fill if already paid
    setIsPaymentModalOpen(true);
    // Calculate initial kembalian when modal opens
    const total = parseFloat(order.total) || 0;
    const bayar = parseFloat(order.bayar) || 0;
    setModalKembalian(calculateKembalian(total, bayar));
  };

  // Define closePaymentModal function
  const closePaymentModal = () => {
    setIsPaymentModalOpen(false);
    setCurrentOrderForPayment(null);
    setModalBayarAmount('');
    setModalKembalian(0);
  };

  // Define handleProsesPembayaran function
  const handleProsesPembayaran = () => {
    if (!currentOrderForPayment) return;

    const orderId = currentOrderForPayment.id;
    const bayarInput = parseFloat(modalBayarAmount) || 0;
    const totalPembayaran = parseFloat(currentOrderForPayment.total) || 0;

    if (bayarInput < totalPembayaran) {
      alert('Jumlah bayar kurang dari total tagihan!');
      return;
    }

    const kembalian = calculateKembalian(totalPembayaran, bayarInput);

    // Update dataOrder
    const updatedDataOrder = dataOrder.map(order => {
      if (order.id === orderId) {
        return {
          ...order,
          bayar: bayarInput,
          kembalian: kembalian,
          status: 'Lunas' // Set status to Lunas after successful payment
        };
      }
      return order;
    });
    setDataOrder(updatedDataOrder);

    // If filters are active, update ordersToDisplay as well
    if (filterTglAwal || filterTglAkhir) {
        const updatedOrdersToDisplay = ordersToDisplay.map(order => {
            if (order.id === orderId) {
                return {
                    ...order,
                    bayar: bayarInput,
                    kembalian: kembalian,
                    status: 'Lunas'
                };
            }
            return order;
        });
        setOrdersToDisplay(updatedOrdersToDisplay);
    }


    alert(`Pembayaran untuk pesanan ID ${orderId} berhasil diproses.`);
    closePaymentModal(); // Close modal after processing
  };


  return (
    <div>
      <h1>Data Pesanan (Order)</h1>
      <p>Ini adalah halaman untuk mengelola data pesanan.</p>

      {/* Form untuk menambah order baru */}
      <form onSubmit={handleSubmitNewOrder} style={{ marginBottom: '30px', padding: '20px', border: '1px solid #ddd', borderRadius: '8px', backgroundColor: '#f9f9f9' }}>
        <h3 style={{ marginTop: 0, marginBottom: '15px' }}>Tambah Pesanan Baru</h3>
        <div style={{ marginBottom: '10px' }}>
          <label htmlFor="pelanggan" style={{ display: 'block', marginBottom: '5px' }}>Pelanggan:</label>
          <input type="text" id="pelanggan" value={inputPelanggan} onChange={(e) => setInputPelanggan(e.target.value)} required style={{ width: 'calc(100% - 22px)', padding: '8px', border: '1px solid #ccc', borderRadius: '4px' }} />
        </div>
        <div style={{ display: 'flex', gap: '10px', marginBottom: '10px' }}>
            <div style={{ flex: 1 }}>
                <label htmlFor="tglOrder" style={{ display: 'block', marginBottom: '5px' }}>Tgl Order:</label>
                <input type="date" id="tglOrder" value={inputTglOrder} onChange={(e) => setInputTglOrder(e.target.value)} required style={{ width: 'calc(100% - 22px)', padding: '8px', border: '1px solid #ccc', borderRadius: '4px' }} />
            </div>
            <div style={{ flex: 1 }}>
                <label htmlFor="total" style={{ display: 'block', marginBottom: '5px' }}>Total (Rp):</label>
                <input type="number" id="total" value={inputTotal} onChange={(e) => setInputTotal(e.target.value)} required min="0" style={{ width: 'calc(100% - 22px)', padding: '8px', border: '1px solid #ccc', borderRadius: '4px' }} />
            </div>
            <div style={{ flex: 1 }}>
                <label htmlFor="bayarForm" style={{ display: 'block', marginBottom: '5px' }}>Bayar (Rp):</label> {/* Changed htmlFor */}
                <input type="number" id="bayarForm" value={inputBayarForm} onChange={(e) => setInputBayarForm(e.target.value)} min="0" style={{ width: 'calc(100% - 22px)', padding: '8px', border: '1px solid #ccc', borderRadius: '4px' }} /> {/* Changed id */}
            </div>
        </div>
        <button type="submit" style={{ padding: '10px 20px', backgroundColor: '#007bff', color: 'white', border: 'none', borderRadius: '5px', cursor: 'pointer' }}>
          Tambah Pesanan
        </button>
      </form>

      {/* Form untuk Filter Tanggal Order */}
      <div style={{ marginBottom: '30px', padding: '20px', border: '1px solid #ddd', borderRadius: '8px', backgroundColor: '#f0f0f0' }}>
        <h3 style={{ marginTop: 0, marginBottom: '15px' }}>Filter Pesanan Berdasarkan Tanggal</h3>
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
              Lihat Hasil
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
            <th>Pelanggan</th>
            <th>Tgl Order</th>
            <th>Total (Rp)</th>
            <th>Bayar (Rp)</th>
            <th>Kembalian (Rp)</th>
            <th>Status</th>
            <th>Ubah Status</th>
            <th>Aksi</th> {/* Kolom Aksi Tambahan */}
          </tr>
        </thead>
        <tbody>
          {ordersToDisplay.length > 0 ? (
            ordersToDisplay.map((order, index) => (
              <tr key={order.id}>
                <td>{index + 1}</td>
                <td>{order.pelanggan}</td>
                <td>{order.tglOrder}</td>
                <td>{order.total.toLocaleString('id-ID')}</td>
                <td>
                  {/* Input di tabel bisa dipertimbangkan untuk di-disable jika menggunakan modal */}
                  <input 
                    type="number" 
                    value={order.bayar} 
                    onChange={(e) => handleBayarChangeInTable(order.id, e.target.value)}
                    style={{width: '100px', padding: '5px', textAlign: 'right', border: '1px solid #ccc', borderRadius: '4px'}}
                    min="0"
                    // disabled // Pertimbangkan untuk disable jika modal adalah cara utama bayar
                  />
                </td>
                <td>{order.kembalian.toLocaleString('id-ID')}</td>
                <td>
                  <span 
                    style={{ 
                      padding: '5px 10px', 
                      borderRadius: '4px', 
                      color: 'white',
                      backgroundColor: 
                        order.status === 'Lunas' ? '#28a745' : 
                        order.status === 'Belum Bayar' ? '#dc3545' :
                        order.status === 'Menunggu Pesanan' ? '#ffc107' : '#6c757d',
                      color: order.status === 'Menunggu Pesanan' ? 'black' : 'white'
                    }}
                  >
                    {order.status}
                  </span>
                </td>
                <td>
                  {statusOptions.map(statusOpt => (
                    <button
                      key={statusOpt}
                      onClick={() => handleUpdateStatus(order.id, statusOpt)}
                      disabled={order.status === statusOpt}
                      style={{ 
                        marginRight: '5px', 
                        padding: '5px 8px', 
                        fontSize: '12px',
                        cursor: 'pointer',
                        border: '1px solid #ccc',
                        borderRadius: '4px',
                        backgroundColor: order.status === statusOpt ? '#007bff' : 'white',
                        color: order.status === statusOpt ? 'white' : 'black',
                      }}
                    >
                      {statusOpt}
                    </button>
                  ))}
                </td>
                <td>
                  {order.status !== 'Lunas' && (
                    <button
                      onClick={() => openPaymentModal(order)}
                      style={{ padding: '5px 10px', backgroundColor: '#007bff', color: 'white', border: 'none', borderRadius: '4px', cursor: 'pointer', fontSize: '12px' }}
                    >
                      Bayar
                    </button>
                  )}
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="9" style={{ textAlign: 'center', padding: '20px' }}> {/* colSpan disesuaikan */}
                { (filterTglAwal || filterTglAkhir) ? 'Tidak ada data pesanan pada rentang tanggal yang dipilih.' : 'Belum ada data pesanan.'}
              </td>
            </tr>
          )}
        </tbody>
      </table>

      {/* Modal Pembayaran */}
      {isPaymentModalOpen && currentOrderForPayment && (
        <div style={{
          position: 'fixed', top: 0, left: 0, right: 0, bottom: 0,
          backgroundColor: 'rgba(0,0,0,0.5)', display: 'flex',
          alignItems: 'center', justifyContent: 'center', zIndex: 1000
        }}>
          <div style={{
            backgroundColor: 'white', padding: '25px', borderRadius: '8px',
            boxShadow: '0 4px 15px rgba(0,0,0,0.2)', width: '400px'
          }}>
            <h2 style={{ marginTop: 0, marginBottom: '20px', textAlign: 'center' }}>Pembayaran Order</h2>
            <div style={{ marginBottom: '15px' }}>
              <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Pelanggan:</label>
              <input type="text" value={currentOrderForPayment.pelanggan} readOnly style={{ width: 'calc(100% - 12px)', padding: '8px', border: '1px solid #ddd', borderRadius: '4px', backgroundColor: '#e9ecef' }}/>
            </div>
            <div style={{ marginBottom: '15px' }}>
              <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Total Tagihan (Rp):</label>
              <input type="text" value={currentOrderForPayment.total.toLocaleString('id-ID')} readOnly style={{ width: 'calc(100% - 12px)', padding: '8px', border: '1px solid #ddd', borderRadius: '4px', backgroundColor: '#e9ecef', textAlign: 'right' }}/>
            </div>
            <div style={{ marginBottom: '15px' }}>
              <label htmlFor="modalBayar" style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Jumlah Bayar (Rp):</label>
              <input
                type="number"
                id="modalBayar"
                value={modalBayarAmount}
                onChange={(e) => setModalBayarAmount(e.target.value)}
                min="0"
                style={{ width: 'calc(100% - 12px)', padding: '8px', border: '1px solid #ccc', borderRadius: '4px', textAlign: 'right' }}
                autoFocus
              />
            </div>
            <div style={{ marginBottom: '20px' }}>
              <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Kembalian (Rp):</label>
              <input type="text" value={modalKembalian.toLocaleString('id-ID')} readOnly style={{ width: 'calc(100% - 12px)', padding: '8px', border: '1px solid #ddd', borderRadius: '4px', backgroundColor: '#e9ecef', textAlign: 'right', color: modalKembalian < 0 ? 'red' : 'inherit' }}/>
            </div>
            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '10px' }}>
              <button onClick={closePaymentModal} style={{ padding: '10px 20px', backgroundColor: '#dc3545', color: 'white', border: 'none', borderRadius: '5px', cursor: 'pointer' }}>
                Batal
              </button>
              <button onClick={handleProsesPembayaran} style={{ padding: '10px 20px', backgroundColor: '#007bff', color: 'white', border: 'none', borderRadius: '5px', cursor: 'pointer' }}>
                Bayar
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

export default Order;