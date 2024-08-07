<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\GambarBarangModel;
use App\Models\ArtikelModel;

class ApiCompany extends BaseController
{
    protected $barangModel;
    protected $gambarBarangModel;
    protected $artikelModel;
    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->gambarBarangModel = new GambarBarangModel();
        $this->artikelModel = new ArtikelModel();
    }
    public function index()
    {
        $arr = [
            'Pesan' => 'Ini API Produk Company Jasmine Furniture'
        ];
        return $this->response->setJSON($arr, false);
    }
    public function formatting($p)
    {
        return [
            'id' => $p['id'],
            'nama' => $p['nama'],
            'harga' => $p['harga'],
            'gambar' =>
            "https://jasminefurniture.store/apicomp/getgambarbarang/" . $p['id'],
            'rate' => $p['rate'],
            'stok' => $p['stok'],
            'deskripsi' => $p['deskripsi'],
            'kategori' => $p['kategori'],
            'subkategori' => $p['subkategori'],
            'diskon' => $p['diskon'],
            'berat' => $p['berat'],
            'dimensi' => $p['dimensi'],
            'varian' => $p['varian'],
            'jml_varian' => $p['jml_varian'],
        ];
    }
    public function getAllBarang($page = 1)
    {
        $produk = $this->barangModel->getBarangPage($page);
        $produkAll = $this->barangModel->getBarang();
        $formatting = array_map("self::formatting", $produk);
        $arr = [
            'pesan' => 'Ok',
            'data' => $formatting,
            'panjang' => count($produkAll)
        ];
        return $this->response->setJSON($arr, false);
    }
    public function barang($id)
    {
        $produk = $this->barangModel->getBarang($id);
        $formatting = [
            'id' => $produk['id'],
            'nama' => $produk['nama'],
            'harga' => $produk['harga'],
            'gambar' =>
            "https://jasminefurniture.store/apicomp/getgambarbarang/" . $produk['id'],
            'rate' => $produk['rate'],
            'stok' => $produk['stok'],
            'deskripsi' => $produk['deskripsi'],
            'kategori' => $produk['kategori'],
            'subkategori' => $produk['subkategori'],
            'diskon' => $produk['diskon'],
            'berat' => $produk['berat'],
            'dimensi' => $produk['dimensi'],
            'varian' => $produk['varian'],
            'jml_varian' => $produk['jml_varian'],
        ];
        $arr = [
            'pesan' => 'Ok',
            'data' => $formatting
        ];
        return $this->response->setJSON($arr, false);
    }
    public function kategori($kategori, $page = 1)
    {
        $hitungPag = 20 * ($page - 1);
        $produk = $this->barangModel->where('kategori', $kategori)->orderBy('nama', 'asc')->findAll(20, $hitungPag);
        $produkAll = $this->barangModel->where('kategori', $kategori)->orderBy('nama', 'asc')->findAll();
        $formatting = array_map("self::formatting", $produk);
        $arr = [
            'pesan' => 'Ok',
            'data' => $formatting,
            'panjang' => count($produkAll)
        ];
        return $this->response->setJSON($arr, false);
    }
    public function subkategori($subkategori, $page = 1)
    {
        $hitungPag = 20 * ($page - 1);
        $produk = $this->barangModel->where('subkategori', $subkategori)->orderBy('nama', 'asc')->findAll(20, $hitungPag);
        $produkAll = $this->barangModel->where('subkategori', $subkategori)->orderBy('nama', 'asc')->findAll();
        $formatting = array_map("self::formatting", $produk);
        $arr = [
            'pesan' => 'Ok',
            'data' => $formatting,
            'panjang' => count($produkAll)
        ];
        return $this->response->setJSON($arr, false);
    }
    public function cari($page, $cari)
    {
        // $bodyJson = $this->request->getBody();
        // $body = json_decode($bodyJson, true);
        $hitungPag = 20 * ($page - 1);
        $produk = $this->barangModel->like("nama", $cari, "both")->orderBy('nama', 'asc')->findAll(20, $hitungPag);
        $produkAll = $this->barangModel->like("nama", $cari, "both")->orderBy('nama', 'asc')->findAll();
        $formatting = array_map("self::formatting", $produk);
        $arr = [
            'pesan' => 'Ok',
            'data' => $formatting,
            'panjang' => count($produkAll)
        ];
        return $this->response->setJSON($arr, false);
    }
    public function gambar($id)
    {
        $gambar = $this->gambarBarangModel->where('id', $id)->first();
        $formatting = [];
        for ($i = 1; $i <= 50; $i++) {
            if ($gambar["gambar" . $i] != null)
                $formatting["gambar" . $i] = "https://jasminefurniture.store/apicomp/getgambar/" . $id . "/" . $i;
        }
        $arr = [
            'pesan' => 'Ok',
            'data' => $formatting
        ];
        return $this->response->setJSON($arr, false);
    }

    //------------- Return Gambar -------------//
    public function getGambarBarang($id_barang)
    {
        $gambar = $this->barangModel->getBarang($id_barang)['gambar'];
        $this->response->setHeader("Content-Type", "image/webp");
        echo $gambar;
    }
    public function getGambar($id_barang, $urutan)
    {
        $gambar = $this->gambarBarangModel->getGambar($id_barang)['gambar' . $urutan];
        $this->response->setHeader("Content-Type", "image/webp");
        echo $gambar;
    }

    //-------------- olah database ------------- //
    public function deskToLuna()
    {
        $semuaProduk = $this->barangModel->findAll();
        foreach ($semuaProduk as $produk) {
            $deksripsiBaru = str_ireplace("<br>", "", $produk['deskripsi']);
            $deksripsiBaru = str_ireplace("<p>Spesifikasi produk:", '<h5>Spesifikasi Produk:</h5><ul class="mb-3"><li>', $produk['deskripsi']);
            $deksripsiBaru = str_ireplace("Finishing:", '</li><li>Finishing :', $produk['deskripsi']);
            $deksripsiBaru = str_ireplace('<p>Berat produk dan kemasan:', '', $produk['deskripsi']);
            $deksripsiBaru = str_ireplace('', '', $produk['deskripsi']);
            $deksripsiBaru = str_ireplace('', '', $produk['deskripsi']);
            $deksripsiBaru = str_ireplace('', '', $produk['deskripsi']);
            $deksripsiBaru = str_ireplace('', '', $produk['deskripsi']);
            $deksripsiBaru = str_ireplace('', '', $produk['deskripsi']);
            $this->barangModel->where(['id' => $produk['id']])->set(['deskripsi' => $deksripsiBaru])->update();
        }
        return $this->response->setJSON([
            'success' => true
        ], false);
    }

    public function isiPath()
    {
        //produk
        $produk = $this->barangModel->findAll();
        foreach ($produk as $p) {
            $path = str_replace("- ", "", $p['nama']);
            $path = str_replace(".", "", $path);
            $path = str_replace(" ", "-", $path);
            $path = strtolower($path);
            $this->barangModel->where(['id' => $p['id']])->set([
                'path' => $path
            ])->update();
        }
        return $this->response->setJSON([
            'success' => true
        ], false);

        //artikel
        $artikel = $this->artikelModel->findAll();
        foreach ($artikel as $a) {
            $path = str_replace(",", "", $a['judul']);
            $path = str_replace(".", "", $path);
            $path = str_replace("& ", "", $path);
            $path = str_replace("?", "", $path);
            $path = str_replace(" ", "-", $path);
            $path = strtolower($path);
            $this->artikelModel->where(['id' => $a['id']])->set([
                'path' => $path
            ])->update();
        }
        return $this->response->setJSON([
            'success' => true
        ], false);
    }
}
