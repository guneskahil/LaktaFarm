<?php
// config.php dosyası

function dbBaglantisi()
{
    $sunucu = "bulutsqldeneme.database.windows.net";
    $veritabani = "LaktaFarmDB";
    $kullanici = "sqladmin";
    $sifre = "admin.123";

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



function editprofile($id = '')
{
    // Veritabanı bağlantısını yap
    $db = new PDO('mysql:host=localhost;dbname=your_database;charset=utf8', 'username', 'password');

    // Kullanıcıdan gelen POST verilerini al
    $id = $_POST['kode'];
    $ktp = $_POST['ktp'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];
    $hp = $_POST['hp'];

    // SQL sorgusu oluştur
    $sql = "UPDATE musteri SET 
            no_ktp_musteri = :ktp, 
            isim_musteri = :nama, 
            email_musteri = :email, 
            resim_musteri = 'assets/frontend/img/default.png', 
            adres_musteri = :alamat, 
            telpon_musteri = :hp 
            WHERE kd_musteri = :id";

    // SQL sorgusunu hazırla
    $stmt = $db->prepare($sql);

    // Değişkenleri bağla
    $stmt->bindParam(':ktp', $ktp);
    $stmt->bindParam(':nama', $nama);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':alamat', $alamat);
    $stmt->bindParam(':hp', $hp);
    $stmt->bindParam(':id', $id);

    // Sorguyu çalıştır
    $stmt->execute();

    // Session mesajını ayarla
    $_SESSION['message'] = 'Güncelleme Başarılı';

    // Profil sayfasına yönlendir
    header('Location: profil/profilesaya/' . $id);
    exit();
}

?>