<style>
/* --- Bagian 2: CSS untuk Nuansa Pink dan Dekorasi Bulat-Bulat --- */

.footer-hero {
    /* Warna latar belakang: Pink Ceria */
    background-color: #ff69b4;
    /* Deep Pink */
    color: #fff;
    padding-top: 5rem !important;
    padding-bottom: 3rem !important;
    flex-shrink: 0;
    /* Mencegah footer mengecil */
    position: relative;
    /* Penting untuk posisi hiasan */
    overflow: hidden;
    /* Menyembunyikan bagian hiasan yang keluar */
    box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.15);
    /* Bayangan lembut */
}

/* Judul Kontak Kami */
.footer-hero h4 {
    color: #fff0f5;
    /* Lavender Blush untuk judul */
    letter-spacing: 1px;
    font-size: 1.75rem;
    margin-bottom: 2rem !important;
}

/* Teks Label (Email, Telepon, Alamat) */
.footer-hero .lead {
    font-weight: 700;
    /* Lebih tebal */
    color: #ffe4e1;
    /* Misty Rose */
    opacity: 0.95;
    margin-bottom: 0.5rem;
}

/* Teks Informasi Kontak */
.footer-hero p.fw-light {
    font-size: 0.9rem;
    line-height: 1.4;
    opacity: 0.9;
}

/* Garis Pembatas di atas Copyright */
.footer-hero .border-top {
    border-color: rgba(255, 255, 255, 0.5) !important;
    padding-top: 1.5rem !important;
    margin-top: 2rem !important;
}


/* --- Hiasan Bulat-Bulat (Menggunakan Pseudo-elements) --- */

/* Hiasan Bulat 1: Kiri Atas */
.footer-hero::before {
    content: '';
    position: absolute;
    top: -40px;
    left: -40px;
    width: 120px;
    height: 120px;
    background-color: rgba(255, 255, 255, 0.15);
    /* Pink sangat transparan */
    border-radius: 50%;
    z-index: 0;
    filter: blur(5px);
}

/* Hiasan Bulat 2: Kanan Bawah */
.footer-hero::after {
    content: '';
    position: absolute;
    bottom: -60px;
    right: -60px;
    width: 180px;
    height: 180px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    z-index: 0;
    filter: blur(8px);
    transform: rotate(30deg);
    /* Sedikit rotasi untuk estetika */
}

/* Memastikan teks tetap terlihat di atas hiasan */
.footer-hero>.container {
    position: relative;
    z-index: 1;
}
</style>
<footer id="contacts-us" class="footer-hero text-white pt-5 pb-3">
    <div class="container">
        <h4 class="text-center mb-4 fw-bold">Kontak Kami</h4>
        <div class="row text-center">
            <div class="col-md-4 mb-3">
                <p class="lead mb-1">Email</p>
                <p class="fw-light">beautyfashionlampung@gmail.com</p>
            </div>
            <div class="col-md-4 mb-3">
                <p class="lead mb-1">Telepon</p>
                <p class="fw-light">+62 823-0601-7068</p>
            </div>
            <div class="col-md-4 mb-3">
                <p class="lead mb-1">Alamat Office</p>
                <p class="fw-light">Purwodadi Dalam Tanjung Sari Lampung Selatan</p>
            </div>
        </div>
        <div class="text-center mt-4 pt-3 border-top border-light border-opacity-25">
            <p>&copy; 2025 Beauty Fashion. All Rights Reserved.</p>
        </div>
    </div>
</footer>