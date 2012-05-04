<?php

class InvalidImageException extends Exception {}

class Image {
	
	/* Allowed mimetypes */
	private $allowed_types = array(
		"image/pjpeg",
		"image/jpeg",
		"image/png",
		"image/x-png",
		"image/gif"
	);
	/* Allowed filename extensions */
	private $allowed_exts = array(
		"jpg",
		"jpeg",
		"png",
		"gif"
	);
	
	/* (Thumbnails are resized proportionally
		according to the original image */
	/* Thumbnail width */
	private $thumb_width = 100;
	/* Thumbnail height */
	private $thumb_height = 100;
	
	/* Directory where images and thumbnails go */
	private $comic_upload_dir = "comics/";
	private $thumb_upload_dir = "thumbs/";
	
	/* DON'T CHANGE VALUES BELOW */
	private $filename;
	private $filetype;
	/* Filename as the image was originally called
	   (if it was renamed) */
	private $orig_filename;
	/* Number to add to the end of the filename */
	private $filename_number = 0;
	
	public function getFilename() {
		return $this->filename;
	}
	public static function getThumbFilename($filename) {
		$array = explode(".", $filename);
		/* Get the extension of the filename */
		$ext = $array[count($array)-1];
		/* Append the extension to the filename ommiting the dot */
		$part = count($array)-1;
		$array[$part] = $array[$part] .= $ext;
		/* Remove the extension part */
		array_pop($array);
		/* Convert the array back in case there were more dots in the filename*/
		$filename = implode(".", $array);
		/* Add the thumbnail part */
		$filename .= "-thumb.png";
		return $filename;
	}
	public function upload($file) {
		//print_r($file);
		$this->filetype = $file["type"];
		$this->filename = basename($file["name"]);
		//print_r($file);
		if ($file["error"] ||
			!$this->mimetypeValid($this->filetype) ||
			!$this->extensionValid($this->filename) ||
			!$this->filenameValid($this->filename)) {
			/* We consider the image invalid if there was
			   an error uploading it, the mimetype isn't valid,
			   the filename isn't valid, or the extension isn't valid */
			throw new InvalidImageException();
		}
		while ($this->filenameTaken($this->filename)) {
			/* Filenames become "origname-#.ext" */
			$this->filename = $this->getNewFilename($this->filename);
		}
		/* Thumbnails become "orignameext-thumb.png" */
		$this->generateThumb($file);
		move_uploaded_file($file['tmp_name'], $this->comic_upload_dir . $this->filename);
		return true;
	}
	private function getThumbnailSize($width_orig, $height_orig) {
		if ($this->thumb_width && ($width_orig < $height_orig)) {
   			$this->thumb_width = ($this->thumb_height / $height_orig) 
			* $width_orig;
		} else {
   			$this->thumb_height = ($this->thumb_width / $width_orig) 
			* $height_orig;
		}
		$this->thumb_width = (integer) $this->thumb_width;
		$this->thumb_height = (integer) $this->thumb_height;
		return array($this->thumb_width, $this->thumb_height);
	}
	private function generateThumb($file) {
		$size = getimagesize($file['tmp_name']);
		$thumb_size = $this->getThumbnailSize($size[0], $size[1]);
		$thumb = imagecreatetruecolor($thumb_size[0], $thumb_size[1]);
		switch($file["type"]) {
		case "image/pjpeg":
		case "image/jpeg":
			$source = imagecreatefromjpeg($file['tmp_name']);
			break;
		case "image/png":
		case "image/x-png":
			$source = imagecreatefrompng($file['tmp_name']);
			break;
		case "image/gif":
			$source = imagecreatefromgif($file['tmp_name']);
			break;
		/* If new image types are added later and they are not supported, we
		   won't do the thumbnail */
		default:
			return;
		}
		imagesavealpha($thumb, true);
		Imagealphablending($thumb, false);
		$colour = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
		imagefill($thumb, 0, 0, $colour);
		imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumb_size[0], $thumb_size[1], $size[0], $size[1]);
		imagepng( $thumb, $this->thumb_upload_dir . Image::getThumbFilename($this->filename) );
	}
	private function mimetypeValid($mimetype) {
		if (!in_array($mimetype, $this->allowed_types)) {
			//echo "mimetype";
			return false;
		}
		return true;
	}
	private function extensionValid($filename) {
		$array = explode(".", $filename);
    	$nr  = count($array);
    	$ext  = $array[$nr-1];
		if (!in_array($ext, $this->allowed_exts)) {
			//echo "extension";
			return false;
		}
		return true;
	}
	private function filenameValid($filename) {
		if (ereg("[^A-Za-z0-9 ._-]", $filename) || strlen($filename) > 20) {
			return false;
		}
		return true;
	}
	private function filenameTaken($filename) {
		if (file_exists($this->comic_upload_dir . $filename) || file_exists($this->thumb_upload_dir . $this->getThumbFilename($filename))) {
			return true; 
		}
		return false;
	}
	private function getNewFilename($old_filename) {
		/* If the old filename isn't set, we set it to the current one */
		if (!isset($this->orig_filename)) {
			$this->orig_filename = $old_filename;
		}
		$array = explode(".", $this->orig_filename);
    	$nr = count($array);
		$this->filename_number += 1;
    	$array[$nr-2] = $array[$nr-2] . "-" . $this->filename_number;
		$new_filename = implode(".", $array);
		return $new_filename;
	}
}

?>
