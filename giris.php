<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['mail']) && isset($_POST['sifre'])) {
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
                $_SESSION['ad'] = $kullanici['ad'];
                $_SESSION['kullanici_id'] = $kullanici['kullanici_id'];
                header("Location: anaSayfa.php");
                exit();
            } else {
                $error = "Hatalı giriş bilgileri. Lütfen tekrar deneyin.";
            }
        } else {
            $error = "Veritabanı bağlantısı yapılamadı.";
        }
    }
}

// Çıkış işlemi
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
?>