<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="konten">
    <div class="container">
        <div class="row">
            <div class="col">
                <h1>Pembayaran Berhasil :)</h1>
                <p><?= $ceking; ?></p>
                <p><?= $keranjang; ?></p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>