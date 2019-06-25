<?php
namespace SqlTable;

require_once __DIR__ . '/table.php';

class TblRequestDocument extends SqlTable {
    public static $table_name = 'tblRequestDocument';
    public $req_doc_id;
    public $req_detail_id;
    public $doc_id;
    function __construct($req_detail_id,  $doc_id){
        $this->req_detail_id = $req_detail_id;
        $this->doc_id = $doc_id;
    }
    public static function create_table() {
            $tname = self::$table_name;
            try {
                $sql = "CREATE table if not exists $tname(
                req_doc_id    INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
                req_detail_id INT( 11 ) NOT NULL,
                doc_id        INT( 11 ) NOT NULL
                );";
                self::exec($sql);
            } catch(PDOException $e) {
                // echo $e->getMessage();
            }
    }

    public static function new_req_doc($req_detail_id, $doc_id){
        $tname = self::$table_name;
        $sql = "
            INSERT into $tname(req_detail_id,doc_id)
            values(?,?)
        ;";
        $conn = self::get_connection();
        foreach ($doc_id as $key => $value) {
            $conn->prepare($sql)->execute([$req_detail_id, $value]);
        }
    }
}

TblRequestDocument::create_table();
