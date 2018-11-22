<?php
/* Copyright (c) 
 * - 2011, Geert Bergman (geert@scrivo.nl)
 * - 2005, DHKold (admin@dhkold.com), CreateImageFromBMP
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. Neither the name of "Scrivo" nor the names of its contributors may be
 *    used to endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * $Id: Crop.php 841 2013-08-19 22:19:47Z geert $
 */

namespace ScrivoUi\Editor\Lib;

use \Scrivo\Str;

/**
 * The ImageUtilities class implements some utility functions for working
 * with Scrivo image assets.
 */
class ImageUtilities {

	/**
	 * Create an PHP image resource from a Scrivo image asset.
	 *
	 * @param \Scrivo\File $asset The Scrivo file to create the asset for.
	 * @return Resource an PHP image resource
	 */
	public static function imageCreateFromAsset($asset) {

		$jpeg = new Str("jpeg");
		$jpg = new Str("jpg");
		$png = new Str("png");
		$gif = new Str("gif");
		$bmp = new Str("bmp");

		if ($asset->mimeType->substr(-4)->equals($jpeg)) {
			return imagecreatefromjpeg($asset->location);
		} else if ($asset->mimeType->substr(-3)->equals($gif)) {
			return imagecreatefromgif($asset->location);
		} else if ($asset->mimeType->substr(-3)->equals($png)) {
			return self::ImageCreateFromPNGBg($asset->location, 255, 255, 255);
		} else if ($asset->mimeType->substr(-3)->equals($bmp)) {
			return self::ImageCreateFromBMP($asset->location);
		} else {
			if ($asset->title->substr(-3)->equals($jpg)
					|| $asset->title->substr(-4)->equals($jpeg)) {
				return imagecreatefromjpeg($asset->location);
			} else if ($asset->title->substr(-3)->equals($gif)) {
				return imagecreatefromgif($asset->location);
			} else if ($asset->title->substr(-3)->equals($png)) {
				return self::ImageCreateFromPNGBg(
					$asset->location, 255, 255, 255);
			} else if ($asset->title->substr(-3)->equals($bmp)) {
				return self::ImageCreateFromBMP($asset->location);
			}
		}
		return null;
	}

	/**
	 * Create an image with a specified background color for a PNG with a
	 * transparent background.
	 *
	 * @param string $filename The file name of the file to create the
	 *     image for.
	 * @param int $r The red color component of the background color (0-255).
	 * @param int $g The green color component of the background color (0-255).
	 * @param int $b The blue color component of the background color (0-255).
	 * @return Resource The image resource.
	 */
	private static function ImageCreateFromPNGBg($filename, $r, $g, $b) {
		$image = getImageSize($filename, $info);
		$img = imagecreatefrompng($filename);
		$imgX = $image[0];
		$imgY = $image[1];
		$imgh = imageCreateTrueColor($imgX, $imgY);
		$backgroundColor = imagecolorallocate($imgh, $r, $g, $b);
		imagefill($imgh, 0, 0, $backgroundColor);
		imagecopyresampled($imgh, $img, 0, 0, 0, 0, $imgX, $imgY, $imgX, $imgY);
		return $imgh;
	}

	/**
	 * Unfortunately some people use BMP files over the internet. Thanks to
	 * admin@dhkold.com for helping out via php.net.
	 * Fonction: ImageCreateFromBMP
	 * Author:   DHKold
	 * Contact:  admin@dhkold.com
	 * Date:     The 15th of June 2005
	 * Version:  2.0B
	 *
	 * @param string $filename The file name of the file to create the
	 *     image for.
	 * @return Resource The image resource.
	 */
	private static function ImageCreateFromBMP($filename) {

		//Ouverture du fichier en mode binaire
		if (! $f1 = fopen($filename,"rb")) return FALSE;

		//1 : Chargement des entêtes FICHIER
		$FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset",
			fread($f1,14));
		if ($FILE['file_type'] != 19778) return FALSE;

		//2 : Chargement des entêtes BMP
		$BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
			'/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
			'/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
		$BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
		if ($BMP['size_bitmap'] == 0)
			$BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
		$BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
		$BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
		$BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
		$BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
		$BMP['decal'] = 4-(4*$BMP['decal']);
		if ($BMP['decal'] == 4) $BMP['decal'] = 0;

		//3 : Chargement des couleurs de la palette
		$PALETTE = array();
		if ($BMP['colors'] < 16777216)
		{
			$PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
		}

		//4 : Création de l'image
		$IMG = fread($f1,$BMP['size_bitmap']);
		$VIDE = chr(0);

		$res = imagecreatetruecolor($BMP['width'],$BMP['height']);
		$P = 0;
		$Y = $BMP['height']-1;
		while ($Y >= 0)
		{
			$X=0;
			while ($X < $BMP['width'])
			{
				if ($BMP['bits_per_pixel'] == 24)
					$COLOR = unpack(
						"V",Scrivo_byte_array_substr($IMG,$P,3).$VIDE);
				elseif ($BMP['bits_per_pixel'] == 16)
				{
					$COLOR = unpack(
						"n",Scrivo_byte_array_substr($IMG,$P,2));
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				elseif ($BMP['bits_per_pixel'] == 8)
				{
					$COLOR = unpack(
						"n",$VIDE.Scrivo_byte_array_substr($IMG,$P,1));
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				elseif ($BMP['bits_per_pixel'] == 4)
				{
					$COLOR = unpack(
						"n",$VIDE.Scrivo_byte_array_substr($IMG,floor($P),1));
					if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ;
					else $COLOR[1] = ($COLOR[1] & 0x0F);
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				elseif ($BMP['bits_per_pixel'] == 1)
				{
					$COLOR = unpack(
						"n",$VIDE.Scrivo_byte_array_substr($IMG,floor($P),1));
					if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
					elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
					elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
					elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
					elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
					elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
					elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
					elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				else
					return FALSE;
				imagesetpixel($res,$X,$Y,$COLOR[1]);
				$X++;
				$P += $BMP['bytes_per_pixel'];
			}
			$Y--;
			$P+=$BMP['decal'];
		}

		//Fermeture du fichier
		fclose($f1);

		return $res;
	}
}

?>