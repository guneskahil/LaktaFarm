<?php

include_once "config.php";
include_once "giris.php";

$db = dbBaglantisi();
if ($db instanceof PDO) {
    try {
        // $_SESSION['kullanici_id'] değişkeni tanımlıysa, $kullanici_id değişkenine atayalım
        $kullanici_id = isset($_SESSION['kullanici_id']) ? $_SESSION['kullanici_id'] : null;

        // Eğer kullanıcı oturumunda kullanıcı id tanımlıysa, sorguyu hazırla ve çalıştır
        if ($kullanici_id !== null) {
            $sql = "SELECT
                sut_dongu.sut_dongu_adi AS sdongu_adi,
                COUNT(inek.inek_id) AS inek_sayisi
            FROM
                sut_dongu
            LEFT JOIN
                inek ON sut_dongu.sut_dongu_id = inek.sut_dongu_id
            WHERE
                inek.kullanici_id = :kullanici_id
            GROUP BY
                sut_dongu.sut_dongu_id,
                sut_dongu.sut_dongu_adi";


            $stmt = $db->prepare($sql);
            $stmt->execute([':kullanici_id' => $kullanici_id]);
            $result_sut_dongu = $stmt->fetchAll(PDO::FETCH_ASSOC);


        } else {
            // Kullanıcı oturumunda kullanıcı id tanımlı değilse, hata mesajı göster
            echo "Kullanıcı ID bulunamadı.";
        }

        if ($kullanici_id !== null) {
            $sql2 = "SELECT
                gebelik_dongu.gebelik_dongu_adi AS gdongu_adi,
                COUNT(inek.inek_id) AS inek_sayisi
            FROM
                gebelik_dongu
            LEFT JOIN
                inek ON gebelik_dongu.gebelik_dongu_id = inek.gebelik_dongu_id
            WHERE
                inek.kullanici_id = :kullanici_id
            GROUP BY
                gebelik_dongu.gebelik_dongu_id,
                gebelik_dongu.gebelik_dongu_adi";


            $stmt = $db->prepare($sql2);
            $stmt->execute([':kullanici_id' => $kullanici_id]);
            $result_gebelik_dongu = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } else {
            // Kullanıcı oturumunda kullanıcı id tanımlı değilse, hata mesajı göster
            echo "Kullanıcı ID bulunamadı.";
        }
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
} else {
    echo "Veritabanı bağlantısı sağlanamadı.";
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
            <div class="row justify-content-center">
                <div class="weather-container row">
                    <div class="weather-info row">
                        <p style="padding-right:20px;"><strong>Şehir:</strong> <span id="city"></span></p>
                        <p style="padding-right:20px;"><strong> Sıcaklık:</strong> <span id="temperature"></span>°C</p>
                        <p style="padding-right:20px;"><strong> Hava Durumu:</strong> <span id="condition"></span></p>
                        <p style="padding-right:20px;"><strong> Nem:</strong> <span id="humidity"></span>%</p>
                    </div>
                </div>
            </div>
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




    <div class="container-fluid">
        <div class="container pt-5 pb-3">
            <div class="row">
                <div class="col-lg-5 col-md-6 mb-4">
                    <!-- Packages Start -->
                    <?php if (!empty($result_sut_dongu)): ?>
                        <!-- $result_sut_dongu üzerinde döngü yapın ve verileri görüntüleyin -->
                        <div class="container-fluid">
                            <div class="container pt-5 pb-3">
                                <div class="text-center mb-3 pb-3">
                                    <h1 class="text-primary" style="letter-spacing: 5px;">Süt Döngüleri</h1>
                                </div>
                                <div class="row">
                                    <?php foreach ($result_sut_dongu as $row): ?>
                                        <div class="col-lg-6 col-md-6 mb-4">
                                            <div class="package-item bg-white mb-2">
                                                <?php
                                                // Süt döngüsü için uygun resmi belirleme
                                                $resim = '';
                                                if ($row['sdongu_adi'] == 'Sagimda') {
                                                    $resim = 'img/anasayfaSagımda1.jpeg'; // Resim adı düzeltildi
                                                } else {
                                                    $resim = 'img/anasayfaKuruda.jpeg';
                                                }
                                                ?>
                                                <img class="img-fluid" src="<?php echo $resim; ?>" alt="">
                                                <div class="p-4">
                                                    <a class="h5 text-decoration-none"
                                                        href=""><?php echo $row['sdongu_adi']; ?></a>
                                                    <div class="border-top mt-4 pt-4">
                                                        <div class="d-flex justify-content-between">
                                                            <h5 class="m-0"><?php echo $row['inek_sayisi']; ?></h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p>Süt döngüsü için sonuç bulunamadı.</p>
                    <?php endif; ?>

                </div>
                <div class="col-lg-7 col-md-6 mb-4">

                    <?php if (!empty($result_gebelik_dongu)): ?>
                        <!-- $result_gebelik_dongu üzerinde döngü yapın ve verileri görüntüleyin -->
                        <div class="container-fluid">
                            <div class="container pt-5 pb-3">
                                <div class="text-center mb-3 pb-3">
                                    <h1 class="text-primary" style="letter-spacing: 5px;">Gebelik Döngüleri</h1>
                                </div>
                                <div class="row">
                                    <?php foreach ($result_gebelik_dongu as $row): ?>
                                        <div class="col-lg-4 col-md-6 mb-4">
                                            <div class="package-item bg-white mb-2">
                                                <?php
                                                // gebelik döngüsü için uygun resmi belirleme
                                                $resim = '';
                                                if ($row['gdongu_adi'] == 'Serviste') {
                                                    $resim = 'img/anasayfaServis.jpeg'; // Resim adı düzeltildi
                                                } elseif($row['gdongu_adi'] == 'Gebe') {
                                                    $resim = 'img/anasayfaGebe.jpeg';
                                                }else{
                                                    $resim = 'img/kuruMetin2.jpg';
                                                }
                                                ?>
                                                <img class="img-fluid" src="<?php echo $resim; ?>" alt="">
                                                <div class="p-4">
                                                    <a class="h5 text-decoration-none"
                                                        href=""><?php echo $row['gdongu_adi']; ?></a>
                                                    <div class="border-top mt-4 pt-4">
                                                        <div class="d-flex justify-content-between">
                                                            <h5 class="m-0"><?php echo $row['inek_sayisi']; ?></h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p>Gebelik döngüsü için sonuç bulunamadı.</p>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Packages End -->
        </div>
    </div>

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
                <p><i class="fa fa-map-marker-alt mr-2"></i>Kabaoğlu, Kocaeli Üniversitesi Umuttepe Kampüsü, A
                    Kapısı,
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
    <!-- JavaScript ile Hava Durumu Bilgisini Getirme ve Gösterme -->
    <script>
        const apiKey = '030fad8f4256b502acc41286b23d4229';
        const latitude = '40.762402'; // Şehrin enlem koordinatını buraya girin
        const longitude = '29.932949'; // Şehrin boylam koordinatını buraya girin

        fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${latitude}&lon=${longitude}&appid=${apiKey}&units=metric`)
            .then(response => response.json())
            .then(data => {
                const city = data.name; // Şehir adını alırken kullanılacak
                const temperature = data.main.temp;
                const condition = data.weather[0].description; // Hava durumu koşulunu alırken kullanılacak
                const humidity = data.main.humidity;

                // Türkçe hava durumu koşulları için çeviri
                const turkishConditions = {
                    'clear sky': 'Açık Hava',
                    'few clouds': 'Az Bulutlu',
                    'scattered clouds': 'Parçalı Bulutlu',
                    'broken clouds': 'Bulutlu',
                    'shower rain': 'Sağanak Yağışlı',
                    'rain': 'Yağmurlu',
                    'thunderstorm with rain': 'Gök Gürültülü Sağanak Yağışlı',
                    'snow': 'Karlı',
                    'mist': 'Sisli',

                };

                // Hava durumu koşulunu Türkçe olarak gösterme
                const turkishCondition = turkishConditions[condition.toLowerCase()] || condition;

                // HTML içine verileri yerleştirme
                document.getElementById('city').textContent = city;
                document.getElementById('temperature').textContent = temperature;
                document.getElementById('condition').textContent = turkishCondition;
                document.getElementById('humidity').textContent = humidity;
            })
            .catch(error => {
                console.error('Hata:', error);
                document.getElementById('city').textContent = 'Bilinmiyor';
                document.getElementById('temperature').textContent = 'Bilinmiyor';
                document.getElementById('condition').textContent = 'Bilinmiyor';
                document.getElementById('humidity').textContent = 'Bilinmiyor';
            });
    </script>
</body>

</html>