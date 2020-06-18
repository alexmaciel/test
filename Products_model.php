<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products_model extends CI_Model 
{
	public function getProducts($data, $where = array())
	{
        $filterDepartment = ($data['Paging']['departmentId'] == 0) ? 'productdepartment.departmentId = productdepartment.departmentId' : ' productdepartment.departmentId = ' . $data['Paging']['departmentId'];
        $filterCategory = ($data['Paging']['categoryId'] == 0) ? 'productdepartment.categoryId = productdepartment.categoryId' : 'productdepartment.categoryId = ' .  $data['Paging']['categoryId'];
        $filterAttributes = ($data['Paging']['attributeId'] == 0) ? 'productattribute.attributeId = productattribute.attributeId' : 'productattribute.attributeId = ' .  $data['Paging']['attributeId'];
        $featured = ($data['Paging']['isFeature'] == 0) ? 'products.isFeature = products.isFeature' : 'products.isFeature = ' .  $data['Paging']['isFeature'];
        $filterSearchString = ''; 
        $data['Paging']['SearchString'] = ($data['Paging']['SearchString'] == 'undefined') ? '' : $data['Paging']['SearchString'];
        
        if($data['Paging']['SearchString'] == ''){
            $filterSearchString = ' products.productName LIKE "%%" OR products.productDesc LIKE "%%"';
        } else {
            $filterSearchString = 'UPPER (products.productName) LIKE UPPER ("%'.$data['Paging']['SearchString'].'%") OR UPPER (products.productDesc) LIKE UPPER ("%'.$data['Paging']['SearchString'].'%")';
        } 

        $this->db->select('
            products.productId,
            products.productName,
            products.productNotes,
            products.productDesc,
            products.isClosed,
            products.productFolder,
            products.url_redirects,
            products.isFeature,
            DATE_FORMAT(products.productDate,"%M %d, %Y") AS date,
            category.categoryId,
            category.categoryName,
            department.departmentId,
            department.departmentName                         
        ');   
        $this->db->where($where);   
        $this->db->where('
            productdepartment.departmentId = department.departmentId
            AND productdepartment.productId = products.productId
            AND productdepartment.categoryId = category.categoryId
            AND productattribute.productId = products.productId
            AND productattribute.attributeId = attributevalue.attributeId
        ');
        $this->db->where($filterDepartment);
        $this->db->where($filterCategory);
        $this->db->where($filterAttributes);
        $this->db->where($featured);
        if($data['Paging']['SearchString'] != ''){
            $this->db->where(($filterSearchString));
        }

        $this->db->where('products.isClosed', 0);
        $this->db->group_by('products.productId');    
        $this->db->order_by('products.productDate', 'desc');    
        
        return $this->db->get('products, category, department, productcategory, productdepartment, attribute, productattribute, attributevalue')->result_array();
    } 

    public function get($id = '', $where = array())
    {
        $this->db->select('*,
            products.productId,
            products.productName,
            products.productNotes,
            products.productDesc,        
            products.productFolder,
            products.url_redirects, 
            DATE_FORMAT(products.productDate,"%M %d, %Y") AS date,
            category.categoryId,
            category.categoryName,
            department.departmentId,
            department.departmentName,              
            pictures.productId
        ');
        $this->db->where($where);
        $this->db->where('products.url_redirects', $id);

        $this->db->where('
            products.productId = pictures.productId
            AND productdepartment.productId = products.productId
            AND productdepartment.categoryId = category.categoryId
            AND productdepartment.departmentId = department.departmentId
            AND productattribute.productId = products.productId
            AND productattribute.attributeId = attributevalue.attributeId            
        ');

        $product =  $this->db->get('products, pictures, category, department, productcategory, productdepartment, attributevalue, productattribute')->row();

        return $product;
    }    


}