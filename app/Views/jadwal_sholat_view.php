<!-- Mendeklarasikan dokumen HTML dan menetapkan bahasa halaman ke bahasa Indonesia. -->
<!DOCTYPE html>
<html lang="id">
<head>
     <!-- Menentukan pengkodean karakter agar teks Indonesia ditampilkan dengan benar. -->
     <meta charset="UTF-8">
     <!-- Menentukan judul yang tampil pada tab browser. -->
     <title>Jadwal Sholat Digital - CI4</title>
     <!-- Memuat Bootstrap untuk menggunakan komponen dan utility CSS. -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">    
    <!-- Membuat wadah konten agar tampil di tengah dengan lebar maksimum 600 piksel. -->
    <div class="container" style="max-width: 600px;">        
         <div class="card shadow-sm border-0 p-4">
            <!-- Menampilkan judul aplikasi. -->
            <h3 class="text-center text-primary fw-bold mb-3">Jadwal Sholat Digital</h3>

             <!-- Membuat pilihan dropdown untuk memilih wilayah atau kota. -->
             <div class="mb-3">
                <label for="selectKota" class="form-label fw-semibold">Pilih Wilayah / Kota:</label>
                <select id="selectKota" class="form-select">
                    <!-- value berisi ID kota yang digunakan sebagai parameter API. -->
                    <option value="1301">Kota Bantul</option>
                    <option value="1203">Kota Bandung</option>
                    <option value="1501">Yogyakarta</option>
                    <option value="1609">Kota Surabaya</option>
                </select>
            </div>

             <!-- Menampilkan indikator pemuatan saat permintaan data ke API sedang berjalan. -->
             <div id="loading" class="text-center d-none my-3">
                 <div class="spinner-border text-primary" role="status"></div>                
                 <p class="text-muted small mt-1">Sedang mengontak server API...</p>
             </div>
      
            <!-- Menjadi wadah tempat kartu jadwal sholat ditambahkan secara dinamis. -->
            <div id="jadwalContainer" class="row g-2 mt-2"></div>
         </div>   
    </div>

    <script>
        // Mengambil elemen dropdown kota dari dokumen.
        const selectKota = document.querySelector('#selectKota'); 
        // Mengambil elemen indikator loading dari dokumen.
        const loading = document.querySelector('#loading');
        // Mengambil wadah yang akan berisi hasil jadwal sholat.
        const jadwalContainer = document.querySelector('#jadwalContainer');

        // Mendefinisikan fungsi untuk mengambil jadwal sholat berdasarkan ID kota.
        const getJadwal = (idKota) => 
        {    
        // Menampilkan loading dan mengosongkan jadwal lama sebelum permintaan baru.
        loading.classList.remove('d-none');    
        jadwalContainer.innerHTML = '';
        
        // Membuat objek tanggal untuk mendapatkan tanggal hari ini.
        const tanggal = new Date();
        // Mengubah tanggal menjadi format YYYY/MM/DD yang diterima oleh API.
        const hariIni = `${tanggal.getFullYear()}/${String(tanggal.getMonth() + 1).padStart(2, '0')}/${String(tanggal.getDate()).padStart(2, '0')}`;

        // Mengirim permintaan HTTP GET ke API MyQuran menggunakan ID kota dan tanggal hari ini.
        fetch(`https://api.myquran.com/v2/sholat/jadwal/${idKota}/${hariIni}`) 
        .then(res => {
            // Memeriksa apakah respons HTTP menunjukkan kegagalan.
            if (!res.ok) {
                throw new Error(`HTTP ${res.status}`);
            }
            // Mengubah respons HTTP menjadi data JSON.
            return res.json();
        })
        .then(result => {              
        // Mengambil objek jadwal dari struktur respons API.
        const jadwal = result?.data?.jadwal;
        // Memeriksa status API dan memastikan data jadwal tersedia.
        if (!result.status || !jadwal) {
            throw new Error(result.message || 'Data jadwal tidak tersedia.');
        }
        // Menyembunyikan indikator loading setelah data berhasil diterima.
        loading.classList.add('d-none');
        // Menyusun data lima waktu sholat beserta waktunya ke dalam sebuah array.
        const waktuSholat = [                
            { nama: 'Subuh', waktu: jadwal.subuh },                
            { nama: 'Dzuhur', waktu: jadwal.dzuhur },                
            { nama: 'Ashar', waktu: jadwal.ashar },                
            { nama: 'Maghrib', waktu: jadwal.maghrib },               
            { nama: 'Isya', waktu: jadwal.isya }
          ];
          // Mengulangi setiap waktu sholat untuk membuat kartu HTML secara dinamis.
        waktuSholat.forEach(item => {
           // Menambahkan HTML kartu nama dan waktu sholat ke wadah jadwal.
           jadwalContainer.innerHTML += `                    
             <div class="col-4 col-md">
                <div class="card text-center bg-primary text-white p-2 border-0 shadow-sm">
                    <small class="text-white-50">${item.nama}</small>                            
                    <h6 class="fw-bold mb-0 mt-1">${item.waktu}</h6>
                </div>
             </div>
            `;
        });
    })
    .catch(err => {
     // Menulis informasi kegagalan ke konsol browser untuk membantu debugging.
     console.error('Gagal memuat jadwal sholat:', err);
     // Menyembunyikan indikator loading dan menampilkan pesan kesalahan kepada pengguna.
     loading.classList.add('d-none');            
     jadwalContainer.innerHTML = `<div class="alert alert-danger text-center">Gagal memuat jadwal sholat. Periksa koneksi internet lalu coba lagi.</div>`;
    });
};

// Menjalankan fungsi getJadwal setiap kali pengguna mengganti pilihan kota.
selectKota.addEventListener('change', (e) => {
     // Mengirim nilai ID kota yang dipilih oleh pengguna ke fungsi getJadwal.
     getJadwal(e.target.value);
});

// Memuat jadwal sholat pertama kali menggunakan pilihan kota yang aktif.
getJadwal(selectKota.value);
    </script>
    </body> 
</html>
