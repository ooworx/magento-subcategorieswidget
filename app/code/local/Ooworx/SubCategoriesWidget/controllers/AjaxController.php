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
class Ooworx_SubCategoriesWidget_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        // Get category id
        $category_id = $this->getRequest()->getParam('category_id', null);

        // Get subcategories from parent category_id
        $data_level = Mage::helper("subcategorieswidget")->getDataSubCategory($category_id);

        // Return json response
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($data_level));
    }
}
