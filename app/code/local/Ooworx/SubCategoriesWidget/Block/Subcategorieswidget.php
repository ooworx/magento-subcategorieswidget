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
class Ooworx_SubCategoriesWidget_Block_Subcategorieswidget extends Mage_Core_Block_Abstract implements Mage_Widget_Block_Interface
{    
    /**
     * Return label from widget config
     *
     * @return string
     */
    protected function getConfigLabel($level = '0') {
        // Get data from widget config
        return $this->getData('subcategorieswidget_label_' . $level);
    }


    /**
     * Return javascript string to be added in html with provided js file path
     *
     * @return string
     */
    protected function getJsToLoadOnce($js_path) {
        return "
	function loadSubcategoriesWidgetJs() {
	    var head= document.getElementsByTagName('head')[0];
	    var script= document.createElement('script');
	    script.type= 'text/javascript';
	    script.src= '" . $js_path . "';
	    head.appendChild(script);
	}

	var scripts = document.getElementsByTagName('script');
	var header_already_added = false;
	for (var i=0; i< scripts.length; i++){

	    if (scripts[i].src.endsWith('" . $js_path . "')){
		header_already_added = true;
	    }
	}
	// Add if not loaded
	if (header_already_added == false){
	    loadSubcategoriesWidgetJs();
	}
       ";
    }
    
    /**
     * Produce links list rendered as html
     *
     * @return string
     */
    protected function _toHtml() {
        // Get data from widget config
        $root_category_id = $this->getData('subcategorieswidget_rootcategory');

        // Basic check
        if (empty($root_category_id)) {
            return "Widget not configured";
        }
        
        // Get subcategories from parent category_id and check
        $data_root_level = Mage::helper("subcategorieswidget")->getDataSubCategory($root_category_id);
        if (empty($data_root_level)) {
            return 'Widget : Root category not found, check widget config...';
        }
        
        // Base html
        $html = '<style> .subcategorieswidget select {width: 100%;margin-bottom:10px; font-size:16px; } .subcategorieswidget ul { list-style-type: none; }</style>';
        $html .= '<div class="subcategorieswidget">';
        // Used by Ajax api
        $html .= '<form action="' . Mage::getBaseUrl() . '">';
        $html .= '<ul>';

        // Root level always here
        $html .= '<li>';
        // Add label if configured
        if (!empty($this->getConfigLabel('0'))) {
            $html .= '<label>' . $this->getConfigLabel('0') . ' : </label>';
        }
        // Create root level with subcategories found
        $html .= '<select name="level_0" data-level="0">';
        foreach($data_root_level as $option) {
            $html .= '<option value="' . $option['value'] . '" data-url="' . $option['url'] . '">' . $option['label'] . '</option>';
        }
        $html .= '</select>';
        $html .= '</li>';

        // Get data from widget config
        $maxdepth = $this->getData('subcategorieswidget_maxdepth');
	
        // Add more select levels if configured
        for($i = 1; $i < $maxdepth; $i++) {
            $html .= '<li>';
            // Add label if configured
            if (!empty($this->getConfigLabel($i))) {
                $html .= '<label>' . $this->getConfigLabel($i) . ' : </label>';
            }
	    // Create empty levels
            $html .= '<select name="level_' . $i . '" data-level="' . $i . '">';
            $html .= '<option value=""></option>';
            $html .= '</select>';
            $html .= '</li>';
        }

        // Submit button
        $html .= ' <li><button class="submit" onclick="return redirectLastCategory(this);">' . $this->__('Submit') . '</button></li>';

        $html .= '</ul>';
        // Js load one time js for all widgets
        $html .= "<script>" . $this->getJsToLoadOnce('js/ooworx/subcategorieswidget.js') . "</script>";
        // Close html tags
        $html .= '</form>';
        $html .= '</div>';

        // Return html
        return $html;
    }
}
