<?php
// config.php dosyasını dahil et
include_once "config.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = $_POST['mail'];
    $sifre = $_POST['sifre'];

    $db = dbBaglantisi();

    if ($db instanceof PDO) {
        $query = $db->prepare("SELECT * FROM kullanici WHERE mail = :mail AND sifre = :sifre");
        $query->bindParam(':mail', $mail);
        $query->bindParam(':sifre', $sifre);
        $query->execute();
        $kullanici = $query->fetch(PDO::FETCH_ASSOC);

        if ($kullanici) {
            // Kullanıcı bulundu, giriş yap
            $_SESSION['ad'] = $kullanici['ad']; // Kullanıcının adını oturum verilerine kaydet
            header("Location: index.php"); // Ana sayfaya yönlendir
            exit(); // Yönlendirme yapıldıktan sonra kodun devamını çalıştırmamak için exit kullanılmalı
        } else {
            // Kullanıcı bulunamadı, hata mesajı ayarla
            $error = "Hatalı giriş bilgileri. Lütfen tekrar deneyin.";
        }
    } else {
        // Veritabanına bağlanılamadı, hata mesajı ayarla
        $error = "Veritabanı bağlantısı yapılamadı.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'logout') {
    // Çıkış işlemi
    session_unset();
    session_destroy();
    // Ana sayfaya yönlendirme
    header("Location: index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>LaktaFarm</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/style.css" rel="stylesheet">


</head>

<body>
    <!-- db connection start-->


    <!-- db connection end-->


    <!-- Topbar Start -->
    <div class="container-fluid bg-light pt-3 d-none d-lg-block">
        <div class="container">
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <div class="container-fluid position-relative nav-bar p-0">
        <div class="container-lg position-relative p-0 px-lg-3" style="z-index: 9;">
            <nav class="navbar navbar-expand-lg bg-light navbar-light shadow-lg py-3 py-lg-0 pl-3 pl-lg-5">
                <img class="img-fluid" src="img/inekikon.png" style="height: 8%; width: 8%;" alt="">
                <a href="" class="navbar-brand">
                    <h1 class="m-0 text-primary"><span class="text-dark">Lakta</span>Farm</h1>
                </a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between px-3" id="navbarCollapse">
                    <div class="navbar-nav ml-auto py-0">
                        <a href="index.php" class="nav-item nav-link active">Ana Sayfa</a>

                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Döngüler</a>
                            <div class="dropdown-menu border-0 rounded-0 m-0">
                                <a href="service.php" class="dropdown-item">Pervis Periyodu</a>
                                <a href="single.html" class="dropdown-item">Kuru Dönem</a>
                                <a href="destination.html" class="dropdown-item">Sağım</a>
                                <a href="guide.html" class="dropdown-item">Gebelik</a>
                            </div>
                        </div>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">İnekler</a>
                            <div class="dropdown-menu border-0 rounded-0 m-0">
                                <a href="gunlukKontrol.html" class="dropdown-item">Günlük Takip</a>
                                <a href="dollemeTakip.html" class="dropdown-item">Dölleme Takip</a>
                                <a href="destination.html" class="dropdown-item">Gebe Takip</a>
                                <a href="single.html" class="dropdown-item">Kuru Dönem Takip</a>
                                <a href="inekKayit.html" class="dropdown-item">İnek Kayıt</a>
                            </div>
                        </div>
                        <?php if (isset($_SESSION['ad'])): ?>
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Merhaba, <?php echo $_SESSION['ad']; ?>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="profil.php">Profilim</a>
                                    <a class="dropdown-item" href="?action=logout">Çıkış Yap</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="#" class="nav-item nav-link" onclick="openModal('myModal')">Giriş Yap</a>
                        <?php endif; ?>

                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Profile Start-->
    <div class="row d-flex justify-content-center" style=" margin-top:60px; margin-bottom:60px">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <table>
                        <tr>
                            <center>
                                <p class="card-title">
                                    <?php if (isset($profile['resim_musteri'])): ?>
                                        <img src="<?php echo base_url($profile['resim_musteri']); ?>"
                                            alt="<?php echo $this->session->userdata('ktp'); ?>"
                                            style="width:100px;height:100px">
                                    <?php else: ?>
                                    <div style="width:100px;height:100px;background-color:lightgray;"></div>
                                <?php endif; ?>
                                </p>
                            </center>
                        </tr>
                        <tr>
                            <td>
                                <h5 class="card-title">Kimlik Numarası</h5>
                            </td>
                            <td>
                                <p class="card-title">
                                    <?php echo $_SESSION['kimlik_no']; ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h5 class="card-title">Ad-Soyad</h5>
                            </td>
                            <td>
                                <p class="card-title">
                                    <?php echo isset($kullanici['ad']) && isset($kullanici['soyad']) ? $kullanici['ad'] . ' ' . $kullanici['soyad'] : ''; ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h5 class="card-title">Email</h5>
                            </td>
                            <td>
                                <p class="card-title">
                                    <?php echo isset($kullanici['mail']) ? $kullanici['mail'] : ''; ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h5 class="card-title">Telefon Numarası</h5>
                            </td>
                            <td>
                                <p class="card-title">
                                    <?php echo isset($kullanici['tel_no']) ? $profile['tel_no'] : ''; ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                    <div class="row">
                        <div class="col-sm-12" align="center">
                            <button data-toggle="modal" data-target="#editModal" class="btn btn-primary">Bilgilerimi
                                Düzenle</button>

                            <a href="<?php echo base_url('profil/changepassword/' . (isset($kullanici['kullanici_id']) ? $kullanici['kullanici_id'] : '')); ?>"
                                class="btn btn-primary">Şifremi Değiştir</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Profile End-->

    <!-- Profile Edit Start-->



    <!-- Buton -->

    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Bilgilerimi Düzenle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="editprofile.php" method="post" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="ktp" class="control-label">Kimlik Numarası</label>
                                        <input type="text" class="form-control" name="ktp"
                                            value="<?php echo isset($profile['no_ktp_musteri']) ? $profile['no_ktp_musteri'] : ''; ?>">
                                        <input type="hidden" name="kode"
                                            value="<?php echo isset($profile['kd_musteri']) ? $profile['kd_musteri'] : ''; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="nama" class="control-label">Ad-Soyad</label>
                                        <input type="text" class="form-control" name="nama"
                                            value="<?php echo isset($profile['isim_musteri']) ? $profile['isim_musteri'] : ''; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="email" class="control-label">Email</label>
                                        <input type="email" class="form-control" name="email"
                                            value="<?php echo isset($profile['email_musteri']) ? $profile['email_musteri'] : ''; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="hp" class="control-label">Telefon Numarası</label>
                                        <input type="text" class="form-control" name="hp"
                                            value="<?php echo isset($profile['telpon_musteri']) ? $profile['telpon_musteri'] : ''; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="alamat" class="control-label">Adres</label>
                                        <input type="text" class="form-control" name="alamat"
                                            value="<?php echo isset($profile['adres_musteri']) ? $profile['adres_musteri'] : ''; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Profil Fotoğrafı</label>
                                        <img src="<?php echo isset($profile['resim_musteri']) ? $profile['resim_musteri'] : ''; ?>"
                                            alt="<?php echo isset($profile['no_ktp_musteri']) ? $profile['no_ktp_musteri'] : ''; ?>"
                                            style="width:150px;height:150px">
                                        <input type="file" class="form-control"
                                            value="<?php echo isset($profile['isim_musteri']) ? $profile['isim_musteri'] : ''; ?>"
                                            name="img">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Vazgeç</button>
                            <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>





    <!-- Profile Edit End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-white-50 py-5 px-sm-3 px-lg-5" style="margin-top: 90px;">
        <div class="row pt-5">
            <div class="col-lg-3 col-md-6 mb-5">
                <a href="" class="navbar-brand">
                    <h1 class="text-primary"><span class="text-white">Lakta</span>Farm</h1>
                </a>
                <h2>LaktaFarmla verimi katla</h2>

            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h5 class="text-white text-uppercase mb-4" style="letter-spacing: 5px;">Döngüler</h5>
                <div class="d-flex flex-column justify-content-start">
                    <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Pervis Periyodu</a>
                    <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Kuru Dönem</a>
                    <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Sağım</a>
                    <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Gebelik</a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h5 class="text-white text-uppercase mb-4" style="letter-spacing: 5px;">İnekler</h5>
                <div class="d-flex flex-column justify-content-start">
                    <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Günlük Takip</a>
                    <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Gebe Takip</a>
                    <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Dölleme Takip</a>
                    <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Kuru Dönem Takip</a>
                    <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>İnek Kayıt</a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h5 class="text-white text-uppercase mb-4" style="letter-spacing: 5px;">İletişim</h5>
                <p><i class="fa fa-map-marker-alt mr-2"></i>Kabaoğlu, Kocaeli Üniversitesi Umuttepe Kampüsü, A Kapısı,
                    41001 İzmit/Kocaeli</p>
                <p><i class="fa fa-phone-alt mr-2"></i>+012 345 67890</p>
                <p><i class="fa fa-envelope mr-2"></i>LaktaFarm@example.com</p>
            </div>
        </div>
    </div>

    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-angle-double-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

    <!-- Contact Javascript File -->
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    <script>
        function openEditModal() {
            $('#editModal').modal('show');
        }
    </script>
</body>

</html>