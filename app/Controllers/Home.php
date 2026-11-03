<?php
namespace App\Controllers;

use App\Models\Home_model;

class Home extends BaseController
{
    protected $all_versions = "2";
    protected $Home_model;

    public function __construct()
    {
        $this->Home_model = new Home_model();
    }

    public function index(): string
{
    helper(['url']);

    $data = [
        'title' => 'मुख्यमंत्री अंत्योदय परिवार उत्थान योजना',
        'Get_department_name' => $this->Home_model->Get_department_name(),
        'counter' => []
    ];

    return view('pages/home', $data);
}

    public function allScheme_list()
	{
		$departmentId                         = $this->request->getPost('department_id');
		$adminpagedata                        = $this->Home_model->schemeName($departmentId);	
		echo json_encode($adminpagedata);

	}

	public function allScheme_list_byId()
	{
		$scheme_id                            =$this->request->getPost('scheme_id');
		$department_id                        = $this->request->getPost('department_id');
		$adminpagedata                        = $this->Home_model->allScheme_list_byId($department_id,$scheme_id);	
		echo json_encode($adminpagedata);
	}

    public function SingleSchemelistbyId()
    {
        $id = $this->request->getPost('id');

        if (empty($id) || !is_numeric($id)) {
            return $this->response->setJSON([]);
        }

        $result = $this->Home_model->SingleSchemelistbyId($id);

        return $this->response->setJSON($result);
    }

public function downloadDocuments(): string
{
            helper(['url']);

        $data = [
            'title' => 'समर्थित दस्तावेज',
            'counter' => []
        ];

        return view('pages/home_documents', $data);
}



}