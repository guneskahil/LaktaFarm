<?php
// config.php dosyası

function dbBaglantisi()
{
    $sunucu = "laktafarm-server.database.windows.net";
    $veritabani = "laktafarm-database";
    $kullanici = "laktafarm-server-admin";
    $sifre = "X4jyRWBDnQMtoNd";

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




?>