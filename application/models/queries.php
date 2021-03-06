<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class queries extends CI_Model {
    
    public function checkuser()
    {
    	$users = $this->input->post();
    	$query = 'Select id as validID from users where email = ? and password = ?';
        return $this->db->query($query, $users)->row_array();
    }
    public function register() 
    {
        $user = $this->input->post();
        return $this->db->query("INSERT INTO users (email, password, created_at) VALUES (?,?,NOW())", array($user['email'], $user['password']));
    }
    public function getdata($id)
    {
    	$logged_user_id = $id;
        $query = "SELECT *, (select imageid from product_image where product_list.id = product_image.prod_id and (upper(main) = 'Y' or line = 1)) as imagefile FROM `product_list` where (user_id = ?)";
    	return $this->db->query($query, $logged_user_id)->result_array();
    }
    public function searchdata($search)
    {
    	$searchItems = array('%'.$search.'%', '%'.$search.'%', $search);
    	$query = "SELECT *, (select imageid from product_image where product_list.id = product_image.prod_id and (upper(main) = 'Y' or line = 1)) as imagefile FROM `product_list` where ( (name like ?) or (description like ?) or (id = ?) )";
    	return $this->db->query($query, $searchItems)->result_array();
    }
    public function delete($id)
    {
    	$query = 'delete from product_list where id=?';
    	$this->db->query($query,$id);
    	$query = 'delete from product_image where prod_id=?';
    	$this->db->query($query,$id);
    	return;
    }
    public function getCategories()
    {
        return $this->db->query('select * from category')->result_array();
    }
    public function getProductImages($product_id)
    {
        return $this->db->query('select * from product_image where prod_id =?',$product_id)->result_array();
    }
    public function getProductDetails($product_id)
    {
        return $this->db->query('select * from product_list where id =?',$product_id)->row_array();
    }
    public function updateProduct($product_id)
    {
        if ( !is_null($this->input->post('new_category')) ){
            $categoryid = $this->addCategory($this->input->post('new_category'));
            $category = $categoryid['category_id'];
        }
        else{
            $category = $this->input->post('categories');
            var_dump($this->input->post('categories'));
        }   
        if ($this->input->post('Update') == 'Add') {
            $post = array($product_id, $this->input->post('name'), $this->input->post('description'), $category);
            $query = 'insert into product_list (id, name, description, category_id) values(?,?,?,?)';
        }
        else{
            $post = array($this->input->post('name'), $this->input->post('description'), $category, $product_id);
            $query = 'update product_list set name = ?, description = ?, category_id = ? where id = ?';
        }
        $this->db->query($query, $post);
        return ;
    }
    private function addCategory($category)
    {
        $query = 'insert into category (`category_name`) values(?)';
        $this->db->query($query,$category);
        $query = 'select category_id from category where category_name =?';
        return $this->db->query($query,$category)->row_array();
    }
    public function addProduct() 
    {
        $query = 'SELECT max(id)+1 as new_product FROM product_list';
        $prod_id = $this->db->query($query)->row_array();
        // var_dump($prod_id['new_product']);
        $item = $this->input->post();
        $quantity_sold = 0;
        $query=$this->db->query("INSERT INTO product_list (name, description, category_id, inventory_count, qrt_sold, price, user_id) VALUES (?,?,?,?,?,?,?)", array($item['name'], $item['description'], $item['categories'], $item['quantity'], $quantity_sold, $item['price'], $item['user_id']));
        // return $query;
    
        return $this->db->query("INSERT INTO product_image (prod_id, imageid, main) VALUES (?,?,?)", array($prod_id['new_product'], $item['image'], $item['main']));
    }
    // public function getLastProductID()
    // {
    //     $query = 'SELECT max(id)+1 as new_product FROM product_list';
    //     $prod_id = $this->db->query($query)->row_array();
    //     return $prod_id;
    //     var_dump($prod_id);
    // }
}
?>