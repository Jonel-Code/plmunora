<?php
namespace SqlTable;

require_once __DIR__ . '/table.php';

class TblDocument extends SqlTable {
    public static $table_name = 'tblDocument';
    public $doc_id;
    public $title;
    public $description;
    public $price; 
    function __construct($title, $description, $price){
        $this->title = $title;
        $this->description = $description;
        $this->price = $price;
    }
    public static function create_table() {
            $tname = self::$table_name;
            try {      
                $sql = "CREATE table if not exists $tname(
                doc_id          INT( 11 )       AUTO_INCREMENT PRIMARY KEY,
                title           VARCHAR( 50 )   NOT NULL,
                description     VARCHAR( 50 )   NOT NULL,
                price           INT( 11 )       NOT NULL
                );";
                self::exec($sql);
            } catch(PDOException $e) {
                // echo $e->getMessage();
            }
    }

    public static function new_docs($title, $desciption, $price){
        $tname = self::$table_name;
        $sql = "
            INSERT into $tname(title,description,price)
            values(?,?,?)
        ;";
        $conn = self::get_connection();
        $conn->prepare($sql)->execute([$title, $desciption, $price]);
    }


    public static function get_all_docs(){
        $tname = self::$table_name;
        $sql = "
            SELECT * from $tname
        ;";
        $conn = self::get_connection();
        $tname = self::$table_name;
        $ex = $conn->prepare($sql);
        $ex->execute();
        return $ex->fetchAll(\PDO::FETCH_ASSOC);
    }

}

TblDocument::create_table();
// var_dump(json_encode(TblDocument::get_all_docs()));
