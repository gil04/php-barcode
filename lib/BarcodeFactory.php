<?php

namespace jucksearm\barcode\lib;

use jucksearm\barcode\lib\Barcode1D;

class BarcodeFactory
{
	private $_attributes;
	private $_scalePx = 1;
	private $_heightPx = 1;

	public function __construct()
	{
		$this->_attributes = [
			'code' => null,
			'type' => null,
			'file' => null,
			'scale'  => 1,
			'height' => 30,
			'rotate' => 0,
			'color'  => '000',
			'text' => null,
			'fontsize' => 12,
			'font' => null,
			'padding' => 0,
		];
	}

	public function __set($name, $value)
	{
		$setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            // set property
            $this->$setter($value);

            return;
         }
	}

	public function __get($name)
	{
		$getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            // read property
            return $this->$getter();
        }
	}

	public function setAttribute($name, $value)
	{
		if ($value === null) return;

		$this->_attributes[$name] = $value;
	}

	public function getAttribute($name)
	{
		return $this->_attributes[$name];
	}

	public function setAttributes($attrs = [])
	{
		array_merge($this->_attributes, $attrs);
	}

	public function getAttributes()
	{
		return $this->_attributes;
	}

	public function setCode($value)
	{
		$this->setAttribute('code', $value);
		return $this;
	}

	public function getCode()
	{
		return $this->getAttribute('code');
	}

	public function setPadding($value)
	{
		$this->setAttribute('padding', $value);
		return $this;
	}

	public function getPadding()
	{
		return $this->getAttribute('padding');
	}

	public function setText($value)
	{
		$this->setAttribute('text', $value);
		return $this;
	}

	public function getText()
	{
		return $this->getAttribute('text');
	}

	public function setFontSize($value)
	{
		$this->setAttribute('fontsize', $value);
		return $this;
	}

	public function getFontSize()
	{
		return $this->getAttribute('fontsize');
	}

	public function getFont()
	{
		return $this->getAttribute('font');
	}

	public function setFont($value)
	{
		$this->setAttribute('font', $value);
		return $this;
	}

	public function setType($value)
	{
		$this->setAttribute('type', $value);
		return $this;
	}

	public function getType()
	{
		return $this->getAttribute('type');
	}

	public function setFile($value)
	{
		$this->setAttribute('file', $value);
		return $this;
	}

	public function getFile()
	{
		return $this->getAttribute('file');
	}

	public function setScale($value)
	{
		$this->setAttribute('scale', $value);
		return $this;
	}

	public function getScale()
	{
		return $this->getAttribute('scale') * $this->_scalePx;
	}

	public function setHeight($value)
	{
		$this->setAttribute('height', $value);
		return $this;
	}

	public function getHeight()
	{
		return $this->getAttribute('height') * $this->_heightPx;
	}

	public function setRotate($value)
	{
		$value = abs($value);
		if ($value != 0 && $value%90 != 0) return $this;

		$this->setAttribute('rotate', $value);
		return $this;
	}

	public function getRotate()
	{
		return $this->getAttribute('rotate');
	}

	public function setColor($value)
	{
		$this->setAttribute('color', $value);
		return $this;
	}

	public function getColor()
	{
		return $this->getAttribute('color');
	}

	public function getBarcode()
	{
		return new Barcode1D($this->code, $this->type);
	}

	public function getHexColor()
	{
		$color = $this->color;

		return '#'.$color;
	}

	public function getRgbColor()
	{
		$color = $this->color;

		if (strlen($color) > 3) {
			$r = hexdec(substr($color, 0, 2));
			$g = hexdec(substr($color, 2, 2));
			$b = hexdec(substr($color, 4, 2));
		} else {
			$r = hexdec(substr($color, 0, 1).substr($color, 0, 1));
			$g = hexdec(substr($color, 1, 1).substr($color, 1, 1));
			$b = hexdec(substr($color, 2, 1).substr($color, 2, 1));
		}

		return [$r, $g, $b];
	}

	public function getBarcodeHtmlData()
	{
		$bcd = $this->barcode->getBarcodeArray();
		$rotate = $this->rotate;
		$color = $this->hexColor;

		if ($rotate == 0 || $rotate%180 == 0) {
			$w = $this->scale;
			$h = $this->height;

			$barcodeData = '<div style="font-size:0;position:relative;width:'.($bcd['maxw'] * $w).'px;height:'.($h).'px;">'."\n";
			// print bars
			$x = 0;
			foreach ($bcd['bcode'] as $k => $v) {
				$bw = round(($v['w'] * $w), 3);
				$bh = round(($v['h'] * $h / $bcd['maxh']), 3);
				if ($v['t']) {
					$y = round(($v['p'] * $h / $bcd['maxh']), 3);
					// draw a vertical bar
					$barcodeData .= '<div style="background-color:'.$color.';width:'.$bw.'px;height:'.$bh.'px;position:absolute;left:'.$x.'px;top:'.$y.'px;">&nbsp;</div>'."\n";
				}
				$x += $bw;
			}
			$barcodeData .= '</div>'."\n";
			if (!is_null($this->text)) {
				$font = ($this->font === null) ?  'monospace' : $this->font . ", monospace";
				$barcodeData .= '<div style="font-size:'. $this->fontsize .'px;margin-top: 4px;font-family: '. $font .';position:relative;text-align:center;width:' . ($bcd['maxw'] * $w) . 'px;height:' . ((int)$this->fontsize + 4) .'px;">' . $this->text ."</div>\n";
			}

		} else {
			$w = $this->height;
			$h = $this->scale;

			$barcodeData = '<div style="font-size:0;position:relative;width:'.($w).'px;height:'.($bcd['maxw'] * $h).'px;">'."\n";
			// print bars
			$y = 0;
			foreach ($bcd['bcode'] as $k => $v) {
				$bw = round(($v['h'] * $w / $bcd['maxh']), 3);
				$bh = round(($v['w'] * $h), 3);
				if ($v['t']) {
					$x = round(($v['p'] * $h / $bcd['maxh']), 3);
					if (!is_null($this->text)) {
						$x = $x + $this->fontsize + 4;
					}
					// draw a vertical bar
					$barcodeData .= '<div style="background-color:'.$color.';width:'.$bw.'px;height:'.$bh.'px;position:absolute;left:'.$x.'px;top:'.$y.'px;">&nbsp;</div>'."\n";
				}
				$y += $bh;
			}

			if (!is_null($this->text)) {
				$font = ($this->font === null) ?  'monospace' : $this->font . ", monospace";
				$barcodeData .= '<div style="font-size:'. $this->fontsize .'px;margin-top: 0px;height:'.$bh.'px;font-family: ' . $font . ';position:absolute;text-align:center;width:' . $w . 'px;height:'.($bcd['maxw'] * $h).'px;"><div style="  position: absolute;top: 50%;left: 6px;transform:  translateX(-50%) translateY(-50%) rotate('. $rotate . 'deg);">' . $this->text ."</div></div>\n";
			}
			$barcodeData .= '</div>'."\n";

		}

		return $barcodeData;
	}

	public function getBarcodePngData()
	{
		$bcd = $this->barcode->getBarcodeArray();
		$rotate = $this->rotate;
		$color = $this->rgbColor;

		if (!is_null($this->text)) {
			if ( is_null($this->font) || !is_readable($this->font))
					throw new \Exception('No ttf font given or ttf font not readble.');


			$font_file = $this->font; // This is the path to your font file.
			// Retrieve bounding box:
			$type_space = imagettfbbox($this->fontsize, 0, $font_file, $this->text);
			$txt_height = abs($type_space[5] - $type_space[1]);
			$txt_width = abs($type_space[4] - $type_space[0]);
			$requiredtxtheight = 4 + $txt_height;
		} else {
			$requiredtxtheight = 0;
		}

		$w = $this->scale;
		$h = $this->height;

		if (function_exists('imagecreate')) {
			$png = imagecreate(($bcd['maxw'] * $w), $h + $requiredtxtheight);
			// Preserve transparency
			imagesavealpha($png , true);
			$pngTransparency = imagecolorallocatealpha($png , 255, 255, 255, 127);
			imagefill($png , 0, 0, $pngTransparency);
			$fgcol = imagecolorallocate($png, $color[0], $color[1], $color[2]);
		} else {
			return false;
		}
		// print bars
		$x = 0;
		foreach ($bcd['bcode'] as $k => $v) {
			$bw = round(($v['w'] * $w), 3);
			$bh = round(($v['h'] * $h / $bcd['maxh']), 3);
			if ($v['t']) {
				$y = round(($v['p'] * $h / $bcd['maxh']), 3);
				// draw a vertical bar
				imagefilledrectangle($png, $x, $y, ($x + $bw - 1), ($y + $bh - 1), $fgcol);

			}
			$x += $bw;
		}

		if (!is_null($this->text)) {
			$xPosition = ((($bcd['maxw'] * $w)/2)-(($txt_width)/2));
			imagettftext($png, $this->fontsize, 0, $xPosition, $h + 2 + $txt_height, $fgcol, $font_file, $this->text);
		}

		if ($this->padding > 0) {
			error_log($this->padding);
			$pngnew = imagecreate(($bcd['maxw'] * $w)+(2 * $this->padding), $h + $requiredtxtheight + (2 * $this->padding));
			// Preserve transparency
			imagesavealpha($pngnew , true);
			$pngnewTransparency = imagecolorallocatealpha($pngnew , 255, 255, 255, 127);
			imagefill($pngnew , 0, 0, $pngnewTransparency);
			$fgcolnew = imagecolorallocate($pngnew, $color[0], $color[1], $color[2]);

			imagecopy($pngnew, $png, $this->padding, $this->padding, 0, 0, ($bcd['maxw'] * $w) + $this->padding, $h + $requiredtxtheight + $this->padding );
			$png = $pngnew;

		}

		if (!($rotate == 0)) {
			$png = imagerotate ( $png , $rotate ,  $pngTransparency);
		}




		if ($this->file === null) {
			ob_start();
			imagepng($png);
			$barcodeData = ob_get_clean();
		} else {
			preg_match("/\.png$/", $this->file, $output);
			if (count($output) == 0)
				throw new \Exception('Incorrect file extension format.');

			$filePath = explode(DIRECTORY_SEPARATOR, $this->file);
			if (count($filePath) == 1 ) {
				$savePath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.$this->file;
			} else {
				$savePath = $this->file;
			}

			if (!@imagepng($png, $savePath))
				throw new \Exception('Not found save path.');

			$barcodeData = file_get_contents($savePath);
		}

		imagedestroy($png);

		return $barcodeData;
	}

	public function getBarcodeJpgData() {
		$png = $this->getBarcodePngData();

		$image = imagecreatefromstring($png);
		$bg = imagecreatetruecolor(imagesx($image), imagesy($image));
		imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
		imagealphablending($bg, TRUE);
		imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
		imagedestroy($image);

		if ($this->file === null) {
			ob_start();
			imagejpeg($bg);
			$barcodeData = ob_get_clean();
		} else {
			preg_match("/\.jpg$/", $this->file, $output);
			if (count($output) == 0)
				throw new \Exception('Incorrect file extension format.');

			$filePath = explode(DIRECTORY_SEPARATOR, $this->file);
			if (count($filePath) == 1 ) {
				$savePath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.$this->file;
			} else {
				$savePath = $this->file;
			}

			if (!@imagepng($png, $savePath))
				throw new \Exception('Not found save path.');

			$barcodeData = file_get_contents($savePath);
		}

		imagedestroy($bg);

		return $barcodeData;
	}

	public function getBarcodeGifData() {
		$png = $this->getBarcodePngData();

		$image = imagecreatefromstring($png);
		$bg = imagecreatetruecolor(imagesx($image), imagesy($image));
		imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
		imagealphablending($bg, TRUE);
		imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
		imagedestroy($image);

		if ($this->file === null) {
			ob_start();
			imagegif($bg);
			$barcodeData = ob_get_clean();
		} else {
			preg_match("/\.gif$/", $this->file, $output);
			if (count($output) == 0)
				throw new \Exception('Incorrect file extension format.');

			$filePath = explode(DIRECTORY_SEPARATOR, $this->file);
			if (count($filePath) == 1 ) {
				$savePath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.$this->file;
			} else {
				$savePath = $this->file;
			}

			if (!@imagepng($png, $savePath))
				throw new \Exception('Not found save path.');

			$barcodeData = file_get_contents($savePath);
		}

		imagedestroy($bg);

		return $barcodeData;
	}

	public function getBarcodeSvgData()
	{
		$bcd = $this->barcode->getBarcodeArray();
		$rotate = $this->rotate;
		$color = $this->hexColor;

		if (!is_null($this->text)) {
			$requiredtxtheight = $this->fontsize + 4;
		} else {
			$requiredtxtheight = 0;
		}

		if ($rotate == 0 || $rotate%180 == 0) {
			$w = $this->scale;
			$h = $this->height;

			$repstr = array("\0" => '', '&' => '&amp;', '<' => '&lt;', '>' => '&gt;');
			$barcodeData = '<'.'?'.'xml version="1.0" standalone="no"'.'?'.'>'."\n";
			$barcodeData .= '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'."\n";
			$barcodeData .= '<svg width="'.round(($bcd['maxw'] * $w), 3).'" height="'.($h + $requiredtxtheight).'" version="1.1" xmlns="http://www.w3.org/2000/svg">'."\n";
			$barcodeData .= "\t".'<desc>'.strtr($bcd['code'], $repstr).'</desc>'."\n";
			$barcodeData .= "\t".'<g id="bars" fill="'.$color.'" stroke="none">'."\n";
			// print bars
			$x = 0;
			foreach ($bcd['bcode'] as $k => $v) {
				$bw = round(($v['w'] * $w), 3);
				$bh = round(($v['h'] * $h / $bcd['maxh']), 3);
				if ($v['t']) {
					$y = round(($v['p'] * $h / $bcd['maxh']), 3);
					// draw a vertical bar
					$barcodeData .= "\t\t".'<rect x="'.$x.'" y="'.$y.'" width="'.$bw.'" height="'.$bh.'" />'."\n";
				}
				$x += $bw;
			}
			$barcodeData .= "\t".'</g>'."\n";
			if (!is_null($this->text)) {
				$font = ($this->font === null) ?  'monospace' : $this->font . ", monospace";
				$barcodeData .= '<text x="'.round(($bcd['maxw'] * $w)/2, 3).'" y="'. ($h + 4) .'" text-anchor="middle" dominant-baseline="hanging" font-family="'. $font . '" font-size="' . $this->fontsize .'" fill="'.$color.'">' . $this->text . '</text>';
			}
			$barcodeData .= '</svg>'."\n";
		} else {
			$w = $this->height;
			$h = $this->scale;

			$repstr = array("\0" => '', '&' => '&amp;', '<' => '&lt;', '>' => '&gt;');
			$barcodeData = '<'.'?'.'xml version="1.0" standalone="no"'.'?'.'>'."\n";
			$barcodeData .= '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'."\n";
			$barcodeData .= '<svg width="'.($w + $requiredtxtheight ).'" height="'.(round(($bcd['maxw'] * $h), 3)).'" version="1.1" xmlns="http://www.w3.org/2000/svg">'."\n";
			$barcodeData .= "\t".'<desc>'.strtr($bcd['code'], $repstr).'</desc>'."\n";
			$barcodeData .= "\t".'<g id="bars" fill="'.$color.'" stroke="none">'."\n";
			// print bars
			$y = 0;
			foreach ($bcd['bcode'] as $k => $v) {
				$bw = round(($v['h'] * $w / $bcd['maxh']), 3);
				$bh = round(($v['w'] * $h), 3);
				if ($v['t']) {
					$x = round(($v['p'] * $h / $bcd['maxh']), 3);
					$x = $x + $requiredtxtheight;
					// draw a vertical bar
					$barcodeData .= "\t\t".'<rect x="'.$x.'" y="'.$y.'" width="'.$bw.'" height="'.$bh.'" />'."\n";
				}
				$y += $bh;
			}
			$barcodeData .= "\t".'</g>'."\n";
			if (!is_null($this->text)) {
				$font = ($this->font === null) ?  'monospace' : $this->font . ", monospace";
				$barcodeData .= '<text x="4" y="'.(round((($bcd['maxw'] * $h)/2), 3)).'"  transform="rotate('. $rotate .',4,'. (round((($bcd['maxw'] * $h)/2), 3)) .')" text-anchor="middle" alignment-baseline="hanging" font-family="'. $font . '" font-size="' . $this->fontsize .'" fill="'.$color.'">' . $this->text . '</text>';
			}

			$barcodeData .= '</svg>'."\n";
		}




		if ($this->file != null) {
			preg_match("/\.svg$/", $this->file, $output);
			if (count($output) == 0)
				throw new \Exception('Incorrect file extension format.');

			$filePath = explode(DIRECTORY_SEPARATOR, $this->file);
			if (count($filePath) == 1 ) {
				$savePath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.$this->file;
			} else {
				$savePath = $this->file;
			}

			if (!@file_put_contents($savePath, $barcodeData))
				throw new \Exception('Not found save path.');
		}

		return $barcodeData;
	}

	public function renderHTML()
	{
		$barcodeData = $this->getBarcodeHtmlData();

		header('Content-Type: text/html');
		header('Content-Length: '.strlen($barcodeData));
		header('Cache-Control: no-cache');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');

		echo $barcodeData;
	}

	public function renderPNG()
	{
		$barcodeData = $this->getBarcodePngData();

		header('Content-Type: image/png');
		header('Content-Length: '.strlen($barcodeData));
		header('Cache-Control: no-cache');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');

		echo $barcodeData;
	}

	public function renderJPG()
	{
		$barcodeData = $this->getBarcodeJpgData();

		header('Content-Type: image/jpeg');
		header('Content-Length: '.strlen($barcodeData));
		header('Cache-Control: no-cache');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');

		echo $barcodeData;
	}

	public function renderGIF()
	{
		$barcodeData = $this->getBarcodeGifData();

		header('Content-Type: image/gif');
		header('Content-Length: '.strlen($barcodeData));
		header('Cache-Control: no-cache');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');

		echo $barcodeData;
	}

	public function renderSVG()
	{
		$barcodeData = $this->getBarcodeSvgData();

		header('Content-Type: image/svg+xml');
		header('Content-Length: '.strlen($barcodeData));
		header('Cache-Control: no-cache');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');

		echo $barcodeData;
	}
}