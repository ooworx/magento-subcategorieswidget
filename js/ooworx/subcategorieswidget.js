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

// Wait loading
window.onload = function () {
    // Bind change on select from widget
    var elems=document.querySelectorAll('.subcategorieswidget select');
    for(var i=0;i<elems.length;i++){
	elems[i].addEventListener('change', loadNextSubCategories);
    }
}

// Redirect to the selected category
function redirectLastCategory(self) {
    // Use self instead of this
    var elems = self.parentNode.parentNode.querySelectorAll('select option:checked');

    // Find last selected category
    var url = '';
    for(var i=0; i<elems.length; i++){
	if (elems[i].value) {
	    url = elems[i].getAttribute('data-url');
	}
    }
    // REDIRECT if at least one is found
    if (url) {
	window.location = url;
    }
    
    // Stop event propagation
    return false;
}

// Remove all children options on select input
function removeOptions(selectbox) {
    while (selectbox.firstChild) {
	selectbox.removeChild(selectbox.firstChild);
    }
}

// Assign data to the next level
function loadNextLevel(json_response, next_select) {
    // Parse JSON from response
    var data = JSON.parse(json_response);
    
    removeOptions(next_select);
    
    for(var i=0; i < data.length; i++){
        var obj = data[i];
        var new_option = document.createElement('option');
        new_option.text = obj.label;
        new_option.value = obj.value;
        new_option.setAttribute('data-url', obj.url);
        next_select.appendChild(new_option);
    }
}

// Fetch json data for next select category
function httpGetAsyncNextLevel(url, next_select) {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() { 
	if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
            loadNextLevel(xmlHttp.responseText, next_select);
    }
    xmlHttp.open('GET', url, true); // true for asynchronous 
    xmlHttp.send(null);
}

function loadNextSubCategories() {
    // Find current category id
    var category_id_selected = this.options[this.selectedIndex].value;
    // Get level integer
    var next_level = (parseInt(this.getAttribute('data-level')) + 1);
    // Find next level select
    var next_select = this.parentNode.parentNode.querySelector(' select[name=level_' + next_level + ']');

    // Do nothing on last level
    if (next_select == null) {
	return;
    }

    // Remove old data after the one selected
    var empty_select = this.parentNode.parentNode.querySelector('select[name=level_' + next_level + ']');
    // Browse all select to remove options
    while(empty_select) {
	removeOptions(empty_select);
	next_level += 1;
	// Load next select if existing currently
	empty_select = this.parentNode.parentNode.querySelector('select[name=level_' + next_level + ']');
    }

	// Do not request if option have no value (or category id)
    if (!category_id_selected) {
	return;
    }

    // Load next level
    var base_url = this.parentNode.parentNode.parentNode.action;
    httpGetAsyncNextLevel(base_url + 'subcategorieswidget/ajax?category_id=' + category_id_selected, next_select)
}

