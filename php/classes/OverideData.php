<?php
class OverideData{
    private $_pdo;
    function __construct(){
        try {
            $this->_pdo = new PDO('mysql:host='.config::get('mysql/host').';dbname='.config::get('mysql/db'),config::get('mysql/username'),config::get('mysql/password'));
        }catch (PDOException $e){
            $e->getMessage();
        }
    }
   public function unique($table,$field,$value){
        if($this->get($table,$field,$value)){
            return true;
        }else{
            return false;
        }
    }

    public function getNo($table){
        $query = $this->_pdo->query("SELECT * FROM $table");
        $num = $query->rowCount();
        return $num;
    }

    public function getCountReport($table,$field,$value,$where2,$id2,$where3,$id3){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field >= '$value' AND $where2 <= '$id2' AND $where3 = '$id3'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCountReport1($table, $field, $value, $where2, $id2, $where3, $id3, $where4, $id4)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field >= '$value' AND $where2 <= '$id2' AND $where3 = '$id3' AND $where4 = '$id4'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCountReport2($table, $field, $value, $where2, $id2, $where3,$id3, $where4,$id4, $where5, $id5)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field >= '$value' AND $where2 <= '$id2' AND $where3 = '$id3' AND $where4 = '$id4' AND $where5 = '$id5'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount($table,$field,$value){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount1($table,$field,$value,$where2,$id2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field <= '$value' AND $where2 = '$id2'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount2($table,$field,$value,$where2,$id2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field <= '$value' AND $where2 = '$id2'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount3($table,$where2,$id2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE notify_amount >= quantity AND $where2 = '$id2'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount4($table,$where2,$id2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE notify_amount >= quantity AND $where2 = '$id2'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount5($table,$where2,$id2,$where3,$id3){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE notify_amount >= quantity AND $where2 = '$id2' AND $where3 = '$id3'");
        $num = $query->rowCount();
        return $num;
    }
    
    public function countData($table,$field,$value,$field1,$value1){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1'");
        $num = $query->rowCount();
        return $num;
    }
    public function getData($table){
        $query = $this->_pdo->query("SELECT * FROM $table");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getReport($table1,$table2,$id1,$id2){
        $query = $this->_pdo->query("SELECT '$table2'.'$id2','$table1'.'$id1' FROM $table2 INNER JOIN '$table1' ON '$table2'.'$id2'='$table1'.'$id1'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNews($table,$where,$id,$where2,$id2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsASC($table, $where, $id, $where2, $id2, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsASC0($table, $where, $id, $where2, $id2, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 > '$id2' ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsASC0Count($table, $where, $id, $where2, $id2, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 > '$id2' ORDER BY $name ASC");
        $num = $query->rowCount();
        return $num;
    }

    public function getNewsASC1($table, $where, $id, $where2, $id2, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 <= '$id2' ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsASC1Count($table, $where, $id, $where2, $id2, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 <= '$id2' ORDER BY $name ASC");
        $num = $query->rowCount();
        return $num;
    }


    public function getNewsASC0G($table, $where, $id, $where2, $id2,$where3, $id3, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 > '$id2' AND $where3 = '$id3'  ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsASC0CountG($table, $where, $id, $where2, $id2,$where3, $id3, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 > '$id2' AND $where3 = '$id3' ORDER BY $name ASC");
        $num = $query->rowCount();
        return $num;
    }

    public function getNewsASC1G($table, $where, $id, $where2, $id2, $where3, $id3, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 <= '$id2' AND $where3 = '$id3' ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsASC1CountG($table, $where, $id, $where2, $id2, $where3, $id3, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 <= '$id2' AND $where3 = '$id3' ORDER BY $name ASC");
        $num = $query->rowCount();
        return $num;
    }
    
    public function getNews1($table,$where,$id,$where2,$id2,$where3,$id3){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 = '$id3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function get3($table,$where,$id,$where2,$id2,$where3,$id3){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 = '$id3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getFull($table,$where,$id,$where2,$id2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where > '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get4($table,$where,$id,$where2,$id2,$where3,$id3){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where > '$id' AND $where2 = '$id2' AND $where3 = '$id3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get4b($table,$where,$id,$where2,$id2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where > '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get5($table,$where,$id,$where2,$id2,$where3,$id3){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where <= '$id' AND $where2 = '$id2' AND $where3 = '$id3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get6($table,$where,$id,$where2,$id2,$where3,$id3){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where < '$id' AND $where2 = '$id2' AND $where3 = '$id3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get7($table,$where,$id,$where2,$id2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where <= '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getSumD($table,$variable){
        $query = $this->_pdo->query("SELECT SUM($variable) FROM $table");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getSumD1($table,$variable, $field, $value){
        $query = $this->_pdo->query("SELECT SUM($variable) FROM $table WHERE $field = '$value' ");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getSumD2($table,$variable, $field, $value,$field1, $value1){
        $query = $this->_pdo->query("SELECT SUM($variable) FROM $table WHERE $field = '$value' AND $field1 = '$value1'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getSumD3($table,$variable, $field, $value,$field1, $value1,$field2, $value2){
        $query = $this->_pdo->query("SELECT SUM($variable) FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $field2 = '$value2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get($table,$where,$id){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }    

    public function get2($table,$value,$where,$id){
        $query = $this->_pdo->query("SELECT $value FROM $table WHERE $where = '$id'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataAsc($table, $where, $id, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function delete($table,$field,$value){
        return $this->_pdo->query("DELETE FROM $table WHERE $field = $value");
    }
    public function lastRow($table,$value){
        $query = $this->_pdo->query("SELECT * FROM $table ORDER BY $value DESC LIMIT 1");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function lastRow2($table,$field,$value,$orderBy){
        $query = $this->_pdo->query("SELECT * FROM $table where $field='$value' ORDER BY $orderBy DESC LIMIT 1");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function selectData($table,$field,$value,$field1,$value1,$value2,$field2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $value2 = '$field2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function selectData1($table,$field,$value,$field1,$value1){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getWithLimit2($table,$field,$value,$field1,$value1,$value2,$field2,$page,$numRec){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $value2 = '$field2' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getWithLimit1($table,$where,$id,$where2,$id2,$page,$numRec){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getWithLimit($table,$where,$id,$page,$numRec){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimitDescendingOrder($table,$where,$id,$page,$numRec,$value){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' limit $page,$numRec ORDER BY $value DESC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit3($table,$value,$where,$id,$page,$numRec){
        $query = $this->_pdo->query("SELECT DISTINCT $value FROM $table WHERE $where = '$id' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimitAscendOrder($table,$where,$id,$page,$numRec,$value){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' limit $page,$numRec ORDER BY $value DESC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataWithLimit($table,$page,$numRec){
        $query = $this->_pdo->query("SELECT * FROM $table limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function searchBtnDate2($table,$var,$value,$var1,$value1){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function searchBtnDate3($table,$var,$value,$var1,$value1,$var2,$value2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1' AND $var2 = '$value2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function searchBtnDate4($table, $var, $value, $var1, $value1, $var2,$value2, $var3, $value3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1' AND $var2 = '$value2' AND $var3 = '$value3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function searchBtnDate5($table, $var, $value, $var1, $value1, $var2, $value2, $var3, $value3, $var4, $value4)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1' AND $var2 = '$value2' AND $var3 = '$value3' OR $var4 = '$value4'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function searchBtnDateSufficient($table,$var,$value,$var1,$value1,$var2,$value2,$var3,$value3,$var4,$value4){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1' AND $var2 < '$value2' AND $var3 = '$value3' AND $var4 = '$value4'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function searchBtnDateLow($table,$var,$value,$var1,$value1,$var2,$value2,$var3,$value3,$var4,$value4){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1' AND $var2 >= '$value2' AND $var3 = '$value3' AND $var4 = '$value4'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function searchBtnDateOutStock($table,$var,$value,$var1,$value1,$var2,$value2,$var3,$value3,$var4,$value4){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1' AND $var2 <= $value2 AND $var3 = '$value3' AND $var4 = '$value4'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function searchBtnDateExpired($table,$var,$value,$var1,$value1,$var2,$value2,$var3,$value3,$var4,$value4){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1' AND $var2 <= $value2 AND $var3 = '$value3' AND $var4 = '$value4'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function searchBtnDateNotChecked($table,$var,$value,$var1,$value1,$var2,$value2,$var3,$value3,$var4,$value4){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1' AND $var2 <= $value2 AND $var3 = '$value3' AND $var4 = '$value4'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getLessThanDate($table,$where,$id,$where2,$id2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where <= '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimitLessThanDate($table,$where,$id,$where2,$id2,$page,$numRec){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where <= '$id' AND $where2 = '$id2' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getLessThanDate30($table,$where,$id,$where2,$id2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where <= '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimitLessThan30($table,$where,$id,$where2,$id2,$page,$numRec){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where <= '$id' AND $where2 = '$id2' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function getNewsASC030($table, $where, $id, $where2, $id2, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 > '$id2' ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsASC0Count30($table, $where, $id, $where2, $id2, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 > '$id2' ORDER BY $name ASC");
        $num = $query->rowCount();
        return $num;
    }

    public function getNewsASC1G30($table, $where, $id, $where2, $id2, $where3, $id3, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 <= '$id2' AND $where3 = '$id3' ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsASC1CountG30($table, $where, $id, $where2, $id2, $where3, $id3, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 <= '$id2' AND $where3 = '$id3' ORDER BY $name ASC");
        $num = $query->rowCount();
        return $num;
    }

}