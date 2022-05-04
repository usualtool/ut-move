<?php
namespace usualtool\Move;
use usualtool\Ftp\Ftp;
/**
 * 同步网站压缩包
 */
class File{
    public function __construct(){
        include 'Config.php';
        $this->backup=UTF_ROOT."/web.zip";
        $this->ftp=new Ftp($sync["ftp_serv"],$sync["ftp_port"],$sync["ftp_user"],$sync["ftp_pass"],$sync["ftp_pasv"]);
    }
    public function Sync(){
        if(file_exists($this->backup)):
            $this->ftp->Upload($this->backup,"web.zip");
        else:
            $this->Pack(UTF_ROOT,$this->backup);
            $this->Sync();
        endif;
    }
    public function Folder($folder, &$zipFile, $exclusiveLength){ 
        $handle = opendir($folder); 
        while (false !== $f = readdir($handle)){ 
            if($f != '.' && $f != '..'){ 
                $filePath = "$folder/$f"; 
                $localPath = substr($filePath, $exclusiveLength); 
                if (is_file($filePath)){ 
                    $zipFile->addFile($filePath, $localPath); 
                }elseif (is_dir($filePath)){ 
                    $zipFile->addEmptyDir($localPath); 
                    $this->Folder($filePath, $zipFile, $exclusiveLength); 
                } 
            } 
        } 
        closedir($handle); 
    }
    public function Pack($sourcePath, $outZipPath){ 
        $pathInfo = pathInfo($sourcePath); 
        $parentPath = $pathInfo['dirname']; 
        $dirName = $pathInfo['basename']; 
        $sourcePath=$parentPath.'/'.$dirName;
        $z = new \ZipArchive(); 
        $z->open($outZipPath, \ZIPARCHIVE::CREATE);
        $z->addEmptyDir($dirName);
        $this->Folder($sourcePath, $z, strlen("$parentPath/")); 
        $z->close(); 
    }
}
