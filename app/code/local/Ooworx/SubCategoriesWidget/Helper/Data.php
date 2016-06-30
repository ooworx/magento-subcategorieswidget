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
class Ooworx_SubCategoriesWidget_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Return data from subcategory
     *
     * @return array
     */
    public function getDataSubCategory($category_id) {
        // Check params
        if (empty($category_id) || !is_numeric($category_id) || $category_id < 0)
            throw new Exception('Invalid parameters category_id.');

        // Load and check
        $level_category = Mage::getModel('catalog/category')->load($category_id);
        if (empty($level_category->getId())) {
            throw new Exception('Category not found.');
        }

        // Load subcategories
        $_subcategories = $level_category->getChildrenCategories();

        $data_level = array();
        $data_level[] = array('value' => '', 'label' => '-'.$this->__('Select').'-', 'url' => '');
        if (count($_subcategories) > 0){
            foreach($_subcategories as $_subcategory){
                $_category = Mage::getModel('catalog/category')->load($_subcategory->getId());

                $data_level[] = array('value' => $_subcategory->getId(), 'label' => $_subcategory->getName(), 'url' => $_subcategory->getUrl($_subcategory));
            }
        }
        return $data_level;
    }
}
