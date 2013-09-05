<?php


/* ----------------------------------------------------------------------
 * 
 * 
 * 
 * 
 * show.php
 * 
 * Description:
 * This is the main display page used for calling each different reresentation
 * of faces.
 * 
 * ----------------------------------------------------------------------
 */
require_once("../../../config.php");
global $CFG, $DB;
require_login();

require_once('renderFaces.php');

$cid = required_param('cid', PARAM_INT);
$gid = optional_param('gid', '', PARAM_INT);    


/** Navigation Bar **/
$PAGE->navbar->ignore_active();
$renderType = '';

$selectgroupsec = optional_param('selectgroupsec', '', PARAM_TEXT);  


 
if(isset($selectgroupsec)){
	
	if($selectgroupsec == 'all'){
		$renderType = 'all';
	}
	else if($selectgroupsec == 'group'){
		$renderType == 'group';
	} 
	
	if(is_numeric($selectgroupsec)) {
		$renderType = 'group';
	}
	
		
} else {
		$renderType = 'all';
}

if($renderType == 'all' || $renderType == ''){
		$courseName = $DB->get_record('course', array('id'=>$cid), 'shortname', $strictness=IGNORE_MISSING); 
		$PAGE->navbar->add($courseName->shortname, new moodle_url($CFG->wwwroot . '/course/view.php?id=' . $cid));
		$PAGE->navbar->add(get_string('showallfaces', 'block_faces'));
	
}
else if($renderType == 'group'){
		$courseName = $DB->get_record('course', array('id'=>$cid), 'shortname', $strictness=IGNORE_MISSING); 
		$PAGE->navbar->add($courseName->shortname, new moodle_url($CFG->wwwroot . '/course/view.php?id=' . $cid));
		$PAGE->navbar->add(get_string('showfacesbygroup', 'block_faces'));
}


$PAGE->set_url('/blocks/faces/showfaces/show.php');
$PAGE->set_context(get_system_context());
$PAGE->set_heading(get_string('pluginname', 'block_faces'));
$PAGE->set_title(get_string('pluginname', 'block_faces'));

echo $OUTPUT->header();
echo buildMenu($cid);


// Render the page
$selectgroupsec = optional_param('selectgroupsec', '', PARAM_TEXT);   
if(isset($selectgroupsec)){
	
	if($selectgroupsec == 'all' || $selectgroupsec == ''){
		 
		echo renderAll();
		
	} else {
		
		echo renderGroup();
	
	}
	
} else {

	echo renderAll();
}

class faces_form extends moodleform {
 
	function definition() {
    global $CFG;
    global $USER, $DB;
    $mform =& $this->_form; // Don't forget the underscore! 
	}
}


/*
 * 
 * Create the HTML output for the list on the right
 * hand side of the showfaces.php page
 * 
 * */
function buildMenu($cid){
	
	global $DB, $CFG, $renderType;
	
	$orderBy = '';
	$orderBy = optional_param('orderby', '', PARAM_TEXT);
	
	
	$outputHTML = '<div style="float:right"><form action="'.$CFG->wwwroot. '/blocks/faces/showfaces/show.php?cid='.$cid.'" method="post">
				 Order By: <select name="orderby" id="orderby">
								<option value="firstname">' .get_string('firstname', 'block_faces').'</option>
								<option value="lastname">'.get_string('lastname', 'block_faces').'</option>
						  </select>
						  
				 Filter: <select id="selectgroupsec" name="selectgroupsec">
				 	<option value="all">'.get_string('showallfaces', 'block_faces').'</option>
				 '. buildGroups($cid).'	
				 </select>
				 <input type="submit" value="'.get_string('update', 'block_faces').'"></input>
				</form>
				
				<span style="float:right">
				
				<form action="../print/page.php">
   				<input type="hidden" name="cid" value="'.$cid.'">
				<input type="hidden" name="rendertype" value="'.$renderType.'">
				
				';
				
				// If a group was selected
				$selectgroupsec = optional_param('selectgroupsec', '', PARAM_TEXT); 
				if(isset($selectgroupsec)){
 					$outputHTML .= '<input type="hidden" name="selectgroupsec" value="'.$selectgroupsec.'">';
				}
				$outputHTML .= '
				<input type="hidden" name="orderby" value="'.$orderBy.'">
					
				
   				<input type="submit" value="'.get_string('print', 'block_faces').'">
				</span>
				</form>
				

			    </div>
			    

				</div>
				';
	
	return $outputHTML;
	
}
/*
 * Build up the dropdown menu items with groups that are associated
 * to the currently open course.
 * 
 */
function buildGroups($cid){
	
	global $DB;
	
	$buildHTML = '';
	$groups = $DB->get_records('groups',array('courseid'=>$cid));

	foreach($groups as $group){
		$groupId = $group->id;
		
		$buildHTML.= '<option value="'.$groupId.'">'. $group->name.'</option>';
	}
	
	return $buildHTML;
	
}

$mform = new faces_form();
$mform->focus();
$mform->display();		
echo $OUTPUT->footer();

 $selectgroupsec = optional_param('selectgroupsec', '', PARAM_TEXT); 
	if(isset($selectgroupsec)){
 		$selectedItem = $selectgroupsec;
		echo '<script>
				document.getElementById("selectgroupsec").value = '.$selectedItem.'
			  </script>';
	 }

 $orderBy = optional_param('orderby', '', PARAM_TEXT);
	if(isset($orderBy)){
		$orderItem = $orderBy;
		
		echo '<script>
				document.getElementById("orderby").value = "'.$orderItem.'"
			  </script>';
			  
			  if($orderItem == ""){
			  	echo '<script>
				document.getElementById("orderby").value = "firstname";
			  </script>';
				
			  }
	} 