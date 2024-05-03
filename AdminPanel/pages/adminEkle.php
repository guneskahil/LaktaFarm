<?php
// Veritabanı bağlantısını gerçekleştiren fonksiyon
function dbBaglantisi()
{
    $sunucu = "sunucu_adı";
    $veritabani = "veritabanı_adı";
    $kullanici = "kullanici_adi";
    $sifre = "sifre";

    try {
        $db = new PDO("sqlsrv:server=$sunucu;Database=$veritabani;", $kullanici, $sifre);
        // Hata modunu ayarlama
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        // Bağlantı hatası durumunda hata mesajını ekrana yazdırma
        echo "Bağlantı hatası: " . $e->getMessage();
        return null;
    }
}

// Formdan gelen verileri işle
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form verilerini al
    $admin_mail = $_POST['email'];
    $admin_sifre = $_POST['sifre'];

    // Veritabanı bağlantısı
    $db = dbBaglantisi();

    if ($db instanceof PDO) {
        try {
            // SQL sorgusu
            $sql = "INSERT INTO admin (admin_mail, admin_sifre) VALUES (:admin_mail, :admin_sifre)";

            // SQL sorgusunu hazırlama
            $stmt = $db->prepare($sql);

            // Parametreleri bağlama
            $stmt->bindParam(':admin_mail', $admin_mail);
            $stmt->bindParam(':admin_sifre', $admin_sifre);

            // SQL sorgusunu çalıştırma
            $stmt->execute();

            echo "Veriler başarıyla eklendi.";
        } catch (PDOException $e) {
            // Hata durumunda hata mesajını ekrana yazdırma
            echo "Hata: " . $e->getMessage();
        }
    } else {
        // Veritabanı bağlantısı sağlanamadı hatası
        echo "Veritabanı bağlantısı sağlanamadı.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    Material Dashboard 2 by Creative Tim
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.1.0" rel="stylesheet" />
  <!-- Nepcha Analytics (nepcha.com) -->
  <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
  <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
</head>

<body class="bg-gray-200">
 
  <main class="main-content  mt-0">
    <div class="page-header align-items-start min-vh-100" style="background-image: url('https://images.unsplash.com/photo-1497294815431-9365093b7331?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1950&q=80');">
      <span class="mask bg-gradient-dark opacity-6"></span>
      <div class="container my-auto">
        <div class="row">
          <div class="col-lg-4 col-md-8 col-12 mx-auto">
            <div class="card z-index-0 fadeIn3 fadeInBottom">
              <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                  <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Admin Ekle</h4>
                  <div class="row mt-3">
                  </div>
                </div>
              </div>
              <div class="card-body">
              <form role="form" class="text-start" method="POST">
                 <div class="input-group input-group-outline my-3">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" class="form-control">
                 </div>
                 <div class="input-group input-group-outline mb-3">
                  <label class="form-label">Password</label>
                  <input type="password" name="sifre" class="form-control">
                 </div>
                 <div class="text-center">
                   <button type="submit" class="btn bg-gradient-primary w-100 my-4 mb-2">Sign in</button>
                  </div>
              </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/material-dashboard.min.js?v=3.1.0"></script>
</body>

</html>