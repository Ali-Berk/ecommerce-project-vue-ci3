<?php
class DbModel extends CI_Model {
    public $tableOrders;
    public $tableOrder_Items;
    public $tableUsers;
    public $tableRoles;
    public $tableProducts_Images;
    public $tableProducts;
    public $tableCategories;
	public $tableAddresses;
	public $tableCart;
	public $tableCartItems;
    
    protected $cache_file_products = 'products_cache.php';
    
    public function __construct() {
        parent::__construct();
        $this->tableOrders = 'orders';
        $this->tableOrder_Items = 'order_items';
        $this->tableUsers = 'users';
        $this->tableRoles = 'roles';
        $this->tableProducts_Images = 'product_images';
        $this->tableProducts = 'products';
        $this->tableCategories = 'categories';
		$this->tableAddresses = 'addresses';
		$this->tableCart = 'cart';
		$this->tableCartItems = 'cart_items';
    }
	
    public function get_all(){
		$raw = $this->db->select('products.*, categories.category_name as category, product_images.image_url, product_images.alt_text')
	    ->from($this->tableProducts)
    	->join($this->tableCategories, 'categories.category_id = products.category_fk', 'left')
    	->join($this->tableProducts_Images, 'products.product_id = product_images.product_fk', 'left')
		->order_by('products.product_id ASC, product_images.image_id ASC')
    	->get()
    	->result_array();

		$products = [];
		foreach ($raw as $row) {
    		$pid = $row['product_id'];

    		if (!isset($products[$pid])) {
        		$products[$pid] = $row;
        		$products[$pid]['images'] = [];
        		unset($products[$pid]['image_url'], $products[$pid]['alt_text']);
		    }

    		$products[$pid]['images'][] = [
        	'image_url' => $row['image_url'],
        	'alt_text' => $row['alt_text']
    		];
		}

		$products = array_values($products);
		return $products;
	}

    public function login($data = array()){
    	$this->db->where('mail', $data['mail']);
    	$query = $this->db->get($this->tableUsers); 
    	if($query->num_rows() == 1){
        	$user = $query->row(); 
	        if($data['password'] == $user->password){
				$this->db->where('user_fk',$user->user_id);
				$addresses = $this->db->get($this->tableAddresses)->result();

    	        return [$user,$addresses]; 
        	}
    	}
    	return false;
    }

    public function signin($user = array()){
        $this->db->insert($this->tableUsers, $user);
    }

	public function check_user($user_id){
		$user = $this->db->where('user_id',$user_id)->get($this->tableUsers)->row_array();
		$this->db->where('user_fk',$user_id);
		$addresses = $this->db->get($this->tableAddresses)->result_array();
		$cart = $this->db->select("products.product_id, products.title, products.price, products.thumbnail, cart_items.qty")
		->from($this->tableCartItems)
		->join($this->tableProducts, 'cart_items.product_fk = products.product_id', 'left')
		->join($this->tableCart, 'cart.id = cart_items.cart_fk')
		->where('cart.user_fk', $user_id)
		->get()->result_array();
		return [$user, $addresses, $cart];

	}

    public function update_user($user_id, $data = array()){
        return $this->db->where('user_id', $user_id)->update('users', $data);
    }

	public function add_address($user_id, $address = array()){
		$data = $address;
		$data['user_fk'] = $user_id;
		$this->db->insert($this->tableAddresses,$data);
		return $this->db->error();
	}

	public function update_address($data = array()){
		$this->db->where('id',$data['id'])->update($this->tableAddresses,$data);
		return $this->db->error();
	}

	public function delete_address($id){
		$this->db->where('id',$id)->delete($this->tableAddresses);
		return $this->db->error();
	}

	public function set_initial_address($data = array()){
		$full_address = $data['address'].'-'.$data['district'].'/'.$data['city'];
		var_dump($full_address);
		$this->db->where('user_id',$data['user_fk'])->update($this->tableUsers,['address' => $full_address]);
		$this->db->where('default',1)->update($this->tableAddresses, ['default' => 0]);
		$this->db->where('id',$data['id'])->update($this->tableAddresses, ['default' => 1]);
	}

    public function get_products(){
        $cache_file = APPPATH.'cache/products_cache.php';


        if(file_exists($cache_file) && (time() - filemtime($cache_file) < 3600)){ // 1 saatlik cache süresi
            $cached = read_file($cache_file);
            if($cached !== false){
                return json_decode($cached, true);
            }
        }
		
		$raw = $this->db->select('products.*,
		product_images.*,
		categories.category_name')
		->from($this->tableProducts)
		->join($this->tableProducts_Images,'product_images.product_fk = products.product_id', 'left')
		->join($this->tableCategories, 'categories.category_id = products.category_fk', 'left')
		->order_by('products.product_id ASC')
		->get()->result_array();
		
		$products = [];
		foreach($raw as &$row){
			$pid = $row['product_id'];
			
			if(!isset($products[$pid])){
				$products[$pid] = $row;
				$products[$pid]['images'] = [];
				unset($products[$pid]['image_id'],$products[$pid]['image_url'],$products[$pid]['product_fk'],$products[$pid]['alt_text']);
			}
			
			$products[$pid]['images'][] = [
				'image_id' 	=> $row['image_id'],
				'image_url'	=> $row['image_url'],
				'alt_text'	=> $row['alt_text']
			];
		}
		write_file($cache_file, json_encode($products));
		return $products;
	}
	
    public function clear_products_cache(){
		$cache_path = APPPATH.'cache/'.$this->cache_file_products;
        if(file_exists($cache_path)){
            opcache_reset();
            unlink($cache_path);
        }
    }

    public function get_product_detail($id){
        $cache_path = APPPATH . 'cache/product_detail' . $id . '.php';

        if(file_exists($cache_path) && (time() - filemtime($cache_path)) < 3){
            $cached = read_file($cache_path);
            if($cached !== false)
            {
                return json_decode($cached,true);
            }
        }

        $this->db->where('product_id', $id);
        $product = $this->db->get($this->tableProducts)->row_array();
        
        if(!$product)
        {
            return false;
        }

        $this->db->where('product_fk',$id);
        $images = $this->db->get($this->tableProducts_Images)->result_array();
        $category = $this->db->get($this->tableCategories)->row_array();
        $product['category'] = $category ? $category['category_name'] : null;
        $product['images'] = $images;
        
        write_file($cache_path, json_encode($product));

        return $product;
    }
	
	public function add_to_cart($userId, $rawItemData = array()){
		$query = $this->db->get_where($this->tableCart, ['user_fk' => $userId]);
		$existing_cart = $query->row();
		if(!$existing_cart){
			$this->db->insert($this->tableCart, ['user_fk' => $userId]);
			$cart_id = $this->db->insert_id();
		}
		else{
			$cart_id = $existing_cart->id;
		}
		$existing_item = $this->db->get_where($this->tableCartItems,
		 ['product_fk' => $rawItemData['product_id'],
		  'cart_fk' => $cart_id,])->row();
		if($existing_item){
			$insertData = [
				'product_fk' 	=> $rawItemData['product_id'],
				'qty'			=> $rawItemData['qty'] ?? 1,
				'cart_fk'		=> $cart_id
				];
			$this->db->where('cart_fk',$cart_id)->where('product_fk', $rawItemData['product_id'])->update($this->tableCartItems, $insertData);
		}
		else{
			$insertData = [
				'product_fk' 	=> $rawItemData['product_id'],
				'qty'			=> $rawItemData['qty'] ?? 1,
				'cart_fk'		=> $cart_id
			];
			$this->db->insert($this->tableCartItems, $insertData);
		}
	}

	public function remove_from_cart($user_id, $product_id){
        if (is_array($product_id)) {
            $product_id = $product_id['product_id'] ?? reset($product_id);
        }
        if (empty($product_id) || !is_numeric($product_id)) {
            return false;
        }
        $this->db->trans_start();
        $query = $this->db->get_where($this->tableCart, ['user_fk' => $user_id]);
        $existing_cart = $query->row_array();
        if(!$existing_cart){
            return false;
        }
        $existing_item = $this->db->get_where($this->tableCartItems, [
            'product_fk' => $product_id,
            'cart_fk'    => $existing_cart['id']
        ])->row();
        if(!$existing_item){
            return false;
        }
        $this->db->where('cart_fk', $existing_cart['id'])
                 ->where('product_fk', $product_id)
                 ->delete($this->tableCartItems);
        $this->db->trans_complete();
        return $this->db->trans_status();
	}

	public function get_orders($id){
    	$this->db->select('
        	orders.order_id, orders.total_price, orders.order_address, orders.order_tel, 
	        orders.status, orders.order_mail, orders.order_name, orders.order_date,
    	    products.title, products.thumbnail,
        	order_items.id, order_items.qty, order_items.price
    	');
		$this->db->from($this->tableOrders);
		$this->db->join($this->tableOrder_Items, 'order_items.order_fk = orders.order_id', 'left');
    	$this->db->join($this->tableProducts, 'products.product_id = order_items.product_fk', 'left');
    	$this->db->where('orders.user_fk', $id);
    	$this->db->order_by('orders.order_id', 'DESC');

    	$query = $this->db->get();

    	if (!$query) {
    	    return false;
    	}

    	$result = [];
    	foreach ($query->result_array() as $row) {
    	    $order_id = $row['order_id'];

    	    if (!isset($result[$order_id])) {
    	        $result[$order_id] = [
    	            'order_id'      => $row['order_id'],
    	            'total_price'   => $row['total_price'],
					'order_name'	=> $row['order_name'],
					'order_mail'	=> $row['order_mail'],
					'order_tel'		=> $row['order_tel'],
    	            'order_address' => $row['order_address'],
    	            'status'        => $row['status'],
    	            'order_date'          => $row['order_date'],
    	            'items'         => []
    	        ];
    	    }

    	    if ($row['title']) { 
    	        $result[$order_id]['items'][] = [
					'id'		=> $row['id'],
    	            'title'     => $row['title'],
    	            'price'     => $row['price'],
    	            'qty'       => $row['qty'],
    	            'thumbnail' => $row['thumbnail']
    	        ];
    	    }
    	}

    	return array_values($result);
	}
	//Admin
	public function get_all_orders(){
    	$this->db->select('
        	orders.order_id, orders.total_price, orders.order_address, orders.order_tel, 
	        orders.status, orders.order_mail, orders.order_name, orders.order_date,
    	    products.title, products.thumbnail,
        	order_items.id, order_items.qty, order_items.price
    	');
		$this->db->from($this->tableOrders);
		$this->db->join($this->tableOrder_Items, 'order_items.order_fk = orders.order_id', 'left');
    	$this->db->join($this->tableProducts, 'products.product_id = order_items.product_fk', 'left');
    	$this->db->order_by('orders.order_id', 'DESC');

    	$query = $this->db->get();

    	if (!$query) {
    	    return false;
    	}

    	$result = [];
    	foreach ($query->result_array() as $row) {
    	    $order_id = $row['order_id'];

    	    if (!isset($result[$order_id])) {
    	        $result[$order_id] = [
    	            'order_id'      => $row['order_id'],
    	            'total_price'   => $row['total_price'],
					'order_name'	=> $row['order_name'],
					'order_mail'	=> $row['order_mail'],
					'order_tel'		=> $row['order_tel'],
    	            'order_address' => $row['order_address'],
    	            'status'        => $row['status'],
    	            'order_date'          => $row['order_date'],
    	            'items'         => []
    	        ];
    	    }

    	    if ($row['title']) { 
    	        $result[$order_id]['items'][] = [
					'id'		=> $row['id'],
    	            'title'     => $row['title'],
    	            'price'     => $row['price'],
    	            'qty'       => $row['qty'],
    	            'thumbnail' => $row['thumbnail']
    	        ];	
    	    }
    	}

    	return array_values($result);
	}
	//Admin
	public function delete_order($order_id){
		$this->db->trans_start();
		$this->db->where('order_fk', $order_id)->delete($this->tableOrder_Items);
		$this->db->where('order_id', $order_id)->delete($this->tableOrders);
		$this->db->trans_complete();
		return $this->db->trans_status();
	}
	//Admin
	public function update_order($order, $order_items){
		$this->db->trans_start();
		$this->db->where('order_id', $order['order_id'])->update($this->tableOrders, $order);
		print_r($this->db->error());
		foreach($order_items as $row){
			//DÜZELTİLECEK
			unset($row['title']);
			unset($row['thumbnail']);
			$this->db->where('order_fk', $order['order_id'])->where('id', $row['id'])->update($this->tableOrder_Items, $row);
			print_r($this->db->error());
		}
		$this->db->trans_complete();
		var_dump($this->db->trans_status());
	}
	//Admin
	public function addNewProduct($product = array()){
    	$this->db->insert($this->tableProducts, $product);
	}

	public function getCategories(){
    	return $this->db->get($this->tableCategories)->result_array();
	}
	//Admin
	public function deleteProduct($where = array()){
    	return $this->db->where($where)->delete($this->tableProducts);
	}
	//Admin
	public function updateProduct($id, $data = array()){
    	return $this->db->where('product_id', $id)->update($this->tableProducts,$data);
	}

	public function getUserByToken($token){
    	return $this->db->where('token', $token)->get($this->tableUsers)->row();
	}

	public function verifyUser($token){
    $data=[
        'is_verified' => 1,
        'token' => null,
    ];
    return $this->db->where('token', $token)->update($this->tableUsers, $data);
	}

	public function createOrder($user,$items,$total){
     	$orderData = [
        	'user_fk' 		=> $user['user_id'], 
	        'order_address' => $user['address'],
    	    'total_price' 		=> $total,
        	'status' 		=> 'Sipariş Alındı', 
	        'order_mail' 	=> $user['mail'],
    	    'order_name' 	=> $user['name'],
			'order_tel'		=> $user['tel']
    	];

    	$this->db->insert($this->tableOrders, $orderData);
    	$order_id = $this->db->insert_id();
    	foreach($items as $item){
        	$orderItems = [
            	'order_fk' => $order_id,
            	'product_fk' => $item['product_id'],
            	'qty' => $item['qty'],
				'price' => $item['price']
        	];
        	$this->db->insert($this->tableOrder_Items, $orderItems);
    	}
		return $orderData;
	}

    public function createGuestAccount($userData){
        return $this->db->insert($this->tableUsers, $userData);
    }



	//Admin
    public function AddProductImages($data,$product_id){
        $data["product_fk"] = $product_id;
        return $this->db->insert($this->tableProducts_Images, $data);

    }
	//Admin
    public function UpdateProductImages($data){
        foreach ($data as $image){
            $this->db->where('image_id', $image["image_id"])->update($this->tableProducts_Images, $image);
        }
    }
	//Admin
    public function deleteProductImage($image_id){
        $this->db->where('image_id',$image_id)->delete($this->tableProducts_Images);
    }
}
