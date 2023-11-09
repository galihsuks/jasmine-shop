<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<div class="konten">
    <div class="container">
        <div class="row">
            <div class="col-3">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Profileku</li>
                    <li class="list-group-item">Buku Alamat</li>
                    <li class="list-group-item">Pilih Pembayaran</li>
                    <li class="list-group-item">Pesananku</li>
                    <li class="list-group-item">Wishlist</li>
                    <li class="list-group-item"><a class="btn btn-outline-danger" href="/keluar">Keluar</a></li>
                </ul>
            </div>
            <div class="col">
                <div class="p-2">
                    <h3>Edit Profilemu</h3>
                    <form class="row g-3">
                        <div class="col-md-6">
                            <label for="inputEmail4" class="form-label">Email</label>
                            <input type="email" class="form-control" id="inputEmail4">
                        </div>
                        <div class="col-md-6">
                            <label for="inputPassword4" class="form-label">Sandi</label>
                            <input type="password" class="form-control" id="inputPassword4">
                        </div>
                        <div class="col-12">
                            <label for="inputAddress" class="form-label">Alamat</label>
                            <input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-danger">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>