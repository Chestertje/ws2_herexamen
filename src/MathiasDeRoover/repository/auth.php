<?php

namespace MathiasDeRoover\Repository;

class auth extends \Knp\Repository {

	public function getTableName() {
		return 'auth';
	}

	public function checkCompanyName($name) {
           return $this->db->fetchAssoc('SELECT Makelaar_id from makelaars where Bedrijf_naam=?',array($name));
        }
        
        public function getUser($email) {
           return $this->db->fetchAssoc('SELECT Makelaar_id, Voornaam from makelaars where Contact_email=?',array($email));
        }
        
        public function getPassword($email) {
           return $this->db->fetchAssoc('SELECT Paswoord from makelaars where Contact_email=?',array($email));
        }
        
        public function addCompany($data) {
            return $this->db->insert('makelaars', $data);
        }
}