<?php

/*
* File: SimpleImage.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 08/11/06
* Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details:
* http://www.gnu.org/licenses/gpl.html
*
* v0.9 - Tom McKenzie 2011 - enhanced resizing functionality (resizeToDims)
* v1.0 - Tom McKenzie 2012/02/23
		More comments, better code layout, minor fixes
*/

class SimpleImage {
	public $image;
	public $image_type;

	public function __construct($filename = null) {
		if ($filename !== null)
			$this->load($filename);
	}

	public function load($filename) {
		$image_info = getimagesize($filename);
		$this->image_type = $image_info[2];

		if( $this->image_type == IMAGETYPE_JPEG ) {
			$this->image = imagecreatefromjpeg($filename);

		} elseif( $this->image_type == IMAGETYPE_GIF ) {
			$this->image = imagecreatefromgif($filename);

		} elseif( $this->image_type == IMAGETYPE_PNG ) {
			$this->image = imagecreatefrompng($filename);
		}

		if (!$this->image) {
			throw new Exception("Invalid image type");
		}
	}
	public function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
		$result = false;

		if( $image_type == IMAGETYPE_JPEG ) {
			$result = imagejpeg($this->image,$filename,$compression);

		} elseif( $image_type == IMAGETYPE_GIF ) {
			$result = imagegif($this->image,$filename);
		
		} elseif( $image_type == IMAGETYPE_PNG ) {
			$result = imagepng($this->image,$filename);
		}
		
		if( $permissions != null) {
			chmod($filename,$permissions);
		}

		if (!$result)
			throw new Exception("Couldn't save image");
	}
	public function output($image_type=IMAGETYPE_JPEG) {
		$result = false;

		if( $image_type == IMAGETYPE_JPEG ) {
			$result = imagejpeg($this->image);

		} elseif( $image_type == IMAGETYPE_GIF ) {
			$result = imagegif($this->image);

		} elseif( $image_type == IMAGETYPE_PNG ) {
			$result = imagepng($this->image);
		}

		if (!$result)
			throw new Exception("Couldn't output image");
	}

	public function getWidth() {
		return imagesx($this->image);
	}
	public function getHeight() {
		return imagesy($this->image);
	}

	// scales image to a maximum height
	public function resizeToHeight($height) {
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width,$height);
	}
	
	// scales image to a maximum width
	public function resizeToWidth($width) {
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize($width,$height);
	}
	
	// scales image by a percentage (100 = no change)
	public function scale($scale) {
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100;
		$this->resize($width,$height);
	}
	
	// resizes an image without cropping
	public function resizeToDims($width, $height) {
		$ratio = $width / $height;
		$origratio =  $this->getWidth() / $this->getHeight();
		
		if ($origratio > $ratio) { // wider than dims
			$ratio = $width / $this->getWidth();
			$height = $this->getheight() * $ratio;

		} else { // taller than dims
			$ratio = $height / $this->getHeight();
			$width = $this->getWidth() * $ratio;
		}

		$this->resize($width,$height);
	}

	// resize to a specified width and height
	public function resize($width, $height) {
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->image = $new_image;
	}

}
?>