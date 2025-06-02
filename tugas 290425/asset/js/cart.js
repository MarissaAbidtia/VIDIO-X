// Fungsi untuk mengelola keranjang belanja
const CartManager = {
    // Key untuk localStorage
    CART_KEY: 'restaurant_cart',
    
    // Inisialisasi keranjang
    init() {
        // Event listener untuk tombol keranjang
        const cartBtn = document.querySelector('.keranjang-icon, #btn-keranjang');
        if (cartBtn) {
            cartBtn.addEventListener('click', this.handleCartClick.bind(this));
        }
    },

    // Handler untuk klik tombol keranjang
    handleCartClick(e) {
        e.preventDefault();
        
        // Langsung arahkan ke halaman keranjang
        window.location.href = 'keranjang/';
    },

    // Menampilkan modal keranjang
    showCartModal() {
        const cart = this.getCart();
        let modalContent = '';

        if (cart.length === 0) {
            modalContent = `
                <div class="text-center py-4">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <p class="mb-0">Keranjang belanja Anda masih kosong</p>
                </div>
            `;
        } else {
            modalContent = `
                <div class="cart-items">
                    ${cart.map(item => `
                        <div class="cart-item d-flex align-items-center mb-3">
                            <img src="${item.gambar}" class="cart-item-img me-3" alt="${item.nama}">
                            <div class="flex-grow-1">
                                <h6 class="mb-0">${item.nama}</h6>
                                <small class="text-muted">
                                    ${item.jumlah} x Rp ${this.formatPrice(item.harga)}
                                </small>
                            </div>
                            <button class="btn btn-link text-danger" onclick="CartManager.removeItem(${item.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `).join('')}
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong>Rp ${this.formatPrice(this.getTotal())}</strong>
                    </div>
                </div>
            `;
        }

        // Tampilkan modal Bootstrap
        const modalHtml = `
            <div class="modal fade" id="cartModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Keranjang Belanja</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            ${modalContent}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <a href="auth/login.php" class="btn btn-primary">Login untuk Checkout</a>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Tambahkan modal ke body jika belum ada
        if (!document.getElementById('cartModal')) {
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        // Tampilkan modal
        const modal = new bootstrap.Modal(document.getElementById('cartModal'));
        modal.show();
    },

    // Mengambil data keranjang dari localStorage
    getCart() {
        const cart = localStorage.getItem(this.CART_KEY);
        return cart ? JSON.parse(cart) : [];
    },

    // Menyimpan data keranjang ke localStorage
    saveCart(cart) {
        localStorage.setItem(this.CART_KEY, JSON.stringify(cart));
    },

    // Menambah item ke keranjang
    addItem(item) {
        const cart = this.getCart();
        const existingItem = cart.find(i => i.id === item.id);

        if (existingItem) {
            existingItem.jumlah += item.jumlah;
        } else {
            cart.push(item);
        }

        this.saveCart(cart);
    },

    // Menghapus item dari keranjang
    removeItem(itemId) {
        const cart = this.getCart().filter(item => item.id !== itemId);
        this.saveCart(cart);
        this.showCartModal(); // Refresh modal
    },

    // Menghitung total harga
    getTotal() {
        return this.getCart().reduce((total, item) => total + (item.harga * item.jumlah), 0);
    },

    // Format harga
    formatPrice(price) {
        return new Intl.NumberFormat('id-ID').format(price);
    }
};

// Inisialisasi CartManager saat dokumen siap
document.addEventListener('DOMContentLoaded', () => CartManager.init());