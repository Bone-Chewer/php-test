<?php


function addTextToImage($news_path, $origin_path, $width, $height, $texts)
{
    $origin_image = imagecreatefromstring(file_get_contents($origin_path));
    $origin_image_width = imagesx($origin_image);
    $origin_image_height = imagesy($origin_image);
    
    $new_image = imagecreatetruecolor($width, $height);
    imagecopyresampled($new_image, $origin_image, 0, 0, 0, 0, $width, $height, $origin_image_width, $origin_image_height);
    
    
    foreach($texts as $text) {
        $content = $text[0];
        $fontSize = $text[1];
        $fontColor = $text[2];        
        $ttf = $text[3];
        $text_x = $text[4];
        $text_y = $text[5];
        
        $fontColorArr = [];
        preg_match("/\s*rgb\(\s*([\d]*)\s*\,\s*([\d]*)\s*\,\s*([\d]*)\s*\)\s*/", $fontColor, $fontColorArr);
        $fontColor = imagecolorallocate($new_image, $fontColorArr[1], $fontColorArr[2], $fontColorArr[3]);
        $fontBox = imagettfbbox($fontSize, 30, $ttf, $content);
        $font_width = abs($fontBox[0] - $fontBox[2]);
        $font_height = abs($fontBox[7] - $fontBox[1]);
        var_dump($fontBox, $font_width, $font_height);
        exit;
        imagettftext($new_image, $fontSize, 30, $text_x, $text_y, $fontColor, $ttf, $content);
    }
    
    $mine = pathinfo($news_path, PATHINFO_EXTENSION);
    switch($mine) {
        case 'png':
            imagepng($new_image, $news_path, 7);
            break;
        
        case 'jpg':
        case 'jpeg':
        default:
            imagejpeg($new_image, $news_path, 100);
            break;            
    }
    
    imagedestroy($origin_image);
    imagedestroy($new_image);
    return true;
}

$news_path = 'data/images/'.uniqid().'.png';
$origin_path = 'data/images/123.jpg';
$texts = [
    [
        '￥你好',
        28,
        'rgb(255, 155, 78)', 
        'data/ttf/msyh.ttf',
        500,
        350,
    ],
];
addTextToImage($news_path, $origin_path, 680, 800, $texts);


