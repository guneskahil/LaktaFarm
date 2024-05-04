<?php
// config.php dosyasını dahil et
include_once "config.php";
include_once "giris.php";

$kullanici_id = $_SESSION['kullanici_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['profilGuncelle'])) {
    // Formdan gelen verileri al
    $kimlik_no = $_POST['kimlik_no'];
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $mail = $_POST['mail'];
    $tel_no = $_POST['tel_no'];

    // Veritabanı bağlantısını yap
    $db = dbBaglantisi();

    if ($db instanceof PDO) {
        // SQL sorgusunu hazırla
        $query = $db->prepare("UPDATE kullanici SET ad = :ad, soyad = :soyad, mail = :mail, tel_no = :tel_no WHERE kullanici_id = :kullanici_id");

        // Parametreleri bağla
        $query->bindParam(':kullanici_id', $kullanici_id);
        $query->bindParam(':ad', $ad);
        $query->bindParam(':soyad', $soyad);
        $query->bindParam(':tel_no', $tel_no);
        $query->bindParam(':mail', $mail);
        // Sorguyu çalıştır
        if ($query->execute()) {
            // Yeni verileri $_SESSION değişkenlerine ata
            $_SESSION['ad'] = $ad;
            $_SESSION['soyad'] = $soyad;
            $_SESSION['mail'] = $mail;
            $_SESSION['tel_no'] = $tel_no;

            // Başarılı bir şekilde eklendiğinde kullanıcıyı başka bir sayfaya yönlendir
            header("Location: profil.php");
            exit();
        } else {
            // Ekleme işlemi başarısız olduysa hata mesajı oluştur
            $error = "Güncelleme yapılırken bir hata oluştu. Lütfen tekrar deneyin.";
        }
    } else {
        // Veritabanına bağlanılamadıysa hata mesajı oluştur
        $error = "Veritabanı bağlantısı yapılamadı.";
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
    <style>
        .modal {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 0;
            height: 800px;
        }

        .modal-content {
            width: auto;
            /* İçeriğin genişliğine göre otomatik olarak ayarlanacak */
            max-width: 600px;
            /* Modal'ın maksimum genişliği sayfanın genişliği kadar olacak şekilde ayarlanabilir */
            height: auto;
            /* İçeriğin yüksekliğine göre otomatik olarak ayarlanacak */
            max-height: 80vh;
            /* Modal içeriğinin ekranın yüksekliğini aşmamasını sağlar */
            overflow-y: auto;
            /* İçerik boyutu ekranı aştığında dikey kaydırma çubuğu ekle */

        }

        .modal-title {
            text-align: center;
            width: 100%;
        }
    </style>

</head>

<body>


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

    <!-- Profile Start-->
    <div class="container">
        <div class="row justify-content-center py-5">
            <div class="col-lg-6">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <div class="d-flex justify-content-center mb-4">
                            <div
                                class="avatar bg-light rounded-circle d-flex align-items-center justify-content-center">
                                <h4>Kullanıcı Bilgileri</h4>
                            </div>
                        </div>
                        <table class="table">
                            <tr>
                                <td>
                                    <h5 class="card-title">Kimlik Numarası </h5>
                                </td>
                                <td>
                                    <p class="card-text">
                                        <?php echo isset($_SESSION['kimlik_no']) ? $_SESSION['kimlik_no'] : ''; ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5 class="card-title">Ad-Soyad </h5>
                                </td>
                                <td>
                                    <p class="card-text">
                                        <?php echo isset($_SESSION['ad']) ? $_SESSION['ad'] : ''; ?>
                                        <?php echo isset($_SESSION['soyad']) ? $_SESSION['soyad'] : ''; ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5 class="card-title">Email </h5>
                                </td>
                                <td>
                                    <p class="card-text">
                                        <?php echo isset($_SESSION['mail']) ? $_SESSION['mail'] : ''; ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5 class="card-title">Telefon Numarası </h5>
                                </td>
                                <td>
                                    <p class="card-text">
                                        <?php echo isset($_SESSION['tel_no']) ? $_SESSION['tel_no'] : ''; ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                        <div class="row justify-content-center">
                            <div class="col-sm-12 text-center">
                                <button id="editProfileBtn" class="btn btn-primary"
                                    onclick="openEditModal()">Bilgilerimi
                                    Düzenle</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile End-->

    <!-- Profile Edit Start-->
    <div id="bilgilerimiDuzenleModal" class="modal" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title row justify-content-center">Bilgilerimi Düzenle</h5>
                    <span class="close" onclick="closeAndResetModal('bilgilerimiDuzenleModal')">×</span>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm" action="profil.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="kimlik_no">Kimlik Numarası</label>
                            <input type="text" class="form-control" id="kimlik_no" name="kimlik_no"
                                value="<?php echo $_SESSION['kimlik_no']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="ad">Adı</label>
                            <input type="text" class="form-control" id="ad" name="ad"
                                value="<?php echo isset($_SESSION['ad']) ? $_SESSION['ad'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="soyad">Soyadı</label>
                            <input type="text" class="form-control" id="soyad" name="soyad"
                                value="<?php echo isset($_SESSION['soyad']) ? $_SESSION['soyad'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="mail">Email</label>
                            <input type="text" class="form-control" id="mail" name="mail"
                                value="<?php echo $_SESSION['mail']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="tel_no">Telefon Numarası</label>
                            <input type="text" class="form-control" id="tel_no" name="tel_no"
                                value="<?php echo $_SESSION['tel_no']; ?>">
                        </div>
                        <div class="modal-footer row justify-content-center">
                            <button type="button" class="btn btn-danger"
                                onclick="closeAndResetModal('bilgilerimiDuzenleModal')">Vazgeç</button>
                            <button type="submit" class="btn btn-primary" name="profilGuncelle">Değişiklikleri
                                Kaydet</button>
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

    <!-- Contact Javascript File -->
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    <script>
        function openEditModal() {
            var modal = document.getElementById('bilgilerimiDuzenleModal');
            modal.style.display = 'block';
            // Bu fonksiyon profil düzenleme butonuna tıklandığında çağrılır
            document.getElementById('editProfileBtn').addEventListener('click', function () {
                openEditModal();
            });
            // Kullanıcı bilgilerini modal içine yükle
            document.getElementById('kimlik_no').value = '<?php echo isset($_SESSION['kimlik_no']) ? $_SESSION['kimlik_no'] : ''; ?>';
            document.getElementById('ad').value = '<?php echo isset($_SESSION['ad']) ? $_SESSION['ad'] : ''; ?>';
            document.getElementById('mail').value = '<?php echo isset($_SESSION['mail']) ? $_SESSION['mail'] : ''; ?>';
            document.getElementById('tel_no').value = '<?php echo isset($_SESSION['tel_no']) ? $_SESSION['tel_no'] : ''; ?>';
        }

        // Modalı kapatma işlevi
        function closeAndResetModal(modalId) {
            document.getElementById(modalId).style.display = "none";
            // Formu sıfırla
            document.getElementById("editProfileForm").reset();
        }

    </script>


</body>

</html>