<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

    public $JSON_DATA;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('DbModel');
        header("Access-Control-Allow-Origin: http://localhost:8081");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Content-Type: application/json; charset=UTF-8");

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit;
        }

        $this->JSON_DATA = (array)json_decode(file_get_contents("php://input"), true);
    }

    public function index(){
        echo "api index page";
    }

    public function get_all_data()
    {
        echo $this->DbModel->get_all();
        
    }

    public function save()
    {
        var_dump($this->JSON_DATA);
        echo $this->Course_Model->save(
            $this->JSON_DATA
        );
        
    }

    public function update()
    {
        $id = $this->JSON_DATA["id"];
        unset($this->JSON_DATA["id"]);
        $this->Course_Model->update(
            $this->JSON_DATA,
        array(
            "id"            => $id));
        
    }

    public function delete()
    {
        echo $this->Course_Model->delete($this->JSON_DATA);
    }

    public function get_products(){
        $this->DbModel->clear_products_cache();
        $products = $this->DbModel->get_products();
        echo json_encode([
            'status' => 'success',
            'data' => $products
        ]);
    }

    public function login(){
        $user = $this->DbModel->login($this->JSON_DATA);
        if($user){
            $this->session->set_userdata('user', [
               'user_id' => $user->user_id,
               'name' => $user->name,
               'mail' => $user->mail,
               'password' => $user->password,
               'address' => $user->address ?? "-",
               'role' => $user->role_fk
            ]);
            switch ($user->role_fk){

                case 1:
                    $session['role'] = "Admin";
                    break;
                case 2:
                    $session['role'] = "Editor";
                default:
                    $session['role'] = "User";
                    break;
                }

            echo json_encode(['status' => 'success', 'message' => 'Giriş Başarılı', 'user' => [
                'name' => $user->name,
                'mail' => $user->mail,
                'role' => $session['role'],
                'address' => $user->address,
            ]]);
        }
        else{
            echo json_encode(['status' => 'error', 'message' => 'E-Mail veya Şifre Hatalı.']);
        }
    }

    public function signin(){
        $this->load->library('email');
        
        $_POST = $this->JSON_DATA;
        $this->form_validation->set_rules('name', 'Ad Soyad','required|min_length[3]');
        $this->form_validation->set_rules('mail', 'E-Posta','required|valid_email');
        $this->form_validation->set_rules('password', 'Şifre','required|min_length[6]');
        $this->form_validation->set_rules('address', 'Adres');
        
        if($this->form_validation->run() == false){
            echo json_encode([
                'status' => 'error',
                'message' => strip_tags(validation_errors())
            ]);
            return;
        }

        $token = bin2hex(random_bytes(16));
            $user = [
                'name'     => $this->input->post('name', TRUE),
                'mail'     => $this->input->post('mail', TRUE),
                'password' => $this->input->post('password', TRUE),
                'address'  => $this->input->post('address', TRUE),
                'token' => $token,
                'is_verified' => 0,
            ];
            
            $this->DbModel->signin($user);

            $verificationLink = "http://localhost:8080/api/verifyUser?token=".$token;
            $this->email->from('lol.oynayabilirmiyimltfn@gmail.com', 'Kara Üzüm Mobilya');
            $this->email->to('aertemur1@gmail.com');
            $this->email->subject('E-Posta Testi');
            $this->email->message('
                <h3>E-Posta Doğrulama</h3><br>
                    <p>Hesabınızı doğrulamak için aşağıdaki bağlantıya tıklayın:</p><br>
                        <a href="' . $verificationLink . '">' . $verificationLink . '</a>
            ');
            
            if($this->email->send()){
            echo 'Doğrulama E-Postası başarıyla gönderildi.';
        }
        else{
            echo 'E-Posta gönderilemedi.';
            echo $this->email->print_debugger(['headers']);
        }
    }

    public function verifyUser(){
        $token = $this->input->get('token');

        if(!$token){
            echo "Token bulunmadı.";
            return;
        }

        $user = $this->DbModel->getUserByToken($token);

        if(!$user){
            echo "Geçersiz veya süresi dolmuş doğrulama bağlantısı.";
            return;
        }
        
        $this->DbModel->verifyUser($token);
        echo "<h2>E-Postanız başarıyla doğrulandı!</h2>";
    }

    public function checkLogin(){
        if($this->session->userdata('user')){
            $user = $this->session->userdata('user');
            echo json_encode([
                'status' => 'success',
                'user' => [
                    'user_id' => $user['user_id'],
                    'name' => $user['name'],
                    'mail' => $user['mail'],
                    'address' => $user['address'],
                    'password' => $user['password'],
                    'role' => $user['role'],
                ]
                ]);
        }
        else{
            echo json_encode(['status'=>'error','message'=>'Cookiede kayıtlı bir kullanıcı bulunamadı.']);
        }
    }

    public function logout(){
        if($this->session->userdata('user')){
            $this->session->unset_userdata('user');
            $this->session->sess_destroy();
            echo json_encode(['status' => 'success', 'message' => 'Oturum Kapatıldı.']);
        }
        else{
            echo json_encode(['status' => 'error', 'message' => 'Oturum bulunamadı']);

        }
    }

    public function update_profile(){
        $data = $this->JSON_DATA;
        $user = $this->session->userdata('user');
        var_dump($data);
        var_dump($user);
        if(!$user){
            echo json_encode(['status' => 'error', 'message' => 'Aktif bir oturum bulunamadı.']);
            return;
        }

        $updateData = [
            'name' => $data['name'] ?? $user['name'],
            'mail' => $data['mail'] ?? $user['mail'],
            'address' => $data['adress'] ?? $user['address'],
            'password' => $data['password'] ?? $user['password']
        ];

        $result = $this->DbModel->update_user($user['user_id'], $updateData);

        if($result){
            $user = array_merge($user,$updateData);
            $this->session->set_userdata('user',$user);
            echo json_encode(['status' => 'success', 'message' => 'Profil başarıyla güncellendi']);
        }
        else{
            echo json_encode(['status' => 'error', 'message' => 'Profil güncellenirken bir hatayla karşılaşıldı']);
        }
    }

    public function detail($id){
        $product = $this->DbModel->get_product_detail($id);
        if($product)
        {
            echo json_encode([
                'status' => 'success',
                'data' => $product
            ]);
        }
        else{
            echo json_encode([
                'status' => 'error',
                'message' => 'ürün bulunamadı'
            ]);
        }
    }

    public function orders(){
        
    if($this->session->userdata('user'))
    {
            $user = $this->session->userdata('user');
            $user_id = $user['user_id'];
            $orders = $this->DbModel->get_orders($user_id);

            echo json_encode([
                'status' => 'success',
                'order' => $orders
            ]);
    }
    else{
        echo json_encode([
                'status' => 'error',
                'message' => 'User boş'
            ]);
    }
}
    public function get_all_orders(){
        $orders = $this->DbModel->get_all_orders();
        echo json_encode([
            'status' => 'success',
            'orders' => $orders
        ]);
    }

    public function addProduct(){
        $data = $this->JSON_DATA;
        if($data)
        {
            $this->DbModel->addNewProduct($data);
            echo json_encode([
                'status' => 'success',
                'message' => 'ürün başarıyla eklendi:',
                'data' => $data
            ]);
        }
        else
        {
            echo json_encode([
                'status' => 'error',
                'message' => 'Ürün yüklenirken bir hata oluştu.'
            ]);
        }
    }

    public function get_categories(){
        $categories = $this->DbModel->getCategories();
        echo json_encode([
            'categories' => $categories
        ]);
    }

    public function delete_product(){
        $data = $this->JSON_DATA;
        if($data){
            $this->DbModel->deleteProduct($data);
            echo json_encode([
                'status' => 'success',
                'message' => 'Ürün silme işlemi başarılı.',
                'data' => $data
            ]);
        }
        else{
            echo json_encode([
                'status' => 'error',
                'message' => 'Ürün silme işleminde bir hatayla karşılaşıldı'
            ]);
        }

    }

    public function update_product($product_id){
        $data = $this->JSON_DATA;
        if($data)
        {
            $this->DbModel->updateProduct($product_id, $data);
            echo "başarılı";
            var_dump($data);
        }
        else{
            echo "başarısız";
        }
    }
    public function send_mail(){
        $this->load->library('email');
        
        $this->email->from('lol.oynayabilirmiyimltfn@gmail.com', 'Kara Üzüm Mobilya');
        $this->email->to('aertemur1@gmail.com');
        $this->email->subject('E-Posta Testi');
        $this->email->message('<h3>Codeigniter mail entegrasyonu testi</h3><br><p>Bu bir test mesajıdır.</p>');
        if($this->email->send()){
            echo 'E-Posta başarıyla gönderildi.';
        }
        else{
            echo 'E-Posta gönderilemedi.';
            echo $this->email->print_debugger(['headers']);
        }
        
    }
}
