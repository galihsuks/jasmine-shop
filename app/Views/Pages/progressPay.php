<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="konten d-flex align-items-center">
    <div class="container">
        <div class="justify-content-center">
            <div class="text-center">
                <h3>Kami menunggu pembayaran Anda</h3>
                <!-- <p>Pembayaran Anda sedang dalam proses verifikasi. Harap tunggu konfirmasi lebih lanjut.</p> -->
                <i class="bi bi-hourglass-split text-warning display-1 mt-4 mb-4"></i>
                <div class="mb-3">
                    <a href="/transaction" class="btn btn-primary1 me-3 mb-2">
                        <p id="counter" class="d-inline m-0">5 |</p> Pergi ke Transaksi
                    </a>
                    <a href="https://wa.me/628112938160?text=Halo%20,%20saya%20mengalami%20masalah%20dengan%20pembayaran%20saya.%20Bisakah%20Anda%20bantu%20saya?" class="btn btn-dark mb-2">Butuh Bantuan?</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    let counter = 5;
    const counterElm = document.getElementById('counter');
    setInterval(() => {
        counterElm.innerHTML = counter + " |";
        counter--;
        if (counter <= 0) window.location.href = '/transaction'
    }, 1000);
</script>
<?= $this->endSection(); ?>