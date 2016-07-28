<?php
/**
   Copyright (C) 2016 - Ooworx

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
class Ooworx_SubCategoriesWidget_Model_Options_Rootcategory {

    protected function getCurrentStoreRootCategory() {
	if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) // store level
	{
	    $store_id = Mage::getModel('core/store')->load($code)->getId();
	}
	elseif (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) // website level
	{
	    $website_id = Mage::getModel('core/website')->load($code)->getId();
	    $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
	}
	else // default level
	{
	    $store_collection = Mage::getModel('core/store')->getCollection();
	    foreach ($store_collection as $store_tmp) {
		$store_id = $store_tmp->getId();
		break;
	    }
	}

	// Load store if found
	$store = Mage::getModel('core/store')->load($store_id);
	return $store->getRootCategoryId();
    }

    /**
     * Provide available options as a value/label array
     *
     * @return array
     */
    public function toOptionArray() {
	// Init data to return
        $data = array();

        // Default category id for root (maybe find more reliable way)
	$root_category_id = $this->getCurrentStoreRootCategory();

	// Load and check root category
        $root_category = Mage::getModel('catalog/category')->load($root_category_id);
	if (empty($root_category->getId())) {
	    throw new Exception("Root category not found...contact dev or support to have help.");
	}
	
	// Root category add data
	$data[] = array('value' => $root_category->getId(), 'label' => $root_category->getName());

	// Subcategories (max 3 level including root)
        $_categories = $root_category->getChildrenCategories();
        if (count($_categories) > 0){
            foreach($_categories as $_category){
                $_category = Mage::getModel('catalog/category')->load($_category->getId());
                $_subcategories = $_category->getChildrenCategories();
                if (count($_subcategories) > 0){
                    $data[] = array('value' => $_category->getId(), 'label' => "-> " . $_category->getName());
                    foreach($_subcategories as $_subcategory){
                        $data[] = array('value' => $_subcategory->getId(), 'label' => "->-> " . $_subcategory->getName());
                    }
                }
            }
        }
        return $data;
    }
}
