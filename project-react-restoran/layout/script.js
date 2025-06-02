// URL API - ganti dengan URL API Anda
const apiUrl = 'https://jsonplaceholder.typicode.com/users';

// Data dummy untuk simulasi
const dummyData = [
    { id: 12, name: "Gerry Moore", address: { street: "99590 Jessie Brook East Donna", city: "NJ 14321-0637" }, phone: "975-439-1644" },
    { id: 13, name: "Kelsi Satterfield", address: { street: "27874 Kuhlman Hill Suite 472", city: "Lake Margaretborough, AK 83798" }, phone: "+1.662.356.9509" },
    { id: 14, name: "Ward Marks", address: { street: "979 Feest Gardens Apt. 221", city: "Robinburgh, VT 70181" }, phone: "+1.785.813.0369" },
    { id: 15, name: "Heber Botsford", address: { street: "86237 Freddie Cliffs", city: "Beattyfort, TN 26790-2583" }, phone: "1-572-640-1722" },
    { id: 16, name: "Leon Heaney", address: { street: "327 Runolfsson Course North", city: "Janetbury, NY 49128" }, phone: "+1-342-736-5553" },
    { id: 17, name: "Estel Simonis", address: { street: "4872 McLaughlin Roads", city: "South Eleanoreton, WY 37095" }, phone: "+19537638910" }
];

// Fungsi untuk menampilkan data dalam tabel
function displayData(data) {
    const tableBody = document.getElementById('tableBody');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    
    data.forEach(user => {
        const row = document.createElement('tr');
        
        // Format alamat
        const alamat = `${user.address.street}<br>${user.address.city}`;
        
        row.innerHTML = `
            <td>${user.id}</td>
            <td>${user.name}</td>
            <td>${alamat}</td>
            <td>${user.phone}</td>
        `;
        
        tableBody.appendChild(row);
    });
}