// File: link.js
// Mungkin untuk menangani navigasi atau tautan dinamis

document.addEventListener('DOMContentLoaded', () => {
  console.log('link.js dimuat dan DOM siap.');

  // Contoh: Menambahkan event listener ke semua tautan dengan class tertentu
  const specialLinks = document.querySelectorAll('a.special-link');
  specialLinks.forEach(link => {
    link.addEventListener('click', (event) => {
      event.preventDefault();
      alert(`Tautan spesial diklik: ${link.href}`);
      // Logika navigasi kustom bisa ditambahkan di sini
    });
  });
});