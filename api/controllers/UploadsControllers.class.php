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
        if ($cut !== false)
            if(imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h))
                if (imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h))
                    if (imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct))
                        return true;
        return false;
    }

    public function merge($data)
    {
        if (!isset($data['user_id']))
            return $this->httpUserResponse(400, "Please login before merging images.");
        if (!isset($data['img1']) || !isset($data['img2']))
            return $this->httpUserResponse(400, "Please login before uploading an image.");
        if (exif_imagetype($data['img1']) == IMAGETYPE_JPEG)
            $img1 = imagecreatefromjpeg($data['img1']);
        else if (exif_imagetype($data['img1']) == IMAGETYPE_PNG)
            $img1 = imagecreatefrompng($data['img1']);
        if (exif_imagetype($data['img2']) == IMAGETYPE_PNG)
            $img2 = imagecreatefrompng($data['img2']);
        else if (exif_imagetype($data['img2']) == IMAGETYPE_JPEG)
            $img2 = imagecreatefromjpeg($data['img2']);
        if (isset($img1) && isset($img2)) {
            $img1 = imagescale($img1, 400, 400);
            $img2 = imagescale($img2, 400, 400);
            if ($img1 === false || $img2 === false)
                return $this->httpUserResponse(400, "Unable to scale images. Please try again later.");
            $merge = $this->imagecopymerge_alpha($img1, $img2, 0, 0, 0, 0, 400, 400, 100);
            if (!$merge)
            {
                imagedestroy($img1);
                imagedestroy($img2);
                return $this->httpUserResponse(400, "Unable to merge images. Please try again later.");
            }
            $n = uniqid() . ".png";
            $name = "../uploads/".$n;
            $success = imagepng($img1, $name);
            imagedestroy($img1);
            imagedestroy($img2);
            if ($success)
            {
                $uploads = new Uploads();
                $target_file = "http://localhost/~mashesha/camagru/uploads/" . $n;
                $uploads->setVars($data);
                $uploads->setFileName($target_file);
                $insert = $uploads->appendArray($uploads->getVars());
                $response = $uploads->insert($uploads->getTableName(), $insert);
                if ($response === false)
                    return $this->httpUserResponse(400, "Failed to save photo. Please try again later.", $insert, $uploads->dbh);
                return $this->httpUserResponse(200, "Image saved successfully.", $insert);
            }
            return $this->httpUserResponse(400, "Unable to save the merged images. Please try again later.");
        }
        return $this->httpUserResponse(422, "One or both of the supplied images is not an image. Please try again.");
    }
}