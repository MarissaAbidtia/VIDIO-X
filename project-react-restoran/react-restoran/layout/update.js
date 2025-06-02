// File: update.js
// Berisi fungsi-fungsi untuk memperbarui (PUT/PATCH) data

async function updateData(url, itemId, dataToUpdate) {
  try {
    const response = await fetch(`${url}/${itemId}`, { // Asumsi URL menggunakan itemId
      method: 'PUT', // atau 'PATCH'
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(dataToUpdate),
    });
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const responseData = await response.json();
    console.log('Data berhasil diperbarui:', responseData);
    return responseData;
  } catch (error) {
    console.error('Gagal memperbarui data:', error);
    return null;
  }
}

// Contoh penggunaan:
// const updatedItemData = { name: 'Produk Diperbarui', price: 120 };
// updateData('https://api.example.com/items', 123, updatedItemData)
//   .then(data => {
//     if (data) {
//       // Lakukan sesuatu setelah berhasil update
//     }
//   });