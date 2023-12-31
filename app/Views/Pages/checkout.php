<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<div id="container-pilih-kurir">
    <div class="container-pilih-kurir">
        <h5>Pilih Kurir</h5>
        <ul class="nav nav-tabs mb-2" id="tabs">
            <!-- <li class="nav-item">
                <a class="nav-link active">Active</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
            </li> -->
        </ul>
        <div id="pilih-kurir" class="d-flex flex-column gap-2">
            <!-- <div class="tombol-pilih-kurir">
                <div class="d-flex align-items-center gap-4">
                    <div class="parent-img">
                        <img src="img/kurir/jnt.png" />
                    </div>
                    <p class="mb-0">J&T Same Day<br>Estimasi Pengiriman 1-4 Hari<br>Rp 120.000</p>
                </div>
            </div>
            <div class="tombol-pilih-kurir">
                <div class="d-flex align-items-center gap-4">
                    <div class="parent-img">
                        <img src="img/kurir/jne.png" />
                    </div>
                    <p class="mb-0">JNE Same Day<br>Estimasi Pengiriman 1-4 Hari<br>Rp 120.000</p>
                </div>
            </div>
            <div class="tombol-pilih-kurir">
                <div class="d-flex align-items-center gap-4">
                    <div class="parent-img">
                        <img src="img/kurir/wahana.png" />
                    </div>
                    <p class="mb-0">Wahana Same Day<br>Estimasi Pengiriman 1-4 Hari<br>Rp 120.000</p>
                </div>
            </div>
            <div class="tombol-pilih-kurir">
                <div class="d-flex align-items-center gap-4">
                    <div class="parent-img">
                        <img src="img/kurir/dakota.png" class="dakota" />
                    </div>
                    <p class="mb-0">Dakota Same Day<br>Estimasi Pengiriman 1-4 Hari<br>Rp 120.000</p>
                </div>
            </div> -->
        </div>
    </div>
</div>
<div class="konten">
    <div class="container">
        <h1>Detail Pembayaran</h1>
        <div class="baris-ke-kolom gap-5 mt-3">
            <div id="form-checkout" class="limapuluh-ke-seratus">
                <div class="form-floating mb-1">
                    <input type="text" class="form-control" placeholder="Email" name="nama" required>
                    <label for="floatingInput">Nama Lengkap</label>
                </div>
                <div class="form-floating mb-1">
                    <input type="email" class="form-control" placeholder="Email" name="email" required value="<?= $user['email']; ?>">
                    <label for="floatingInput">Email</label>
                </div>
                <div class="form-floating mb-1">
                    <input type="text" class="form-control" placeholder="Alamat Lengkap" name="alamat" required value="<?= $user['alamat']; ?>">
                    <label for="floatingPassword">Alamat Lengkap</label>
                </div>
                <div class="form-floating mb-1">
                    <input type="number" class="form-control" placeholder="Nomor Handphone" name="phone" required>
                    <label for="floatingInput">No. HP</label>
                </div>
                <div class="form-floating mb-1">
                    <select class="form-select" aria-label="Default select example" name="provinsi">
                        <option selected value="-1">-- Pilih provinsi --</option>
                        <?php foreach ($provinsi as $p) { ?>
                            <option value="<?= $p['province_id']; ?>"><?= $p['province']; ?></option>
                        <?php } ?>
                    </select>
                    <label for="floatingProvinsi">Provinsi</label>
                </div>
                <div class="form-floating mb-1">
                    <select class="form-select" aria-label="Default select example" name="kota">
                        <option value="-1">-- Pilih kota --</option>
                    </select>
                    <label for="floatingProvinsi">Kota</label>
                </div>
                <div class="form-floating mb-1">
                    <select class="form-select" aria-label="Default select example" name="area">
                        <option value="-1">-- Pilih area --</option>
                    </select>
                    <label for="floatingProvinsi">Area</label>
                </div>
                <div class="tombol-pilih-kurir" id="btn-pilih-kurir" onclick="pilihKurir()">
                    <p class="mb-0">Pilih Kurir</p>
                    <span><i class="material-icons">chevron_right</i></span>
                </div>
                <div class="form-floating mb-1 d-none">
                    <select class="form-select" aria-label="Default select example" name="ekspedisi">
                        <option value="pos">Pos Indonesia</option>
                        <option value="tiki">TIKI</option>
                        <option value="jne" selected>JNE</option>
                    </select>
                    <label for="floatingProvinsi">Ekspedisi</label>
                </div>
                <div class="form-floating mb-1 d-none">
                    <select class="form-select" aria-label="Default select example" name="paket">
                        <option value="-1">-- Pilih paket --</option>
                    </select>
                    <label for="floatingProvinsi">Paket</label>
                </div>
            </div>
            <div class="limapuluh-ke-seratus">
                <div>
                    <table class="table table-borderless">
                        <tbody>
                            <?php foreach ($produk as $index => $p) { ?>
                                <tr>
                                    <td><?= $p['nama'] . " (" . $keranjang[$index]['varian'] . ")"; ?></td>
                                    <td><?= $jumlah[$index]; ?></td>
                                    <td class="text-end">Rp
                                        <?php
                                        if ($p['diskon']) {
                                            $persen = (100 - $p['diskon']) / 100;
                                            $hasil = $persen * $p['harga'];
                                            echo number_format($hasil, 0, ",", ".");
                                        } else {
                                            $hasil = $p['harga'];
                                            echo number_format($p['harga'], 0, ",", ".");
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between border-bottom border-top mt-3" style="gap: 10em;">
                    <p class="my-2">Subtotal:</p>
                    <p class="my-2"><b>Rp <?= number_format(session()->get('subtotal'), 0, ",", "."); ?></b></p>
                </div>
                <div class="d-flex justify-content-between border-bottom" style="gap: 10em;">
                    <p class="my-2">Biaya Admin:</p>
                    <p class="my-2"><b>Rp 5.000</b></p>
                </div>
                <div class="d-flex justify-content-between border-bottom" style="gap: 10em;">
                    <p class="my-2">Pengiriman:</p>
                    <p class="my-2"><b id="total-pengiriman">-</b></p>
                </div>
                <div class="d-flex justify-content-between border-bottom" style="gap: 10em;">
                    <p class="my-2">Total:</p>
                    <p class="my-2"><b id="total-semua">Rp
                            <?= number_format($total, 0, ",", "."); ?></b>
                    </p>
                </div>
                <div class="d-flex justify-content-between" style="gap: 10em;">
                    <p class="my-2">Berat:</p>
                    <p class="my-2"><b id="total-semua"><?= $berat; ?> gram</b>
                    </p>
                </div>
                <button id="btn-checkout" class="btn btn-primary1" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Pastikan seluruh detail pembayaran telah diisi">Pesan</button>
            </div>
        </div>
    </div>
</div>
<script>
    const provElm = document.querySelector('select[name="provinsi"]');
    const kotaElm = document.querySelector('select[name="kota"]');
    const ekspedisiElm = document.querySelector('select[name="ekspedisi"]');
    const paketElm = document.querySelector('select[name="paket"]');
    const areaElm = document.querySelector('select[name="area"]');
    const costElm = document.getElementById("total-pengiriman");
    const totalElm = document.getElementById("total-semua");
    const containerPilihKurir = document.getElementById("container-pilih-kurir");
    const pilihKurirElm = document.getElementById("pilih-kurir");
    const tabsElm = document.getElementById("tabs");
    const btnPilihKurirElm = document.getElementById("btn-pilih-kurir");
    const subtotal = "<?= session()->get('subtotal'); ?>";
    const beratTotal = Number("<?= $berat; ?>");
    let hasilApiKurir = [];

    async function getKota(idprov) {
        const response = await fetch("getkota/" + idprov);
        const kota = await response.json();
        const hasil = kota.rajaongkir.results;
        console.log(hasil)
        kotaElm.innerHTML = '<option value="-1">-- Pilih kota --</option>';
        hasil.forEach(element => {
            const optElm = document.createElement("option");
            optElm.value = element.city_id + "-" + element.city_name
            optElm.innerHTML = element.city_name
            kotaElm.appendChild(optElm);
        });
    }
    async function getArea(kota) {
        const response = await fetch("getarea/" + kota);
        const result = await response.json();
        const hasil = result.areas;
        console.log(hasil)
        areaElm.innerHTML = '<option value="-1">-- Pilih area --</option>';
        hasil.forEach(element => {
            const optElm = document.createElement("option");
            optElm.value = element.id;
            optElm.innerHTML = element.name
            areaElm.appendChild(optElm);
        });
    }
    async function getPaket(asal, tujuan, berat, kurir) {
        console.log("getpaket/" + asal + "/" + tujuan + "/" + berat + "/" + kurir)
        const response = await fetch("getpaket/" + asal + "/" + tujuan + "/" + berat + "/" + kurir);
        const paket = await response.json();
        const hasil = paket.rajaongkir.results[0].costs;
        paketElm.innerHTML = '<option value="-1">-- Pilih paket --</option>';
        hasil.forEach(element => {
            const optElm = document.createElement("option");
            optElm.value = element.cost[0].value
            optElm.innerHTML =
                `${element.description} | Rp ${element.cost[0].value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}`
            paketElm.appendChild(optElm);
        });
    }
    provElm.addEventListener("change", (e) => {
        kotaElm.innerHTML = '<option value="-1">Loading..</option>'
        paketElm.innerHTML = '<option value="-1">-- Pilih paket --</option>';
        costElm.innerHTML = '-'
        const idprov = Number(e.target.value)
        if (idprov > 0)
            getKota(idprov)
    })
    kotaElm.addEventListener("change", (e) => {
        paketElm.innerHTML = '<option value="-1">Loading..</option>'
        areaElm.innerHTML = '<option value="-1">Loading..</option>'
        costElm.innerHTML = '-'
        const value = e.target.value.split("-")
        const idkota = Number(value[0])
        const ekspedisi = ekspedisiElm.value;
        if (idkota > 0) {
            getPaket("399", idkota, beratTotal, ekspedisi) //399 adalah id kota semarang
            getArea(value[1])
        }
    })
    ekspedisiElm.addEventListener("change", (e) => {
        paketElm.innerHTML = '<option value="-1">Loading..</option>'
        costElm.innerHTML = '-'
        const idkota = kotaElm.value;
        const ekspedisi = e.target.value;
        if (idkota > 0)
            getPaket("399", idkota, beratTotal, ekspedisi) //399 adalah id kota semarang
    })
    paketElm.addEventListener("change", (e) => {
        costElm.innerHTML = `Rp ${e.target.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}`;
        totalElm.innerHTML =
            `Rp ${(5000 + Number(e.target.value) + Number(subtotal)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}`
    })

    function pilihKurir() {
        if (hasilApiKurir.length > 0) {
            const hasilApi = hasilApiKurir
            // const hasilApi = [{
            //         available_collection_method: [
            //             "pickup"
            //         ],
            //         available_for_cash_on_delivery: true,
            //         available_for_proof_of_delivery: false,
            //         available_for_instant_waybill_id: true,
            //         available_for_insurance: true,
            //         company: "jne",
            //         courier_name: "JNE",
            //         courier_code: "jne",
            //         courier_service_name: "Reguler",
            //         courier_service_code: "reg",
            //         description: "Layanan reguler",
            //         duration: "1 - 2 days",
            //         shipment_duration_range: "1 - 2",
            //         shipment_duration_unit: "days",
            //         service_type: "standard",
            //         shipping_type: "parcel",
            //         price: 40000,
            //         type: "reg"
            //     },
            //     {
            //         available_collection_method: [
            //             "pickup"
            //         ],
            //         available_for_cash_on_delivery: false,
            //         available_for_proof_of_delivery: false,
            //         available_for_instant_waybill_id: true,
            //         available_for_insurance: true,
            //         company: "jne",
            //         courier_name: "JNE",
            //         courier_code: "jne",
            //         courier_service_name: "Yakin Esok Sampai (YES)",
            //         courier_service_code: "yes",
            //         description: "Yakin esok sampai",
            //         duration: "1 - 1 days",
            //         shipment_duration_range: "1 - 1",
            //         shipment_duration_unit: "days",
            //         service_type: "overnight",
            //         shipping_type: "parcel",
            //         price: 72000,
            //         type: "yes"
            //     },
            //     {
            //         available_collection_method: [
            //             "pickup"
            //         ],
            //         available_for_cash_on_delivery: false,
            //         available_for_proof_of_delivery: false,
            //         available_for_instant_waybill_id: true,
            //         available_for_insurance: true,
            //         company: "jnt",
            //         courier_name: "J&T",
            //         courier_code: "jnt",
            //         courier_service_name: "EZ",
            //         courier_service_code: "ez",
            //         description: "Layanan reguler",
            //         duration: "2 - 3 days",
            //         shipment_duration_range: "2 - 3",
            //         shipment_duration_unit: "days",
            //         price: 32000,
            //         service_type: "standard",
            //         shipping_type: "parcel",
            //         type: "ez"
            //     }
            // ]
            console.log(hasilApi);
            let cekTabs = []
            tabsElm.innerHTML = "";
            hasilApi.forEach((element, ind) => {
                const liElm = document.createElement("li");
                liElm.classList.add("nav-item");
                const aElm = document.createElement("a")
                aElm.classList.add("nav-link");
                aElm.classList.add("text-dark");
                if (ind == 0) {
                    aElm.classList.add("active");
                    tampilkanPilihanKurir(element.service_type, hasilApi);
                }
                aElm.innerHTML = element.service_type;
                liElm.appendChild(aElm);
                if (!cekTabs.includes(element.service_type)) {
                    tabsElm.appendChild(liElm);
                    cekTabs.push(element.service_type);
                    liElm.addEventListener("click", (e) => {
                        removeActiveTabs();
                        e.target.classList.add("active")
                        tampilkanPilihanKurir(element.service_type, hasilApi)
                    })
                }
            })
            containerPilihKurir.style.display = "flex";
        }
    }

    function removeActiveTabs() {
        const seluruhTabsElm = document.querySelectorAll('a[class="nav-link"]');
        console.log(seluruhTabsElm);
        seluruhTabsElm.forEach((elmT) => elmT.classList.remove("active"));
    }

    function tampilkanPilihanKurir(serviceType, hasilApi) {
        const filterHasilApi = hasilApi.filter((elm) => {
            if (elm.service_type == serviceType) return true;
            else return false;
        }).map((e) => {
            return e
        });
        console.log(filterHasilApi);
        pilihKurirElm.innerHTML = ""
        filterHasilApi.forEach((elm) => {
            const tmbPilihElm = document.createElement("div");
            tmbPilihElm.classList.add("tombol-pilih-kurir");
            const tmbPilihElmChild = document.createElement("div")
            tmbPilihElmChild.setAttribute("class", "d-flex align-items-center gap-4");
            const parentImgElm = document.createElement("div");
            parentImgElm.classList.add("parent-img");
            const keteranganElm = document.createElement("p");
            keteranganElm.classList.add("mb-0");
            keteranganElm.innerHTML = `${elm.courier_name} ${elm.description}<br>Estimasi Pengiriman ${elm.duration}<br>Rp ${elm.price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}`
            const imgElm = document.createElement("img");
            imgElm.src = `img/kurir/${elm.courier_code}.png`;
            parentImgElm.appendChild(imgElm)
            tmbPilihElmChild.appendChild(parentImgElm)
            tmbPilihElmChild.appendChild(keteranganElm)
            tmbPilihElm.appendChild(tmbPilihElmChild);
            pilihKurirElm.appendChild(tmbPilihElm);
            tmbPilihElm.addEventListener("click", () => {
                //     <div class="d-flex align-items-center gap-3">
                //         <img src="img/kurir/jnt.png" />
                //         <p class="mb-0">J&T Same Day<br>Estimasi Pengiriman 1-4 Hari<br>Rp 120.000</p>
                //     </div>
                //     <span><i class="material-icons">chevron_right</i></span>
                btnPilihKurirElm.innerHTML = "";
                const divElmPK = document.createElement("div");
                divElmPK.setAttribute("class", "d-flex align-items-center gap-3");
                const imgElmPK = document.createElement("img")
                imgElmPK.src = `img/kurir/${elm.courier_code}.png`;
                const keteranganElmPK = document.createElement("p");
                keteranganElmPK.classList.add("mb-0");
                keteranganElmPK.innerHTML = `${elm.courier_name} ${elm.description}<br>Estimasi Pengiriman ${elm.duration}<br>Rp ${elm.price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}`
                const iconElmPK = document.createElement("span");
                iconElmPK.innerHTML = '<i class="material-icons">chevron_right</i>';
                divElmPK.appendChild(imgElmPK)
                divElmPK.appendChild(keteranganElmPK)
                btnPilihKurirElm.appendChild(divElmPK)
                btnPilihKurirElm.appendChild(iconElmPK);

                costElm.innerHTML = `Rp ${elm.price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}`;
                totalElm.innerHTML =
                    `Rp ${(5000 + Number(elm.price) + Number(subtotal)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}`

                containerPilihKurir.style.display = "none";
            })
        })
    }

    areaElm.addEventListener("change", (e) => {
        const id_area = e.target.value;
        const bodynya = {
            origin_area_id: "IDNP10IDNC393IDND4705IDZ50122", //id kota semarang (tpi belum fix bener ato nggk postal codenya)
            destination_area_id: id_area,
            couriers: "jne,jnt,wahana",
            items: [{
                name: "Shoes",
                description: "Black colored size 45",
                value: 199000,
                length: 30,
                width: 15,
                height: 20,
                weight: 200,
                quantity: 2
            }]
        }
        console.log(bodynya)
    })
</script>
<?= $this->endSection(); ?>