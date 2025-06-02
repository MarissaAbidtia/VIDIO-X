// File: delete.js
// Berisi fungsi-fungsi untuk menghapus data

function deleteItemById(itemId) {
  console.log(`Mencoba menghapus item dengan ID: ${itemId}`);
  // Logika untuk menghapus item, misalnya:
  // fetch(`/api/items/${itemId}`, { method: 'DELETE' })
  //   .then(response => response.json())
  //   .then(data => console.log('Item berhasil dihapus:', data))
  //   .catch(error => console.error('Gagal menghapus item:', error));
  alert(`Fungsi deleteItemById dipanggil untuk ID: ${itemId}. Implementasikan logika penghapusan.`);
}

// Contoh penggunaan:
// deleteItemById(123);