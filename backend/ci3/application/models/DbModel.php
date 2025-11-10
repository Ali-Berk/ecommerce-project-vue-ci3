<?php
class DbModel extends CI_Model {
    public $tableOrders;
    public $tableOrder_Items;
    public $tableUsers;
    public $tableRoles;
    public $tableProducts_Images;
    public $tableProducts;
    public $tableCategories;
    
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

    }

    public function get_all(){
        $products = $this->db->get($this->tableProducts)->result_array();
        foreach ($products as &$product) {
            // Kategori adını al
            $this->db->where('category_id', $product['category_fk']);
            $category = $this->db->get($this->tableCategories)->row_array();
            $product['category'] = $category ? $category['category_name'] : null;
            // category_fk'yı kaldır
            unset($product['category_fk']);
        }
        return json_encode($products);
    }

    public function save($data = array()){
        return json_encode($this->db->insert($this->tableName, $data));
        
    }

    public function update($data = array(), $where = array()){

        return json_encode($this->db->where($where)->update($this->tableName, $data));
        
    }
    
    public function delete($where = array()){
        return json_encode($this->db->where($where)->delete($this->tableName));
        
    }

    public function login($data = array()){
    $this->db->where('mail', $data['mail']);
    $query = $this->db->get($this->tableUsers); 

    if($query->num_rows() == 1){
        $user = $query->row(); 
        if($data['password'] == $user->password){
            return $user; 
        }
    }
    return false;
    }

    public function signin($user = array()){
        $this->db->insert($this->tableUsers, $user);
    }

    public function update_user($user_id, $data = array())
    {
        var_dump($data);
        return $this->db->where('user_id', $user_id)->update('users', $data);
    }

    public function get_products(){
        $cache_file = APPPATH.'cache/products_cache.php';


        if(file_exists($cache_file) && (time() - filemtime($cache_file) < 3600)){ // 3600 saniye = 1 saat
            $cached = read_file($cache_file);
            if($cached !== false){
                return json_decode($cached, true);
            }
        }

        $products = $this->db->get($this->tableProducts)->result_array();

        foreach($products as &$product){
            $this->db->where('product_fk', $product['product_id']);
            $product['images'] = $this->db->get($this->tableProducts_Images)->result_array();

            $this->db->where('category_id', $product['category_fk']);
            $product['category'] = $this->db->get($this->tableCategories)->row_array();
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

public function get_orders($id){
    $this->db->select('orders.order_id, orders.user_fk, orders.order_address, orders.status, 
        GROUP_CONCAT(products.title) AS product_titles, SUM(products.price) AS total_price');
    $this->db->from($this->tableOrders);
    $this->db->join($this->tableOrder_Items, 'order_items.order_fk = orders.order_id', 'left');
    $this->db->join($this->tableProducts, 'products.product_id = order_items.product_fk', 'left');
    $this->db->where('orders.user_fk', $id);
    $this->db->group_by('orders.order_id');

    $result = $this->db->get();
    if(!$result) {
        log_message('error', 'DB Error: ' . $this->db->last_query());
        return false;
    }

    return $result->result_array();
}

public function get_all_orders(){
    $orders = $this->db->get($this->tableOrders)->result_array();
    foreach ($orders as &$order){
        $this->db->where('user_id', $order['user_fk']);
        $user = $this->db->get($this->tableUsers)->row_array();
        $order['user_name'] = $user ? $user['name'] : null;
        unset($order['user_fk']); 
    }
    return $orders;
}

public function addNewProduct($product = array()){
    $this->db->insert($this->tableProducts, $product);
}

public function getCategories(){
    return $this->db->get($this->tableCategories)->result_array();
}

public function deleteProduct($where = array()){
    return $this->db->where($where)->delete($this->tableProducts);
}

public function updateProduct($id, $data = array())
{
    return $this->db->where('product_id', $id)->update($this->tableProducts,$data);
}
}