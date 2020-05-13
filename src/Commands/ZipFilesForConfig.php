<?php

namespace Eric\ZipFilePhp\Commands;

use Illuminate\Console\Command;
use  ZipArchive;

class ZipFilesForConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zip:forConfig {name=app}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @retu
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $zip_name = $this->argument('name');
        $this->insertZip($zip_name);

    }

    public function insertZip($zip_name)
    {
        //需要打包的文件
        list($file_path_arr, $root_files) = $this->parse_zip_file_list(config('zip-file-php.need_config'));
        $file_list = [];
        foreach ($file_path_arr as $k => $v) {
            if (is_file($v)) {
                array_push($file_list, $v);
                continue;
            }
            $file_list = array_merge($file_list, $this->list_dir($v . "/"));
        }
        $exportPath = $zip_name;
        $zip        = new ZipArchive();
        //创建压缩包
        if (!$zip->open("$exportPath.zip", ZIPARCHIVE::CREATE)) {
            $this->info("创建[$exportPath.zip]失败");
            return;
        }
        //加入根目录的文件
        foreach ($root_files as $v) {
            if (file_exists($v)) {
                $zip->addFile($v, basename($v));
            }
        }
        //加入其他目录的文件
        $base_path       = base_path();
        $base_path_count = count(str_split($base_path)) + 1;
        foreach ($file_list as $k => $v) {
            $v          = substr($v, $base_path_count);
            $first_path = explode('/', $v);
            array_pop($first_path);
            $new_path = join("/", $first_path);
            $zip->addEmptyDir($new_path);
            $zip->addFile($base_path . "/" . $v, $v);
        }
        $zip->close();
    }

    /**
     * 获取需要压缩的文件列表
     * @param string $dir 需要压缩的文件夹
     * @return array
     * */
    function list_dir($dir)
    {
        $result = array();
        if (is_dir($dir)) {
            $file_dir = scandir($dir);
            foreach ($file_dir as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                } elseif (is_dir($dir . $file)) {
                    $result = array_merge($result, $this->list_dir($dir . $file . '/'));
                } else {

                    array_push($result, $dir . $file);
                }
            }
        }
        foreach ($result as &$v) {
            $v = str_replace('\\', "/", $v);
//            $v = str_replace("\\\\", "\\", $v);
        }
        return $result;
    }

    /**
     * 获取需要压缩的文件
     * 遵循ignore文件
     * @param array $fileArr 需要压缩文件列表
     * @return array
     * */
    function parse_zip_file_list($fileArr)
    {
        $dir        = base_path();
        $matches    = array();
        $root_files = [];

        foreach ($fileArr as $line) {


            $line = trim($line);


            if ($line === '') {
                continue;
            } # empty line


            if (substr($line, 0, 1) == '#') {
                continue;
            } # a comment

            if (substr($line, 0, 1) == '!') { # negated glob

                $line = substr($line, 1);

                $files = array_diff(glob("$dir/*"), glob("$dir/$line"));

            } else { # normal glob
                if (strstr($line, '/')) {
                    $files = glob("$dir$line");

                } else {
                    $root_files[] = $dir . "/" . $line;
                    $files        = [];
                }

            }
            $matches = array_merge($matches, $files);

        }
        foreach ($matches as &$v) {
            $v = str_replace('\\', "/", $v);
        }
        return [$matches, $root_files];


    }


}
