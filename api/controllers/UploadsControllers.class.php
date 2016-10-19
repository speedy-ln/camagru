<?php

/**
 * Created by IntelliJ IDEA.
 * User: Lebelo Nkadimeng
 * Date: 2016/10/19
 * Time: 1:36 PM
 */
class UploadsControllers extends Controller
{

    public function new_picture($_request)
    {
        if (!isset($_request['user_id']))
            return $this->httpUserResponse(400, "Please login before uploading an image.");
        if (isset($_FILES['file'])){
            $allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG);
            $detectedType = exif_imagetype($_FILES['file']['tmp_name']);
            $error = !in_array($detectedType, $allowedTypes);
            if ($error)
                return $this->httpUserResponse(415, "Uploaded file is not an image. Please upload a png or jpg image.");
            $upload_dir = "../uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir);
            }
            $file_name = uniqid() . ".png";
            $target_file = $upload_dir.$file_name;
            $uploaded = move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
            if ($uploaded === FALSE)
                return $this->httpUserResponse(417, "Failed to upload file. Please try again later.");
            $target_file = "http://localhost/~mashesha/camagru/uploads/" . $file_name;
            $uploads = new Uploads();
            $uploads->setVars($_request);
            $uploads->setFileName($target_file);
            $uploads->setVisible(0);
            $insert = $uploads->appendArray($uploads->getVars());
            $response = $uploads->insert($uploads->getTableName(), $insert);
            if ($response === false)
                return $this->httpUserResponse(400, "Failed to save photo. Please try again later.", false, $uploads->dbh);
            return $this->httpUserResponse(200, "Image saved successfully.");
        }
        return $this->httpUserResponse(400, "No file was uploaded. please upload a file.");
    }

    public function save_base($param)
    {
        if (!isset($param['user_id']))
            return $this->httpUserResponse(400, "Please login before uploading an image.");
        if (!isset($param['data']))
            return $this->httpUserResponse(400, "Please login before uploading an image.");
        $img = str_replace('data:image/png;base64,', '', $param['data']);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $upload_dir = "../uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir);
        }
        $file_name = uniqid() . ".png";
        $target_file = $upload_dir.$file_name;
        $bytes = file_put_contents($target_file, $data);
        if ($bytes !== false) {
            $uploads = new Uploads();
            $target_file = "http://localhost/~mashesha/camagru/uploads/" . $file_name;
            $uploads->setVars($param);
            $uploads->setFileName($target_file);
            $uploads->setVisible(0);
            $insert = $uploads->appendArray($uploads->getVars());
            $response = $uploads->insert($uploads->getTableName(), $insert);
            if ($response === false)
                return $this->httpUserResponse(400, "Failed to save photo. Please try again later.", false, $uploads->dbh);
            return $this->httpUserResponse(200, "Image saved successfully.", $insert);
        }
        return $this->httpUserResponse(400, "Unable to save image.");
    }

    private function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
    {
        $cut = imagecreatetruecolor($src_w, $src_h);
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
    }

    public function merge()
    {
        if (exif_imagetype($_SESSION['img1']) == IMAGETYPE_JPEG)
            $img1 = imagecreatefromjpeg($_SESSION['img1']);
        else if (exif_imagetype($_SESSION['img1']) == IMAGETYPE_PNG)
            $img1 = imagecreatefrompng($_SESSION['img1']);
        if (exif_imagetype($_SESSION['img2']) == IMAGETYPE_PNG)
            $img2 = imagecreatefrompng($_SESSION['img2']);
        else if (exif_imagetype($_SESSION['img2']) == IMAGETYPE_JPEG)
            $img2 = imagecreatefromjpeg($_SESSION['img2']);
        if (isset($img1) && isset($img2)) {
            $img2 = imagescale($img2, 100, 100);
            $this->imagecopymerge_alpha($img1, $img2, 10, 10, 0, 0, 100, 100, 100);
            $name = uniqid() . ".png";
            imagepng($img1, $name);
            imagedestroy($img1);
            imagedestroy($img2);
            $final = 'data:image/png;base64,' . base64_encode(file_get_contents($name));
            unlink($name);
        }
    }
}