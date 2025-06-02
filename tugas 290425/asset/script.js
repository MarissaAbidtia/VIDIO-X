// Fungsi untuk menambah ke keranjang
function tambahKeKeranjang(produkId) {
    fetch('cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add&produk_id=${produkId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateKeranjangBadge(data.total_items);
            alert('Produk berhasil ditambahkan ke keranjang!');
        } else {
            alert(data.message || 'Gagal menambahkan produk ke keranjang');
        }
    });
}

// Update badge keranjang
function updateKeranjangBadge(total) {
    const badge = document.getElementById('cart-badge');
    if (badge) {
        badge.textContent = total;
    }
}

// Fungsi untuk update jumlah di keranjang
function updateJumlah(produkId, perubahan) {
    const inputJumlah = document.getElementById(`jumlah_${produkId}`);
    let jumlah = parseInt(inputJumlah.value) + perubahan;
    
    if (jumlah < 1) jumlah = 1;
    
    fetch('cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update&produk_id=${produkId}&jumlah=${jumlah}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            inputJumlah.value = jumlah;
            document.getElementById(`subtotal_${produkId}`).textContent = 
                `Rp ${data.subtotal.toLocaleString()}`;
            document.getElementById('total').textContent = 
                `Rp ${data.total.toLocaleString()}`;
        }
    });
}