<?php
class AdminController {
    private $userModel;
    private $newsModel;
    private $categoryModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->newsModel = new News();
        $this->categoryModel = new Category();
    }
    
    public function index() {
        $this->checkAdmin();
        $news = $this->newsModel->getAllNews();
        require 'views/admin/index.php';
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $user = $this->userModel->login($username, $password);
            
            if ($user) {
                $_SESSION['admin'] = $user;
                header('Location: /tlu/tlunews/admin');
                exit;
            }
        }
        require 'views/admin/login.php';
    }
    
    public function logout() {
        unset($_SESSION['admin']);
        header('Location: /tlu/tlunews');
        exit;
    }
    
    public function addNews() {
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Xử lý upload ảnh
            $target_dir = "uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
            
            // Thêm tin tức
            $this->newsModel->addNews(
                $_POST['title'],
                $_POST['content'],
                $target_file,
                $_POST['category_id']
            );
            
            header('Location: /tlu/tlunews/admin');
            exit;
        }
        
        $categories = $this->categoryModel->getAllCategories();
        require 'views/admin/news/add.php';
    }
    
    public function editNews($id) {
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $image = $_POST['current_image'];
            
            // Nếu có upload ảnh mới
            if ($_FILES['image']['name']) {
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
                $image = $target_file;
            }
            
            $this->newsModel->updateNews(
                $id,
                $_POST['title'],
                $_POST['content'],
                $image,
                $_POST['category_id']
            );
            
            header('Location: /tlu/tlunews/admin');
            exit;
        }
        
        $news = $this->newsModel->getNewsById($id);
        $categories = $this->categoryModel->getAllCategories();
        require 'views/admin/news/edit.php';
    }
    
    public function deleteNews($id) {
        $this->checkAdmin();
        
        // Xóa ảnh cũ
        $news = $this->newsModel->getNewsById($id);
        if (file_exists($news['image'])) {
            unlink($news['image']);
        }
        
        // Xóa tin tức
        $this->newsModel->deleteNews($id);
        
        header('Location: /tlu/tlunews/admin');
        exit;
    }
    
    private function checkAdmin() {
        if (!isset($_SESSION['admin'])) {
            header('Location: /tlu/tlunews/admin/login');
            exit;
        }
    }
}
