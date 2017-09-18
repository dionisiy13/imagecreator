<?php 
header("Content-Type: image/gif");

class GenerateImage {
	
	private $_params;
	
	public function __construct($params)
	{
		$this->_params = $params;
	}
	
	private function generateThings()
	{
		$count = $this->_params['LabelTableSize']['Width'];
		$count *= $this->_params['LabelTableSize']['Height'];
		
		$leftMargin = $this->_params['LeftMargin'];
		$topMargin = $this->_params['TopMargin'];
		
		$blockSizeH = $this->_params['LabelSize']['Height'];
		$blockSizeW = $this->_params['LabelSize']['Width'];
		
		$spaceW = $this->_params['HorizSpacing'];
		$spaceH = $this->_params['VertSpacing'];
		
		$x1 = $leftMargin;
		$y1 = $topMargin;
		$x2 = $leftMargin;
		$y2 = $blockSizeH + $topMargin;
		
		$flagW = false;
		$flagH = false;
		
		for ($i = 0; $i < $count; $i++) {
			
			if ($flagW) {
				$x1 = $x1 + $spaceW;
				$x2 = $x2 + $spaceW;
				$flagW = true;
			}
			
			$x2 += $blockSizeW;
			// new row
			if ($i % $this->_params['LabelTableSize']['Width'] == 0 && $i != 0) {
				$x1 = $this->_params['LeftMargin'];
				$x2 = $blockSizeW + $leftMargin;
				$y1 = $y2 ;
				$y2 += $blockSizeH;
				if ($flagH) {
					$y1 = $y1 + $spaceH;
					$y2 = $y2 + $spaceH;
					$flagH = true;
				}
			} 
			
			yield [
				'x1'=>$x1, 
				'x2' => $x2,
				'y1' => $y1,
				'y2' => $y2
			];
			$x1 = $x2;
			$flagW = $flagH = true;
		}
	}
	
	public function generate()
	{
		$width = $this->_params['PaperSize']['Width']; 
		$height = $this->_params['PaperSize']['Height']; 

		$img = imagecreatetruecolor($width, $height);

		$transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
		imagefill($img, 0, 0, $transparent);
		 
		imagesavealpha($img, true);
		
		$red = imagecolorallocate($img, 0, 0, 0);

		foreach ($this->generateThings() as $item){
			imagerectangle($img, $item['x1'], $item['y1'], $item['x2'], $item['y2'], $red);
		}		
	
		$this->showImage($img);
	}
	
	private function showImage($img)
	{
		imagepng($img); 
		imagedestroy($img);
	}
	
}

$obj = new GenerateImage(array
(
		"TopMargin" => 25.4,
		"BottomMargin" => 25.4,
		"LeftMargin" => 31.75,
		"RightMargin" => 31.75,

		"HorizSpacing" => 0,
		"VertSpacing" => 25.4,

		"LabelSize" => array
		(
		  "Width"=>152.4,
		  "Height"=>101.6,
		),

		"PaperSize" => array
		(
		  "Width"=>215.9,
		  "Height"=>279.4,
		),

		"LabelTableSize" => array
		(
		  "Width"=>1,
		  "Height"=>2,
		),
));
$obj->generate();