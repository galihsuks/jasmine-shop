<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="konten">
    <div class="container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="/all">Produk</a></li>
                <li class="breadcrumb-item"><a href="/all/<?= $produk['subkategori']; ?>"><?= str_replace('-', ' ', ucfirst($produk['subkategori'])) ?></a>
                </li>
                <li class="breadcrumb-item active"><a><?= $produk['nama']; ?></a></li>
            </ol>
        </nav>
        <div class="baris-ke-kolom">
            <?php if (isset($gambar)) { ?>
                <div class="img-produk limapuluh-ke-seratus">
                    <figure onmousemove="zoom(event)" onmouseleave="mouseoff(event)" class="img-produk-prev" style="background-image: url('data:image/webp;base64,<?= base64_encode($gambar['gambar1']); ?>')">
                    </figure>
                    <img src="data:image/webp;base64,<?= base64_encode($gambar['gambar1']); ?>" alt="" class="img-produk-prev hide-ke-show-block">
                    <div>
                        <?php foreach ($gambar as $key => $value) {
                            if ($value && $key != 'id') { ?>
                                <div class="img-produk-select <?= $key == 'gambar1' ? "selected" : "" ?>"><img src="data:image/webp;base64,<?= base64_encode($value); ?>" alt=""></div>
                        <?php }
                        } ?>
                    </div>
                </div>
            <?php } ?>
            <div class="limapuluh-ke-seratus">
                <?php if ($msg) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $msg; ?>
                    </div>
                <?php } ?>
                <h3><?= $produk['nama']; ?></h3>
                <?php if ($produk['diskon']) { ?>
                    <p class="mb-0 harga d-inline">Rp
                        <?php
                        $persen = (100 - $produk['diskon']) / 100;
                        $hasil = $persen * $produk['harga'];
                        echo number_format($hasil, 0, ",", ".");
                        ?></p>
                    <p class="mb-0 d-inline" style="text-decoration: line-through; font-size: small; color: grey;">Rp
                        <?= number_format($produk['harga'], 0, ",", "."); ?></p>
                <?php } else { ?>
                    <p class="mb-0 harga">Rp <?= number_format($produk['harga'], 0, ",", "."); ?></p>
                <?php } ?>
                <p class="mb-0">★★★☆☆ (<?= $produk['rate']; ?>)</p>
                <?php if ((int)$produk['stok'] > 0) { ?>
                    <p class="<?= (int)$produk['stok'] < 5 ? "text-danger " : "text-dark"; ?>"><b>Stok :
                            <?= $produk['stok']; ?></b></p>
                <?php } else { ?>
                    <p class="text-danger"><b>Stok habis</b></p>
                <?php } ?>
                <span class="garis mb-2"></span>
                <h5>Varian</h5>
                <div class="btn-group mb-3" id="varian-group" role="group" aria-label="Basic radio toggle button group">
                    <?php foreach ($varian as $key => $value) { ?>
                        <input type="radio" value="<?= $key ?>" class="btn-check" name="btnradio" autocomplete="off" id="btnradio<?= $key ?>" <?= $key == 0 ? "checked" : "" ?>>
                        <label class="btn btn-outline-dark" for="btnradio<?= $key ?>"><?= $value ?></label>
                    <?php } ?>
                </div>
                <h5>Dimensi Pengiriman</h5>
                <p><?= $dimensi[0] . " cm x " . $dimensi[1] . " cm x " . $dimensi[2] . " cm"; ?></p>
                <h5>Berat</h5>
                <p><?= $produk['berat'] ?> kg</p>
                <h5>Deskripsi</h5>
                <p><?= $produk['deskripsi']; ?></p>
                <div class="show-ke-hide">
                    <?php if (session()->get('isLogin')) { ?>
                        <?php if (session()->get('role') == 0) { ?>
                            <?php if (session()->get('active') == '1') { ?>
                                <a class="btn btn-primary1 btn-beli-product" href="/addcart/<?= $produk['id']; ?>" <?= (int)$produk['stok'] <= 0 ? "disabled" : ""; ?>>Beli Sekarang</a>
                                <?php if (in_array($produk['id'], session()->get('wishlist'))) { ?>
                                    <a class="btn btn-outline-dark" href="/delwishlist/<?= $produk['id']; ?>"><i class="material-icons">favorite</i></a>
                                <?php } else { ?>
                                    <a class="btn btn-outline-dark" href="/addwishlist/<?= $produk['id']; ?>"><i class="material-icons">favorite_border</i></a>
                                <?php } ?>
                            <?php } else { ?>
                                <a class="btn btn-primary1" href="/verify">Verifikasi Email</a>
                            <?php } ?>
                        <?php } else { ?>
                            <a class="btn btn-primary1" href="/editproduct/<?= $produk['id']; ?>">Edit produk</a>
                        <?php } ?>
                    <?php } else { ?>
                        <a class="btn btn-primary1" href="/login">Masuk untuk membeli</a>
                    <?php } ?>
                </div>
                <div class="hide-ke-show-flex justify-content-center align-items-center p-2 gap-1" style="background-color: white; position:fixed; bottom: 0; left: 0; width: 100vw; z-index: 9; box-shadow: 0 0 10px rgba(0,0,0,0.5);">
                    <?php if (session()->get('isLogin')) { ?>
                        <?php if (session()->get('role') == 0) { ?>
                            <?php if (session()->get('active') == '1') { ?>
                                <a class="btn btn-primary1 flex-grow-1 btn-beli-product" href="/addcart/<?= $produk['id']; ?>" <?= (int)$produk['stok'] <= 0 ? "disabled" : ""; ?>>Beli Sekarang</a>
                                <?php if (in_array($produk['id'], session()->get('wishlist'))) { ?>
                                    <a class="btn btn-outline-dark" href="/delwishlist/<?= $produk['id']; ?>"><i class="material-icons">favorite</i></a>
                                <?php } else { ?>
                                    <a class="btn btn-outline-dark" href="/addwishlist/<?= $produk['id']; ?>"><i class="material-icons">favorite_border</i></a>
                                <?php } ?>
                            <?php } else { ?>
                                <a class="btn btn-primary1 flex-grow-1" href="/verify">Verifikasi Email</a>
                            <?php } ?>
                        <?php } else { ?>
                            <a class="btn btn-primary1 flex-grow-1" href="/editproduct/<?= $produk['id']; ?>">Edit produk</a>
                        <?php } ?>
                    <?php } else { ?>
                        <a class="btn btn-primary1 flex-grow-1" href="/login">Masuk untuk membeli</a>
                    <?php } ?>
                </div>


                <div class="mt-2">
                    <p class="mb-1">
                        <?php if ($produk['tokped'] || $produk['shopee'] || $produk['tiktok']) { ?>
                            Produk ini juga tersedia di
                        <?php } else { ?>
                            Lihat produk kami lainnya di
                        <?php } ?>
                    </p>
                    <div>
                        <a href="<?= $produk['tokped'] ? $produk['tokped'] : 'https://www.tokopedia.com/jasminefurnitureofc'; ?>" title="Tokopedia" target="blank"><img src="/img/logo/tokopedia.webp" class="marketplace"></a>
                        <a href="<?= $produk['shopee'] ? $produk['shopee'] : 'https://shopee.co.id/jasminefurniture123'; ?>" title="Shopee" target="blank"><img src="/img/logo/shopee.webp" class="marketplace"></a>
                        <!-- <a href="<?= $produk['tiktok']; ?>" title="Tiktok" target="blank"><img src="/img/logo/tiktokshop.webp" class="marketplace"></a> -->
                    </div>
                </div>

                <?php if ($produk['youtube']) { ?>
                    <span class="garis my-3"></span>
                    <a href="<?= $produk['youtube']; ?>" class="btn btn-light d-flex gap-2" style="width: fit-content;">
                        <p class="mb-0">Lihat Video Perakitan</p><i class="material-icons">chevron_right</i>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <h5 class="jdl-section">Produk serupa</h5>
        <div class="card-group1 no-scroll">
            <?php foreach ($produksekategori as $p) { ?>
                <a class="card1" href="/product/<?= urlencode($p['nama']); ?>">
                    <?php if ($p['diskon']) { ?>
                        <p class="diskon">-<?= $p['diskon']; ?>%</p>
                    <?php } ?>
                    <img src="data:image/webp;base64,<?= base64_encode($p['gambar']); ?>" alt="">
                    <div class="mt-3">
                        <h5 class="mb-0"><?= $p['nama']; ?></h5>
                        <?php if ($p['diskon']) { ?>
                            <p class="mb-0 harga d-inline">Rp
                                <?php
                                $persen = (100 - $p['diskon']) / 100;
                                $hasil = $persen * $p['harga'];
                                echo number_format($hasil, 0, ",", ".");
                                ?></p>
                            <p class="mb-0 d-inline" style="text-decoration: line-through; font-size: small; color: grey;">Rp
                                <?= number_format($p['harga'], 0, ",", "."); ?></p>
                        <?php } else { ?>
                            <p class="mb-0 harga">Rp <?= number_format($p['harga'], 0, ",", "."); ?></p>
                        <?php } ?>
                        <!-- <p>★★★☆☆ (<?= $p['rate']; ?>)</p> -->
                    </div>
                </a>
            <?php } ?>
        </div>
    </div>
</div>
<script>
    const elmVarianSelect = document.querySelectorAll(".btn-check")
    const imgProdukSelect = document.querySelectorAll(".img-produk-select")
    const imgProdukPrev = document.querySelector(".img-produk-prev")
    const imgProdukPrevImg = document.querySelector("img.img-produk-prev")
    const elmVarian = document.getElementById('varian-group')
    const elmBtnBeli = document.querySelectorAll('.btn-beli-product')
    const jmlVarian = "<?= $produk['jml_varian'] ?>";
    const idProduk = "<?= $produk['id'] ?>";

    if (imgProdukSelect.length > 0) {
        imgProdukSelect.forEach((element, index) => {
            element.addEventListener("click", () => {
                console.log(index, jmlVarian);
                imgProdukSelect.forEach(e => e.classList.remove("selected"));
                element.classList.add("selected");
                imgProdukPrev.style = "background-image: url('" + element.childNodes[0].src + "')";
                imgProdukPrevImg.src = element.childNodes[0].src;
                // const hitungBagi4 = Math.floor(index / Number(jmlVarian));
                elmVarianSelect.forEach(e => e.checked = false);
                if (index >= Number(jmlVarian)) {
                    elmVarianSelect[index - Number(jmlVarian) + 1].checked = true
                    console.log(`index elmVarianSelect: ${index - Number(jmlVarian) - 1}`)
                } else {
                    elmVarianSelect[0].checked = true
                    console.log(`index elmVarianSelect: 0`)
                }
                setUrlElmBeli()
            })
        });
    }
    elmVarian.addEventListener("change", (e) => {
        imgProdukSelect.forEach(e => e.classList.remove("selected"));
        if (e.target.value == '0') {
            imgProdukSelect[0].classList.add("selected")
            imgProdukPrev.style = "background-image: url('" + imgProdukSelect[0].childNodes[0].src + "')"
            imgProdukPrevImg.src = imgProdukSelect[0].childNodes[0].src
        } else {
            imgProdukSelect[Number(e.target.value) + Number(jmlVarian) - 1].classList.add("selected")
            imgProdukPrev.style = "background-image: url('" + imgProdukSelect[Number(e.target.value) + Number(jmlVarian) - 1].childNodes[0].src + "')"
            imgProdukPrevImg.src = imgProdukSelect[Number(e.target.value) + Number(jmlVarian) - 1].childNodes[0].src
        }
        // imgProdukSelect[Number(e.target.value) * Number(jmlVarian)].classList.add("selected");
        setUrlElmBeli()
    });

    function zoom(e) {
        e.target.style.backgroundSize = "auto"
        const widthGambar = e.target.offsetWidth;
        const gmbrPosition = [
            (e.offsetX / widthGambar) * 100,
            (e.offsetY / widthGambar) * 100,
        ];
        // console.log("X: " + (e.offsetX / widthGambar) * 100);
        // console.log("Y: " + (e.offsetY / widthGambar) * 100);
        e.target.style.backgroundPosition =
            gmbrPosition[0] + "% " + gmbrPosition[1] + "%";
    }

    function mouseoff(e) {
        e.target.style.backgroundSize = "cover"
    }

    function setUrlElmBeli() {
        let elmSelected;
        elmVarianSelect.forEach((e) => {
            if (e.checked) elmSelected = e.value
        })
        const varians = "<?php
                            foreach ($varian as $i => $v) {
                                echo $v;
                                if ($i < (count($varian) - 1)) echo ",";
                            }
                            ?>";
        const varianArray = varians.split(",")
        // const indexGambar = Number(elmSelected) * Number(jmlVarian)
        const indexGambar = Number(jmlVarian) + Number(elmSelected) - 1;
        console.log(varians, varianArray, indexGambar)
        console.log("/addcart/" + idProduk + "/" + varianArray[Number(elmSelected)] + "/" + indexGambar)
        elmBtnBeli.forEach(element => {
            element.href = "/addcart/" + idProduk + "/" + varianArray[Number(elmSelected)] + "/" + indexGambar;
        });
    }
    setUrlElmBeli()
</script>
<?= $this->endSection(); ?>