<?php

namespace App\Controllers;

use App\Models\HomeModel;

class HomeController extends BaseController
{
    protected $all_versions = "2";
    protected $HomeModel;

    public function __construct()
    {
        $this->HomeModel = new HomeModel();
    }

    public function index(): string
    {
        helper(['url']);

        $data = [
            'title' => 'मुख्यमंत्री अंत्योदय परिवार उत्थान योजना',
            'Get_department_name' => $this->HomeModel->Get_department_name(),
            'counter' => []
        ];

        return view('pages/home/index', $data);
    }

    public function getAllSchemes()
    {
        $departmentId                         = $this->request->getPost('department_id');
        $adminpagedata                        = $this->HomeModel->schemeName($departmentId);
        echo json_encode($adminpagedata);
    }

    public function getSchemesById()
    {
        $scheme_id                            = $this->request->getPost('scheme_id');
        $department_id                        = $this->request->getPost('department_id');
        $adminpagedata                        = $this->HomeModel->allScheme_list_byId($department_id, $scheme_id);
        echo json_encode($adminpagedata);
    }

    public function getSchemeDetailsById()
    {
        $id = $this->request->getPost('id');

        if (empty($id) || !is_numeric($id)) {
            return $this->response->setJSON([]);
        }

        $result = $this->HomeModel->SingleSchemelistbyId($id);

        return $this->response->setJSON($result);
    }

    public function downloadDocuments(): string
    {
        helper(['url']);

        $data = [
            'title' => 'समर्थित दस्तावेज',
            'counter' => []
        ];

        return view('pages/home/home_documents', $data);
    }
}
