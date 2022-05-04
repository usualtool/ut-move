<?php
namespace usualtool\Move;
use usualtool\Move\Db;
use usualtool\Move\File;
/**
 * 一键搬家
 */
class Sync{
    public function __construct(){
        $sync_db=new Db();
        $sync_file=new File();
        $sync_db->Sync();
        $sync_file->Sync();
    }
}
