<?php
namespace usualtool\Move;
use library\UsualToolMysql;
/**
 * 同步Mysql数据
 */
class Db{
    public function __construct(){
        include 'Config.php';
        $this->backup=UTF_ROOT."/mysql.sql";
        $this->dbname=$sync['db_name'];
        $this->the_db=UsualToolMysql\UTMysql::GetMysql();
        $this->ftp_db=new \mysqli($sync["db_serv"].":".$sync["db_port"],$sync["db_user"],$sync["db_pass"],$this->dbname);
        $this->ftp_db->set_charset("utf8");
    }
    public function Sync(){
        if(file_exists($this->backup)):
            $sql = file_get_contents($this->backup);
            $this->ftp_db->multi_query($sql);
        else:
            $this->Backup();
            $this->Sync();
        endif;
    }
    public function Backup(){
        global$config;
        $query = "show tables from ".$config["MYSQL_DB"];
        $tables = mysqli_query($this->the_db,$query);
        $tabList = array();
        while($row = mysqli_fetch_row($tables)){
            $tabList[] = $row[0];
        }
        $wj=fopen($this->backup,'w+');
        fclose($wj);
        file_put_contents($this->backup,$info,FILE_APPEND);
        foreach($tabList as $val){
            $sql = "show create table ".$val;
            $res = mysqli_query($this->the_db,$sql);
            $row = mysqli_fetch_array($res);
            $info = "DROP TABLE IF EXISTS `".$val."`;\r\n";
            $sqlStr = $info.$row[1].";\r\n\r\n";
            file_put_contents($this->backup,$sqlStr,FILE_APPEND);
            mysqli_free_result($res);
        } 
        foreach($tabList as $val){
            $sql = "select * from ".$val;
            $res = mysqli_query($this->the_db,$sql);
            if(mysqli_num_rows($res)<1) continue;
            while($row = mysqli_fetch_row($res)){
                $sqlStr = "INSERT INTO `".$val."` VALUES (";
                foreach($row as $v){
                    $sqlStr .= "'".$v."', ";
                }
                $sqlStr = substr($sqlStr,0,strlen($sqlStr)-2);
                $sqlStr .= ");\r\n";
                file_put_contents($this->backup,$sqlStr,FILE_APPEND);
            }
            mysqli_free_result($res);
            file_put_contents($this->backup,"\r\n",FILE_APPEND);
        }
    }
}
