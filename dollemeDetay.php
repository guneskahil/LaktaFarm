<?php
// config.php dosyasını dahil et
include_once "config.php";
include_once "giris.php";

$error = '';

// Veritabanından döllenme sayısını çekmek için gerekli kod
$dollenme_sayisi = '';
if (isset($_GET['inek_id'])) {
    $inek_id = $_GET['inek_id'];
    $db = dbBaglantisi();
    if ($db instanceof PDO) {
        $sqlGetDollenme = "SELECT dollenme_sayisi FROM dollenme WHERE inek_id = :inek_id";
        $stmtGetDollenme = $db->prepare($sqlGetDollenme);
        $stmtGetDollenme->bindParam(':inek_id', $inek_id);
        $stmtGetDollenme->execute();
        $row = $stmtGetDollenme->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $dollenme_sayisi = $row['dollenme_sayisi'];
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['bilgiGuncelle'])) {
    // Formdan gelen verileri al
    $dollenme_sayisi = $_POST['dollenme_sayisi'];
    $dollenme_durumu = $_POST['dollenme_durumu'];
    $dollenme_tarihi = $_POST['dollenme_tarihi'];

    // inek_id'yi $_GET ile al
    $inek_id = isset($_GET['inek_id']) ? $_GET['inek_id'] : '';

    try {
        $db = dbBaglantisi();

        if ($db instanceof PDO) {
            // Döllenme sayısını veritabanından çek
            $sqlGetDollenme = "SELECT dollenme_sayisi FROM dollenme WHERE inek_id = :inek_id";
            $stmtGetDollenme = $db->prepare($sqlGetDollenme);
            $stmtGetDollenme->bindParam(':inek_id', $inek_id);
            $stmtGetDollenme->execute();
            $dollenme_row = $stmtGetDollenme->fetch(PDO::FETCH_ASSOC);
            $dollenme_sayisi = $dollenme_row['dollenme_sayisi'];

            // Güncelleme sorgusunu hazırla
            $sqlUpdateDollenme = "UPDATE dollenme 
                                  SET dollenme_sayisi = :dollenme_sayisi, 
                                      dollenme_durumu = :dollenme_durumu, 
                                      dollenme_tarihi = :dollenme_tarihi 
                                  WHERE inek_id = :inek_id";

            $stmtUpdateDollenme = $db->prepare($sqlUpdateDollenme);

            // Döllenme durumu "Hayır" olarak işaretlenirse, döllenme sayısını artır
            if ($dollenme_durumu === "Hayır") {
                $dollenme_sayisi++;
                if ($dollenme_sayisi >= 3) {
                    // UPDATE sorgusunu oluştur
                    $sqlUpdateGebelikDongu = "UPDATE inek SET gebelik_dongu_id = 3 WHERE inek_id = :inek_id";
                    // Bağlantıyı hazırla ve sorguyu çalıştır
                    $stmtUpdateGebelikDongu = $db->prepare($sqlUpdateGebelikDongu);
                    $stmtUpdateGebelikDongu->bindParam(':inek_id', $inek_id);
                    $stmtUpdateGebelikDongu->execute();
                }
            }

            // Bağlantı parametrelerini bağla
            $stmtUpdateDollenme->bindParam(':inek_id', $inek_id);
            $stmtUpdateDollenme->bindParam(':dollenme_sayisi', $dollenme_sayisi);
            $stmtUpdateDollenme->bindParam(':dollenme_durumu', $dollenme_durumu);
            $stmtUpdateDollenme->bindParam(':dollenme_tarihi', $dollenme_tarihi);

            // Güncelleme sorgusunu çalıştır
            if ($stmtUpdateDollenme->execute()) {
                // İneğin gebe olma durumunu güncelle
                if ($dollenme_durumu === "Evet") {
                    $sqlUpdateInek = "UPDATE inek SET gebelik_dongu_id = 2 WHERE inek_id = :inek_id";
                    $stmtUpdateInek = $db->prepare($sqlUpdateInek);
                    $stmtUpdateInek->bindParam(':inek_id', $inek_id);
                    $stmtUpdateInek->execute();
                }
                header("Location: dollemeTakip.php");
                exit();
            } else {
                // Hata durumunda hata mesajı oluştur
                $error = "Bir hata oluştu. Lütfen tekrar deneyin.";
            }
        } else {
            $error = "Veritabanı bağlantısı yapılamadı.";
        }
    } catch (PDOException $e) {
        $error = "Veritabanı hatası: " . $e->getMessage();
    }
}

?>


<!DOCTYPE html>
<html lang="tr">

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


    <!-- Header Start -->
    <div class="container-fluid page-header">
        <div class="container">
            <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px">

                <div class="d-inline-flex text-white" style="font-size: 30px;">
                    <p class="m-0 ">Döllenme Detay</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Contact Start -->
    <div class="container-fluid">
        <div class="container py-5">
            <div class="text-center mb-3 pb-3">
                <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;"></h6>
                <h1>Döllenme Detayı</h1>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="contact-form bg-white" style="padding: 30px;">
                        <div id="success"></div>
                        <form name="guncelle" id="guncelle"
                            action="dollemeDetay.php?inek_id=<?php echo isset($_GET['inek_id']) ? $_GET['inek_id'] : ''; ?>"
                            method="POST" novalidate="novalidate">
                            <input type="hidden" name="inek_id"
                                value="<?php echo isset($_GET['QR']) ? $_GET['QR'] : ''; ?>">
                            <div class="control-group col-md-10">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Döllenme Tarihi</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input class="form-control p-4" type="date" name="dollenme_tarihi">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="control-group col-md-10">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Döllenme Sayısı</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <!-- Döllenme sayısını form alanına yazdır -->
                                            <input class="form-control p-4" type="text" name="dollenme_sayisi"
                                                value="<?php echo $dollenme_sayisi; ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mb-3 py-3" style="margin-bottom:10px">
                                <p style="color:red">**Dölleme gerçekleştiyse inek 'Gebe' periyoduna alınacaktır.</p>
                                <?php
                                if ($dollenme_sayisi == 2) {
                                    echo '<p style="color:red">**Dölleme gerçekleşmediyse inek sürüden çıkarılacaktır.</p>';
                                }
                                ?>
                            </div>
                            <div class="text-center mb-3">
                                <h5>Dölleme Gerçekleşti Mi?</h5>
                            </div>
                            <div class="control-group ">
                                <div class="row justify-content-center">
                                    <div class="form-check mr-2">
                                        <input class="form-check-input" type="radio" name="dollenme_durumu"
                                            id="döllenme1" value="Evet">
                                        <label class="form-check-label" for="döllenme1">Evet</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="dollenme_durumu"
                                            id="döllenme2" value="Hayır">
                                        <label class="form-check-label" for="döllenme2">Hayır</label>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group" style="padding-top: 10px;">
                                <input type="submit" name="bilgiGuncelle" value="Bilgileri Kaydet">
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

</body>

</html>