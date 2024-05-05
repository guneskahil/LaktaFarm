<?php
session_start();

// Veritabanı bağlantısını gerçekleştiren fonksiyon
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

// Admin ekleme fonksiyonu
function adminEkle($email, $sifre)
{
  $db = dbBaglantisi();

  if ($db instanceof PDO) {
    try {
      // SQL sorgusu
      $sql = "INSERT INTO admin (admin_mail, admin_sifre) VALUES (:email, :sifre)";

      // SQL sorgusunu hazırlama
      $stmt = $db->prepare($sql);

      // Parametreleri bağlama
      $stmt->bindParam(':email', $email);
      $stmt->bindParam(':sifre', $sifre);

      // SQL sorgusunu çalıştırma
      $stmt->execute();

      // Ekleme işlemi başarılıysa true dön
      return true;
    } catch (PDOException $e) {
      // Hata durumunda hata mesajını ekrana yazdırma
      echo "Hata: " . $e->getMessage();
      return false;
    }
  } else {
    // Veritabanı bağlantısı sağlanamadı hatası
    echo "Veritabanı bağlantısı sağlanamadı.";
    return false;
  }
}


// Admin verilerini çeken fonksiyon
function adminVerileriGetir()
{
  $db = dbBaglantisi();

  if ($db instanceof PDO) {
    try {
      // SQL sorgusu
      $sql = "SELECT * FROM admin";

      // SQL sorgusunu hazırlama
      $stmt = $db->prepare($sql);

      // SQL sorgusunu çalıştırma
      $stmt->execute();

      // Sonuçları alma
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return $result;
    } catch (PDOException $e) {
      // Hata durumunda hata mesajını ekrana yazdırma
      echo "Hata: " . $e->getMessage();
      return false;
    }
  } else {
    // Veritabanı bağlantısı sağlanamadı hatası
    echo "Veritabanı bağlantısı sağlanamadı.";
    return false;
  }
}

// Admin verilerini al
$result = adminVerileriGetir();

// Admin silme fonksiyonu
function adminSil($admin_id)
{
  $db = dbBaglantisi();

  if ($db instanceof PDO) {
    try {
      // SQL sorgusu
      $sql = "DELETE FROM admin WHERE admin_id = :admin_id";

      // SQL sorgusunu hazırlama
      $stmt = $db->prepare($sql);

      // Parametreleri bağlama
      $stmt->bindParam(':admin_id', $admin_id);

      // SQL sorgusunu çalıştırma
      $stmt->execute();

      // Silme işlemi başarılıysa true dön
      return true;
    } catch (PDOException $e) {
      // Hata durumunda hata mesajını ekrana yazdırma
      echo "Hata: " . $e->getMessage();
      return false;
    }
  } else {
    // Veritabanı bağlantısı sağlanamadı hatası
    echo "Veritabanı bağlantısı sağlanamadı.";
    return false;
  }
}

// Formdan gelen admin_id'yi kullanarak admin silme işlemini gerçekleştirin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sil'])) {
  $admin_id = $_POST['admin_id'];

  // Admin silme işlemi
  if (adminSil($admin_id)) {
    // Silme başarılıysa yönlendirme yapabilirsiniz
    header("Location: adminTablo.php");
    exit();
  } else {
    // Silme başarısızsa hata mesajı göster
    echo "Admin silme işlemi başarısız.";
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
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
      <div class="container-fluid py-4">
        <div class="row">
          <div class="col-12">
            <div class="card my-1">
              <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                  <h6 class="text-white text-capitalize ps-3">Kayıtlı Admin Bilgileri</h6>
                </div>
              </div>
              <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                  <table class="table align-items-center mb-0">
                    <thead>
                      <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Admin ID</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Admin Mail
                        </th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                          Şifre</th>

                        <th class="text-secondary opacity-7"></th>
                      </tr>

                    </thead>
                    <tbody>
                      <?php
                      // Eğer verileri başarıyla çektinizse foreach döngüsüyle tabloyu oluşturun
                      if ($result) {
                        foreach ($result as $row) {
                          ?>
                          <tr>
                            <td><?php echo $row['admin_id']; ?></td>
                            <td><?php echo $row['admin_mail']; ?></td>
                            <td class="text-center"><?php echo $row['admin_sifre']; ?></td>
                            <td>
                              <!-- Silme işlemi için form -->
                              <form method="POST">
                                <input type="hidden" name="admin_id" value="<?php echo $row['admin_id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger" name="sil">Sil</button>
                              </form>
                            </td>
                            <td>
                              <a href="adminGuncelle.php?admin_id=<?php echo $row['admin_id']; ?>"
                                class="btn btn-primary">Güncelle</a>
                            </td>
                          </tr>
                          <?php
                        }
                      } else {
                        // Veritabanından veri alınamadıysa hata mesajı göster
                        echo "<tr><td colspan='4'>Veri bulunamadı.</td></tr>";
                      }
                      ?>

                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

  </main>
  <div class="fixed-plugin">
    <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
      <i class="material-icons py-2">settings</i>
    </a>
    <div class="card shadow-lg">
      <div class="card-header pb-0 pt-3">
        <div class="float-start">
          <h5 class="mt-3 mb-0">Material UI Configurator</h5>
          <p>See our dashboard options.</p>
        </div>
        <div class="float-end mt-4">
          <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
            <i class="material-icons">clear</i>
          </button>
        </div>
        <!-- End Toggle Button -->
      </div>
      <hr class="horizontal dark my-1">
      <div class="card-body pt-sm-3 pt-0">
        <!-- Sidebar Backgrounds -->
        <div>
          <h6 class="mb-0">Sidebar Colors</h6>
        </div>
        <a href="javascript:void(0)" class="switch-trigger background-color">
          <div class="badge-colors my-2 text-start">
            <span class="badge filter bg-gradient-primary active" data-color="primary"
              onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-dark" data-color="dark" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
          </div>
        </a>
        <!-- Sidenav Type -->
        <div class="mt-3">
          <h6 class="mb-0">Sidenav Type</h6>
          <p class="text-sm">Choose between 2 different sidenav types.</p>
        </div>
        <div class="d-flex">
          <button class="btn bg-gradient-dark px-3 mb-2 active" data-class="bg-gradient-dark"
            onclick="sidebarType(this)">Dark</button>
          <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-transparent"
            onclick="sidebarType(this)">Transparent</button>
          <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-white"
            onclick="sidebarType(this)">White</button>
        </div>
        <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
        <!-- Navbar Fixed -->
        <div class="mt-3 d-flex">
          <h6 class="mb-0">Navbar Fixed</h6>
          <div class="form-check form-switch ps-0 ms-auto my-auto">
            <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
          </div>
        </div>
        <hr class="horizontal dark my-3">
        <div class="mt-2 d-flex">
          <h6 class="mb-0">Light / Dark</h6>
          <div class="form-check form-switch ps-0 ms-auto my-auto">
            <input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version" onclick="darkMode(this)">
          </div>
        </div>
        <hr class="horizontal dark my-sm-4">
        <a class="btn bg-gradient-info w-100" href="https://www.creative-tim.com/product/material-dashboard-pro">Free
          Download</a>
        <a class="btn btn-outline-dark w-100"
          href="https://www.creative-tim.com/learning-lab/bootstrap/overview/material-dashboard">View documentation</a>
        <div class="w-100 text-center">
          <a class="github-button" href="https://github.com/creativetimofficial/material-dashboard"
            data-icon="octicon-star" data-size="large" data-show-count="true"
            aria-label="Star creativetimofficial/material-dashboard on GitHub">Star</a>
          <h6 class="mt-3">Thank you for sharing!</h6>
          <a href="https://twitter.com/intent/tweet?text=Check%20Material%20UI%20Dashboard%20made%20by%20%40CreativeTim%20%23webdesign%20%23dashboard%20%23bootstrap5&amp;url=https%3A%2F%2Fwww.creative-tim.com%2Fproduct%2Fsoft-ui-dashboard"
            class="btn btn-dark mb-0 me-2" target="_blank">
            <i class="fab fa-twitter me-1" aria-hidden="true"></i> Tweet
          </a>
          <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.creative-tim.com/product/material-dashboard"
            class="btn btn-dark mb-0 me-2" target="_blank">
            <i class="fab fa-facebook-square me-1" aria-hidden="true"></i> Share
          </a>
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