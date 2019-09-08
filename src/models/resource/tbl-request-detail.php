<?php

namespace SqlTable;

require_once __DIR__ . '/table.php';
require_once __DIR__ . '/tbl-request-document.php';
require_once __DIR__ . '/tbl-admin.php';

class TblRequestDetails extends SqlTable
{
    public static $table_name = 'tblRequestDetails';
    public $req_id;
    public $stud_acc_id;
    public $date_of_request;
    public $hash_key;
    public $registrar_acc_id;
    public $treasury_acc_id;
    function __construct($stud_acc_id, $date_of_request, $hash_key = '')
    {
        $this->stud_acc_id = $stud_acc_id;
        $this->date_of_request = $date_of_request;
        $this->hash_key = $hash_key;
        if (strlen($hash_key) == 0) {
            $this->hash_key = uniqid();
        }
    }
    public static function create_table()
    {
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
        } catch (PDOException $e) {
            // echo $e->getMessage();
        }
    }

    public static function new_request($stud_acc_id, $doc_ids)
    {
        $tname = self::$table_name;
        $hash_key = uniqid();
        // $date_of_request = date('m/d/Y h:i:s');
        $sql = "
            INSERT into $tname(stud_acc_id,date_of_request, hash_key)
            values(?, NOW(),?)
        ;";
        $conn = self::get_connection();
        $conn->prepare($sql)->execute([$stud_acc_id, $hash_key]);
        $curr_req = self::get_request_details($stud_acc_id, $hash_key)[0];
        $req_id = $curr_req['req_id'];
        TblRequestDocument::new_req_doc($req_id, $doc_ids);
        return $curr_req;
    }

    public static function get_request_details($stud_acc_id, $hash_key)
    {
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


    public static function student_req_listing($student_id)
    {
        $tname = self::$table_name;
        $sql = "
        select $tname.req_id, hash_key, $tname.date_of_request,
        tblRequestDetails.registrar_acc_id,
        tblRequestDetails.treasury_acc_id,
        GROUP_CONCAT(DISTINCT(
            select title from tblDocument
            where tblRequestDocument.doc_id = tblDocument.doc_id
        ) Separator ', ') as titles,
        GROUP_CONCAT(DISTINCT(
            select description from tblDocument
            where tblRequestDocument.doc_id = tblDocument.doc_id
        ) Separator ', ') as description,
        Sum(DISTINCT(
            select price from tblDocument
            where tblRequestDocument.doc_id = tblDocument.doc_id
        )) as total
        from $tname 
        inner join tblRequestDocument on 
        $tname.req_id = tblRequestDocument.req_detail_id
        inner join tblDocument on
        tblRequestDocument.doc_id = tblDocument.doc_id
        inner join tblStudent on
        $tname.stud_acc_id = tblStudent.acc_id
        where sid = ?
        GROUP BY
        $tname.req_id
        ;";
        $conn = self::get_connection();
        $ex = $conn->prepare($sql);
        $ex->execute([$student_id]);
        return $ex->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function get_req_contents($req_id)
    {
        $tname = self::$table_name;
        $sql = "
        select $tname.req_id, hash_key, $tname.date_of_request,
        tblRequestDetails.registrar_acc_id,
        tblRequestDetails.treasury_acc_id,
        GROUP_CONCAT(DISTINCT(
            select title from tblDocument
            where tblRequestDocument.doc_id = tblDocument.doc_id
        ) Separator ', ') as titles,
        GROUP_CONCAT(DISTINCT(
            select description from tblDocument
            where tblRequestDocument.doc_id = tblDocument.doc_id
        ) Separator ', ') as description,
        Sum(DISTINCT(
            select price from tblDocument
            where tblRequestDocument.doc_id = tblDocument.doc_id
        )) as total
        from $tname 
        inner join tblRequestDocument on 
        $tname.req_id = tblRequestDocument.req_detail_id
        inner join tblDocument on
        tblRequestDocument.doc_id = tblDocument.doc_id
        inner join tblStudent on
        $tname.stud_acc_id = tblStudent.acc_id
        where $tname.req_id = ?
        GROUP BY
        $tname.req_id
        ;";
        $conn = self::get_connection();
        $ex = $conn->prepare($sql);
        $ex->execute([$req_id]);
        return $ex->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function delete_request($req_id)
    {
        try {
            $toDelete = self::get_req_contents($req_id)[0];
            // var_dump($toDelete);
            if (
                isset($toDelete['registrar_acc_id'])
                || isset($toDelete['treasury_acc_id'])
            ) {
                return ['message' => 'Cannot delete Request'];
            }
            $conn = self::get_connection();
            $sql = "
                delete from tblRequestDetails where req_id = ?;
                delete from tblRequestDocument where req_detail_id = ?;
            ";
            $ex = $conn->prepare($sql);
            $ex->execute([$req_id, $req_id]);
            return ['message' => 'Deleted.', 'hash_key' => $toDelete['hash_key']];
        } catch (Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public static function get_all_request()
    {
        $tname = self::$table_name;
        $sql = "
        select $tname.req_id, hash_key, $tname.date_of_request,
        tblRequestDetails.registrar_acc_id,
        tblRequestDetails.treasury_acc_id,
        tblStudent.sid,
        GROUP_CONCAT(DISTINCT(
            select title from tblDocument
            where tblRequestDocument.doc_id = tblDocument.doc_id
        ) Separator ', ') as titles,
        GROUP_CONCAT(DISTINCT(
            select description from tblDocument
            where tblRequestDocument.doc_id = tblDocument.doc_id
        ) Separator ', ') as description,
        Sum(DISTINCT(
            select price from tblDocument
            where tblRequestDocument.doc_id = tblDocument.doc_id
        )) as total
        from $tname 
        inner join tblRequestDocument on 
        $tname.req_id = tblRequestDocument.req_detail_id
        inner join tblDocument on
        tblRequestDocument.doc_id = tblDocument.doc_id
        inner join tblStudent on
        $tname.stud_acc_id = tblStudent.acc_id
        GROUP BY
        $tname.req_id
        ;";
        $conn = self::get_connection();
        $ex = $conn->prepare($sql);
        $ex->execute();
        return $ex->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function approve($request_id, $employee_name, $employee_id)
    {
        $employee = TblAdmin::get_admin($employee_name, $employee_id);

        if ($employee == null) {
            throw new \Exception('Error in getting the data of approver');
        }
        $eid = $employee[0]['eid'];
        $office = $employee[0]['office'];
        $columnToUpdate = '';
        // registrar_acc_id
        // treasury_acc_id
        if ($office == 'registrar') {
            $columnToUpdate = 'registrar_acc_id';
        } elseif ($office == 'treasury') {
            $columnToUpdate = 'treasury_acc_id';
        } else {
            throw new \Exception('Unknown employee office');
        }
        $tname = self::$table_name;
        $sql = "
            UPDATE $tname
            SET $columnToUpdate=?
            WHERE req_id=?
        ;";
        $conn = self::get_connection();
        $conn->prepare($sql)->execute([$eid, $request_id,]);
    }


    public static function un_approve($request_id, $employee_name, $employee_id)
    {
        $employee = TblAdmin::get_admin($employee_name, $employee_id);

        if ($employee == null) {
            throw new \Exception('Error in getting the data of approver');
        }
        $eid = $employee[0]['eid'];
        $office = $employee[0]['office'];
        $columnToUpdate = '';
        if ($office == 'registrar') {
            $columnToUpdate = 'registrar_acc_id';
        } elseif ($office == 'treasury') {
            $columnToUpdate = 'treasury_acc_id';
        } else {
            throw new \Exception('Unknown employee office');
        }
        $tname = self::$table_name;
        $sql = "
            UPDATE $tname
            SET $columnToUpdate=NULL
            WHERE req_id=?
        ;";
        $conn = self::get_connection();
        $conn->prepare($sql)->execute([$request_id]);
    }
}

TblRequestDetails::create_table();
// TblRequestDetails::new_request('1',[1,2]);
