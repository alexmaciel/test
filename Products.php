<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('products_model');		
		$this->load->helper('url');
		$this->load->helper('text');
    }

    public function allProducts()
    {

        $products = $this->products_model->getProducts(
			$featured=false, 
			$filterDepartment='', 
			$filterCategory='', 
			$recentproducts=false
		);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($products));        
	} 
	
    public function getDepartments($departmentId)
    {

		$fromObject = array(
			'departmentId' => $departmentId
		);

        $products = $this->products_model->getProducts(
			$fromObject
		);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($products));        
	}
	
    public function getFilteredProducts()
    {
		$ts_filter_data = array();
		$ts_filter_data['SearchString'] = $this->input->get('SearchString');
		$ts_filter_data['isFeature'] = $this->input->get('isFeature');
		$ts_filter_data['departmentId'] = $this->input->get('departmentId');
		$ts_filter_data['categoryId'] = $this->input->get('categoryId');
		$ts_filter_data['attributeId'] = $this->input->get('attributeId');
		$paging = array('Paging' => $ts_filter_data);

		$products 			= $this->products_model->getProducts($paging);
		$data['products']   = $products;


		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));        
    }	

	public function getFeaturedProducts()
	{

		$ts_filter_data = array();
		$ts_filter_data['departmentId'] = $this->input->get('departmentId');
		$ts_filter_data['categoryId'] = $this->input->get('categoryId');
		$ts_filter_data['attributeId'] = $this->input->get('attributeId');
		$paging = array('Paging' => $ts_filter_data);

		$products = $this->products_model->getProducts($paging, $featured=true);
		$data['products']   = $products;

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));          
    }    

    public function getProductDetails($id= '')
    {

		//$this->load->model('nutricional_model');		
		//$this->load->model('ingredients_model');
		//$this->load->model('attributes_model');
		$this->load->model('pictures_model');

		$product 			= $this->products_model->get($id);
		$data['product']     = $product;
		$data['pictures'] = $this->pictures_model->get($product->productId);

		/*
		if (is_numeric($product->productId)) {
			$data['ingredients'] = $this->ingredients_model->get($product->productId);
			$data['nutricional'] = $this->nutricional_model->get($product->productId);
			$data['nutricionalInfo'] = $this->nutricional_model->getInfo($product->productId);
			$data['attributes'] = $this->attributes_model->get($product->productId);
		}
		*/
        
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));        
    }    
}