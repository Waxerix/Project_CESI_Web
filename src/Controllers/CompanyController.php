<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\CompanyModel;
use App\Models\AccessModel;

class CompanyController extends Controller {

    private $companyModel;

    /**
     * Constructeur : Initialise Twig, PDO et le modèle Company
     */
    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
        // 모델 초기화
        $this->companyModel = new CompanyModel($this->pdo);
        $this->Model = new AccessModel($this->pdo);
    }

    /**
     * 기업 관리 메인 메뉴 (두 가지 옵션 선택 화면)
     * URL: /admin/entreprises
     */
    public function index() {
        $user = $this->Model->currentUser();
        echo $this->twig->render('admin-entreprises-menu.html.twig',['user'=> $user]);
    }

    /**
     * 기업 생성 폼 표시
     * URL: /admin/entreprises/create
     */
    public function create() {
        $user = $this->Model->currentUser();
        echo $this->twig->render('creation-entreprise.html.twig',['user'=> $user]);
    }

    /**
     * 기업 리스트 표시
     * URL: /admin/entreprises/list
     */
    public function list() {
        $user = $this->Model->currentUser();
        $companies = $this->companyModel->getAll();
        echo $this->twig->render('list-entreprises.html.twig', [
            'companies' => $companies,
            'user' => $user
        ]);
    }

    /**
     * 기업 저장 로직
     * URL: /admin/entreprises/store
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name'        => $_POST['name'] ?? null,
                'email'       => $_POST['email'] ?? null,
                'phone'       => $_POST['phone'] ?? null,
                'description' => $_POST['description'] ?? null
            ];

            if ($data['name'] && $data['email']) {
                $success = $this->companyModel->create($data);

                if ($success) {
                    // 저장 성공 시 리스트 페이지로 이동
                    header('Location: /admin/entreprises/list?success=created');
                    exit();
                } else {
                    echo "Erreur lors de l'insertion dans la base de données.";
                }
            } else {
                echo "Veuillez remplir tous les champs obligatoires.";
            }
        }
    }

    /**
     * 기업 삭제 로직
     * URL: /admin/entreprises/delete/:id
     */
    public function delete($id) {
        $success = $this->companyModel->delete($id);

        if ($success) {
            header('Location: /admin/entreprises/list?success=deleted');
            exit();
        } else {
            echo "Erreur lors de la suppression.";
        }
    }
}