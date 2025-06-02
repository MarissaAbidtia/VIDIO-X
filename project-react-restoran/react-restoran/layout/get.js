// File: get.js
// Berisi fungsi-fungsi untuk mengambil (GET) data

async function fetchData(url) {
  try {
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    console.log('Data berhasil diambil:', data);
    return data;
  } catch (error) {
    console.error('Gagal mengambil data:', error);
    return null;
  }
}

// Contoh penggunaan:
// fetchData('https://api.example.com/data')
//   .then(data => {
//     if (data) {
//       // Lakukan sesuatu dengan data
//     }
//   });