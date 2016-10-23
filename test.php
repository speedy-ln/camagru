<?php
/**
 * Created by IntelliJ IDEA.
 * User: Lebelo Nkadimeng
 * Date: 2016/10/19
 * Time: 3:46 PM
 */
$src = imagecreatefrompng('leaves.png');
$dest = imagecreatefrompng('5807c7eae663a.png');

$src = imagescale($src, 400, 400);
$dest = imagescale($dest, 400, 400);

function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
{
    $cut = imagecreatetruecolor($src_w, $src_h);
    imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
    imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
    imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
}
imagecopymerge_alpha($dest, $src, 0, 0, 0, 0, 400, 400, 100);
//imagealphablending($dest, false);
//imagesavealpha($dest, true);

//imagecopymerge($dest, $src, 10, 10, 0, 0, 400, 400, 100); //have to play with these numbers for it to work for you, etc.
//$name = uniqid() . "-img.png";
//imagepng($dest, $name);
header('Content-Type: image/png');
imagepng($dest);

imagedestroy($dest);
imagedestroy($src);
hash