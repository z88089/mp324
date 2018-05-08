<?php

// echo "<pre>";

class Mp324 {

	/**
	 * [$ffmpeg_path]
	 * @var [string]
	 */
	protected $ffmpeg_path;

	/**
	 * [$img]
	 * @var [string]
	 */
	protected $img;

	/**
	 * [$command]
	 * @var [string]
	 */
	protected $command;

	/**
	 * [$files]
	 * @var [array]
	 */
	public $files;

	/**
	 * [$new_dir]
	 * @var [string]
	 */
	public $new_dir;

	public function __construct() {
		$this->ffmpeg_path = __DIR__."/mp324/ffmpeg/bin/";
		$this->img = __DIR__."/mp324/img/img.jpg";
		$this->new_dir = 'success';
		$this->files = $this->scan();
	}

	public function run()
	{
		foreach ($this->files as $key => $value) {
			$this->do($value);
		}
	}

	public function setCommand($mp3, $mp4)
	{
		$this->command = $this->ffmpeg_path . "ffmpeg -i ". $mp3 ." -f image2 -i ". $this->img ." -acodec aac -strict -2 -vcodec libx264 -ar 22050 -ab 128k -ac 2  -y ". $mp4;
	}

	public function do($file)
	{
		try {
			if (! is_dir($file['path'])) throw new Exception("file path ". $file['path'] ." is not dir\n");

			$new_path = "./" . $this->new_dir . mb_substr($file['path'], 1);

			if (! is_dir($new_path)) {
			
				mkdir($new_path, '0777', true);
			}
			$new_name = mb_substr($file['name'], 0, -1) . '4';
			$this->setCommand($file['path']."/".$file['name'], $new_path."/".$new_name);

			$output = shell_exec($this->command);

			if ($output) {
				echo "转码成功\n";
			} else {
				echo $output."\n";
			}

		} catch(Exception $e) {
			echo $e->getMessages() . "\n";
		}
	}

	public function scan($path='.')
	{

		$list = scandir($path);

		$files = [];

		foreach($list as $key => $val) {

			#filter
			if ($val == "." || $val == ".." || $val == "mp324" || $val == "mp324.php") {
				unset($list[$key]);
				continue;
			}
			
			if (mb_substr($val, 0, 1) == '.') {
				unset($list[$key]);
				continue;
			}

			if (is_dir($path."/".$val)) {
				$arr = [];
				$arr = $this->scan($path."/".$val);
				$files = array_merge($files, $arr);
			} else {
				if (mb_substr($val, -3) !== 'mp3') {
					unset($list[$key]);
					continue;
				}
				$files[] = ['path'=>$path, 'name'=>$val];
			}

		}

		return $files;
	}
}


$mp324 = new Mp324();
$mp324->run();
