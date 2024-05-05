<?php

function dbBaglantisi()
{
  $sunucu = "bulutserversql.database.windows.net";
  $veritabani = "LaktaFarmDB";
  $kullanici = "sqladmin";
  $sifre = "bulutadmin.123";
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

$db = dbBaglantisi();

if ($db instanceof PDO) {
  try {
    // SQL sorgusu
    $sql = "SELECT 
            k.kullanici_id,
            k.ad AS kullanici_adi,
            k.kimlik_no AS tc_kimlik_no,
            k.mail AS kullanici_mail,
            COUNT(i.inek_id) AS inek_miktari
        FROM 
            kullanici k
        LEFT JOIN 
            inek i ON k.kullanici_id = i.kullanici_id
        GROUP BY 
            k.kullanici_id, k.ad, k.kimlik_no, k.mail";


    // SQL sorgusunu hazırlama
    $stmt = $db->prepare($sql);

    // SQL sorgusunu çalıştırma
    $stmt->execute();

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
// Formdan gelen veriyi işle
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sil'])) {
  // Kullanıcı ID'sini al
  $kullanici_id = $_POST['kullanici_id'];

  // Kullanıcıya ait "inek" kayıtlarını sil
  $db = dbBaglantisi();
  if ($db instanceof PDO) {
    try {
      // "dollenme" tablosundan kullanıcıya ait olan kayıtları sil
      $sql_dollenme_sil = "DELETE FROM dollenme WHERE inek_id IN (SELECT inek_id FROM inek WHERE kullanici_id = :kullanici_id)";
      $stmt_dollenme_sil = $db->prepare($sql_dollenme_sil);
      $stmt_dollenme_sil->bindParam(':kullanici_id', $kullanici_id);
      $stmt_dollenme_sil->execute();

      // "sut_olcum" tablosundan kullanıcıya ait olan kayıtları sil
      $sql_sut_olcum_sil = "DELETE FROM sut_olcum WHERE QR IN (SELECT QR FROM inek WHERE kullanici_id = :kullanici_id)";
      $stmt_sut_olcum_sil = $db->prepare($sql_sut_olcum_sil);
      $stmt_sut_olcum_sil->bindParam(':kullanici_id', $kullanici_id);
      $stmt_sut_olcum_sil->execute();

      // "kilo_olcum" tablosundan kullanıcıya ait olan kayıtları sil
      $sql_kilo_olcum_sil = "DELETE FROM kilo_olcum WHERE QR IN (SELECT QR FROM inek WHERE kullanici_id = :kullanici_id)";
      $stmt_kilo_olcum_sil = $db->prepare($sql_kilo_olcum_sil);
      $stmt_kilo_olcum_sil->bindParam(':kullanici_id', $kullanici_id);
      $stmt_kilo_olcum_sil->execute();
      // "inek" tablosundaki kullanıcıya ait kayıtları silme işlemi
      $sql_inek_sil = "DELETE FROM inek WHERE kullanici_id = :kullanici_id";
      $stmt_inek_sil = $db->prepare($sql_inek_sil);
      $stmt_inek_sil->bindParam(':kullanici_id', $kullanici_id);
      $stmt_inek_sil->execute();

      // Kullanıcıyı veritabanından silme işlemi
      $sql_kullanici_sil = "DELETE FROM kullanici WHERE kullanici_id = :kullanici_id";
      $stmt_kullanici_sil = $db->prepare($sql_kullanici_sil);
      $stmt_kullanici_sil->bindParam(':kullanici_id', $kullanici_id);
      $stmt_kullanici_sil->execute();

      // Kullanıcı başarıyla silindi mesajını göster
      echo "Kullanıcı başarıyla silindi.";
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
    AdminPanel
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css"
    href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
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

<body class="g-sidenav-show  bg-gray-200">
  <aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark"
    id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
        aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href=" https://demos.creative-tim.com/material-dashboard/pages/dashboard "
        target="_blank">
        <img src="../assets/img/logo-ct.png" class="navbar-brand-img h-100" alt="main_logo">
        <span class="ms-1 font-weight-bold text-white">Admin Kontrol Paneli</span>
      </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-white " href="../pages/dashboard.html">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <div class="mt-2 d-flex">
                <h6 class="mb-0">Light / Dark</h6>
                <div class="form-check form-switch ps-5 ms-auto my-auto">
                  <input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version"
                    onclick="darkMode(this)">
                </div>
              </div>
            </div>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white active bg-gradient-primary" href="../pages/KullaniciBilgi.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">receipt_long</i>
            </div>
            <span class="nav-link-text ms-1">Kullanıcı Bilgileri</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white " href="../pages/adminTablo.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">receipt_long</i>
            </div>
            <span class="nav-link-text ms-1">Admin Bilgileri</span>
          </a>
        </li>
      </ul>
    </div>
    <div class="sidenav-footer position-absolute w-100 bottom-0 ">
      <div class="mx-3">
        <a class="btn btn-outline-primary mt-4 w-100" href="../pages/adminDuzenle.php" type="button">Admin Ekle</a>
        <a class="btn bg-gradient-primary w-100" href="../pages/adminGiris.php" type="button">Çıkış Yap</a>
      </div>
    </div>
  </aside>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Kullanıcı Bilgileri</h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kullanıcı Adı</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kimlik
                        Numarası</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">e-mail
                        Adresi</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">sahip
                        olduğu inek sayısı</th>
                      <th class="text-secondary opacity-7"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($result as $row): ?>
                      <tr>
                        <td><?php echo $row['kullanici_adi']; ?></td>
                        <td><?php echo $row['tc_kimlik_no']; ?></td>
                        <td class="text-center"><?php echo $row['kullanici_mail']; ?></td>
                        <td class="text-center"><?php echo $row['inek_miktari']; ?></td>
                        <td>
                          <form method="POST">
                            <input type="hidden" name="kullanici_id" value="<?php echo $row['kullanici_id']; ?>">
                            <button type="submit" name="sil" class="btn btn-danger">Sil</button>
                          </form>
                        </td>
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
  </main>

  </div>
  </div>
  </div>
  </div>
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