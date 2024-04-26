<?php
session_start();

function dbBaglantisi()
{
    $sunucu = "bulutsqldeneme.database.windows.net";
    $veritabani = "LaktaFarmDB";
    $kullanici = "sqladmin";
    $sifre = "admin.123";

    try {
        $db = new PDO("sqlsrv:server=$sunucu;Database=$veritabani;", $kullanici, $sifre);
        return $db;
    } catch (PDOException $e) {
        echo "<script>alert('Veritabanına bağlanırken hata oluştu: " . $e->getMessage() . "');</script>";
        return null;
    }
}

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
            // Kullanıcı bulunamadı, hata mesajını JavaScript ile göster
            echo "<script>alert('Hatalı giriş bilgileri. Lütfen tekrar deneyin.');</script>";
        }
    } else {
        // Veritabanına bağlanılamadı, hata mesajını JavaScript ile göster
        echo "<script>alert('Veritabanı bağlantısı yapılamadı.');</script>";
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

    <!-- Login Modal Start-->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" style="" onclick="closeModal('myModal')">×</span>
            <h2 style="text-align: center">Giriş Yap</h2>
            <form class="login-form" onsubmit="checkCredentials(event)" action="index.php" method="POST">
                <div class="input-group" style="margin-bottom: 5px;">
                    <label for="mail" style="display: block; margin-bottom: 5px;">Mail Adresi:</label>
                    <input type="text" id="mail" name="mail" style="width: 100%; padding: 8px;">
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



    <!-- Carousel Start -->
    <div class="container-fluid p-0">
        <div id="header-carousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="w-100" src="img/inek3.jpg" alt="Image">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3" style="max-width: 900px;">
                            <h4 class="m-0 text-primary"><span class="text-dark">Lakta</span>Farm</h4>
                            <h1 class="display-3 text-white mb-md-4">LaktaFarmla verimi katla</h1>
                            <a href="#" class="btn btn-primary py-md-3 px-md-5 mt-2"
                                onclick="openModal('myModal')">Giriş Yap</a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Destination Start -->
    <div class="container-fluid py-5">
        <div class="container pt-5 pb-3">
            <div class="text-center mb-3 pb-3">
                <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;">Laktasyon Süreci</h6>
                <h1>Yılda Bir Buzağı Ve Maksimum Süt İçin</h1>
            </div>
            <div class="row">
                <div class="col-lg-6  mb-6">
                    <div class="destination-item position-relative overflow-hidden mb-2">
                        <img class="img-fluid" src="img/inek1.jpg" alt="">
                        <a class="destination-overlay text-white text-decoration-none" href="">
                            <h5 class="text-white">Pervis Periyodu</h5>
                            <span>85 Gün</span>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6  mb-6">
                    <div class="destination-item position-relative overflow-hidden mb-2">
                        <img class="img-fluid" src="img/inek8.jpg" alt="">
                        <a class="destination-overlay text-white text-decoration-none" href="">
                            <h5 class="text-white">Kuru Dönem</h5>
                            <span>60 Gün</span>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6  mb-6">
                    <div class="destination-item position-relative overflow-hidden mb-2">
                        <img class="img-fluid" src="img/inek3.jpg" alt="">
                        <a class="destination-overlay text-white text-decoration-none" href="">
                            <h5 class="text-white">Sağım</h5>
                            <span>305 Gün</span>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6  mb-6">
                    <div class="destination-item position-relative overflow-hidden mb-2">
                        <img class="img-fluid" src="img/inek9.jpg" alt="">
                        <a class="destination-overlay text-white text-decoration-none" href="">
                            <h5 class="text-white">Gebelik süresi</h5>
                            <span>280 gün</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Destination Start -->



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

    <!-- Contact Javascript File -->
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>

</body>

</html>