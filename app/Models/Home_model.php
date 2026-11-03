<?php
namespace App\Models;

use CodeIgniter\Model;

class Home_model extends Model
{
    public function Get_department_name()
    {
        $qr = "SELECT * FROM departments_list WHERE status = '1'";

        $query = $this->db->query($qr);

        if ($query->getNumRows() > 0) {
            return $query->getResultArray();
        } else {
            return false;
        }
    }

    public function schemeName($departmentId)
		{
			$qr = "SELECT id,scheme_name FROM scheme_list WHERE department_id='$departmentId' and status='1'";		
			$query = $this->db->query($qr);		
			
			if($query->getNumRows() > 0 ) {            
				return $query->getResultArray();
			} else {             
				return FALSE;
			}
		}

		public function allScheme_list_byId($department_id,$scheme_id)
		{
			$qr = "SELECT * FROM schemes_master WHERE new_depat_id='$department_id' AND new_schemeid='$scheme_id' AND status='1' GROUP BY new_schemeid";		
			$query = $this->db->query($qr);		
			
			if($query->getNumRows() > 0 ) {            
				return $query->getResultArray();
			} else {             
				return FALSE;
			}
		}

        public function SingleSchemelistbyId($id)
{
    $sql = "SELECT
                departments_list.name,
                scheme_list.scheme_name,
                scheme_list.applying_scheme_point,
                scheme_list.benefits_from_the_scheme,
                scheme_list.financial_benefit_scheme,
                scheme_list.scheme_category,
                scheme_list.scheme_type,
                scheme_list.if_scheme_isloan

            FROM scheme_list

            INNER JOIN departments_list
                ON departments_list.id = scheme_list.department_id

            WHERE scheme_list.id = ?
            AND scheme_list.status = '1'";

    $query = $this->db->query($sql, [$id]);

    return $query->getResultArray();
}
        public function visitor_counter(){
			
			$this->db->select('counter');
			// $this->db->where('role_id',$roleid);
			$this->db->from('visitor_counter');
			$qry2 = $this->db->get();
			
			if($qry2->result_id->num_rows > 0) {             
				return $qry2->result_array();
			} else {             
				return FALSE;
			}
		}

}
