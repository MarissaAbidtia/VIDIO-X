// File: show.js
// Berisi fungsi-fungsi untuk menampilkan elemen atau informasi

function showElementById(elementId) {
  const element = document.getElementById(elementId);
  if (element) {
    element.style.display = 'block'; // atau 'flex', 'inline-block', dll.
    console.log(`Elemen dengan ID "${elementId}" ditampilkan.`);
  } else {
    console.warn(`Elemen dengan ID "${elementId}" tidak ditemukan.`);
  }
}

function hideElementById(elementId) {
  const element = document.getElementById(elementId);
  if (element) {
    element.style.display = 'none';
    console.log(`Elemen dengan ID "${elementId}" disembunyikan.`);
  } else {
    console.warn(`Elemen dengan ID "${elementId}" tidak ditemukan.`);
  }
}

// Contoh penggunaan:
// showElementById('myModal');
// hideElementById('loadingSpinner');