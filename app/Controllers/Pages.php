<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\GambarBarangModel;
use App\Models\PembeliModel;
use App\Models\PemesananModel;
use App\Models\UserModel;
use App\Models\FormModel;
use App\Models\ArtikelModel;
use App\Models\GambarArtikelModel;

class Pages extends BaseController
{
    protected $barangModel;
    protected $gambarBarangModel;
    protected $userModel;
    protected $pembeliModel;
    protected $pemesananModel;
    protected $formModel;
    protected $artikelModel;
    protected $gambarArtikelModel;
    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->gambarBarangModel = new GambarBarangModel();
        $this->userModel = new UserModel();
        $this->pembeliModel = new PembeliModel();
        $this->pemesananModel = new PemesananModel();
        $this->formModel = new FormModel();
        $this->artikelModel = new ArtikelModel();
        $this->gambarArtikelModel = new GambarArtikelModel();
    }
    public function index()
    {
        $produk = $this->barangModel->getBarangLimit();
        $produkBaru = $this->barangModel->getBarangPopuler();
        $data = [
            'title' => 'Beranda',
            'produk' => $produk,
            'produkBaru' => $produkBaru,
        ];
        return view('pages/home', $data);
    }
    public function kebijakanprivasi()
    {
        $data = [
            'title' => 'Kebijakan Privasi',
        ];
        return view('pages/kebijakanprivasi', $data);
    }
    public function syaratdanketentuan()
    {
        $data = [
            'title' => 'Syarat dan Ketentuan',
        ];
        return view('pages/syaratdanketentuan', $data);
    }
    public function faq()
    {
        $data = [
            'title' => 'FAQ',
        ];
        return view('pages/faq', $data);
    }
    public function article($judul_article = false)
    {
        $artikel = $this->artikelModel->getArtikelJudul(urldecode($judul_article));
        $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        if (!$artikel) return redirect()->to('article');
        if ($judul_article) {
            $artikel['header'] = '/imgart/' . $artikel['id'];
            $artikel['isi'] = json_decode($artikel['isi'], true);
            $artikel['kategori'] = explode(",", $artikel['kategori']);
            $artikel['waktu'] = date("d", strtotime($artikel['waktu'])) . " " . $bulan[date("m", strtotime($artikel['waktu'])) - 1] . " " . date("Y", strtotime($artikel['waktu']));
            $data = [
                'title' => 'Artikel ' . $artikel['judul'],
                'artikel' => $artikel
            ];
            return view('pages/artikel', $data);
        } else {
            foreach ($artikel as $ind_a => $a) {
                $artikel[$ind_a]['header'] = '/imgart/' . $a['id'];
                $artikel[$ind_a]['isi'] = json_decode($a['isi'], true);
                $artikel[$ind_a]['kategori'] = explode(",", $a['kategori']);
                $artikel[$ind_a]['waktu'] = date("d", strtotime($a['waktu'])) . " " . $bulan[date("m", strtotime($a['waktu'])) - 1] . " " . date("Y", strtotime($a['waktu']));
            }
            $data = [
                'title' => 'Artikel',
                'artikel' => $artikel
            ];
            return view('pages/artikelAll', $data);
        }
    }
    public function articleCategory($kategori)
    {
        $artikel = $this->artikelModel->getArtikelKategori(str_replace("-", " ", $kategori));
        $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        if (!$artikel) return redirect()->to('article');
        foreach ($artikel as $ind_a => $a) {
            $artikel[$ind_a]['header'] = '/imgart/' . $a['id'];
            $artikel[$ind_a]['isi'] = json_decode($a['isi'], true);
            $artikel[$ind_a]['kategori'] = explode(",", $a['kategori']);
            $artikel[$ind_a]['waktu'] = date("d", strtotime($a['waktu'])) . " " . $bulan[date("m", strtotime($a['waktu'])) - 1] . " " . date("Y", strtotime($a['waktu']));
        }
        $data = [
            'title' => 'Artikel',
            'artikel' => $artikel
        ];
        return view('pages/artikelAll', $data);
    }
    public function addArticle()
    {
        $data = [
            'title' => 'Tambah Artikel',
        ];
        return view('pages/addArtikel', $data);
    }
    public function actionAddArticle()
    {
        $judul = $this->request->getVar('judul');
        $penulis = $this->request->getVar('penulis');
        $kategori = $this->request->getVar('kategori');
        $waktu = $this->request->getVar('waktu');
        $header = file_get_contents($this->request->getFile('header'));
        $counter = explode(",", $this->request->getVar('arrCounter'));

        $d = strtotime("+7 Hours");
        $id = "A" . date("YmdHis", $d);
        $insertGambarArtikel = ['id' => $id];

        $isi = [];
        $counterGambar = 0;
        foreach ($counter as $c) {
            $itemIsi = [];
            $tag = $this->request->getVar('tag' . $c);
            $itemIsi['tag'] = $tag;
            if ($tag == 'h2' || $tag == 'h4' || $tag == 'p') {
                $itemIsi['teks'] = $this->request->getVar('teks' . $c);
                $itemIsi['style'] = $this->request->getVar('style' . $c);
            } else if ($tag == 'a') {
                $itemIsi['link'] = $this->request->getVar('link' . $c);
                $itemIsi['teks'] = $this->request->getVar('teks' . $c);
                $itemIsi['style'] = $this->request->getVar('style' . $c);
            } else if ($tag == 'img') {
                $counterGambar++;
                $insertGambarArtikel["gambar" . $counterGambar] = file_get_contents($this->request->getFile('file' . $c));
                $itemIsi['src'] = "/imgart/" . $id . "/" . $counterGambar;
                $itemIsi['style'] = $this->request->getVar('style' . $c);
            }
            array_push($isi, $itemIsi);
        }

        $this->artikelModel->insert([
            'id' => $id,
            'judul' => $judul,
            'penulis' => $penulis,
            'kategori' => $kategori,
            'waktu' => $waktu,
            'isi' => json_encode($isi),
            'header' => $header,
        ]);
        $this->gambarArtikelModel->insert($insertGambarArtikel);

        session()->setFlashdata('msg', 'Artikel berhasil ditambahkan');
        return redirect()->to('/article/' . $id);
    }
    public function form()
    {
        $data = [
            'title' => 'Kontak Kami',
            'val' => [
                'msg' => session()->getFlashdata('val-msg')
            ]
        ];
        return view('pages/form', $data);
    }
    public function actionForm()
    {
        $nama = $this->request->getVar('nama');
        $nohp = $this->request->getVar('nohp');
        $alamat = $this->request->getVar('alamat');
        $pesan = $this->request->getVar('pesan');

        $email = \Config\Services::email();
        $email->setFrom('no-reply@jasminefurniture.com', 'Jasmine Furniture');
        $email->setTo('infojasmine@jasminefurniture.co.id');
        $email->setSubject('Jasmine Store - Formulir');
        $isiEmail = "<div>
            <h1>Pengisian Formulir</h1
            <p>Pesan :</p>
            <p>" . $pesan . "</p>
        </div>";
        $email->setMessage($isiEmail);
        $email->send();

        $this->formModel->insert([
            'nama' => $nama,
            'nohp' => $nohp,
            'alamat' => $alamat,
            'pesan' => $pesan,
        ]);
        session()->setFlashdata('form-thanks', true);
        return redirect()->to('/formthanks');
    }
    public function formThanks()
    {
        if (!session()->getFlashdata('form-thanks')) return redirect()->to('/form');
        $data = [
            'title' => 'Terima kasih atas pengisian Formulir',
        ];
        return view('pages/formThanks', $data);
    }
    public function all($subkategori = false)
    {
        $produk = $this->barangModel->where('subkategori', $subkategori)->orderBy('nama', 'asc')->findAll(20, 0);
        $semuaproduk = $this->barangModel->where('subkategori', $subkategori)->orderBy('nama', 'asc')->findAll();
        $data = [
            'title' => 'Semua Produk',
            'produk' => $produk,
            'kategori' => $subkategori,
            'page' => 1,
            'nama' => false,
            'semuaProduk' => $semuaproduk
        ];
        return view('pages/all', $data);
    }
    public function allPage($page, $subkategori = false)
    {
        $pagination = (int)$page;
        if ($pagination > 1) {
            $hitungOffset = 20 * ($pagination - 1);
            $produk = $this->barangModel->where('subkategori', $subkategori)->orderBy('nama', 'asc')->findAll(20, $hitungOffset);
        } else {
            $produk = $this->barangModel->where('subkategori', $subkategori)->orderBy('nama', 'asc')->findAll(20, 0);
        }
        $semuaproduk = $this->barangModel->where('subkategori', $subkategori)->orderBy('nama', 'asc')->findAll();
        $data = [
            'title' => 'Semua Produk',
            'produk' => $produk,
            'kategori' => $subkategori,
            'page' => $page,
            'nama' => false,
            'semuaProduk' => $semuaproduk
        ];
        return view('pages/all', $data);
    }
    public function signup()
    {
        $data = [
            'title' => 'Daftar',
            'val' => [
                'val_nama' => session()->getFlashdata('val-nama'),
                'val_email' => session()->getFlashdata('val-email'),
                'val_sandi' => session()->getFlashdata('val-sandi'),
                'val_nohp' => session()->getFlashdata('val-nohp'),
                'msg' => session()->getFlashdata('msg'),
                // 'val_alamat' => session()->getFlashdata('val-alamat'),
            ]
        ];
        return view('pages/signup', $data);
    }
    public function kirimOTP()
    {
        $emailUser = session()->get('email');
        $otp_number = rand(100000, 999999);
        $waktu_otp = time() + 300;
        $d = strtotime("+425 Minutes");
        $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        $waktu_otp_tanggal = date("d", $d) . " " . $bulan[date("m", $d) - 1] . " " . date("Y H:i:s", $d);

        $email = \Config\Services::email();
        $email->setFrom('no-reply@jasminefurniture.com', 'Jasmine Furniture');
        $email->setTo(session()->get('email'));
        $email->setSubject('Jasmine Store - Verifikasi OTP');
        $email->setMessage("<p>Berikut kode OTP verifikasi</p><h1>" . $otp_number . "</h1><p>Kode ini berlaku hingga " . $waktu_otp_tanggal . "</p>");
        $email->send();

        $this->userModel->where('email', $emailUser)->set([
            'otp' => $otp_number,
            'waktu_otp' => $waktu_otp
        ])->update();

        session()->setFlashdata('msg', "OTP telah dikirim ke email " . $emailUser . " dan berlaku hingga " . $waktu_otp_tanggal);
        return redirect()->to('/verify');
    }
    public function actionSignup()
    {
        session()->setFlashdata('msg', "Maaf, masih dalam masa perbaikan. Akan aktif kembali ketika pukul 07:30 WIB");
        return redirect()->to('/signup');
    }
    public function actionSignupBenar()
    {

        if (!$this->validate([
            'nama' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama lengkap harus diisi',
                ]
            ],
            'email' => [
                'rules' => 'required|is_unique[user.email]',
                'errors' => [
                    'required' => 'Email harus diisi',
                    'is_unique' => 'Email sudah terdaftar',
                ]
            ],
            'sandi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Sandi harus diisi'
                ]
            ],
            'nohp' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nomor handphone harus diisi'
                ]
            ],
        ])) {
            $validation = \Config\Services::validation();
            session()->setFlashdata('val-nama', $validation->getError('nama'));
            session()->setFlashdata('val-nohp', $validation->getError('nohp'));
            session()->setFlashdata('val-email', $validation->getError('email'));
            session()->setFlashdata('val-sandi', $validation->getError('sandi'));
            session()->setFlashdata('val-nohp', $validation->getError('nohp'));
            // session()->setFlashdata('val-alamat', $validation->getError('alamat'));
            return redirect()->to('/signup')->withInput();
        }

        $otp_number = rand(100000, 999999);
        $waktu_otp = time() + 300;
        $d = strtotime("+425 Minutes");
        $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        $waktu_otp_tanggal = date("d", $d) . " " . $bulan[date("m", $d) - 1] . " " . date("Y H:i:s", $d);

        $email = \Config\Services::email();
        $email->setFrom('no-reply@jasminefurniture.com', 'Jasmine Furniture');
        $email->setTo($this->request->getVar('email'));
        $email->setSubject('Jasmine Store - Verifikasi OTP');
        $email->setMessage("<p>Berikut kode OTP verifikasi</p><h1>" . $otp_number . "</h1><p>Kode ini berlaku hingga " . $waktu_otp_tanggal . "</p>");
        $email->send();

        $this->userModel->insert([
            'email' => $this->request->getVar('email'),
            'sandi' => password_hash($this->request->getVar('sandi'), PASSWORD_DEFAULT),
            'role' => '0',
            'otp' => $otp_number,
            'active' => '0',
            'waktu_otp' => $waktu_otp
        ]);
        $this->pembeliModel->insert([
            'nama' => $this->request->getVar('nama'),
            'email_user' => $this->request->getVar('email'),
            'nohp' => $this->request->getVar('nohp'),
            'alamat' => json_encode([]),
            'wishlist' => json_encode([]),
            'keranjang' => json_encode([]),
            'transaksi' => json_encode([]),
        ]);

        $emailUser = $this->request->getVar('email');
        $getUser = $this->userModel->getUser($emailUser);
        $ses_data = [
            'email' => $getUser['email'],
            'active' => '0',
            'isLogin' => true
        ];
        session()->set($ses_data);
        session()->setFlashdata('msg', "OTP telah dikirim ke email " . $emailUser . " dan berlaku hingga " . $waktu_otp_tanggal);
        return redirect()->to('/verify');
    }
    public function verify()
    {
        $data = [
            'title' => 'Verifikasi',
            'val' => [
                'msg' => session()->getFlashdata('msg'),
                'val_verify' => session()->getFlashdata('val_verify')
            ]
        ];
        return view('pages/verify', $data);
    }
    public function actionVerify()
    {
        $otp = $this->request->getVar("otp");
        $email = session()->get("email");
        $getUser = $this->userModel->getUser($email);
        if ($otp != $getUser['otp']) {
            session()->setFlashdata('val_verify', "OTP salah");
            return redirect()->to("/verify");
        }
        $waktu_otp = time();
        if ($waktu_otp > (int)$getUser['waktu_otp']) {
            session()->setFlashdata('msg', "OTP telah berakhir. Minta kirim ulang<br>dibawah untuk mendapatkan kembali");
            return redirect()->to("/verify");
        }

        $getPembeli = $this->pembeliModel->getPembeli($email);
        $ses_data = [
            'active' => '1',
            'role' => $getUser['role'],
            'nama' => $getPembeli['nama'],
            'alamat' => json_decode($getPembeli['alamat'], true),
            'nohp' => $getPembeli['nohp'],
            'wishlist' => json_decode($getPembeli['wishlist'], true),
            'keranjang' => json_decode($getPembeli['keranjang'], true),
            'transaksi' => json_decode($getPembeli['transaksi'], true)
        ];
        $this->userModel->where('email', $email)->set([
            'active' => '1',
            'otp' => '0',
            'waktu_otp' => '0'
        ])->update();
        session()->set($ses_data);
        return redirect()->to(site_url('/'));
    }
    public function login()
    {
        $data = [
            'title' => 'Masuk',
            'val' => [
                'msg' => session()->getFlashdata('msg'),
                'val_email' => session()->getFlashdata('val-email'),
                'val_sandi' => session()->getFlashdata('val-sandi'),
                'isiEmail' => session()->getFlashdata('isiEmail'),
            ]
        ];
        return view('pages/login', $data);
    }
    public function actionLoginSalah()
    {
        session()->setFlashdata('msg', "Maaf, masih dalam masa perbaikan. Akan aktif kembali ketika pukul 07:30 WIB");
        return redirect()->to('/login');
    }
    public function actionLogin()
    {
        if (!$this->validate([
            'email' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Email harus diisi'
                ]
            ],
            'sandi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Sandi harus diisi'
                ]
            ]
        ])) {
            $validation = \Config\Services::validation();
            session()->setFlashdata('val-email', $validation->getError('email'));
            session()->setFlashdata('val-sandi', $validation->getError('sandi'));
            return redirect()->to('/login')->withInput();
        }

        $email = $this->request->getVar('email');
        $sandi = $this->request->getVar('sandi');
        $getUser = $this->userModel->getUser($email);
        if (!$getUser) {
            session()->setFlashdata('msg', 'Email tidak terdaftar');
            return redirect()->to('/login');
        }
        $authSandi = password_verify($sandi, $getUser['sandi']);
        if (!$authSandi) {
            session()->setFlashdata('msg', 'Sandi salah');
            session()->setFlashdata('isiEmail', $email);
            return redirect()->to('/login');
        }
        if ($getUser['active'] == '0') {
            $ses_data = [
                'email' => $getUser['email'],
                'active' => '0',
                'isLogin' => true
            ];
            session()->set($ses_data);
            session()->setFlashdata('msg', "Email " . $email . " perlu diverifikasi");
            return redirect()->to('/verify');
        }
        if ($getUser['role'] == '0') {
            $getPembeli = $this->pembeliModel->getPembeli($email);
            $ses_data = [
                'active' => '1',
                'email' => $getUser['email'],
                'role' => $getUser['role'],
                'nama' => $getPembeli['nama'],
                'alamat' => json_decode($getPembeli['alamat'], true),
                'nohp' => $getPembeli['nohp'],
                'wishlist' => json_decode($getPembeli['wishlist'], true),
                'keranjang' => json_decode($getPembeli['keranjang'], true),
                'transaksi' => json_decode($getPembeli['transaksi'], true),
                'isLogin' => true
            ];
            session()->set($ses_data);
            return redirect()->to(site_url('/'));
        } else {
            $ses_data = [
                'active' => '1',
                'email' => $getUser['email'],
                'role' => $getUser['role'],
                'isLogin' => true
            ];
            session()->set($ses_data);
            return redirect()->to('/');
        }
    }
    public function actionLoginTamu()
    {
        $ses_data = [
            'active' => '1',
            'email' => 'tamu',
            'role' => 0,
            'nama' => 'tamu',
            'alamat' => [],
            'nohp' => 'tamu',
            'wishlist' => [],
            'keranjang' => [],
            'transaksi' => [],
            'isLogin' => true
        ];
        session()->set($ses_data);
        return redirect()->to('/');
    }
    public function actionLogout()
    {
        $ses_data = ['email', 'role', 'alamat', 'wishlist', 'keranjang', 'isLogin', 'active', 'transaksi', 'nama', 'nohp'];
        session()->remove($ses_data);
        session()->setFlashdata('msg', 'Kamu telah keluar');
        return redirect()->to('/login');
    }
    public function wishlist()
    {
        $wishlist = session()->get('wishlist');
        $produk = [];
        if (count($wishlist) > 0) {
            foreach ($wishlist as $w) {
                array_push($produk, $this->barangModel->getBarang($w));
            }
        }
        $data = [
            'title' => 'Wishlist',
            'produk' => $produk,
            'wishlist' => $wishlist
        ];
        return view('pages/wishlist', $data);
    }
    public function addWishlist($id)
    {
        $wishlist = session()->get('wishlist');
        $email = session()->get('email');
        array_push($wishlist, $id);
        session()->set(['wishlist' => $wishlist]);

        if ($email != 'tamu')
            $this->pembeliModel->where('email_user', $email)->set(['wishlist' => json_encode($wishlist)])->update();
        return redirect()->to('/wishlist');
    }
    public function delWishlist($id)
    {
        $wishlist = session()->get('wishlist');
        $email = session()->get('email');
        if (($key = array_search($id, $wishlist)) !== false) {
            unset($wishlist[$key]);
        }
        session()->set(['wishlist' => $wishlist]);

        if ($email != 'tamu')
            $this->pembeliModel->where('email_user', $email)->set(['wishlist' => json_encode($wishlist)])->update();
        return redirect()->to('/wishlist');
    }

    public function wishlistToCart()
    {
        $wishlist = session()->get('wishlist');
        $keranjang = session()->get('keranjang');
        $email = session()->get('email');
        foreach ($wishlist as $id_barang) {
            $produknya = $this->barangModel->getBarang($id_barang);
            $varian = json_decode($produknya['varian'], true)[0];
            $ketemu = false;
            foreach ($keranjang as $index => $element) {
                if ($element['id'] == $id_barang && $element['varian'] == $varian) {
                    $keranjang[$index]['jumlah'] += 1;
                    $ketemu = true;
                }
            }
            if (!$ketemu) {
                $keranjangBaru = array(
                    'id' => $id_barang,
                    'jumlah' => 1,
                    'varian' => $varian,
                    'index_gambar' => 0
                );
                array_push($keranjang, $keranjangBaru);
            }
        }
        session()->set(['keranjang' => $keranjang]);

        if ($email != 'tamu')
            $this->pembeliModel->where('email_user', $email)->set(['keranjang' => json_encode($keranjang)])->update();
        return redirect()->to('/cart');
    }
    public function cart()
    {
        $keranjang = session()->get('keranjang');
        $email = session()->get('email');
        $produk = [];
        $gambar = [];
        $jumlah = [];
        $itemDetails = [];
        $indElementNotFound = [];
        $indElementStokHabis = [];
        $subtotal = 0;
        $berat = 0;
        if (!empty($keranjang)) {
            foreach ($keranjang as $ind => $element) {
                $produknya = $this->barangModel->getBarang($element['id']);
                if ($produknya) {
                    $gambarnya = $this->gambarBarangModel->getGambar($element['id']);
                    array_push($produk, $produknya);
                    array_push($gambar, $gambarnya["gambar" . ($element['index_gambar'] + 1)]);
                    array_push($jumlah, $element['jumlah']);
                    $item = array(
                        'id' => $produknya["id"],
                        'price' => $produknya["harga"],
                        'quantity' => $element['jumlah'],
                        'name' => $produknya["nama"],
                    );
                    array_push($itemDetails, $item);

                    $persen = (100 - $produknya['diskon']) / 100;
                    $hasil = floor($persen * $produknya['harga']);
                    $subtotal += $hasil * $element['jumlah'];
                    $berat += $produknya['berat'] * $element['jumlah'];

                    //cek stok habis
                    if ((int)$produknya['stok'] - (int)$element['jumlah'] < 0)
                        array_push($indElementStokHabis, $ind);
                } else {
                    array_push($indElementNotFound, $ind);
                }
            }
            $item = array(
                'id' => 'Biaya Tambahan',
                'price' => 10000,
                'quantity' => 1,
                'name' => 'Biaya Ongkir',
            );
            array_push($itemDetails, $item);
            $total = $subtotal + 10000;
        }

        if (count($indElementNotFound) > 0) {
            session()->setFlashdata('msg', 'Terdapat produk yang dihapus dari keranjang karena produk sudah tidak tersedia');
            foreach ($indElementNotFound as $ind) {
                unset($keranjang[$ind]);
            }
            $keranjangBaru = array_values($keranjang);
            session()->set(['keranjang' => $keranjangBaru]);
            if ($email != 'tamu')
                $this->pembeliModel->where('email_user', $email)->set(['keranjang' => json_encode($keranjangBaru)])->update();
            return redirect()->to('/cart');
        }

        $data = [
            'title' => 'Keranjang',
            'produk' => $produk,
            'gambar' => $gambar,
            'jumlah' => $jumlah,
            'keranjang' => $keranjang,
            'tokenMid' => false,
            'berat' => $berat,
            'msg' => session()->getFlashdata('msg'),
            'indStokHabis' => $indElementStokHabis
        ];

        if (!isset($total)) {
            return view('pages/cart', $data);
        }

        return view('pages/cart', $data);
    }
    public function addCart($id_barang, $varian, $index_gambar)
    {
        $keranjang = session()->get('keranjang');
        $email = session()->get('email');
        $ketemu = false;
        foreach ($keranjang as $index => $element) {
            if ($element['id'] == $id_barang && $element['varian'] == $varian) {
                $produknya = $this->barangModel->getBarang($element['id']);
                if ((int)$produknya['stok'] - (int)$keranjang[$index]['jumlah'] - 1 < 0) {
                    session()->setFlashdata('msg', 'Stok kurang');
                    return redirect()->to("/product/" . $produknya['nama']);
                }
                $keranjang[$index]['jumlah'] += 1;
                $ketemu = true;
            }
        }
        if (!$ketemu) {
            $keranjangBaru = array(
                'id' => $id_barang,
                'jumlah' => 1,
                'varian' => $varian,
                'index_gambar' => $index_gambar
            );
            array_push($keranjang, $keranjangBaru);
        }
        session()->set(['keranjang' => $keranjang]);
        if ($email != 'tamu')
            $this->pembeliModel->where('email_user', $email)->set(['keranjang' => json_encode($keranjang)])->update();
        return redirect()->to('/cart');
    }
    public function redCart($index_cart)
    {
        $keranjang = session()->get('keranjang');
        $email = session()->get('email');
        $keranjang[$index_cart]['jumlah'] -= 1;
        if ($keranjang[$index_cart]['jumlah'] == 0) {
            unset($keranjang[$index_cart]);
            $keranjangBaru = array_values($keranjang);
            session()->set(['keranjang' => $keranjangBaru]);
            if ($email != 'tamu')
                $this->pembeliModel->where('email_user', $email)->set(['keranjang' => json_encode($keranjangBaru)])->update();
            return redirect()->to('/cart');
        }
        session()->set(['keranjang' => $keranjang]);

        if ($email != 'tamu')
            $this->pembeliModel->where('email_user', $email)->set(['keranjang' => json_encode($keranjang)])->update();
        return redirect()->to('/cart');
    }
    public function delCart($index_cart)
    {
        $keranjang = session()->get('keranjang');
        $email = session()->get('email');
        unset($keranjang[$index_cart]);
        $keranjangBaru = array_values($keranjang);
        session()->set(['keranjang' => $keranjangBaru]);

        if ($email != 'tamu')
            $this->pembeliModel->where('email_user', $email)->set(['keranjang' => json_encode($keranjangBaru)])->update();
        return redirect()->to('/cart');
    }
    public function successPay()
    {
        $id_pesanan = session()->getFlashdata('id_pesanan');
        if ($id_pesanan == null) return redirect()->to('/');
        $getPesanan = $this->pemesananModel->like("id_midtrans", $id_pesanan, "both")->first();
        $data = [
            'title' => 'Pembayaran Sukses',
            'id_pesanan' => $getPesanan['id_midtrans']
        ];
        return view('pages/successPay', $data);
    }
    public function progressPay()
    {
        $id_pesanan = session()->getFlashdata('id_pesanan');
        if ($id_pesanan == null) return redirect()->to('/');
        $getPesanan = $this->pemesananModel->like("id_midtrans", $id_pesanan, "both")->first();
        $data = [
            'title' => 'Pembayaran Pending',
            'id_pesanan' => $getPesanan['id_midtrans']
        ];
        return view('pages/progressPay', $data);
    }
    public function errorPay()
    {
        $id_pesanan = session()->getFlashdata('id_pesanan');
        if ($id_pesanan == null) return redirect()->to('/');
        $getPesanan = $this->pemesananModel->like("id_midtrans", $id_pesanan, "both")->first();
        $data = [
            'title' => 'Pembayaran Gagal',
            'id_pesanan' => $getPesanan['id_midtrans']
        ];
        return view('pages/errorPay', $data);
    }
    public function checkout()
    {
        $keranjang = session()->get('keranjang');
        $email = session()->get('email');
        $alamat = session()->get('alamat');
        $nama = session()->get('nama');
        $nohp = session()->get('nohp');
        $produk = [];
        $jumlah = [];
        $produkJson = [];
        $subtotal = 0;
        $berat = 0;
        $beratHitung = 0;
        $dimensiSemua = [];
        $paket = [];
        $paketFilter = [];
        if (!empty($keranjang)) {
            foreach ($keranjang as $ind => $element) {
                $produknya = $this->barangModel->getBarang($element['id']);
                array_push($produk, $produknya);
                array_push($jumlah, $element['jumlah']);

                $persen = (100 - $produknya['diskon']) / 100;
                $hasil = round($persen * $produknya['harga']);
                $subtotal += $hasil * $element['jumlah'];
                $dimensi = explode("X", $produknya['dimensi']);
                array_push($dimensiSemua, $produknya['dimensi']);
                $berat += $produknya['berat'] * $element['jumlah'];
                $beratHitung += ceil((float)$dimensi[0] * (float)$dimensi[1] * (float)$dimensi[2] / 3500) * $element['jumlah']; //kg

                array_push($produkJson, array(
                    'name' => $produknya['nama'] . " (" . $element['varian'] . ")",
                    // 'description' => $produknya['deskripsi'],
                    'value' => $hasil,
                    'length' => (float)$dimensi[0],
                    'width' => (float)$dimensi[1],
                    'height' => (float)$dimensi[2],
                    'weight' => (float)$produknya['berat'],
                    'quantity' => (int)$element['jumlah'],
                ));

                //cek stok habis
                if ((int)$produknya['stok'] - (int)$element['jumlah'] < 0)
                    return redirect()->to('cart');
            }
            $total = $subtotal + 5000;
        }

        $beratAkhir = $berat > $beratHitung ? $berat : $beratHitung;

        //Dapatkan data provinsi
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/province",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: 6bc9315fb7a163e74a04f9f54ede3c2c"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $provinsi = json_decode($response, true);

        if (count($alamat) > 0) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://pro.rajaongkir.com/api/city?province=" . $alamat['prov_id'],
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "key: 6bc9315fb7a163e74a04f9f54ede3c2c"
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return "cURL Error #:" . $err;
            }
            $kota = json_decode($response, true);

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://pro.rajaongkir.com/api/subdistrict?city=" . $alamat['kab_id'],
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "key: 6bc9315fb7a163e74a04f9f54ede3c2c"
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return "cURL Error #:" . $err;
            }
            $kec = json_decode($response, true);

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://dakotacargo.co.id/api/api_glb_M_kodepos.asp?key=15f6a51696a8b034f9ce366a6dc22138&id=11022019000001&aKec=" . rawurlencode($alamat['kec']),
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return "cURL Error #:" . $err;
            }
            $desa = json_decode($response, true);
        }
        // if (count($alamat) > 0) {
        //     $curl_jne = curl_init();
        //     curl_setopt_array($curl_jne, array(
        //         CURLOPT_URL => "https://pro.rajaongkir.com/api/cost",
        //         CURLOPT_SSL_VERIFYHOST => 0,
        //         CURLOPT_SSL_VERIFYPEER => 0,
        //         CURLOPT_RETURNTRANSFER => true,
        //         CURLOPT_ENCODING => "",
        //         CURLOPT_MAXREDIRS => 10,
        //         CURLOPT_TIMEOUT => 30,
        //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //         CURLOPT_CUSTOMREQUEST => "POST",
        //         CURLOPT_POSTFIELDS => "origin=5504&originType=subdistrict&destination=" . $alamat['kec_id'] . "&destinationType=subdistrict&weight=" . $beratAkhir * 1000 . "&courier=jne",
        //         CURLOPT_HTTPHEADER => array(
        //             "content-type: application/x-www-form-urlencoded",
        //             "key: 6bc9315fb7a163e74a04f9f54ede3c2c"
        //         ),
        //     ));
        //     $response = curl_exec($curl_jne);
        //     $err = curl_error($curl_jne);
        //     curl_close($curl_jne);
        //     if ($err) {
        //         return "cURL Error #:" . $err;
        //     }
        //     $jne = json_decode($response, true);

        //     $curl_jnt = curl_init();
        //     curl_setopt_array($curl_jnt, array(
        //         CURLOPT_URL => "https://pro.rajaongkir.com/api/cost",
        //         CURLOPT_SSL_VERIFYHOST => 0,
        //         CURLOPT_SSL_VERIFYPEER => 0,
        //         CURLOPT_RETURNTRANSFER => true,
        //         CURLOPT_ENCODING => "",
        //         CURLOPT_MAXREDIRS => 10,
        //         CURLOPT_TIMEOUT => 30,
        //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //         CURLOPT_CUSTOMREQUEST => "POST",
        //         CURLOPT_POSTFIELDS => "origin=5504&originType=subdistrict&destination=" . $alamat['kec_id'] . "&destinationType=subdistrict&weight=" . $beratAkhir * 1000 . "&courier=jnt",
        //         CURLOPT_HTTPHEADER => array(
        //             "content-type: application/x-www-form-urlencoded",
        //             "key: 6bc9315fb7a163e74a04f9f54ede3c2c"
        //         ),
        //     ));
        //     $response = curl_exec($curl_jnt);
        //     $err = curl_error($curl_jnt);
        //     curl_close($curl_jnt);
        //     if ($err) {
        //         return "cURL Error #:" . $err;
        //     }
        //     $jnt = json_decode($response, true);

        //     $curl_wahana = curl_init();
        //     curl_setopt_array($curl_wahana, array(
        //         CURLOPT_URL => "https://pro.rajaongkir.com/api/cost",
        //         CURLOPT_SSL_VERIFYHOST => 0,
        //         CURLOPT_SSL_VERIFYPEER => 0,
        //         CURLOPT_RETURNTRANSFER => true,
        //         CURLOPT_ENCODING => "",
        //         CURLOPT_MAXREDIRS => 10,
        //         CURLOPT_TIMEOUT => 30,
        //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //         CURLOPT_CUSTOMREQUEST => "POST",
        //         CURLOPT_POSTFIELDS => "origin=5504&originType=subdistrict&destination=" . $alamat['kec_id'] . "&destinationType=subdistrict&weight=" . $beratAkhir * 1000 . "&courier=wahana",
        //         CURLOPT_HTTPHEADER => array(
        //             "content-type: application/x-www-form-urlencoded",
        //             "key: 6bc9315fb7a163e74a04f9f54ede3c2c"
        //         ),
        //     ));
        //     $response = curl_exec($curl_wahana);
        //     $err = curl_error($curl_wahana);
        //     curl_close($curl_wahana);
        //     if ($err) {
        //         return "cURL Error #:" . $err;
        //     }
        //     $wahana = json_decode($response, true);

        //     $curl_dakota = curl_init();
        //     $data_dakota = [
        //         'prov' => $alamat['prov'],
        //         'kab' => $alamat['kab'],
        //         'kec' => $alamat['kec'],
        //     ];
        //     curl_setopt_array($curl_dakota, array(
        //         CURLOPT_URL => "https://api.jasminefurniture.co.id/dakota",
        //         CURLOPT_SSL_VERIFYHOST => 0,
        //         CURLOPT_SSL_VERIFYPEER => 0,
        //         CURLOPT_RETURNTRANSFER => true,
        //         CURLOPT_ENCODING => "",
        //         CURLOPT_MAXREDIRS => 10,
        //         CURLOPT_TIMEOUT => 30,
        //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //         CURLOPT_CUSTOMREQUEST => "POST",
        //         CURLOPT_POSTFIELDS => json_encode($data_dakota),
        //         CURLOPT_HTTPHEADER => array(
        //             "content-type: application/json"
        //         ),
        //     ));
        //     $response = curl_exec($curl_dakota);
        //     $err = curl_error($curl_dakota);
        //     curl_close($curl_dakota);
        //     if ($err) {
        //         return "cURL Error #:" . $err;
        //     }
        //     $dakota = json_decode($response, true);

        //     $costs_dakota = [];
        //     foreach ($dakota['data'] as $deskripsi => $value_dakota) {
        //         if ($deskripsi != 'UNIT') {
        //             array_push($costs_dakota, [
        //                 'service' => $deskripsi,
        //                 'description' => ucwords($deskripsi),
        //                 'cost' => [
        //                     [
        //                         'value' => $beratHitung > (int)$value_dakota[0]['minkg'] ? (int)$value_dakota[0]['kgnext'] * $beratHitung : (int)$value_dakota[0]['pokok'],
        //                         'etd' => $value_dakota[0]['LT']
        //                     ]
        //                 ]
        //             ]);
        //         }
        //     }

        //     $paket = [
        //         $jne['rajaongkir']['results'][0],
        //         $jnt['rajaongkir']['results'][0],
        //         $wahana['rajaongkir']['results'][0],
        //         [
        //             'code' => 'dakota',
        //             'name' => 'Dakota Cargo',
        //             'costs' => $costs_dakota
        //         ]
        //     ];
        //     for ($i = 0; $i < 4; $i++) {
        //         if ($paket[$i]['costs'][0]['cost'][0]['value'] != 0) {
        //             array_push($paketFilter, $paket[$i]);
        //         }
        //     }
        // }

        $user = [
            'nama' => $email == 'tamu' ? (session()->getFlashdata('namaPen') ? session()->getFlashdata('namaPen') : '') : $nama,
            'alamat' => $alamat,
            'nohp' => $email == 'tamu' ? (session()->getFlashdata('nohpPen') ? session()->getFlashdata('nohpPen') : '') : $nohp,
            'email' => $email,
        ];
        $data = [
            'title' => 'Check Out',
            'produk' => $produk,
            'produkJson' => json_encode($produkJson),
            'alamatJson' => json_encode($alamat),
            'jumlah' => $jumlah,
            'beratAkhir' => $beratAkhir, //kilogram
            'dimensiSemua' => implode("-", $dimensiSemua),
            'user' => $user,
            'total' => $total,
            'subtotal' => $subtotal,
            'provinsi' => $provinsi["rajaongkir"]["results"],
            'kabupaten' => isset($kota) ? $kota["rajaongkir"]["results"] : [],
            'kecamatan' => isset($kec) ? $kec["rajaongkir"]["results"] : [],
            'desa' => isset($desa) ? $desa : [],
            'keranjang' => $keranjang,
            'keranjangJson' => json_encode($keranjang),
            // 'paket' => $paketFilter,
            // 'paketJson' => json_encode($paketFilter),
        ];
        return view('pages/checkout', $data);
    }
    public function updateAlamat($dataString, $dataLain)
    {
        $email = session()->get("email");
        $a = explode("&", $dataString);
        $arr = [
            "prov_id" => explode("-", $a[0])[0],
            "prov" => explode("-", $a[0])[1],
            "kab_id" => explode("-", $a[1])[0],
            "kab" => explode("-", $a[1])[1],
            "kec_id" => explode("-", $a[2])[0],
            "kec" => explode("-", $a[2])[1],
            "desa" => explode("-", $a[3])[0],
            "kodepos" => explode("-", $a[3])[1],
            "add" => $a[4],
            "alamat" => $a[5],
        ];
        if ($email != 'tamu')
            $this->pembeliModel->where('email_user', $email)->set([
                'alamat' => json_encode($arr),
            ])->update();

        if ($dataLain != '0') {
            $stringDataLain = explode("&", $dataLain);
            session()->setFlashdata('emailPem', $stringDataLain[0]);
            session()->setFlashdata('namaPem', $stringDataLain[1]);
            session()->setFlashdata('nohpPem', $stringDataLain[2]);
            session()->setFlashdata('namaPen', $stringDataLain[3]);
            session()->setFlashdata('nohpPen', $stringDataLain[4]);
        }
        session()->set(['alamat' => $arr]);
        return redirect()->to('/checkout');
    }
    public function getKota($id_prov)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/city?province=" . $id_prov,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: 6bc9315fb7a163e74a04f9f54ede3c2c"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $kota = json_decode($response, true);
        return $this->response->setJSON($kota, false);
    }
    public function getKec($id_kota)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/subdistrict?city=" . $id_kota,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: 6bc9315fb7a163e74a04f9f54ede3c2c"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $kec = json_decode($response, true);
        return $this->response->setJSON($kec, false);
    }
    public function getKode($kec)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://dakotacargo.co.id/api/api_glb_M_kodepos.asp?key=15f6a51696a8b034f9ce366a6dc22138&id=11022019000001&aKec=" . rawurlencode($kec),
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $kode = json_decode($response, true);
        // dd([
        //     'URL' => "https://dakotacargo.co.id/api/api_glb_M_kodepos.asp?key=15f6a51696a8b034f9ce366a6dc22138&id=11022019000001&aKec=" . $kec,
        //     'hasil' => $kode
        // ]);
        return $this->response->setJSON($kode, false);
    }

    public function getPaket($asal, $tujuan, $berat, $kurir)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/cost",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "origin=" . $asal . "&originType=subdistrict&destination=" . $tujuan . "&destinationType=subdistrict&weight=" . $berat . "&courier=" . $kurir,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "key: 6bc9315fb7a163e74a04f9f54ede3c2c"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $paket = json_decode($response, true);
        return $this->response->setJSON($paket, false);
    }
    public function getDakota()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://www.dakotacargo.co.id/api/api_glb_M_kodepos.asp?key=15f6a51696a8b034f9ce366a6dc22138&id=11022019000001&aKdp=13890",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            // CURLOPT_HTTPHEADER => array(
            //     "authorization: biteship_test.eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiamFzbWluZSB0ZXN0aW5nIiwidXNlcklkIjoiNjU4M2I1MmY2YzAyMTAxZjVhZTJlNWY5IiwiaWF0IjoxNzAzMTMxOTQ5fQ.22F0VWJe-JavNsxaw_s68ErNv41cTVcYIm1OWtJF9og"
            // ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $kota = json_decode($response, true);
        return $this->response->setJSON($kota, false);
    }
    public function actionPayCoreAPI()
    {
        $emailPem = $this->request->getVar('emailPem') ? $this->request->getVar('emailPem') : session()->get('email');
        $namaPem = $this->request->getVar('namaPem') ? $this->request->getVar('namaPem') : session()->get('nama');
        $nohpPem = $this->request->getVar('nohpPem') ? $this->request->getVar('nohpPem') : session()->get('nohp');
        $nama = $this->request->getVar('nama');
        $nohp = $this->request->getVar('nohp');
        $prov = $this->request->getVar('provinsi');
        $kab = $this->request->getVar('kota');
        $kec = $this->request->getVar('kecamatan');
        $kode = $this->request->getVar('kodepos');
        $alamatAdd = $this->request->getVar('alamat_add');
        $alamatLengkap = $this->request->getVar('alamat');
        $pembayaran = $this->request->getVar('pembayaran');

        $alamat = [
            "prov_id" => explode("-", $prov)[0],
            "prov" => explode("-", $prov)[1],
            "kab_id" => explode("-", $kab)[0],
            "kab" => explode("-", $kab)[1],
            "kec_id" => explode("-", $kec)[0],
            "kec" => explode("-", $kec)[1],
            "desa" => explode("-", $kode)[0],
            "kodepos" => explode("-", $kode)[1],
            "add" => $alamatAdd,
            "alamat" => $alamatLengkap,
        ];
        if (session()->get('email') != 'tamu') {
            $this->pembeliModel->where('email_user', $emailPem)->set([
                'alamat' => json_encode($alamat),
            ])->update();
        }
        session()->set(['alamat' => $alamat]);
        $keranjang = session()->get('keranjang');
        $produk = [];

        $subtotal = 0;
        $itemDetails = [];
        if (!empty($keranjang)) {
            foreach ($keranjang as $ind => $element) {
                $produknya = $this->barangModel->getBarang($element['id']);
                $persen = (100 - $produknya['diskon']) / 100;
                $hasil = round($persen * $produknya['harga']);
                $subtotal += $hasil * (int)$element['jumlah'];
                $dimensi = explode("X", $produknya['dimensi']);
                array_push($produk, array(
                    'name' => $produknya['nama'] . " (" . $element['varian'] . ")",
                    'value' => $hasil,
                    'length' => (float)$dimensi[0],
                    'width' => (float)$dimensi[1],
                    'height' => (float)$dimensi[2],
                    'weight' => (float)$produknya['berat'],
                    'quantity' => (int)$element['jumlah'],
                ));

                //untuk midtrans
                $item = array(
                    'id' => $produknya["id"],
                    'price' => $hasil,
                    'quantity' => $element['jumlah'],
                    'name' => substr($produknya["nama"] . " (" . ucfirst($element['varian']) . ")", 0, 50),
                    'packed' => false
                );
                array_push($itemDetails, $item);
            }
            $total = $subtotal + 5000;
        }

        $biayaadmin = array(
            'id' => 'Biaya Admin',
            'price' => 5000,
            'quantity' => 1,
            'name' => 'Biaya Admin',
        );
        array_push($itemDetails, $biayaadmin);

        $auth = base64_encode("SB-Mid-server-3M67g25LgovNPlwdS4WfiMsh" . ":");
        $pesananke = $this->pemesananModel->orderBy('id', 'desc')->first();
        $idFix = "JM" . (sprintf("%08d", $pesananke ? ((int)$pesananke['id'] + 1) : 1));
        $randomId = "JM" . rand();
        // $customField = json_encode([
        //     'e' => $emailPem,
        //     'n' => $nama,
        //     'h' => $nohp,
        //     'a' => $alamatLengkap,
        //     'i' => $produk
        // ]);
        $arrPostField = [
            "transaction_details" => [
                "order_id" => $idFix,
                "gross_amount" => $total,
                // "payment_link_id" => "payment-link-lunarea-" . $idFix
            ],
            'customer_details' => array(
                'email' => $emailPem,
                'first_name' => $namaPem,
                'phone' => $nohpPem,
                'billing_address' => array(
                    'email' => $emailPem,
                    'first_name' => $namaPem,
                    'phone' => $nohpPem,
                    'address' => $alamatLengkap,
                ),
                'shipping_address' => array(
                    'email' => $emailPem,
                    'first_name' => $nama,
                    'phone' => $nohp,
                    'address' => $alamatLengkap,
                )
            ),
            // 'customer_details' => array(
            //     'email' => $emailPem,
            //     'phone' => $nohp,
            //     'first_name' => $nama,
            // ),
            'item_details' => $itemDetails,
            // "usage_limit" =>  1,
            // "enabled_payments" => ["gopay", "cimb_clicks", "bca_klikbca", "bca_klikpay", 'bri_epay', 'echannel', 'permata_va', 'bca_va', 'bni_va', 'bri_va', 'shopeepay'],
            // "expiry" => [
            //     "duration" => 24,
            //     "unit" => "hours"
            // ],
            // "custom_field1" => substr($customField, 0, 255),
            // "custom_field2" => substr($customField, 255, 255),
            // "custom_field3" => substr($customField, 510, 255),
        ];

        switch ($pembayaran) {
            case 'bca':
                $arrPostField["payment_type"] = "bank_transfer";
                $arrPostField["bank_transfer"] = ["bank" => "bca"];
                break;
            case 'bri':
                $arrPostField["payment_type"] = "bank_transfer";
                $arrPostField["bank_transfer"] = ["bank" => "bri"];
                break;
            case 'bni':
                $arrPostField["payment_type"] = "bank_transfer";
                $arrPostField["bank_transfer"] = ["bank" => "bni"];
                break;
            case 'cimb':
                $arrPostField["payment_type"] = "bank_transfer";
                $arrPostField["bank_transfer"] = ["bank" => "cimb"];
                break;
            case 'permata':
                $arrPostField["payment_type"] = "permata";
                break;
            case 'mandiri':
                $arrPostField["payment_type"] = "echannel";
                $arrPostField["echannel"] = [
                    "bill_info1" => "Payment:",
                    "bill_info2" => "Online purchase"
                ];
                break;
            default:
                return redirect()->to('/cart');
                break;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            // CURLOPT_URL => "https://api.midtrans.com/v1/payment-links",
            CURLOPT_URL => "https://api.midtrans.com/v2/charge",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($arrPostField),
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: Basic " . $auth,
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $hasilMidtrans = json_decode($response, true);
        dd([
            'body' => $arrPostField,
            'hasilMidtrans' => $hasilMidtrans
        ]);

        if ($hasilMidtrans['fraud_status'] == "accept") {
            switch ($hasilMidtrans['transaction_status']) {
                case 'settlement':
                    $status = "Proses";
                    break;
                case 'capture':
                    $status = "Proses";
                    break;
                case 'pending':
                    $status = "Menunggu Pembayaran";
                    break;
                case 'expire':
                    $status = "Kadaluarsa";
                    break;
                case 'deny':
                    $status = "Ditolak";
                    break;
                case 'failure':
                    $status = "Gagal";
                    break;
                case 'refund':
                    $status = "Refund";
                    break;
                case 'partial_refund':
                    $status = "Partial Refund";
                    break;
                case 'cancel':
                    $status = "Dibatalkan";
                    break;
                default:
                    $status = "No Status";
                    break;
            }
        } else {
            $status = 'Forbidden';
        }
        $this->pemesananModel->where(['id_midtrans' => $idFix])->set([
            'nama_cus' => $namaPem,
            'email_cus' => $emailPem,
            'hp_cus' => $nohpPem,
            'nama_pen' => $nama,
            'hp_pen' => $nohp,
            'alamat_pen' => $alamatLengkap,
            'resi' => "Menunggu pengiriman",
            'items' => json_encode($produk),
            'status' => $status,
            'kurir' => 'kosong'
        ])->update();
        // session()->setFlashdata('id_pesanan', $hasilMidtrans['order_id']);
        return redirect()->to('/order/' . $idFix);
    }
    public function actionPaySnap()
    {
        $bodyJson = $this->request->getBody();
        $body = json_decode($bodyJson, true);
        $email = $body['email'];
        $nama = $body['nama'];
        $nohp = $body['nohp'];
        $prov = $body['provinsi'];
        $kab = $body['kota'];
        $kec = $body['kecamatan'];
        $kode = $body['kodepos'];
        $alamatAdd = $body['alamat_add'];
        $alamatLengkap = $body['alamat'];
        $keranjang = json_decode($body['keranjang'], true);

        $alamat = [
            "prov_id" => explode("-", $prov)[0],
            "prov" => explode("-", $prov)[1],
            "kab_id" => explode("-", $kab)[0],
            "kab" => explode("-", $kab)[1],
            "kec_id" => explode("-", $kec)[0],
            "kec" => explode("-", $kec)[1],
            "desa" => explode("-", $kode)[0],
            "kodepos" => explode("-", $kode)[1],
            "add" => $alamatAdd,
            "alamat" => $alamatLengkap,
        ];

        $cariUser = $this->pembeliModel->getPembeli($email);
        if ($cariUser) {
            $this->pembeliModel->where('email_user', $email)->set([
                'alamat' => json_encode($alamat),
            ])->update();
        }

        $produk = [];
        $subtotal = 0;
        $itemDetails = [];
        if (!empty($keranjang)) {
            foreach ($keranjang as $ind => $element) {
                $produknya = $this->barangModel->getBarang($element['id']);
                $persen = (100 - $produknya['diskon']) / 100;
                $hasil = round($persen * $produknya['harga']);
                $subtotal += $hasil * (int)$element['jumlah'];
                array_push($produk, array(
                    'id' => $produknya["id"],
                    'name' => $produknya['nama'] . " (" . $element['varian'] . ")",
                    'value' => $hasil,
                    'quantity' => (int)$element['jumlah'],
                ));

                //untuk midtrans
                $item = array(
                    'id' => $produknya["id"],
                    'price' => $hasil,
                    'quantity' => $element['jumlah'],
                    'name' => substr($produknya["nama"] . " (" . ucfirst($element['varian']) . ")", 0, 50),
                    'packed' => false
                );
                array_push($itemDetails, $item);
            }
            $total = $subtotal + 5000;
        }

        $biayaadmin = array(
            'id' => 'Biaya Admin',
            'price' => 5000,
            'quantity' => 1,
            'name' => 'Biaya Admin',
        );
        array_push($itemDetails, $biayaadmin);

        // \Midtrans\Config::$serverKey = "SB-Mid-server-3M67g25LgovNPlwdS4WfiMsh";
        // \Midtrans\Config::$isProduction = false;
        $auth = base64_encode("SB-Mid-server-3M67g25LgovNPlwdS4WfiMsh" . ":");
        $pesananke = $this->pemesananModel->orderBy('id', 'desc')->first();
        $idFix = "L" . (sprintf("%08d", $pesananke ? ((int)$pesananke['id'] + 1) : 1));
        $randomId = "L" . rand();
        $customField = json_encode([
            'e' => $email,
            'n' => $nama,
            'h' => $nohp,
            'a' => $alamatLengkap,
            'i' => $produk
        ]);
        $arrPostField = [
            "transaction_details" => [
                "order_id" => $idFix,
                "gross_amount" => $total,
            ],
            'customer_details' => array(
                'email' => $email,
                'first_name' => $nama,
                'phone' => $nohp,
                'billing_address' => array(
                    'email' => $email,
                    'first_name' => $nama,
                    'phone' => $nohp,
                    'address' => $alamatLengkap,
                ),
                'shipping_address' => array(
                    'email' => $email,
                    'first_name' => $nama,
                    'phone' => $nohp,
                    'address' => $alamatLengkap,
                )
            ),
            'callbacks' => array(
                'finish' => "https://lunareafurniture.com/order/" . $idFix,
            ),
            'item_details' => $itemDetails,
            "custom_field1" => substr($customField, 0, 255),
            "custom_field2" => substr($customField, 255, 255),
            "custom_field3" => substr($customField, 510, 255),
        ];

        // $snapToken = \Midtrans\Snap::getSnapToken($arrPostField);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://app.midtrans.com/snap/v1/transactions",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($arrPostField),
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: Basic " . $auth,
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $hasilMidtrans = json_decode($response, true);
        return $this->response->setJSON($hasilMidtrans, false);
    }
    public function actionPay()
    {
        $emailPem = $this->request->getVar('emailPem') ? $this->request->getVar('emailPem') : session()->get('email');
        $namaPem = $this->request->getVar('namaPem') ? $this->request->getVar('namaPem') : session()->get('nama');
        $nohpPem = $this->request->getVar('nohpPem') ? $this->request->getVar('nohpPem') : session()->get('nohp');
        $nama = $this->request->getVar('nama');
        $nohp = $this->request->getVar('nohp');
        $prov = $this->request->getVar('provinsi');
        $kab = $this->request->getVar('kota');
        $kec = $this->request->getVar('kecamatan');
        $kode = $this->request->getVar('kodepos');
        $alamatAdd = $this->request->getVar('alamat_add');
        $alamatLengkap = $this->request->getVar('alamat');
        $pembayaran = $this->request->getVar('pembayaran');

        $alamat = [
            "prov_id" => explode("-", $prov)[0],
            "prov" => explode("-", $prov)[1],
            "kab_id" => explode("-", $kab)[0],
            "kab" => explode("-", $kab)[1],
            "kec_id" => explode("-", $kec)[0],
            "kec" => explode("-", $kec)[1],
            "desa" => explode("-", $kode)[0],
            "kodepos" => explode("-", $kode)[1],
            "add" => $alamatAdd,
            "alamat" => $alamatLengkap,
        ];
        if (session()->get('email') != 'tamu') {
            $this->pembeliModel->where('email_user', $emailPem)->set([
                'alamat' => json_encode($alamat),
            ])->update();
        }
        session()->set(['alamat' => $alamat]);
        $keranjang = session()->get('keranjang');
        $produk = [];

        $subtotal = 0;
        $itemDetails = [];
        if (!empty($keranjang)) {
            foreach ($keranjang as $ind => $element) {
                $produknya = $this->barangModel->getBarang($element['id']);
                $persen = (100 - $produknya['diskon']) / 100;
                $hasil = round($persen * $produknya['harga']);
                $subtotal += $hasil * (int)$element['jumlah'];
                $dimensi = explode("X", $produknya['dimensi']);
                array_push($produk, array(
                    'name' => $produknya['nama'] . " (" . $element['varian'] . ")",
                    'value' => $hasil,
                    'length' => (float)$dimensi[0],
                    'width' => (float)$dimensi[1],
                    'height' => (float)$dimensi[2],
                    'weight' => (float)$produknya['berat'],
                    'quantity' => (int)$element['jumlah'],
                ));

                //untuk midtrans
                $item = array(
                    'id' => $produknya["id"],
                    'price' => $hasil,
                    'quantity' => $element['jumlah'],
                    'name' => substr($produknya["nama"] . " (" . ucfirst($element['varian']) . ")", 0, 50),
                    'packed' => false
                );
                array_push($itemDetails, $item);
            }
            $total = $subtotal + 5000;
        }

        $biayaadmin = array(
            'id' => 'Biaya Admin',
            'price' => 5000,
            'quantity' => 1,
            'name' => 'Biaya Admin',
        );
        array_push($itemDetails, $biayaadmin);

        $auth = base64_encode("SB-Mid-server-3M67g25LgovNPlwdS4WfiMsh" . ":");
        $pesananke = $this->pemesananModel->orderBy('id', 'desc')->first();
        $idFix = "JM" . (sprintf("%08d", $pesananke ? ((int)$pesananke['id'] + 1) : 1));
        $randomId = "JM" . rand();
        $customField = json_encode([
            'e' => $emailPem,
            'n' => $nama,
            'h' => $nohp,
            'a' => $alamatLengkap,
            'i' => $produk
        ]);
        $arrPostField = [
            "transaction_details" => [
                "order_id" => $idFix,
                "gross_amount" => $total,
                "payment_link_id" => "payment-link-lunarea-" . $idFix
            ],
            // 'customer_details' => array(
            //     'email' => $emailPem,
            //     'first_name' => $namaPem,
            //     'phone' => $nohpPem,
            //     'billing_address' => array(
            //         'email' => $emailPem,
            //         'first_name' => $namaPem,
            //         'phone' => $nohpPem,
            //         'address' => $alamatLengkap,
            //     ),
            //     'shipping_address' => array(
            //         'email' => $emailPem,
            //         'first_name' => $nama,
            //         'phone' => $nohp,
            //         'address' => $alamatLengkap,
            //     )
            // ),
            'customer_details' => array(
                'email' => $emailPem,
                'phone' => $nohp,
                'first_name' => $nama,
            ),
            'item_details' => $itemDetails,
            "usage_limit" =>  1,
            "enabled_payments" => ["gopay", "cimb_clicks", "bca_klikbca", "bca_klikpay", 'bri_epay', 'echannel', 'permata_va', 'bca_va', 'bni_va', 'bri_va', 'shopeepay'],
            "expiry" => [
                "duration" => 24,
                "unit" => "hours"
            ],
            "custom_field1" => substr($customField, 0, 255),
            "custom_field2" => substr($customField, 255, 255),
            "custom_field3" => substr($customField, 510, 255),
        ];

        // switch ($pembayaran) {
        //     case 'bca':
        //         $arrPostField["payment_type"] = "bank_transfer";
        //         $arrPostField["bank_transfer"] = ["bank" => "bca"];
        //         break;
        //     case 'bri':
        //         $arrPostField["payment_type"] = "bank_transfer";
        //         $arrPostField["bank_transfer"] = ["bank" => "bri"];
        //         break;
        //     case 'bni':
        //         $arrPostField["payment_type"] = "bank_transfer";
        //         $arrPostField["bank_transfer"] = ["bank" => "bni"];
        //         break;
        //     case 'cimb':
        //         $arrPostField["payment_type"] = "bank_transfer";
        //         $arrPostField["bank_transfer"] = ["bank" => "cimb"];
        //         break;
        //     case 'permata':
        //         $arrPostField["payment_type"] = "permata";
        //         break;
        //     case 'mandiri':
        //         $arrPostField["payment_type"] = "echannel";
        //         $arrPostField["echannel"] = [
        //             "bill_info1" => "Payment:",
        //             "bill_info2" => "Online purchase"
        //         ];
        //         break;
        //     default:
        //         return redirect()->to('/cart');
        //         break;
        // }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.midtrans.com/v1/payment-links",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($arrPostField),
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: Basic " . $auth,
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $hasilMidtrans = json_decode($response, true);
        // dd($hasilMidtrans);

        // if ($hasilMidtrans['fraud_status'] == "accept") {
        //     switch ($hasilMidtrans['transaction_status']) {
        //         case 'settlement':
        //             $status = "Proses";
        //             break;
        //         case 'capture':
        //             $status = "Proses";
        //             break;
        //         case 'pending':
        //             $status = "Menunggu Pembayaran";
        //             break;
        //         case 'expire':
        //             $status = "Kadaluarsa";
        //             break;
        //         case 'deny':
        //             $status = "Ditolak";
        //             break;
        //         case 'failure':
        //             $status = "Gagal";
        //             break;
        //         case 'refund':
        //             $status = "Refund";
        //             break;
        //         case 'partial_refund':
        //             $status = "Partial Refund";
        //             break;
        //         case 'cancel':
        //             $status = "Dibatalkan";
        //             break;
        //         default:
        //             $status = "No Status";
        //             break;
        //     }
        // } else {
        //     $status = 'Forbidden';
        // }
        // $this->pemesananModel->set([
        //     'nama_cus' => $namaPem,
        //     'email_cus' => $emailPem,
        //     'hp_cus' => $nohpPem,
        //     'nama_pen' => $nama,
        //     'hp_pen' => $nohp,
        //     'alamat_pen' => json_encode($alamat),
        //     'resi' => "Menunggu pengiriman",
        //     'items' => json_encode($produk),
        //     'status' => $status,
        //     'kurir' => 'kosong'
        // ])->update();

        session()->setFlashdata('id_pesanan', $hasilMidtrans['order_id']);
        return redirect()->to($hasilMidtrans['payment_url']);
    }
    public function actionCheckout()
    {
        $namaPen = $this->request->getVar('namaPen');
        $nohpPen = $this->request->getVar('nohpPen');
        $nama = $this->request->getVar('nama');
        $alamat = $this->request->getVar('alamat');
        $nohp = $this->request->getVar('nohp');
        $email = $this->request->getVar('email');
        $paketData = $this->request->getVar('paket');
        $items = $this->request->getVar('produk');
        $keranjangJson = $this->request->getVar('keranjang');
        $paket = (int)explode("@", base64_decode($paketData))[0];
        $kurir = explode("@", base64_decode($paketData))[1];

        // $getPembeli = $this->pembeliModel->getPembeli($email);
        // $keranjang = json_decode($getPembeli['keranjang'], true);
        $keranjang = json_decode($keranjangJson, true);
        // $produk = [];
        $jumlah = [];
        $subtotal = 0;
        $total = 0;
        $itemDetails = [];
        if (!empty($keranjang)) {
            foreach ($keranjang as $element) {
                $produknya = $this->barangModel->getBarang($element['id']);
                // array_push($produk, $produknya);
                array_push($jumlah, $element['jumlah']);
                $persen = (100 - $produknya['diskon']) / 100;
                $hasil = round($persen * $produknya['harga']);
                $subtotal += $hasil * $element['jumlah'];
                $item = array(
                    'id' => $produknya["id"],
                    'price' => $hasil,
                    'quantity' => $element['jumlah'],
                    'name' => $produknya["nama"] . " (" . $element['varian'] . ")",
                );
                array_push($itemDetails, $item);
            }
            // $item = array(
            //     'id' => 'Biaya Tambahan',
            //     'price' => $paket,
            //     'quantity' => 1,
            //     'name' => 'Biaya Ongkir',
            // );
            $biayaadmin = array(
                'id' => 'Biaya Admin',
                'price' => 5000,
                'quantity' => 1,
                'name' => 'Biaya Admin',
            );
            // array_push($itemDetails, $item);
            array_push($itemDetails, $biayaadmin);
            $total = $subtotal + $paket + 5000;
        }

        \Midtrans\Config::$serverKey = "";
        \Midtrans\Config::$isProduction = false;
        $pesananke = $this->pemesananModel->orderBy('id', 'desc')->first();
        $idFix = "JM" . (sprintf("%08d", $pesananke ? ((int)$pesananke['id'] + 1) : 1));
        $randomId = rand();
        $stringData = $email . "&" . $nama . "&" . $nohp . "&" . $namaPen . "&" . $nohpPen . "&" . $alamat . "&" . $idFix . "&" . str_replace("&", "@", $kurir) . "&" . $items;
        $params = array(
            'transaction_details' => array(
                'order_id' => $idFix,
                //'order_id' => $randomId,
                'gross_amount' => $total,
            ),
            'callbacks' => array(
                'finish' => "https://lunareafurniture.com/finish_url/JSM-zWYWObdPEKlHA0PWP6BN/" . $stringData,
            ),
            'customer_details' => array(
                'email' => $email,
                'first_name' => $nama,
                'phone' => $nohp,
                'billing_address' => array(
                    'email' => $email,
                    'first_name' => $nama,
                    'phone' => $nohp,
                    'address' => json_decode($alamat, true)['alamat'],
                ),
                'shipping_address' => array(
                    'email' => $email,
                    'first_name' => $namaPen,
                    'phone' => $nohpPen,
                    'address' => json_decode($alamat, true)['alamat'],
                )
            ),
            'item_details' => $itemDetails
        );
        $snapToken = \Midtrans\Snap::getSnapToken($params);
        $arr = array(
            'snapToken' => $snapToken,
            'email' => $email,
            'alamat' => $alamat,
            'nama' => $nama,
            'phone' => $nohp,
        );
        return $this->response->setJSON($arr, false);
    }

    public function tracking($tipe, $resi)
    {
        $curl = curl_init();
        if ($tipe == "da") {
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://staging.dakotacargo.co.id/api/trace/?b=" . $resi,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return "cURL Error #:" . $err;
            }
            $hasilnya = json_decode($response, true)['detail'];
        } else {
            switch ($tipe) {
                case 'je':
                    $kurir = 'jne';
                    break;
                case 'jt':
                    $kurir = 'jnt';
                    break;
                case 'wa':
                    $kurir = 'wahana';
                    break;
                case 'in':
                    $kurir = 'indah';
                    break;
            }
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://pro.rajaongkir.com/api/waybill",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "waybill=SOCAG00183235715&courier=" . $kurir,
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded",
                    "key: your-api-key"
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return "cURL Error #:" . $err;
            }
            $hasilnya = json_decode($response, true)['rajaongkir']['results']['manifest'];
        }


        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        // $hasilnya= [
        //     [
        //     'tanggal' => "9/3/2022 1:15:56 PM",
        //     "keterangan"=> "Barang Diterima Oleh : JAKARTA CABANG Toko Purnama Baru, Jam :01:15",
        //     "posisi"=> "DITERIMA",
        //     "status"=> "Delivered"
        // ],
        //     [
        //     "tanggal"=> "9/3/2022 11:10:05 AM",
        //     "keterangan"=> "Barang  Diloper Oleh Petugas :  JAKARTA CABANG 002000062/09/2022/LA",
        //     "posisi"=> "Jakarta Timur",
        //     "status"=> "shipped"
        //     ],
        //     [
        //     "tanggal"=> "9/3/2022 10:13:05 AM",
        //     "keterangan"=> "Barang Sampai (Transit) di : JAKARTA CABANG ",
        //     "posisi"=> "Jakarta Timur",
        //     "status"=> "shipped"
        //     ],

        // ];

        // $hasilnyaRO = [
        //     [
        //        "manifest_code"=>"1",
        //        "manifest_description"=>"Manifested",
        //        "manifest_date"=>"2015-03-04",
        //        "manifest_time"=>"03:41",
        //        "city_name"=>"SOLO"
        // ],
        //     [
        //        "manifest_code"=>"2",
        //        "manifest_description"=>"On Transit",
        //        "manifest_date"=>"2015-03-04",
        //        "manifest_time"=>"15:44",
        //        "city_name"=>"JAKARTA"
        // ],
        //     [
        //        "manifest_code"=>"3",
        //        "manifest_description"=>"Received On Destination",
        //        "manifest_date"=>"2015-03-05",
        //        "manifest_time"=>"08:57",
        //        "city_name"=>"PALEMBANG"
        // ],
        // ];

        $data = [
            'title' => 'Tracking',
            'hasilnya' => $hasilnya,
            'bulan' => $bulan,
        ];
        return view('pages/tracking', $data);
    }
    public function transaction()
    {
        $email = session()->get("email");
        $detailTransaksi = [];
        if ($email != 'tamu')
            $detailTransaksi = $this->pemesananModel->getPemesananCus($email);
        else {
            $transaksi = session()->get("transaksi");
            foreach ($transaksi as $idMid) {
                array_push($detailTransaksi, $this->pemesananModel->getPemesanan($idMid));
            }
        }
        $data = [
            'title' => 'Transaksi Pembayaran',
            'transaksi' => $detailTransaksi,
        ];
        return view('pages/transaction', $data);
    }
    public function addTransaction()
    {
        $bodyJson = $this->request->getBody();
        $body = json_decode($bodyJson, true);
        $getPembeli = $this->pembeliModel->getPembeli($body['emailCus']);
        $transaksi = json_decode($getPembeli['transaksi'], true);
        array_push($transaksi, $body['idMid']);

        $d = strtotime("+7 Hours");
        $tanggal = date("d/m/Y", $d);

        $this->pembeliModel->where('email_user', $body['emailCus'])->set(['transaksi' => json_encode($transaksi)])->update();
        $this->pemesananModel->insert([
            'nama_cus' => $body['namaCus'],
            'email_cus' => $body['emailCus'],
            'hp_cus' => $body['hpCus'],
            'nama_pen' => $body['namaPen'],
            'hp_pen' => $body['hpPen'],
            'alamat_pen' => json_encode($body['alamatPen']),
            'resi' => $body['resi'],
            'id_midtrans' => $body['idMid'],
            'items' => json_encode($body['items']),
            'status' => $body['status'],
            'kurir' => $body['kurir'],
            'data_mid' => json_encode($body['dataMid']),
        ]);

        $arr = array(
            'success' => true,
            'transaksi' => implode("-", $transaksi),
            'hasil' => [
                'nama_cus' => $body['namaCus'],
                'email_cus' => $body['emailCus'],
                'hp_cus' => $body['hpCus'],
                'nama_pen' => $body['namaPen'],
                'hp_pen' => $body['hpPen'],
                'alamat_pen' => $body['alamatPen'],
                'resi' => $body['resi'],
                'id_midtrans' => $body['idMid'],
                'items' => $body['items'],
                'status' => $body['status'],
                'kurir' => $body['kurir'],
                'data_mid' => json_encode($body['dataMid']),
            ]
        );
        return $this->response->setJSON($arr, false);
    }
    public function afterAddTransaction($transaksi)
    {
        $hasilnya = explode("-", $transaksi);
        session()->set(['transaksi' => $hasilnya]);
        return redirect()->to('/transaction');
    }
    public function finishUrlMid($code, $status)
    {
        // dd([
        //     'code' => $code,
        //     'status' => $status
        // ]);
        if ($code != "JSM-zWYWObdPEKlHA0PWP6BN") {
            return redirect()->to("/");
        }
        // session()->setFlashdata('id_pesanan', 'JSM0000000');
        switch ($status) {
            case 'success':
                return redirect()->to("/successpay");
                break;
            case 'pending':
                return redirect()->to("/progresspay");
                break;
            case 'error':
                return redirect()->to("/errorpay");
                break;
            default:
                return redirect()->to("/errorpay");
                break;
        }
    }
    public function finishUrl($code, $data = false)
    {
        if ($code != "JSM-zWYWObdPEKlHA0PWP6BN") {
            return redirect()->to("/");
        }
        if ($data) {
            $dataArr = explode("&", $data);
            $email = $dataArr[0];
            $nama = $dataArr[1];
            $nohp = $dataArr[2];
            $namaPen = $dataArr[3];
            $nohpPen = $dataArr[4];
            $alamat = $dataArr[5];
            $orderId = $dataArr[6];
            $kurir = str_replace("@", "&", $dataArr[7]);
            $items = $dataArr[8];

            //pengurangan stok produk
            $itemsArr = json_decode($items, true);
            foreach ($itemsArr as $i) {
                $barangCurr = $this->barangModel->where('nama', rtrim(explode("(", $i['name'])[0]))->first();
                $this->barangModel->where('nama', rtrim(explode("(", $i['name'])[0]))->set([
                    'stok' => $barangCurr['stok'] - $i['quantity']
                ])->update();
            }

            $this->pemesananModel->where('id_midtrans', $orderId)->set([
                'nama_cus' => $nama,
                'email_cus' => $email,
                'hp_cus' => $nohp,
                'nama_pen' => $namaPen,
                'hp_pen' => $nohpPen,
                'alamat_pen' => $alamat,
                'resi' => "Menunggu pengiriman " . $kurir,
                'items' => $items,
                'kurir' => $kurir,
            ])->update();
            $pemesananSelected = $this->pemesananModel->getPemesanan($orderId);
            session()->setFlashdata('id_pesanan', $orderId);

            $transaksi = session()->get('transaksi');
            array_push($transaksi, $orderId);
            session()->set(['transaksi' => $transaksi]);

            switch ($pemesananSelected['status']) {
                case 'Proses':
                    return redirect()->to("/successpay");
                    break;
                case 'Menunggu Pembayaran':
                    return redirect()->to("/progresspay");
                    break;
                case 'Kadaluarsa':
                    return redirect()->to("/expirepay");
                    break;
                case 'Ditolak':
                    return redirect()->to("/denypay");
                    break;
                case 'Gagal':
                    return redirect()->to("/failurepay");
                    break;
                case 'Dibatalkan':
                    return redirect()->to("/cancelpay");
                    break;
                default:
                    return redirect()->to("/errorpay");
                    break;
            }
        } else {
            session()->setFlashdata('id_pesanan', 'JSM0010101010101');
            return redirect()->to("/errorpay");
        }
    }
    public function updateTransaction()
    {
        $arr = [
            'success' => true,
        ];
        $bodyJson = $this->request->getBody();
        $body = json_decode($bodyJson, true);
        $order_id = $body['order_id'];
        $fraud = $body['fraud_status'];
        if (isset($body['custom_field1'])) {
            $customField = json_decode($body['custom_field1'] . (isset($body['custom_field2']) ? $body['custom_field2'] : '') . (isset($body['custom_field3']) ? $body['custom_field3'] : ''), true);
        }
        if ($fraud == "accept") {
            switch ($body['transaction_status']) {
                case 'settlement':
                    $status = "Proses";
                    break;
                case 'capture':
                    $status = "Proses";
                    break;
                case 'pending':
                    $status = "Menunggu Pembayaran";
                    break;
                case 'expire':
                    $status = "Kadaluarsa";
                    break;
                case 'deny':
                    $status = "Ditolak";
                    break;
                case 'failure':
                    $status = "Gagal";
                    break;
                case 'refund':
                    $status = "Refund";
                    break;
                case 'partial_refund':
                    $status = "Partial Refund";
                    break;
                case 'cancel':
                    $status = "Dibatalkan";
                    break;
                default:
                    $status = "No Status";
                    break;
            }
        } else {
            $status = 'Forbidden';
        }

        $order_id_first_char = substr($order_id, 0, 1);
        if ($order_id_first_char == 'L') {
            $dataTransaksi_curr = $this->pemesananModel->getPemesanan($order_id);
            if (isset($dataTransaksi_curr)) {
                $dataMid_curr = json_decode($dataTransaksi_curr['data_mid'], true);
                $dataMid_curr['transaction_status'] = $body['transaction_status'];
                $this->pemesananModel->where('id_midtrans', $order_id)->set([
                    'status' => $status,
                    'data_mid' => json_encode($dataMid_curr),
                ])->update();

                //reset jumlah produk
                if ($status == 'Kadaluarsa' || $status == 'Ditolak' || $status == 'Gagal') {
                    $dataTransaksiFulDariDatabase = $this->pemesananModel->where('id_midtrans', $order_id)->first();
                    $dataTransaksiFulDariDatabase_items = json_decode($dataTransaksiFulDariDatabase['items'], true);
                    foreach ($dataTransaksiFulDariDatabase_items as $item) {
                        $barangCurr = $this->barangModel->where('nama', rtrim(explode("(", $item['name'])[0]))->first();
                        $this->barangModel->where('nama', rtrim(explode("(", $item['name'])[0]))->set([
                            'stok' => $barangCurr['stok'] + $item['quantity']
                        ])->update();
                    }
                }
            } else {
                $this->pemesananModel->insert([
                    'email_cus' => $customField['e'],
                    'nama_pen' => $customField['n'],
                    'hp_pen' => $customField['h'],
                    'alamat_pen' => $customField['a'],
                    'resi' => 'Menunggu pengiriman',
                    'items' => json_encode($customField['i']),
                    'kurir' => 'kosong',
                    'id_midtrans' => $order_id,
                    'status' => $status,
                    'data_mid' => json_encode($body),
                ]);

                //pengurangan stok produk
                if ($status == 'Kadaluarsa' || $status == 'Ditolak' || $status == 'Gagal') {
                    $dataTransaksiFulDariDatabase = $this->pemesananModel->where('id_midtrans', $order_id)->first();
                    $dataTransaksiFulDariDatabase_items = json_decode($dataTransaksiFulDariDatabase['items'], true);
                    foreach ($dataTransaksiFulDariDatabase_items as $item) {
                        $barangCurr = $this->barangModel->where('nama', rtrim(explode("(", $item['name'])[0]))->first();
                        $this->barangModel->where('nama', rtrim(explode("(", $item['name'])[0]))->set([
                            'stok' => $barangCurr['stok'] - $item['quantity']
                        ])->update();
                    }
                }
            }
        } else if ($order_id_first_char == 'I') {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://ilenafurniture.com/updatetransaction",
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($body),
                CURLOPT_HTTPHEADER => array(
                    "Accept: application/json",
                    "Content-Type: application/json",
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return "cURL Error #:" . $err;
            }
            $arr['hasil_curl'] = json_decode($response, true);
        }
        // $this->pembeliModel->where('email_user', 'sahrulcbm@gmail.com')->set(['transaksi' => json_encode($body)])->update();

        return $this->response->setJSON($arr, false);
    }
    public function updateTransactionCoreAPI()
    {
        $bodyJson = $this->request->getBody();
        $body = json_decode($bodyJson, true);
        $order_id = $body['order_id'];
        $fraud = $body['fraud_status'];
        // if (isset($body['custom_field1'])) {
        //     $customField = json_decode($body['custom_field1'] . (isset($body['custom_field2']) ? $body['custom_field2'] : '') . (isset($body['custom_field3']) ? $body['custom_field3'] : ''), true);
        // }
        if ($fraud == "accept") {
            switch ($body['transaction_status']) {
                case 'settlement':
                    $status = "Proses";
                    break;
                case 'capture':
                    $status = "Proses";
                    break;
                case 'pending':
                    $status = "Menunggu Pembayaran";
                    break;
                case 'expire':
                    $status = "Kadaluarsa";
                    break;
                case 'deny':
                    $status = "Ditolak";
                    break;
                case 'failure':
                    $status = "Gagal";
                    break;
                case 'refund':
                    $status = "Refund";
                    break;
                case 'partial_refund':
                    $status = "Partial Refund";
                    break;
                case 'cancel':
                    $status = "Dibatalkan";
                    break;
                default:
                    $status = "No Status";
                    break;
            }
        } else {
            $status = 'Forbidden';
        }

        $order_id_first_char = substr($order_id, 0, 1);
        if ($order_id_first_char == 'J') {
            $dataTransaksi_curr = $this->pemesananModel->getPemesanan($order_id);
            if (isset($dataTransaksi_curr)) {
                $dataMid_curr = json_decode($dataTransaksi_curr['data_mid'], true);
                $dataMid_curr['transaction_status'] = $body['transaction_status'];
                $this->pemesananModel->where('id_midtrans', $order_id)->set([
                    'status' => $status,
                    'data_mid' => json_encode($dataMid_curr),
                ])->update();

                //reset jumlah produk
                if ($status == 'Kadaluarsa' || $status == 'Ditolak' || $status == 'Gagal') {
                    $dataTransaksiFulDariDatabase = $this->pemesananModel->where('id_midtrans', $order_id)->first();
                    $dataTransaksiFulDariDatabase_items = json_decode($dataTransaksiFulDariDatabase['items'], true);
                    foreach ($dataTransaksiFulDariDatabase_items as $item) {
                        $barangCurr = $this->barangModel->where('nama', rtrim(explode("(", $item['name'])[0]))->first();
                        $this->barangModel->where('nama', rtrim(explode("(", $item['name'])[0]))->set([
                            'stok' => $barangCurr['stok'] + $item['quantity']
                        ])->update();
                    }
                }
            } else {
                // $this->pemesananModel->insert([
                //     'email_cus' => $customField['e'],
                //     'nama_pen' => $customField['n'],
                //     'hp_pen' => $customField['h'],
                //     'alamat_pen' => $customField['a'],
                //     'resi' => 'Menunggu pengiriman',
                //     'items' => json_encode($customField['i']),
                //     'kurir' => 'kosong',
                //     'id_midtrans' => $order_id,
                //     'status' => $status,
                //     'data_mid' => json_encode($body),
                // ]);
                $this->pemesananModel->insert([
                    'id_midtrans' => $order_id,
                    'status' => $status,
                    'data_mid' => json_encode($body),
                ]);
            }
        } else if ($order_id_first_char == 'I') {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://ilenafurniture.com/updatetransaction",
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($body),
                CURLOPT_HTTPHEADER => array(
                    "Accept: application/json",
                    "Content-Type: application/json"
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return "cURL Error #:" . $err;
            }
        }
        // $this->pembeliModel->where('email_user', 'sahrulcbm@gmail.com')->set(['transaksi' => json_encode($body)])->update();
        $arr = [
            'success' => true,
        ];
        return $this->response->setJSON($arr, false);
    }

    public function updateTransactionBackup()
    {
        // $bodyJson = $this->request->getBody();
        // $body = json_decode($bodyJson, true);
        \Midtrans\Config::$serverKey = "";
        \Midtrans\Config::$isProduction = true;

        $notif = new \Midtrans\Notification();
        $transaction = $notif->transaction_status;
        $order_id = $notif->order_id;
        $fraud = $notif->fraud_status;
        $customerDetails = $notif->customer_details;

        if ($fraud == "accept") {
            switch ($transaction) {
                case 'settlement':
                    $status = "Proses";
                    break;
                case 'capture':
                    $status = "Proses";
                    break;
                case 'pending':
                    $status = "Menunggu Pembayaran";
                    break;
                case 'expire':
                    $status = "Kadaluarsa";
                    break;
                case 'deny':
                    $status = "Ditolak";
                    break;
                case 'failure':
                    $status = "Gagal";
                    break;
                case 'refund':
                    $status = "Refund";
                    break;
                case 'partial_refund':
                    $status = "Partial Refund";
                    break;
                case 'cancel':
                    $status = "Dibatalkan";
                    break;
                default:
                    $status = "No Status";
                    break;
            }
            $dataTransaksi_curr = $this->pemesananModel->getPemesanan($order_id);

            if (isset($dataTransaksi_curr)) {
                $this->pembeliModel->where('email_user', 'sahrulcbm@gmail.com')->set(['transaksi' => json_encode($customerDetails)])->update();
            }
            $dataMid_curr = json_decode($dataTransaksi_curr['data_mid'], true);
            $dataMid_curr['transaction_status'] = $transaction;
            $this->pemesananModel->where('id_midtrans', $order_id)->set([
                'status' => $status,
                'data_mid' => json_encode($dataMid_curr),
            ])->update();
        }
        $arr = [
            'success' => true,
        ];
        return $this->response->setJSON($arr, false);
    }
    public function orderLocal()
    {
        $data = [
            'title' => 'Peroses Pembayaran',
            'dataMid' => [
                'gross_amount' => 156000
            ],
            'va_number' => '',
            'biller_code' => '029941234123',
            'bank' => 'mandiri',
            'waktuExpire' => '24 Maret 2024'
        ];
        return view('pages/orderExpire', $data);
    }
    public function order($id_midtrans = false)
    {
        $pemesanan = $this->pemesananModel->getPemesanan($id_midtrans);
        $carapembayaran = [
            'mandiri' => [
                [
                    'nama' => 'Livin by Mandiri',
                    'isi' => '1. Pilih bayar pada menu utama.<br>
                                2. Pilih Ecommerce.<br>
                                3. Pilih Midtrans di bagian penyedia jasa.<br>
                                4. Masukkan nomor virtual account pada bagian kode bayar.<br>
                                5. Klik lanjutkan untuk konfirmasi.<br>
                                6. Pembayaran selesai.'
                ],
                [
                    'nama' => 'ATM Mandiri',
                    'isi' => '1. Pilih bayar/beli pada menu utama.<br>
                                2. Pilih lainnya.<br>
                                3. Pilih multi payment.<br>
                                4. Masukkan kode perusahaan Midtrans 70012.<br>
                                5. Masukkan kode pembayaran, lalu konfirmasi.<br>
                                6. Pembayaran selesai.'
                ],
                [
                    'nama' => 'Mandiri Internet Banking',
                    'isi' => '1. Pilih bayar pada menu utama.<br>
                                2. Pilih multi payment.<br>
                                3. Pilih dari rekening.<br>
                                4. Pilih Midtrans di bagian penyedia jasa.<br>
                                5. Masukkan kode pembayaran, lalu konfirmasi.<br>
                                6. Pembayaran selesai.'
                ],
            ],
            'bni' => [
                [
                    'nama' => 'ATM BNI',
                    'isi' => '1. Pilih menu lain pada menu utama.<br>
                                2. Pilih transfer.<br>
                                3. Pilih ke rekening BNI.<br>
                                4. Masukkan nomor rekening pembayaran.<br>
                                5. Masukkan jumlah yang akan dibayar, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.<br>
                                7. Internet Banking'
                ],
                [
                    'nama' => 'BNI Internet Banking',
                    'isi' => '1. Pilih transaksi, lalu info & administrasi transfer.<br>
                                2. Pilih atur rekening tujuan.<br>
                                3. Masukkan informasi rekening, lalu konfirmasi.<br>
                                4. Pilih transfer, lalu transfer ke rekening BNI.<br>
                                5. Masukkan detail pembayaran, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.'
                ],
                [
                    'nama' => 'BNI Mobile Banking',
                    'isi' => '1. Pilih transfer.<br>
                                2. Pilih virtual account billing.<br>
                                3. Pilih rekening debit yang akan digunakan.<br>
                                4. Masukkan nomor virtual account, lalu konfirmasi.<br>
                                5. Pembayaran berhasil.'
                ],
            ],
            'bri' => [
                [
                    'nama' => 'ATM BRI',
                    'isi' => '1. Pilih transaksi lainnya pada menu utama.<br>
                                2. Pilih pembayaran.<br>
                                3. Pilih lainnya.<br>
                                4. Pilih BRIVA.<br>
                                5. Masukkan nomor BRIVA, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.'
                ],
                [
                    'nama' => 'IB BRI',
                    'isi' => '1. Pilih pembayaran & pembelian.<br>
                                2. Pilih BRIVA.<br>
                                3. Masukkan nomor BRIVA, lalu konfirmasi.<br>
                                4. Pembayaran berhasil.'
                ],
                [
                    'nama' => 'BRImo',
                    'isi' => '1. Pilih pembayaran.<br>
                                2. Pilih BRIVA.<br>
                                3. Masukkan nomor BRIVA, lalu konfirmasi.<br>
                                4. Pembayaran berhasil.'
                ],
            ],
            'permata' => [
                [
                    'nama' => 'ATM Permata/ALTO',
                    'isi' => '1. Pilih transaksi lainnya pada menu utama.<br>
                                2. Pilih pembayaran.<br>
                                3. Pilih pembayaran lainnya.<br>
                                4. Pilih virtual account.<br>
                                5. Masukkan nomor virtual account Permata, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.'
                ],
            ],
            'cimb' => [
                [
                    'nama' => 'ATM CIMB Niaga',
                    'isi' => '1. Pilih pembayaran pada menu utama.<br>
                                2. Pilih virtual account.<br>
                                3. Masukkan nomor virtual account, lalu konfirmasi.<br>
                                4. Pembayaran selesai.'
                ],
                [
                    'nama' => 'OCTO Clicks',
                    'isi' => '1. Pilih pembayaran tagihan pada menu utama.<br>
                                2. Pilih mobile rekening virtual.<br>
                                3. Masukkan nomor virtual account, lalu klik lanjut untuk verifikasi detail.<br>
                                4. Pilih kirim OTP untuk lanjut.<br>
                                5. Masukkan OTP yang dikirimkan ke nomor HP Anda, lalu konfirmasi.<br>
                                6. Pembayaran selesai.'
                ],
                [
                    'nama' => 'OCTO Mobile',
                    'isi' => '1. Pilih menu transfer.<br>
                                2. Pilih transfer to other CIMB Niaga account.<br>
                                3. Pilih sumber dana: CASA atau rekening ponsel.<br>
                                4. Masukkan nomor virtual account.<br>
                                5. Masukkan jumlah yang akan dibayar.<br>
                                6. Ikuti instruksi untuk menyelesaikan pembayaran.<br>
                                7. Pembayaran selesai.'
                ],
            ],
            'qris' => [
                [
                    'nama' => 'QRIS',
                    'isi' => '1. Buka aplikasi yang mendukung pembayaran dengan QRIS.<br>
                                2. Download atau pindai QRIS pada layar.<br>
                                3. Konfirmasi pembayaran pada aplikasi.<br>
                                4. Pembayaran berhasil.'
                ],
            ],
            'gopay' => [
                [
                    'nama' => 'GoPay',
                    'isi' => '1. Klik Bayar sekarang.<br>
                                2. Aplikasi Gojek atau GoPay akan terbuka.<br>
                                3. Konfirmasi pembayaran di aplikasi Gojek atau GoPay.<br>
                                4. Pembayaran berhasil.'
                ],
            ],
        ];
        if ($pemesanan) {
            $dataMid = json_decode($pemesanan['data_mid'], true);
            dd($pemesanan);
            $kurir = $pemesanan['kurir'];
            $items = json_decode($pemesanan['items'], true);
            switch ($pemesanan['status']) {
                case 'Menunggu Pembayaran':
                    $biller_code = "";
                    $bank = "";
                    switch ($dataMid['payment_type']) {
                        case 'bank_transfer':
                            if (isset($dataMid['permata_va_number'])) {
                                $va_number = $dataMid['permata_va_number'];
                                $bank = "permata";
                            } else {
                                $va_number = $dataMid['va_numbers'][0]['va_number'];
                                $bank = $dataMid['va_numbers'][0]['bank'];
                            }
                            break;
                        case 'echannel':
                            $va_number = $dataMid['bill_key'];
                            $biller_code = $dataMid['biller_code'];
                            $bank = "mandiri";
                            break;
                        case 'qris':
                            $va_number = 'https://api.midtrans.com/v2/qris/' . $dataMid['transaction_id'] . '/qr-code';
                            $bank = "qris";
                            break;
                        default:
                            $va_number = "";
                            break;
                    }

                    $waktuExpire = strtotime($dataMid['expiry_time']);
                    $waktuCurr = strtotime("+7 Hours");
                    $waktuSelisih = $waktuExpire - $waktuCurr;
                    $waktu = date("H:i:s", $waktuSelisih);

                    $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                    $data = [
                        'title' => 'Peroses Pembayaran',
                        'pemesanan' => $pemesanan,
                        'dataMid' => $dataMid,
                        'va_number' => $va_number,
                        'biller_code' => $biller_code,
                        'bank' => $bank,
                        'items' => $items,
                        'waktu' => $waktu,
                        'caraPembayaran' => $carapembayaran[$bank],
                        'waktuExpire' => date("d", $waktuExpire) . " " . $bulan[(int)date("m", $waktuExpire) - 1] . " " . date("Y H:i:s", $waktuExpire)
                    ];
                    return view('pages/orderProgress', $data);
                    break;
                case 'Proses':
                    $biller_code = "";
                    $bank = "";
                    switch ($dataMid['payment_type']) {
                        case 'bank_transfer':
                            if (isset($dataMid['permata_va_number'])) {
                                $va_number = $dataMid['permata_va_number'];
                                $bank = "permata";
                            } else {
                                $va_number = $dataMid['va_numbers'][0]['va_number'];
                                $bank = $dataMid['va_numbers'][0]['bank'];
                            }
                            break;
                        case 'echannel':
                            $va_number = $dataMid['bill_key'];
                            $biller_code = $dataMid['biller_code'];
                            $bank = "mandiri";
                            break;
                        case 'qris':
                            $va_number = 'https://api.midtrans.com/v2/qris/' . $dataMid['transaction_id'] . '/qr-code';
                            $bank = "qris";
                            break;
                        default:
                            $va_number = "";
                            break;
                    }

                    $data = [
                        'title' => 'Pembayaran Sukes',
                        'pemesanan' => $pemesanan,
                        'dataMid' => $dataMid,
                        'kurir' => $kurir,
                        'items' => $items,
                        'bank' => $bank,
                        'va_number' => $va_number,
                        'biller_code' => $biller_code,
                        'caraPembayaran' => $carapembayaran[$bank],
                    ];
                    return view('pages/orderShipping', $data);
                    break;
                case 'Dikirim':
                    $biller_code = "";
                    $bank = "";
                    switch ($dataMid['payment_type']) {
                        case 'bank_transfer':
                            if (isset($dataMid['permata_va_number'])) {
                                $va_number = $dataMid['permata_va_number'];
                                $bank = "permata";
                            } else {
                                $va_number = $dataMid['va_numbers'][0]['va_number'];
                                $bank = $dataMid['va_numbers'][0]['bank'];
                            }
                            break;
                        case 'echannel':
                            $va_number = $dataMid['bill_key'];
                            $biller_code = $dataMid['biller_code'];
                            $bank = "mandiri";
                            break;
                        case 'qris':
                            $va_number = 'https://api.midtrans.com/v2/qris/' . $dataMid['transaction_id'] . '/qr-code';
                            $bank = "qris";
                            break;
                        default:
                            $va_number = "";
                            break;
                    }

                    $data = [
                        'title' => 'Pembayaran Sukes',
                        'pemesanan' => $pemesanan,
                        'dataMid' => $dataMid,
                        'kurir' => $kurir,
                        'items' => $items,
                        'bank' => $bank,
                        'va_number' => $va_number,
                        'biller_code' => $biller_code,
                        'caraPembayaran' => $carapembayaran[$bank],
                    ];
                    return view('pages/orderShipping', $data);
                    break;
                case 'Kadaluarsa':
                    $biller_code = "";
                    $bank = "";
                    switch ($dataMid['payment_type']) {
                        case 'bank_transfer':
                            if (isset($dataMid['permata_va_number'])) {
                                $va_number = $dataMid['permata_va_number'];
                                $bank = "permata";
                            } else {
                                $va_number = $dataMid['va_numbers'][0]['va_number'];
                                $bank = $dataMid['va_numbers'][0]['bank'];
                            }
                            break;
                        case 'echannel':
                            $va_number = $dataMid['bill_key'];
                            $biller_code = $dataMid['biller_code'];
                            $bank = "mandiri";
                            break;
                        case 'qris':
                            $va_number = 'https://api.midtrans.com/v2/qris/' . $dataMid['transaction_id'] . '/qr-code';
                            $bank = "qris";
                            break;
                        default:
                            $va_number = "";
                            break;
                    }

                    $waktuExpire = strtotime($dataMid['expiry_time']);
                    $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                    $data = [
                        'title' => 'Peroses Pembayaran',
                        'pemesanan' => $pemesanan,
                        'dataMid' => $dataMid,
                        'va_number' => $va_number,
                        'biller_code' => $biller_code,
                        'bank' => $bank,
                        'caraPembayaran' => $carapembayaran[$bank],
                        'waktuExpire' => date("d", $waktuExpire) . " " . $bulan[(int)date("m", $waktuExpire) - 1] . " " . date("Y H:i:s", $waktuExpire)
                    ];
                    return view('pages/orderExpire', $data);
                    break;
                case 'Ditolak':
                    $status = "Ditolak";
                    break;
                case 'Gagal':
                    $status = "Gagal";
                    break;
                case 'Refund':
                    $status = "Refund";
                    break;
                case 'Partial Refund':
                    $status = "Partial Refund";
                    break;
                case 'Dibatalkan':
                    $status = "Dibatalkan";
                    break;
            }
        } else {
            return redirect()->to('/');
        }
    }
    public function account()
    {
        $nama = session()->get("nama");
        $nohp = session()->get("nohp");
        $data = [
            'title' => 'Akun Saya',
            'nama' => $nama,
            'nohp' => $nohp,
        ];
        return view('pages/account', $data);
    }

    public function editAccount()
    {
        $email = session()->get("email");
        $role = session()->get("role");
        $sandi = $this->request->getVar('sandi');
        $nama = $this->request->getVar('nama');
        $nohp = $this->request->getVar('nohp');

        if ($sandi != '') {
            $this->userModel->where('email', $email)->set([
                'sandi' => password_hash($sandi, PASSWORD_DEFAULT),
            ])->update();
        }
        if ($role == '0') {
            $this->pembeliModel->where('email_user', $email)->set([
                'nama' => $nama,
                'nohp' => $nohp,
            ])->update();

            session()->set([
                'nama' => $nama,
                'nohp' => $nohp,
            ]);
        }

        $data = [
            'title' => 'Akun Saya',
            'nama' => $nama,
            'nohp' => $nohp
        ];
        return view('pages/account', $data);
    }
    public function contact()
    {
        $data = [
            'title' => 'Kontak'
        ];
        return view('pages/contact', $data);
    }
    public function about()
    {
        $data = [
            'title' => 'Tentang'
        ];
        return view('pages/about', $data);
    }
    public function product($nama = false)
    {
        $produk = $this->barangModel->getBarangNama(urldecode($nama));
        $produksekategori = $this->barangModel->where('kategori', $produk['kategori'])->where('id !=', $produk['id'])->orderBy('tracking_pop', 'desc')->findAll(10, 0);
        // $gambarnya = $this->gambarBarangModel->getGambar($produk['id']);
        $varian = json_decode($produk['varian'], true);
        $dimensi = explode("X", $produk['dimensi']);

        $this->barangModel->where(['id' => $produk['id']])->set([
            'tracking_pop' => (int)$produk['tracking_pop'] + 1
        ])->update();

        $data = [
            'title' => $produk['nama'],
            'produk' => $produk,
            // 'gambar' => $gambarnya,
            'varian' => $varian,
            'dimensi' => $dimensi,
            'produksekategori' => $produksekategori,
            'msg' => session()->getFlashdata('msg'),
            'geser_container_melayang' => true
        ];
        return view('pages/product', $data);
    }

    public function productFilter($namaDash, $page = 1)
    {
        $nama = str_replace("-", " ", $namaDash);
        $pagination = (int)$page;
        if ($pagination > 1) {
            $hitungOffset = 20 * ($pagination - 1);
            $produk = $this->barangModel->like("pencarian", $nama, "both")->orderBy('pencarian', 'asc')->findAll(20, $hitungOffset);
        } else {
            $produk = $this->barangModel->like("pencarian", $nama, "both")->orderBy('pencarian', 'asc')->findAll(20, 0);
        }
        $semuaproduk = $this->barangModel->like("pencarian", $nama, "both")->orderBy('pencarian', 'asc')->findAll();

        $data = [
            'title' => 'Produk',
            'produk' => $produk,
            'nama' => $nama,
            'kategori' => false,
            'semuaProduk' => $semuaproduk,
            'page' => $page,
        ];
        return view('pages/all', $data);
    }

    public function invoice($id_mid)
    {
        $transaksi = $this->pemesananModel->getPemesanan($id_mid);
        $arr = [
            'id' => $transaksi['id'],
            'email_cus' => $transaksi['email_cus'],
            'nama_pen' => $transaksi['nama_pen'],
            'hp_pen' => $transaksi['hp_pen'],
            'alamat_pen' => $transaksi['alamat_pen'],
            'resi' => $transaksi['resi'],
            'id_midtrans' => $transaksi['id_midtrans'],
            'items' => json_decode($transaksi['items'], true),
            'status' => $transaksi['status'],
            'kurir' => $transaksi['kurir'],
            'data_mid' => json_decode($transaksi['data_mid'], true),
        ];
        $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        $data = [
            'title' => 'Print Preview',
            'transaksi' => $arr,
            'transaksiJson' => json_encode($arr),
            'bulan' => $bulan
        ];
        return view('pages/invoice', $data);
    }
    public function qris($string)
    {
        $auth = base64_encode("" . ":"); //yg kiri midtrans server key
        $order_id = explode("-", $string)[0];
        $amount = (int)explode("-", $string)[1];
        $body = [
            "payment_type" => "qris",
            "transaction_details" => [
                "order_id" => $order_id,
                "gross_amount" => $amount,
            ],
        ];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.midtrans.com/v2/charge",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                "authorization: Basic " . $auth,
                "content-type: application/json",
                "Accept: application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $qris = json_decode($response, true);
        dd($qris);
    }

    //============ ADMIN ==============//
    public function listForm()
    {
        $form = $this->formModel->getForm();
        $data = [
            'title' => 'List Formulir',
            'form' => $form
        ];
        return view('pages/listForm', $data);
    }
    public function listCustomer($page = 1)
    {
        $transaksiCus = $this->pemesananModel->getPemesananPage($page);
        $semuaTransaksiCus = $this->pemesananModel->getPemesanan();
        $transaksiCusNoJSON = [];
        foreach ($transaksiCus as $transaksi) {
            $arr = [
                'id' => $transaksi['id'],
                'email_cus' => $transaksi['email_cus'],
                'nama_pen' => $transaksi['nama_pen'],
                'hp_pen' => $transaksi['hp_pen'],
                'alamat_pen' => json_decode($transaksi['alamat_pen'], true),
                'resi' => $transaksi['resi'],
                'id_midtrans' => $transaksi['id_midtrans'],
                'items' => json_decode($transaksi['items'], true),
                'status' => $transaksi['status'],
                'kurir' => $transaksi['kurir'],
                'data_mid' => json_decode($transaksi['data_mid'], true),
            ];
            array_push($transaksiCusNoJSON, $arr);
        }
        $transaksiJson = json_encode($transaksiCusNoJSON);
        $data = [
            'title' => 'List Customer',
            'transaksiCus' => $transaksiCusNoJSON,
            'semuaTransaksiCus' => $semuaTransaksiCus,
            'transaksiJson' => $transaksiJson,
            'page' => $page
        ];
        return view('pages/listCustomer', $data);
    }
    public function pdf($id_mid)
    {
        $transaksi = $this->pemesananModel->getPemesanan($id_mid);
        $arr = [
            'id' => $transaksi['id'],
            'nama_cus' => $transaksi['nama_cus'],
            'email_cus' => $transaksi['email_cus'],
            'hp_cus' => $transaksi['hp_cus'],
            'nama_pen' => $transaksi['nama_pen'],
            'hp_pen' => $transaksi['hp_pen'],
            'alamat_pen' => json_decode($transaksi['alamat_pen'], true),
            'resi' => $transaksi['resi'],
            'id_midtrans' => $transaksi['id_midtrans'],
            'items' => json_decode($transaksi['items'], true),
            'status' => $transaksi['status'],
            'kurir' => $transaksi['kurir'],
            'data_mid' => json_decode($transaksi['data_mid'], true),
        ];
        $data = [
            'title' => 'Print Preview',
            'transaksi' => $arr,
            'transaksiJson' => json_encode($arr),
        ];
        return view('pages/pdf', $data);
    }
    public function editResi()
    {
        $bodyJson = $this->request->getBody();
        $body = json_decode($bodyJson, true);
        $this->pemesananModel->where('id_midtrans', $body['idMid'])->set([
            'resi' => $body['resi'],
            'kurir' => $body['kurir'],
            'status' => 'Dikirim',
        ])->update();

        $list_item = "";
        foreach ($body['data']['items'] as $item) {
            $list_item = $list_item . "<p>" . $item['quantity'] . " " . $item['name'] . "</p>";
        }
        $email = \Config\Services::email();
        $email->setFrom('no-reply@jasminefurniture.com', 'Jasmine Furniture');
        $email->setTo($body['data']['email_cus']);
        $email->setSubject('Jasmine Store - Pesananmu sudah dikirim');
        $email->setMessage("<p>Berikut nomor resi pada pesanan " . $body['data']['id_midtrans'] . "</p>
        <h1>" . $body['resi'] . '</h1>
        <p style="margin-bottom: 10px">' . $body['data']['kurir'] . '</p>
        <span style="margin-bottom: 10px>-------------------------------------------------</span>       
        <p style="margin-bottom: 10px"><b>Informasi terkait pesanan</b></p>
        <p>Nama : ' . $body['data']['nama_cus'] . '</p>
        <p>Email : ' . $body['data']['email_cus'] . '</p>
        <p style="margin-bottom: 10px">Kode Pesanan : ' . $body['data']['id_midtrans'] . '</p>
        <p>Item Pesanan :</p>' . $list_item);
        $email->send();

        $arr = [
            'success' => true,
            'status' => 'Dikirim',
            'resi' => $body['resi']
        ];
        return $this->response->setJSON($arr, false);
    }
    public function listProduct($page = 1)
    {
        $produk = $this->barangModel->getBarangPage($page);
        $semuaproduk = $this->barangModel->getBarang();
        $data = [
            'title' => 'List Produk',
            'produk' => $produk,
            'page' => $page,
            'semuaProduk' => $semuaproduk
        ];
        return view('pages/listProduct', $data);
    }
    public function addProduct()
    {
        $data = [
            'title' => 'Tambah Produk'
        ];
        return view('pages/addProduct', $data);
    }
    public function actionAddProduct()
    {
        $d = strtotime("+7 Hours");
        $tanggal = "B" . date("YmdHis", $d);
        $varian = explode(",", $this->request->getVar('varian'));
        $hasilVarian = count(explode(",", $this->request->getVar('varian'))) + (int)$this->request->getVar('jml_varian') - 1;
        $gambarnya = [];
        $insertGambarBarang = [
            'id' => $tanggal
        ];
        for ($i = 1; $i <= $hasilVarian; $i++) {
            array_push($gambarnya, file_get_contents($this->request->getFile("gambar" . $i)));
            $insertGambarBarang["gambar" . $i] = file_get_contents($this->request->getFile("gambar" . $i));
        }
        // dd([
        //     'varian' => $varian,
        //     'hasilVarian' => $hasilVarian,
        //     'gambar1' => file_get_contents($this->request->getFile("gambar1")),
        //     'gambarnya' => $gambarnya
        // ]);
        $this->barangModel->insert([
            'id'            => $tanggal,
            'nama'          => $this->request->getVar('nama'),
            'pencarian'     => $this->request->getVar('pencarian'),
            'gambar'        => $gambarnya[0],
            'harga'         => $this->request->getVar('harga'),
            'berat'         => $this->request->getVar('berat'),
            'stok'          => $this->request->getVar('stok'),
            'dimensi'       => $this->request->getVar('dimensi'),
            'deskripsi'     => $this->request->getVar('deskripsi'),
            'kategori'      => $this->request->getVar('kategori'),
            'subkategori'   => $this->request->getVar('subkategori'),
            'diskon'        => $this->request->getVar('diskon'),
            'varian'        => json_encode($varian),
            'jml_varian'    => $this->request->getVar('jml_varian'),
            'shopee'        => $this->request->getVar('shopee'),
            'tokped'        => $this->request->getVar('tokped'),
            'tiktok'        => $this->request->getVar('tiktok'),
            'youtube'       => $this->request->getVar('youtube'),
        ]);
        $this->gambarBarangModel->insert($insertGambarBarang);

        session()->setFlashdata('msg', 'Produk berhasil ditambahkan');
        return redirect()->to('/listproduct');
    }
    public function editProduct($id)
    {
        $produk = $this->barangModel->getBarang($id);
        $gambar = $this->gambarBarangModel->getGambar($id);
        $varian = json_decode($produk['varian'], true);
        if ($produk['pencarian'] == null || $produk['pencarian'] == '') {
            $diskon = '';
            $varianJadi = '';
            foreach ($varian as $va) {
                $varianJadi = $varianJadi . $produk['kategori'] . " " . $va . " " . str_replace("-", " ", $produk['subkategori']) . " " . $va . " ";
            }
            if ((int)$produk['diskon'] > 0) {
                $diskon = $produk['kategori'] . " promo " . str_replace("-", " ", $produk['subkategori']) . " promo " . $produk['kategori'] . " diskon " . str_replace("-", " ", $produk['subkategori']) . " diskon ";
            }

            $produk['pencarian'] = $produk['nama'] . " " . $produk['kategori'] . " elegan " . $produk['kategori'] . " simpel " . $produk['kategori'] . " minimalis " . $produk['kategori'] . " estetik " . $produk['kategori'] . " modern " . str_replace("-", " ", $produk['subkategori']) . " elegan " . str_replace("-", " ", $produk['subkategori']) . " simpel " . str_replace("-", " ", $produk['subkategori']) . " minimalis " . str_replace("-", " ", $produk['subkategori']) . " estetik " . str_replace("-", " ", $produk['subkategori']) . " modern " . $varianJadi . $diskon;
        }
        $data = [
            'title'     => 'Edit Produk',
            'produk'    => $produk,
            'gambar'    => $gambar,
            'varian'    => implode(',', $varian)
        ];
        return view('pages/editProduct', $data);
    }
    public function actionEditProduct($id)
    {
        $varian = explode(",", $this->request->getVar('varian'));
        // dd(file_get_contents($this->request->getFile("gambar1")));
        if (!empty($_FILES['gambar1']['tmp_name'])) {
            $hasilVarian = count(explode(",", $this->request->getVar('varian'))) + (int)$this->request->getVar('jml_varian') - 1;
            $gambarnya = [];
            $insertGambarBarang = [
                'id' => $id
            ];
            for ($i = 1; $i <= $hasilVarian; $i++) {
                array_push($gambarnya, file_get_contents($this->request->getFile("gambar" . $i)));
                $insertGambarBarang["gambar" . $i] = file_get_contents($this->request->getFile("gambar" . $i));
            }
            $this->barangModel->save([
                'id'            => $id,
                'nama'          => $this->request->getVar('nama'),
                'pencarian'     => $this->request->getVar('pencarian'),
                'gambar'        => $gambarnya[0],
                'harga'         => $this->request->getVar('harga'),
                'berat'         => $this->request->getVar('berat'),
                'stok'          => $this->request->getVar('stok'),
                'dimensi'       => $this->request->getVar('dimensi'),
                'deskripsi'     => $this->request->getVar('deskripsi'),
                'kategori'      => $this->request->getVar('kategori'),
                'subkategori'   => $this->request->getVar('subkategori'),
                'diskon'        => $this->request->getVar('diskon'),
                'varian'        => json_encode($varian),
                'jml_varian'    => $this->request->getVar('jml_varian'),
                'shopee'        => $this->request->getVar('shopee'),
                'tokped'        => $this->request->getVar('tokped'),
                'tiktok'        => $this->request->getVar('tiktok'),
                'youtube'       => $this->request->getVar('youtube'),
            ]);
            $this->gambarBarangModel->save($insertGambarBarang);
        } else {
            $this->barangModel->save([
                'id' => $id,
                'nama'          => $this->request->getVar('nama'),
                'pencarian'     => $this->request->getVar('pencarian'),
                'harga'         => $this->request->getVar('harga'),
                'berat'         => $this->request->getVar('berat'),
                'stok'          => $this->request->getVar('stok'),
                'dimensi'       => $this->request->getVar('dimensi'),
                'deskripsi'     => $this->request->getVar('deskripsi'),
                'kategori'      => $this->request->getVar('kategori'),
                'subkategori'   => $this->request->getVar('subkategori'),
                'diskon'        => $this->request->getVar('diskon'),
                'varian'        => json_encode($varian),
                'jml_varian'    => $this->request->getVar('jml_varian'),
                'shopee'        => $this->request->getVar('shopee'),
                'tokped'        => $this->request->getVar('tokped'),
                'tiktok'        => $this->request->getVar('tiktok'),
                'youtube'       => $this->request->getVar('youtube'),
            ]);
        }

        session()->setFlashdata('msg', 'Produk telah ditambahkan');
        return redirect()->to('/listproduct');
    }
    public function delProduct($id)
    {
        $produk = $this->barangModel->where('id', $id)->delete();
        $gambar = $this->gambarBarangModel->where('id', $id)->delete();
        return redirect()->to('/listproduct');
    }

    public function notFound()
    {
        return redirect()->to('/');
    }
}
