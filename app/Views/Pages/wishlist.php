<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<div class="konten">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <p class="mb-0">Wishlist (4)</p>
            <a href="#" class="btn btn-outline-dark">Beli Semua</a>
        </div>
        <div class="card-group1 no-scroll">
            <?php if (count($wishlist) > 0) { ?>
                <?php foreach ($produk as $p) { ?>
                    <a class="card1" href="/product/<?= $p['id']; ?>">
                        <img src="data:image/jpeg;base64,<?= base64_encode($p['gambar']); ?>" alt="">
                        <div class="mt-3">
                            <h5 class="mb-0"><?= $p['nama']; ?></h5>
                            <?php if ($p['diskon']) { ?>
                                <p class="mb-0 harga d-inline">Rp
                                    <?php
                                    $persen = (100 - $p['diskon']) / 100;
                                    $hasil = $persen * $p['harga'];
                                    echo number_format($hasil, 0, ",", ".");
                                    ?></p>
                                <p class="mb-0 d-inline" style="text-decoration: line-through; font-size: small; color: grey;">Rp <?= number_format($p['harga'], 0, ",", "."); ?></p>
                            <?php } else { ?>
                                <p class="mb-0 harga">Rp <?= number_format($p['harga'], 0, ",", "."); ?></p>
                            <?php } ?>
                            <p>★★★☆☆ (<?= $p['rate']; ?>)</p>
                        </div>
                        <?php if ($p['diskon']) { ?>
                            <p class="diskon">-<?= $p['diskon']; ?>%</p>
                        <?php } ?>
                    </a>
                <?php } ?>
            <?php } else { ?>
                <h5>Tidak ada wishlist</h5>
            <?php } ?>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>