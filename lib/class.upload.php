<?

class Upload
{
	var $fsrc = array(); 
	var $dir;
	var $naming = "";
	var $thumbnail = array();	// array('경로',사이즈,타입);
	var $width = 0;

	function Upload(){
	}

	function exec($f,$d=array()){

		if (!is_dir($this->dir)){
			mkdir($this->dir,0707); chmod($this->dir,0707);
		}

		$d = upFileType2Array($d);
		$this->fsrc = upFileType2Array($this->fsrc);
		$f = array_map("upFileType2Array",$f);
		if (!$f[tmp_name]) $f[tmp_name] = array();
		foreach ($f[tmp_name] as $k=>$v){
			if ($d[$k]){
				if (is_file($this->dir.$this->fsrc[$k])) unlink($this->dir.$this->fsrc[$k]);
				$this->fsrc[$k] = "";
			}
			if (is_uploaded_file($v)){
				if ($this->fsrc[$k]) @unlink($this->dir.$this->fsrc[$k]);
				$this->fsrc[$k] = ($this->naming=="random") ? md5(uniqid('')) : $f[name][$k];
				move_uploaded_file($v,$this->dir.$this->fsrc[$k]);
				if ($this->thumbnail){
					if (!is_dir($this->thumbnail[0])) mkdir($this->thumbnail[0],0707);
					thumbnail($this->dir.$this->fsrc[$k],$this->thumbnail[0].$this->fsrc[$k],$this->thumbnail[1],$this->thumbnail[2]);
				}
				if ($this->width) thumbnail($this->dir.$this->fsrc[$k],$this->dir.$this->fsrc[$k],$this->width);
			}
		}
	}

	function del($src){
		if (is_file($this->dir.$src)) unlink($this->dir.$src);
	}

	function reset(){
		$this->fsrc = array();
	}
}

function upFileType2Array($v){ return (!is_array($v)) ? array($v) : $v; }

?>