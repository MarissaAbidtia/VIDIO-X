// File: post.js
// Berisi fungsi-fungsi untuk mengirim (POST) data

async function postData(url, data) {
  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    });
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const responseData = await response.json();
    console.log('Data berhasil dikirim:', responseData);
    return responseData;
  } catch (error) {
    console.error('Gagal mengirim data:', error);
    return null;
  }
}

// Contoh penggunaan:
// const newData = { name: 'Produk Baru', price: 100 };
// postData('https://api.example.com/items', newData)
//   .then(data => {
//     if (data) {
//       // Lakukan sesuatu setelah berhasil post
//     }
//   });