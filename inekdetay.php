<?php
// config.php dosyasını dahil et
include_once "config.php";

session_start();

$yasam_payi = ""; // varsayılan değer
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['hesaplaButton'])) {
    // hesapla butonuna tıklanmışsa giriş kontrolü yapma
    if (isset($_POST['kilo'])) {
        $kilo = $_POST['kilo'];
        // Yaşam Payını Hesapla: 0,080  x 0,75 x kilo
        $yasam_payi = number_format(0.080 * 0.75 * $kilo, 2); // Sonucu virgülden sonra iki basamakla göstermek için
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Giriş yapma işlemi
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
$db = dbBaglantisi();

// Eğer QR kodu gönderilmişse
if (isset($_GET['QR'])) {
    // GET isteğiyle gelen QR kodunu al
    $QR = $_GET['QR'];

    // Veritabanından ilgili ineği sorgula
    $query = $db->prepare("SELECT * FROM inek WHERE QR = :QR");
    $query->bindParam(':QR', $QR);
    $query->execute();
    $inek = $query->fetch(PDO::FETCH_ASSOC);

    // İnek bulunduysa, giriş alanlarına değerleri yerleştir
    if ($inek) {
        $inek_id = $inek['inek_id'];

        // İnek kilo ölçüm verisini al
        $query_kilo = $db->prepare("SELECT TOP 1 kilo FROM kilo_olcum WHERE QR = :QR ORDER BY kolcum_id DESC");
        $query_kilo->bindParam(':QR', $QR);
        $query_kilo->execute();
        $kilo_olcum = $query_kilo->fetch(PDO::FETCH_ASSOC);

        if ($kilo_olcum) {
            $kilo = $kilo_olcum['kilo'];
        } else {
            $kilo = "Kilo bilgisi bulunamadı.";
        }

        $dogum_tarihi = new DateTime($inek['dogum_tarihi']);
        // Bugünkü tarih
        $bugun = new DateTime();
        // Yaş hesaplaması
        $yas = $dogum_tarihi->diff($bugun)->y;
        $irk = $inek['irk'];
    } else {
        // İnek bulunamadıysa, hata mesajı göster
        echo "Belirtilen QR koduna sahip inek bulunamadı.";
    }
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
                <img class="img-fluid" src="img/logo.jpg" style="height: 8%; width: 8%;" alt="">
                <a href="anaSayfa.php" class="navbar-brand">
                    <h1 class="m-0 text-primary"><span class="text-dark">Lakta</span>Farm</h1>
                </a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between px-3" id="navbarCollapse">
                    <div class="navbar-nav ml-auto py-0">
                        <a href="anaSayfa.php" class="nav-item nav-link active">Ana Sayfa</a>

                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Döngüler</a>
                            <div class="dropdown-menu border-0 rounded-0 m-0">
                                <a href="servisPeriyoduMetin.php" class="dropdown-item">Servis Periyodu</a>
                                <a href="kuruDonemMetin.php" class="dropdown-item">Kuru Dönem Periyodu</a>
                                <a href="sagimMetin.php" class="dropdown-item">Sağım Periyodu</a>
                                <a href="gebelikMetin.php" class="dropdown-item">Gebelik Periyodu</a>
                            </div>
                        </div>
                        <?php if (isset($_SESSION['ad'])): ?>
                            <!-- Giriş yapıldığında görünecek menü -->
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">İnekler</a>
                                <div class="dropdown-menu border-0 rounded-0 m-0">
                                    <a href="gunlukKontrol.php" class="dropdown-item">Günlük Takip</a>
                                    <a href="dollemeTakip.php" class="dropdown-item">Dölleme Takip</a>
                                    <a href="gebeTakip.php" class="dropdown-item">Gebe Takip</a>
                                    <a href="kuruDonemTakip.php" class="dropdown-item">Kuru Dönem Takip</a>
                                </div>
                            </div>

                            <!-- Giriş yapıldığında görünen diğer menü -->
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Merhaba, <?php echo $_SESSION['ad']; ?>
                                </a>
                                <div class="dropdown-menu border-0 rounded-0 m-0" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="profil.php">Profilim</a>
                                    <a class="dropdown-item" href="?action=logout">Çıkış Yap</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Giriş yapılmadığında görünen menü -->
                            <a href="#" class="nav-item nav-link" onclick="openModal('myModal')">Giriş Yap</a>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Modal Start -->
    <div id="myModal" class="modal">
        <div class="modal-content" style="border-radius: 20px !important;">
            <span class="close" onclick="closeAndResetModal('myModal')">×</span>
            <h2 style="text-align: center " onclick="">Giriş Yap</h2>
            <!-- Hata Mesajı için -->
            <p id="loginError" style="text-align: center; color: red;"><?php echo isset($error) ? $error : ''; ?></p>
            <form class="login-form" action="index.php" method="POST">
                <div class="input-group" style="margin-bottom: 5px;">
                    <label for="mail" style="display: block; margin-bottom: 5px; ">Mail Adresi:</label>
                    <input type="text" id="mail" name="mail" style="width: 100%; padding: 8px; ">
                </div>

                <div class="input-group" style="margin-bottom: 5px;">
                    <label for="password" style="display: block; margin-bottom: 5px;">Şifre:</label>
                    <input type="password" id="sifre" name="sifre" style="width: 100%; padding: 8px;">
                </div>

                <div class="input-group">
                    <input type="submit" name="login_btn" value="Giriş Yap">
                </div>
            </form>
            <p style="text-align: center; margin-top: 10px;">
                Hesabınız yok mu? <a href="kayit.html">Kayıt olun</a>.
            </p>
            <label class="rememberme" for="rememberme"><input type="checkbox" id="rememberme"> Beni Hatırla</label>
        </div>
    </div>
    <!-- Modal End -->




    <!-- Header Start -->
    <div class="container-fluid page-header">
        <div class="container">
            <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px">

                <div class="d-inline-flex text-white" style="font-size: 30px;">
                    <p class="m-0 ">İnek Detay</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->


    <!-- Contact Start -->
    <div class="container-fluid py-5" style="background-color: #f8f9fa;">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;"></h6>
                <h1>İnek Detay Bilgileri</h1>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="contact-form bg-white shadow-lg p-5 rounded">
                        <div id="success"></div>
                        <form name="hesaplaForm" id="hesaplaForm" method="POST" action="#">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">QR</label>
                                <div class="col-md-8">
                                    <input class="form-control" type="text" name="qr"
                                        value="<?php echo isset($QR) ? $QR : ''; ?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Yaşı</label>
                                <div class="col-md-8">
                                    <input class="form-control" type="text" name="yas"
                                        value="<?php echo isset($yas) ? $yas : ''; ?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Irkı</label>
                                <div class="col-md-8">
                                    <input class="form-control" type="text" name="irki"
                                        value="<?php echo isset($irk) ? $irk : ''; ?>" readonly>
                                </div>
                            </div>
                            <div class="text-center mb-4">
                                <h5>Yaşam Payı Enerji İhtiyacı</h5>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label">Kilo</label>
                                <div class="col-md-8">
                                    <input class="form-control" type="text" name="kilo"
                                        value="<?php echo isset($kilo) ? $kilo : ''; ?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <div class="text-center">
                                    <button class="btn btn-primary btn-lg" type="submit" name="hesaplaButton">Yaşam
                                        Payını Hesapla</button>
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <div class="col-sm-9">
                                    <input class="form-control" type="text" name="sonuc"
                                        value="<?php echo $yasam_payi; ?>" readonly>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact End -->


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
                    <a class="text-white-50 mb-2" href="servisPeriyoduMetin.php"><i
                            class="fa fa-angle-right mr-2"></i>Servis Periyodu</a>
                    <a class="text-white-50 mb-2" href="kuruDonemMetin.php"><i class="fa fa-angle-right mr-2"></i>Kuru
                        Dönem Periyodu</a>
                    <a class="text-white-50 mb-2" href="sagimMetin.php"><i class="fa fa-angle-right mr-2"></i>Sağım
                        Periyodu</a>
                    <a class="text-white-50 mb-2" href="gebelikMetin.php"><i class="fa fa-angle-right mr-2"></i>Gebelik
                        Periyodu</a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h5 class="text-white text-uppercase mb-4" style="letter-spacing: 5px;">İnekler</h5>
                <div class="d-flex flex-column justify-content-start">
                    <a class="text-white-50 mb-2" href="gunlukKontrol.php"><i class="fa fa-angle-right mr-2"></i>Günlük
                        Takip</a>
                    <a class="text-white-50 mb-2" href="gebeTakip.php"><i class="fa fa-angle-right mr-2"></i>Gebelik
                        Takip</a>
                    <a class="text-white-50 mb-2" href="dollemeTakip.php"><i class="fa fa-angle-right mr-2"></i>Dölleme
                        Takip</a>
                    <a class="text-white-50 mb-2" href="kuruDonemTakip.php"><i class="fa fa-angle-right mr-2"></i>Kuru
                        Dönem Takip</a>
                    <a class="text-white-50 mb-2" href="inekKayit.php"><i class="fa fa-angle-right mr-2"></i>İnek
                        Kayıt</a>
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

    <!-- Contact Javascript File -->
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>

    <script>

        // Hata mesajı kontrolü ve modal açma
        document.addEventListener("DOMContentLoaded", function () {
            var error = "<?php echo isset($error) ? $error : '' ?>";
            if (error !== '') {
                document.getElementById('loginError').innerText = error;
                openModal('myModal');
            }
        });

        // Modal kapatma fonksiyonu ve hata mesajını temizleme
        function closeAndResetModal(modalId) {
            document.getElementById(modalId).style.display = "none";
            document.getElementById('loginError').innerText = '';
        }
    </script>
</body>

</html>