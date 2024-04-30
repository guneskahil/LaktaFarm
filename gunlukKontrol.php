<?php
// config.php dosyasını dahil et
include_once "config.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = $_POST['mail'];
    $sifre = $_POST['sifre'];

    $db = dbBaglantisi(); // Veritabanı bağlantısını sağla

    if ($db instanceof PDO) {
        $query = $db->prepare("SELECT * FROM kullanici WHERE mail = :mail AND sifre = :sifre");
        $query->bindParam(':mail', $mail);
        $query->bindParam(':sifre', $sifre);
        $query->execute();
        $kullanici = $query->fetch(PDO::FETCH_ASSOC);

        if ($kullanici) {
            // Kullanıcı bulundu, giriş yap
            $_SESSION['ad'] = $kullanici['ad']; // Kullanıcının adını oturum verilerine kaydet
            $_SESSION['kullanici_id'] = $kullanici['kullanici_id']; // Kullanıcının id'sini oturum verilerine kaydet
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
if ($db instanceof PDO) {
    try {
        // SQL sorgusu
        $sql = "SELECT 
        i.QR,
        i.inek_id,
        i.ad,
        i.dogum_tarihi,
        i.dogurma_tarihi,
        d.dollenme_durumu,
        ko.kilo,
        so.sut_miktar,
        DATEDIFF(day, i.dogurma_tarihi, GETDATE()) AS gun_farki,
        CASE 
                    WHEN DATEDIFF(day, i.dogurma_tarihi, GETDATE()) < 305 THEN 'Sağım Dönemi'
                    WHEN DATEDIFF(day, i.dogurma_tarihi, GETDATE()) BETWEEN 305 AND 365 THEN 'Kuru Dönem'
        END AS sut_durum,
        CASE 
            WHEN GETDATE() < DATEADD(day, 85, i.dogurma_tarihi) THEN 'Servis Periyodu'
            WHEN d.dollenme_durumu = 'evet' AND GETDATE() < DATEADD(day, 280, d.dollenme_tarihi) THEN 'Gebelik Periyodu'
            ELSE 'Sürüden Çıkarılmalı'
        END AS gebe_durum,
        CASE 
            WHEN GETDATE() < DATEADD(day, 85, i.dogurma_tarihi) THEN DATEDIFF(day, i.dogurma_tarihi, GETDATE())
            WHEN d.dollenme_durumu = 'evet' AND GETDATE() < DATEADD(day, 280, d.dollenme_tarihi) THEN DATEDIFF(day, d.dollenme_tarihi, GETDATE())
            ELSE NULL
        END AS gun
    FROM 
        inek i
    LEFT JOIN 
        dollenme d ON i.inek_id = d.inek_id
LEFT JOIN
    kilo_olcum ko ON i.QR = ko.QR
LEFT JOIN
    sut_olcum so ON i.QR = so.QR
    WHERE
        i.kullanici_id = :kullanici_id    
    ";

        // SQL sorgusunu hazırlama
        $stmt = $db->prepare($sql);

        // Oturumda kullanıcı id'sini al
        $kullanici_id = isset($_SESSION['kullanici_id']) ? $_SESSION['kullanici_id'] : null;

        // SQL sorgusunu çalıştırma
        $stmt->execute([':kullanici_id' => $kullanici_id]);

        // Sonuçları alma
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Eğer sonuç yoksa veya boşsa, uyarı mesajı göster
        if (!$result) {
            echo "Sonuç bulunamadı.";
        }
    } catch (PDOException $e) {
        // Hata durumunda hata mesajını ekrana yazdırma
        echo "Hata: " . $e->getMessage();
    }
} else {
    // Veritabanı bağlantısı sağlanamadı hatası
    echo "Veritabanı bağlantısı sağlanamadı.";
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
                        <?php if (isset($_SESSION['ad'])): ?>
                            <!-- Giriş yapıldığında görünecek menü -->
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">İnekler</a>
                                <div class="dropdown-menu border-0 rounded-0 m-0">
                                    <a href="gunlukKontrol.php" class="dropdown-item">Günlük Takip</a>
                                    <a href="dollemeTakip.php" class="dropdown-item">Dölleme Takip</a>
                                    <a href="destination.html" class="dropdown-item">Gebe Takip</a>
                                    <a href="single.html" class="dropdown-item">Kuru Dönem Takip</a>
                                    <a href="inekKayit.html" class="dropdown-item">İnek Kayıt</a>
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
                    <p class="m-0 ">Günlük Takip</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Booking Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="justify-content-center">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-uppercase text-primary mb-3 row justify-content-center"
                            style="letter-spacing: 5px;">Günlük
                            Takip Tablosu</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>QR</th>
                                        <th>Adı</th>
                                        <th>Gebelik Döngü Adı</th>
                                        <th>Gebelik Döngüsünün Kaçıncı Günü</th>
                                        <th>Süt Döngü Adı</th>
                                        <th>Süt Döngüsünün Kaçıncı Günü</th>
                                        <th>Kg</th>
                                        <th>Süt Miktarı</th>
                                        <th>Detay</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($result as $row): ?>
                                        <tr>
                                            <td>
                                                <?php if (isset($row['QR'])): ?>
                                                    <?php echo $row['QR']; ?>
                                                <?php else: ?>
                                                    QR değeri yok
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $row['ad']; ?></td>
                                            <td><?php echo $row['gebe_durum']; ?></td>
                                            <td>
                                                <?php if (isset($row['gun'])): ?>
                                                    <?php echo $row['gun']; ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (isset($row['sut_durum'])): ?>
                                                    <?php echo $row['sut_durum']; ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (isset($row['gun_farki']) && $row['gebe_durum'] !== 'Sürüden Çikarilmali') {
                                                    if ($row['sut_durum'] === 'Kuru Dönem') {
                                                        // Kuru dönemdeyken gun_farki hesaplaması
                                                        echo $row['gun_farki'] - 305;
                                                    } else {
                                                        // Diğer durumlarda gun_farki direkt olarak gösterilsin
                                                        echo $row['gun_farki'];
                                                    }
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>

                                            <td><?php echo $row['kilo']; ?></td>
                                            <td><?php echo $row['sut_miktar']; ?></td>
                                            <td><a href="dollemeDetay.php" class="btn btn-primary">Detay</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking End -->


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