<?php
function init_sql($inst_id, $adminww) {

$init_data = array(
"user" => array(
  "instance_id,user_id,status,user_code,password,given_name,family_name_prefix,family_name,email_address",
  array(
	"#INSTID#,3,3,'anoniem','','','','',''",
	"#INSTID#,4,1,'admin','$adminww','','','',''"
  )
),
"page_definition" => array(
  "instance_id,page_definition_id,title,description,action,search_index_rule,config_only,type_set,default_tab_id",
  array(
	"#INSTID#,7,'Home','Sjabloon voor de Home pagina','templates/home.php','page',0,1,8"
  )
),
"page_definition_tab" => array(
  "instance_id,page_definition_tab_id,page_definition_id,sequence_no,title,php_key,css_selector,page_css,stylesheet,application_definition_id,css_id",
  array(
	"#INSTID#,8,7,0,'Content','CONTENT','','','',0,''"
  )
),
"page_property_html" => array(
  "instance_id,page_id,version,page_definition_tab_id,raw_html,html",
  array(
	"#INSTID#,9,0,8,'<P>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin tincidunt sagittis elit, eu tincidunt ante ultricies sit amet. Aliquam faucibus suscipit nunc egestas aliquam. Donec pretium tincidunt dolor, ut pellentesque velit scelerisque et. Nam vitae sem at justo mattis aliquet. Aliquam scelerisque, sem eget venenatis consequat, urna urna imperdiet velit, ut lobortis purus augue sit amet neque. Ut a nulla at tortor ultricies pellentesque. Sed interdum, lectus quis tincidunt interdum, est dolor ornare ligula, id auctor metus neque sed felis. Aliquam vel velit arcu. Phasellus eu cursus est. Curabitur id sapien felis, vel placerat risus. Praesent a purus at odio tempor scelerisque gravida eu orci.</P>',''"
  )
),
"page" => array(
  "instance_id,page_id, parent_id, sequence_no, type, page_definition_id, language_id, date_created, date_modified, title, description, keywords, javascript, stylesheet",
  array(
	"#INSTID#,1,0,0,1,7,'nl-NL',NOW(),NOW(),'Home','','','',''"
  )
),
"pretty_url" => array(
  "instance_id,type,page_id,original_url,translated_url",
  array(
	"#INSTID#,1,1,'index.php?p=1','home.html'"
  )
),
"asset" => array(
  "instance_id, asset_id, parent_id, sequence_no, type, size, date_created, date_modified, title, location, mime_type",
  array(
	"#INSTID#, 2, 0, 0, 0, 0, NOW(), NOW(), 'Ge-uploade bestanden', '', ''"
  )
),
"role" => array(
  "instance_id,role_id, type, title, description",
  array(
	"#INSTID#, 5, 3, 'Openbaar', ''",
	"#INSTID#, 6, 2, 'Web-beheer', ''"
  )
),
"user_role" => array(
  "instance_id,role_id, user_id",
  array(
	"#INSTID#, 5, 3",
	"#INSTID#, 6, 4"
  )
),
"object_role" => array(
  "instance_id,role_id, page_id",
  array(
	"#INSTID#, 5, 1",
	"#INSTID#, 6, 1",
	"#INSTID#, 5, 2",
	"#INSTID#, 6, 2"
  )
),
"ui_lang" => array(
  "instance_id,iso_code,description",
  array(
	"#INSTID#,'nl','Nederlands'",
	"#INSTID#,'en','English'"
  )
),
);

  $res = array();
  foreach ($init_data as $table => $data) {
	foreach ($data[1] as $d) {
	  $res[] = "INSERT INTO " . $table . " (" . $data[0] . ") VALUES (" . str_replace("#INSTID#",$inst_id,$d) . ")";
	}
  }
  return $res;
}

?>