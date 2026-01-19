<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

    public $JSON_DATA;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('DbModel');
		$this->load->library('RateLimit');
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
            header_remove("Content-Type");
        header("Content-Type: text/html; charset=UTF-8");
        echo '
            <div style="font-family:Arial, sans-serif; max-width:900px; margin:40px auto; padding:20px; border:1px solid #ccc; border-radius:10px; background:#fafafa;">
                <h1 style="text-align:center; color:#333; margin-bottom:30px;">API Endpoint Documentation</h1>

                <h2 style="color:#444;">Auth & User Endpoints</h2>
                <ul style="line-height:1.8; color:#222;">
                    <li><strong>POST /api/login</strong> — Kullanıcı giriş yapar.</li>
                    <li><strong>POST /api/signin</strong> — Yeni kullanıcı kaydı oluşturur ve doğrulama maili gönderir.</li>
                    <li><strong>GET /api/verifyUser?token=TOKEN</strong> — E-posta doğrulama işlemi.</li>
                    <li><strong>GET /api/checkLogin</strong> — Aktif session varsa kullanıcıyı döner.</li>
                    <li><strong>GET /api/logout</strong> — Kullanıcı oturumunu sonlandırır.</li>
                    <li><strong>POST /api/update_profile</strong> — Profil bilgilerini günceller.</li>
                </ul>

                <h2 style="color:#444;">Product Endpoints</h2>
                <ul style="line-height:1.8; color:#222;">
                    <li><strong>GET /api/get_products</strong> — Tüm ürünleri listeler.</li>
                    <li><strong>GET /api/detail/{id}</strong> — Belirtilen ürünün detaylarını döner.</li>
                    <li><strong>POST /api/addProduct</strong> — Yeni ürün ekler.</li>
                    <li><strong>POST /api/update_product/{id}</strong> — Ürün bilgilerini günceller.</li>
                    <li><strong>POST /api/delete_product</strong> — Ürünü siler.</li>
                </ul>

                <h2 style="color:#444;">Product Images Endpoints</h2>
                <ul style="line-height:1.8; color:#222;">
                    <li><strong>POST /api/AddProductImages/{id}</strong> — Ürüne yeni resim ekler.</li>
                    <li><strong>POST /api/UpdateProductImages</strong> — Ürün resimlerini günceller.</li>
                    <li><strong>POST /api/DeleteProductImage/{image_id}</strong> — Belirtilen resmi siler.</li>
                </ul>

                <h2 style="color:#444;">Category Endpoints</h2>
                <ul style="line-height:1.8; color:#222;">
                    <li><strong>GET /api/get_categories</strong> — Tüm kategorileri listeler.</li>
                </ul>

                <h2 style="color:#444;">Order Endpoints</h2>
                <ul style="line-height:1.8; color:#222;">
                    <li><strong>POST /api/createOrder</strong> — Sipariş oluşturur (guest + registered).</li>
                    <li><strong>GET /api/orders</strong> — Kullanıcının kendi siparişlerini listeler.</li>
                    <li><strong>GET /api/get_all_orders</strong> — Tüm siparişleri listeler (Admin).</li>
                </ul>

                <h2 style="color:#444;">Misc Endpoints</h2>
                <ul style="line-height:1.8; color:#222;">
                    <li><strong>GET /api/get_all_data</strong> — Demo amaçlı tüm ürün/kategori datası.</li>
                    <li><strong>GET /api/index</strong> — API test endpointi.</li>
                    <li><strong>POST /api/send_mail</strong> — Test e-postası gönderir.</li>
                    <li><strong>POST /api/guestAccount</strong> — Misafir kullanıcı hesabı oluşturur.</li>
                </ul>

            </div>
            ';

    }

    // AUTH SECTION
    public function login(){
		if(!$this->ratelimit->check(5,60))
		{
			$this->output->set_status_header(429);
			echo json_encode([
				'status' => 'error',
				'message' => 'Çok Fazla İstek Gönderdiniz. Daha Sonra Tekrar Deneyiniz.',
			]);
			return;
		}
        $user = $this->DbModel->login($this->JSON_DATA);
        if($user){
            $this->session->set_userdata('user', [
               'user_id' => $user->user_id,
               'name' => $user->name,
               'mail' => $user->mail,
               'password' => $user->password,
               'address' => $user->address ?? "-",
               'role' => $user->role_fk,
			   'tel' => $user->tel
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
				'tel' => $user->tel
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
					'tel' => $user['tel'],
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

    // PRODUCT FUNCTIONS

    public function get_products(){
        $this->DbModel->clear_products_cache();
        $products = $this->DbModel->get_products();
        echo json_encode([
            'status' => 'success',
            'data' => $products
        ]);
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

    public function update_product($product_id){
        $data = $this->JSON_DATA;
        unset($data["newImage"]);
        unset($data["images"]);
        if($data){

            $this->DbModel->updateProduct($product_id, $data);
			echo json_encode([
				'status' 	=> 'success',
				'message' 	=> 'Ürün Güncelleme Başarılı',
				'data' 		=> $data
			]);
		}
        else{
            echo json_encode([
            'status' 	=> 'error',
			'message' 	=> 'Ürün Güncelleme Başarısız'
    		]);
        }
    }

    public function delete_product(){
        $data = $this->JSON_DATA;
        if($data){
            $this->DbModel->deleteProduct($data);
            echo json_encode([
                'status' 	=> 'success',
                'message' 	=> 'Ürün silme işlemi başarılı.',
                'data' 		=> $data
            ]);
        }
        else{
            echo json_encode([
                'status' 	=> 'error',
                'message' 	=> 'Ürün silme işleminde bir hatayla karşılaşıldı'
            ]);
        }

    }

    public function AddProductImages($product_id){
        $data = $this->JSON_DATA["newImage"];
        if(!empty($data['image_url'])){

            return $this->DbModel->AddProductImages($data,$product_id);
			echo json_encode([
				'status'	=> 'success',
				'message'	=> 'Resim Başarıyla Eklendi',
				'data'		=> $data
			]);
        }
        else{
            echo json_encode([  
				"status" 	=> "error",
                "message" 	=> "URL Kısmı Boş Bırakılamaz."
                ]);
        }
    }

    public function UpdateProductImages(){
        $data = $this->JSON_DATA;
		if($data){
			$this->DbModel->UpdateProductImages($data);
			echo json_encode([
				'status' 	=> 'success',
				'message' 	=> 'Resim Güncelleme Başarılı.',
				'data' 		=> $data
			]);
		}
		else{
			echo json_encode([
				'status' => 'error',
				'message' => 'URL Kısmı Boş Bırakılamaz.'
			]);
		}
    }

    public function DeleteProductImage($image_id){
		if($image_id){
			return $this->DbModel->DeleteProductImage($image_id);
			echo json_encode([
				'status'	=> 'success',
				'message'	=> 'Resim Başarıyla Kaldırıldı.',
				'data'		=> $image_id
			]);
		}
		else{
			echo json_encode([
				'status'	=> 'error',
				'message'	=> 'ID bilgisi alınamadı.',
				'data'		=> $image_id
			]);
		}
    }

    public function get_categories(){
        $categories = $this->DbModel->getCategories();
		if($categories){

			echo json_encode([
				'status' 	=> 'success',
				'data' 		=> $categories
			]);
		}
		else{
			echo json_encode([
				'status'	=> 'error',
				'message'	=> 'Kategoriler Yüklenirken Bir Hata Oluştu.'
			]);
		}
    }

    // ORDER FUNCTIONS

    public function createOrder(){
        $customerData = $this->JSON_DATA['customer'];
        $itemsData = $this->JSON_DATA['items'];
        $totalPriceData = $this->JSON_DATA['total'];
        var_dump($customerData);
		if($itemsData && $totalPriceData && $customerData){

			if($this->session->userdata('user')){
				$customerData['user_id'] = $this->session->userdata('user')['user_id'];
				$data = $this->DbModel->createOrder($customerData,$itemsData,$totalPriceData);
				echo json_encode([
					'status'	=> 'success',
					'message'	=> 'Sipariş Başarıyla Oluşturuldu.',
					'data'		=> $data
				]);
			}
			else{
				$customerData['user_id'] = 7;
				$data = $this->DbModel->createOrder($customerData,$itemsData,$totalPriceData);
				echo json_encode([
					'status' 	=> 'success',
					'message'	=> 'Sipariş Başarıyla Oluşturuldu.',
					'data'		=> $data
				]);
			}
		}
		else{
			echo json_encode([
				'status' 	=> 'error',
				'message'	=> 'Bilgiler Hatalı Girildi.',
			]);
		}
        var_dump($itemsData);

    }

    public function orders(){
        
        if($this->session->userdata('user'))
        {
            $user = $this->session->userdata('user');
            $user_id = $user['user_id'];
            $orders = $this->DbModel->get_orders($user_id);

            echo json_encode([
                'status' 	=> 'success',
				'message'	=> 'Siparişler Başarıyla Listelendi.',
                'data' 		=> $orders
            ]);
        }
        else{
            echo json_encode([
                'status' 	=> 'error',
                'message' 	=> 'Cookie\'de kayıtlı kullanıcı bulunmadı.'
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

    // MISC FUNCTIONS

    public function guestAccount($customerData){
        $this->DbModel->createGuestAccount($customerData);
    }

    public function get_all_data(){
        $data = $this->DbModel->get_all();
		if($data){
			echo json_encode([
				'status' 	=> 'success',
				'message'	=> 'Tüm Ürünler Başarıyla Listelendi.',
				'data'		=> $data
			]);
		}
		else{
			echo json_encode([
				'status'	=> 'error',
				'message'	=> 'Ürünler Listelenirken Bir Sorunla Karşılaşıldı.'
			]);
		}
    }

    // TEST FUNCTIONS
    public function save(){
        var_dump($this->JSON_DATA);
        echo $this->Course_Model->save(
            $this->JSON_DATA
        );
        
    }

    public function update(){
        $id = $this->JSON_DATA["id"];
        unset($this->JSON_DATA["id"]);
        $this->Course_Model->update(
            $this->JSON_DATA,
        array(
            "id"            => $id));
        
    }

    public function delete(){
        echo $this->Course_Model->delete($this->JSON_DATA);
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

	public function handleClearRateLimit(){
		$rows = $this->ratelimit->clear(5);
		echo json_encode([
			'status' 			=> 'success',
			'message' 			=> 'RateLimit Kayıtları Başarıyla Silindi.',
			'affected_rows' 	=> $rows
		]);
	}
}
