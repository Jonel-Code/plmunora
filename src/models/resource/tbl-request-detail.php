<?php
namespace SqlTable;
require_once __DIR__ . '/table.php';
require_once __DIR__ . '/tbl-request-document.php';

class TblRequestDetails extends SqlTable {
    public static $table_name = 'tblRequestDetails';
    public $req_id;
    public $stud_acc_id;
    public $date_of_request;
    public $hash_key; 
    public $registrar_acc_id; 
    public $treasury_acc_id;     
    function __construct($stud_acc_id, $date_of_request, $hash_key=''){
        $this->stud_acc_id = $stud_acc_id;
        $this->date_of_request = $date_of_request;
        $this->hash_key = $hash_key;
        if(strlen($hash_key) == 0){
            $this->hash_key = uniqid();
        }        
    }
    public static function create_table() {
            $tname = self::$table_name;
            try {   
                $sql = "CREATE table if not exists $tname(
                req_id           INT( 11 )     AUTO_INCREMENT PRIMARY KEY,
                stud_acc_id      VARCHAR( 50 ) NOT NULL,
                date_of_request  DATE          NOT NULL,
                hash_key         VARCHAR( 23 ) NOT NULL,
                registrar_acc_id INT( 11 )     ,
                treasury_acc_id  INT( 11 )                     
                );";
                self::exec($sql);
            } catch(PDOException $e) {
                // echo $e->getMessage();
            }
    }

    public static function new_request($stud_acc_id, $doc_ids){
        $tname = self::$table_name;
        $hash_key = uniqid();
        // $date_of_request = date('m/d/Y h:i:s');
        $sql = "
            INSERT into $tname(stud_acc_id,date_of_request, hash_key)
            values(?, NOW(),?)
        ;";
        $conn = self::get_connection();
        $conn->prepare($sql)->execute([$stud_acc_id , $hash_key]);
        $curr_req = self::get_request_details($stud_acc_id, $hash_key)[0];
        $req_id = $curr_req['req_id'];
        TblRequestDocument::new_req_doc($req_id, $doc_ids);
        return $curr_req;
    }

    public static function get_request_details($stud_acc_id, $hash_key){
        $tname = self::$table_name;
        $sql = "
            SELECT * from $tname
            where stud_acc_id=? and hash_key=?
        ;";
        $conn = self::get_connection();
        $tname = self::$table_name;
        $ex = $conn->prepare($sql);
        $ex->execute([$stud_acc_id, $hash_key]);
        return $ex->fetchAll(\PDO::FETCH_ASSOC);
    }
}

TblRequestDetails::create_table();
// TblRequestDetails::new_request('1',[1,2]);
