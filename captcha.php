<?php
session_start();
header('Content-type: image/png');

// Ukuran gambar
$width = 120;
$height = 40;

// Buat gambar
$image = imagecreate($width, $height);

// Warna
$background_color = imagecolorallocate($image, 255, 255, 255); // putih
$text_color = imagecolorallocate($image, 0, 0, 0); // hitam
$line_color = imagecolorallocate($image, 64, 64, 64); // abu-abu
$pixel_color = imagecolorallocate($image, 0, 0, 255); // biru

// Karakter captch
$chars = 'ABCDEFGHJKLMNPRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
$captcha_text = '';
for ($i = 0; $i < 6; $i++) {
    $captcha_text .= $chars[rand(0, strlen($chars) - 1)];
}
$_SESSION['captcha'] = $captcha_text;

// Garis acak
for ($i = 0; $i < 5; $i++) {
    imageline($image, 0, rand() % 40, 100, rand() % 40, $line_color);
}

// Titik noise
for ($i = 0; $i < 200; $i++) {
    imagesetpixel($image, rand() % 100, rand() % 40, $pixel_color);
}

// Tulis karakter satu per satu agar bisa acak posisinya
for ($i = 0; $i < strlen($captcha_text); $i++) {
    $x = 10 + ($i * 15);
    $y = rand(5, 20);
    imagestring($image, 5, $x, $y, $captcha_text[$i], $text_color);
}

// Output
imagepng($image);
imagedestroy($image);
