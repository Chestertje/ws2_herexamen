<?php
namespace MathiasDeRoover\Repository;

class admin extends \Knp\Repository {
    
    public function getTableName() {
		return 'vastgoed';
    }
    public function getItemsByCompany($id) {
            return $this->db->fetchAll("SELECT vastgoed.*,Vastgoedtypes.*, status.* from vastgoed inner join Vastgoedtypes on vastgoed.Vastgoedtype_id=Vastgoedtypes.Vastgoedtype_id inner join status on status.Status_id=vastgoed.Status_id  WHERE Makelaar_id=?",array($id));
    }
    public function getStructureType() {
            return $this->db->fetchAll("SELECT * FROM vastgoedtypes");
    }
    public function getProvinces() {
            return $this->db->fetchAll("SELECT * FROM provincies");
    }
    public function getStatuses() {
            return $this->db->fetchAll("SELECT * FROM status");
    }
    public function getBuildingType() {
            return $this->db->fetchAll("SELECT * FROM bebouwing_types");
    }
    public function insertVastgoed(array $data) {
            $this->db->insert('vastgoed', $data);
    }
    public function getVastgoed($id,$makelaar) {
            return $this->db->fetchAssoc("SELECT * FROM vastgoed WHERE Vastgoed_id=? && Makelaar_id=?",array($id,$makelaar));
    }
    public function updateVastgoed($id,$data){
        $array = [
                "Vastgoed_id"=>$id
                ];
        return $this->db->update('vastgoed' , $data, $array);
    }
    public function updateCompany($id,$data){
        $array = [
                "Makelaar_id"=>$id
                ];
        return $this->db->update('makelaars' , $data, $array);
    }
    public function deleteVastgoed($id) {
        $array = [
            "Vastgoed_id"=>$id
            ];
        return $this->db->delete('vastgoed', $array);
    }
    public function getLastInsert($id) {
        return $this->db->fetchAssoc("SELECT Vastgoed_id FROM vastgoed WHERE Makelaar_id=? ORDER BY Vastgoed_id desc",array($id));
    }
    public function findCompanyByID($id) {
        return $this->db->fetchAssoc("SELECT * FROM makelaars WHERE Makelaar_id=?",array($id));
    }
    public function findCompanyByName($name){
        return $this->db->fetchAssoc("SELECT Makelaar_id FROM makelaars WHERE Bedrijf_naam=?",array($name));
    }
    public function findContactEmail($email){
        return $this->db->fetchAssoc("SELECT Makelaar_id FROM makelaars WHERE Contact_email=?",array($email));  
    }
    public function filter($id, array $where, $limit=null ){
            $whereclause='';
            foreach ($where as $key => $value) {
                if($value != null){
                    
                    //$value = $app->escape($value);
                    
                   $whereclause .= " AND $key='".$value . "'";
                }
            }
            
            return $this->db->fetchAll("SELECT vastgoed.*,Vastgoedtypes.*, status.* from vastgoed inner join Vastgoedtypes on vastgoed.Vastgoedtype_id=Vastgoedtypes.Vastgoedtype_id inner join status on status.Status_id=vastgoed.Status_id  WHERE Makelaar_id=?".$whereclause .  $limit,array($id));
        }
    public function count($id, array $where=[]){
        $whereclause='';
        foreach ($where as $key => $value) {
            if($value != null){
                   $whereclause .= " AND $key='".$value . "' ";
            }

        }
        return $this->db->fetchAssoc('SELECT COUNT(*) as count from vastgoed Where Makelaar_id=?' . $whereclause , array($id));
    }
}
?>
