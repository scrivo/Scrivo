<?php
function init_sql($inst_id, $adminww) {

$init_data = array(

"user" => array(
  "instance_id,user_id,status,user_code,password,given_name,family_name_prefix,family_name,email_address",
  array(
	"#INSTID#,1,3,'anoniem','','','','',''",
	"#INSTID#,2,1,'admin','$adminww','','','',''"
  )
),
"page_definition" => array(
  "instance_id,page_definition_id,title,description,action,search_index_rule,config_only,type_set,default_tab_id",
  array(
	"#INSTID#,4,'Home','Sjabloon voor de Home pagina','templates/home.php','page',0,1,5"
  )
),
"page_definition_tab" => array(
  "instance_id,page_definition_tab_id,page_definition_id,sequence_no,title,php_key,css_selector,page_css,stylesheet,application_definition_id,css_id",
  array(
	"#INSTID#,5,4,0,'Content','CONTENT','','','',0,''"
  )
),
"page_property_html" => array(
  "instance_id,page_id,version,page_definition_tab_id,raw_html,html,STRIPPED",
  array(
	"#INSTID#,1,0,5,'<P>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin tincidunt sagittis elit, eu tincidunt ante ultricies sit amet. Aliquam faucibus suscipit nunc egestas aliquam. Donec pretium tincidunt dolor, ut pellentesque velit scelerisque et. Nam vitae sem at justo mattis aliquet. Aliquam scelerisque, sem eget venenatis consequat, urna urna imperdiet velit, ut lobortis purus augue sit amet neque. Ut a nulla at tortor ultricies pellentesque. Sed interdum, lectus quis tincidunt interdum, est dolor ornare ligula, id auctor metus neque sed felis. Aliquam vel velit arcu. Phasellus eu cursus est. Curabitur id sapien felis, vel placerat risus. Praesent a purus at odio tempor scelerisque gravida eu orci.</P>','',''"
  )
),
"page" => array(
  "instance_id,page_id, parent_id, sequence_no, type, page_definition_id, language_id, READ_ONLY, AUTHOR_ID, date_created, date_modified, title, description, keywords, javascript, stylesheet, DATE_STAGING",
  array(
	"#INSTID#,1,0,0,1,4,85,0,1,NOW(),NOW(),'Home','','','','', NOW()"
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
	"#INSTID#, 1, 3, 'Openbaar', ''",
	"#INSTID#, 2, 2, 'Web-beheer', ''"
  )
),
"user_role" => array(
  "instance_id,role_id, user_id",
  array(
	"#INSTID#, 1, 1",
	"#INSTID#, 2, 2"
  )
),
"object_role" => array(
  "instance_id,role_id, page_id",
  array(
	"#INSTID#, 1, 1",
	"#INSTID#, 2, 1",
	"#INSTID#, 1, 2",
	"#INSTID#, 2, 2"
  )
),
"USER_VAR" => array(
  "instance_id,VARIABLE_ID,VARIABLE,DEFAULT_VAL",
  array(
	"#INSTID#,1,'language','nl'",
	"#INSTID#,2,'default_language_id','85'",
	"#INSTID#,3,'style','win'",
	"#INSTID#,4,'editor_zoom','100%'"
  )
),
"ui_lang" => array(
  "instance_id,iso_code,description",
  array(
	"#INSTID#,'name_nl','Nederlands'",
	"#INSTID#,'name_en','English'"
  )
),
"language" => array(
  "instance_id,language_id,short_list,iso_code,family,name_en,name_nl",
  array(
	"#INSTID#,1,0,'AA','Hamitic','Afar','Afar'",
	"#INSTID#,2,0,'AB','Ibero-Caucasian','Abkhazian','Abkhaziaans'",
	"#INSTID#,3,0,'AF','Germanic','Afrikaans','Afrikaans'",
	"#INSTID#,4,0,'AM','Semitic','Amharic','Amharisch'",
	"#INSTID#,5,0,'AR','Semitic','Arabic','Arabisch'",
	"#INSTID#,6,0,'AS','Indian','Assamese','Assamees'",
	"#INSTID#,7,0,'AY','Amerindian','Aymara','Aymara'",
	"#INSTID#,8,0,'AZ','Turkic/altaic','Azerbaijani','Azerbeidzjaans'",
	"#INSTID#,9,0,'BA','Turkic/altaic','Bashkir','Bashkir'",
	"#INSTID#,10,0,'BE','Slavic','Byelorussian','Wit-Russisch'",
	"#INSTID#,11,0,'BG','Slavic','Bulgarian','Bulgaars'",
	"#INSTID#,12,0,'BH','Indian','Bihari','Bihari'",
	"#INSTID#,13,0,'BI','[not given]','Bislama','Bislama'",
	"#INSTID#,14,0,'BN','Indian','Bengali;bangla','Bengalees'",
	"#INSTID#,15,0,'BO','Asian','Tibetan','Tibetaans'",
	"#INSTID#,16,0,'BR','Celtic','Breton','Bretons'",
	"#INSTID#,17,1,'CA','Romance','Catalan','Catalaans'",
	"#INSTID#,18,1,'CO','Romance','Corsican','Corsicaans'",
	"#INSTID#,19,0,'CS','Slavic','Czech','Tsjechisch'",
	"#INSTID#,20,0,'CY','Celtic','Welsh','Welsh'",
	"#INSTID#,21,1,'DA','Germanic','Danish','Deens'",
	"#INSTID#,22,1,'DE','Germanic','German','Duits'",
	"#INSTID#,23,0,'DZ','Asian','Bhutani','Dzongkha; Bhutaans'",
	"#INSTID#,24,1,'EL','Latin/greek','Greek','Modern-Grieks (1453­)'",
	"#INSTID#,25,1,'EN','Germanic','English','Engels'",
	"#INSTID#,26,0,'EO','International aux.','Esperanto','Esperanto'",
	"#INSTID#,27,1,'ES','Romance','Spanish','Spaans'",
	"#INSTID#,28,0,'ET','Finno-ugric','Estonian','Estlands'",
	"#INSTID#,29,0,'EU','Basque','Basque','Baskisch'",
	"#INSTID#,30,0,'FA','iranian','Persian(farsi)','Perzisch'",
	"#INSTID#,31,1,'FI','Finno-ugric','Finnish','Fins'",
	"#INSTID#,32,0,'FJ','Oceanic/indonesian','Fiji','Fiji'",
	"#INSTID#,33,0,'FO','Germanic','Faroese','Faer&ouml;ers'",
	"#INSTID#,34,1,'FR','Romance','French','Frans'",
	"#INSTID#,35,1,'FY','Germanic','Frisian','Fries'",
	"#INSTID#,36,0,'GA','Celtic','Irish','Iers'",
	"#INSTID#,37,0,'GD','celtic','Scots GAELIC','Schots Gaelic'",
	"#INSTID#,38,1,'GL','Romance','Galician','Galicisch'",
	"#INSTID#,39,0,'GN','Amerindian','Guarani','Guarani'",
	"#INSTID#,40,0,'GU','Indian','Gujarati','Gujarati'",
	"#INSTID#,41,0,'GV','[]','Manx','Manx'",
	"#INSTID#,42,0,'HA','Negro-african','Hausa','Hausa'",
	"#INSTID#,43,0,'HE','Semitic','Hebrew','Hebreeuws'",
	"#INSTID#,44,0,'HI','Indian','Hindi','Hindoestaans'",
	"#INSTID#,45,0,'HR','Slavic','Croatian','Kroatisch'",
	"#INSTID#,46,0,'HU','Finno-ugric','Hungarian','Hongaars'",
	"#INSTID#,47,0,'HY','Indo-european (other)','Armenian','Armeens'",
	"#INSTID#,48,0,'IA','International aux.','Interlingua','Interlingua'",
	"#INSTID#,49,0,'id','Oceanic/indonesian','Indonesian','Indonesisch'",
	"#INSTID#,50,0,'IE','International aux.','Interlingue','Interlingue'",
	"#INSTID#,51,0,'IK','Eskimo','Inupiak','Inupiak'",
	"#INSTID#,52,1,'IS','Germanic','Icelandic','IJslands'",
	"#INSTID#,53,1,'IT','Romance','Italian','Italiaans'",
	"#INSTID#,54,0,'IU','[]','Inuktitut','Inuktitut'",
	"#INSTID#,55,0,'JA','Asian','Japanese','Japans'",
	"#INSTID#,56,0,'JV','Oceanic/indonesian','Javanese','Javaans'",
	"#INSTID#,57,0,'KA','Ibero-caucasian','Georgian','Georgisch'",
	"#INSTID#,58,0,'KK','Turkic/altaic','Kazakh','Kazakstaans'",
	"#INSTID#,59,0,'KL','Eskimo','Greenlandic','Kalaallisut (Groenlands)'",
	"#INSTID#,60,0,'KM','Asian','Cambodian','Khmer (Cambodjaans)'",
	"#INSTID#,61,0,'KN','Dravidian','Kannada','Kannada'",
	"#INSTID#,62,0,'KO','Asian','Korean','Koreaans'",
	"#INSTID#,63,0,'KS','Indian','Kashmiri','Kashmiri'",
	"#INSTID#,64,0,'KU','Iranian','Kurdish','Koerdisch'",
	"#INSTID#,65,0,'KW','[]','Cornish','Cornish'",
	"#INSTID#,66,0,'KY','Turkic/altaic','Kirghiz','Kirghizisch'",
	"#INSTID#,67,0,'LA','Latin/greek','Latin','Latijn'",
	"#INSTID#,68,1,'LB','Germanic','Letzeburgesch (Luxemburgs)','Letzeburgesch (Luxemburgs)'",
	"#INSTID#,69,0,'LN','Negro-african','Lingala','Lingala'",
	"#INSTID#,70,0,'LO','Asian','Laothian','Laotiaans'",
	"#INSTID#,71,0,'LT','Baltic','Lithuanian','Litouws'",
	"#INSTID#,72,0,'LV','Baltic','Latvian;lettish','Lets'",
	"#INSTID#,73,0,'MG','Oceanic/indonesian','Malagasy','Malagasi'",
	"#INSTID#,74,0,'MI','Oceanic/indonesian','Maori','Maori'",
	"#INSTID#,75,0,'MK','Slavic','Macedonian','Macedonisch'",
	"#INSTID#,76,0,'ML','Dravidian','Malayalam','Malayalaams'",
	"#INSTID#,77,0,'MN','[not given]','Mongolian','Mongools'",
	"#INSTID#,78,1,'MO','Romance','Moldavian','Moldavisch'",
	"#INSTID#,79,0,'MR','Indian','Marathi','Marathi'",
	"#INSTID#,80,0,'MS','Oceanic/indonesian','Malay','Maleisisch'",
	"#INSTID#,81,0,'MT','Semitic','Maltese','Maltees'",
	"#INSTID#,82,0,'MY','Asian','Burmese','Burmees'",
	"#INSTID#,83,0,'NA','[not given]','Nauru','Nauru'",
	"#INSTID#,84,0,'NE','Indian','Nepali','Nepalees'",
	"#INSTID#,85,1,'NL','Germanic','Dutch','Nederlands'",
	"#INSTID#,86,1,'NO','Germanic','Norwegian','Noors'",
	"#INSTID#,87,1,'OC','Romance','Occitan','Occitaans (post 1500)'",
	"#INSTID#,88,0,'OM','Hamitic','Afan(Oromo)','Oromo'",
	"#INSTID#,89,0,'OR','Indian','Oriya','Oriya'",
	"#INSTID#,90,0,'PA','Indian','Punjabi','Panjabi'",
	"#INSTID#,91,0,'PL','Slavic','Polish','Pools'",
	"#INSTID#,92,0,'PS','Iranian','Pashto;pushto','Pashto'",
	"#INSTID#,93,1,'PT','Romance','Portuguese','Portugees'",
	"#INSTID#,94,0,'QU','Amerindian','Quechua','Quechuaans'",
	"#INSTID#,95,1,'RM','Romance','Rhaeto-romance','Rhaeto-Romaans'",
	"#INSTID#,96,0,'RN','Negro-african','Kurundi','Kirundi'",
	"#INSTID#,97,1,'RO','Romance','Romanian','Roemeens'",
	"#INSTID#,98,0,'RU','Slavic','Russian','Russisch'",
	"#INSTID#,99,0,'RW','Negro-african','Kinyarwanda','Kiyarwandees'",
	"#INSTID#,100,0,'SA','Indian','Sanskrit','Sanskriet'",
	"#INSTID#,101,0,'SD','Indian','Sindhi','Sindhi'",
	"#INSTID#,102,0,'SG','Negro-african','Sangho','Sango'",
	"#INSTID#,103,0,'SH','Slavic','Serbo-croatian','Servo-Kroatisch'",
	"#INSTID#,104,0,'SI','Indian','Singhalese','Singalees'",
	"#INSTID#,105,0,'SK','Slavic','Slovak','Slovaaks'",
	"#INSTID#,106,0,'SL','Slavic','Slovenian','Sloveens'",
	"#INSTID#,107,0,'SM','Oceanic/indonesian','Samoan','Samoaans'",
	"#INSTID#,108,0,'SN','Negro-african','Shona','Shona'",
	"#INSTID#,109,0,'SO','Hamitic','Somali','Somalisch'",
	"#INSTID#,110,0,'SQ','Indo-european (other)','Albanian','Albanees'",
	"#INSTID#,111,0,'SR','Slavic','Serbian','Servisch'",
	"#INSTID#,112,0,'SS','Negro-african','Siswati','Swati'",
	"#INSTID#,113,0,'ST','Negro-african','Sesotho','Zuid-Sotho'",
	"#INSTID#,114,0,'SU','Oceanic/indonesian','Sundanese','Sundanees'",
	"#INSTID#,115,1,'SV','Germanic','Swedish','Zweeds'",
	"#INSTID#,116,0,'SW','Negro-african','Swahili','Swahili'",
	"#INSTID#,117,0,'TA','Dravidian','Tamil','Tamil'",
	"#INSTID#,118,0,'TE','Dravidian','Telugu','Telugu'",
	"#INSTID#,119,0,'TG','Iranian','Tajik','Tajik'",
	"#INSTID#,120,0,'TH','Asian','Thai','Thai'",
	"#INSTID#,121,0,'TI','Semitic','Tigrinya','Tigrinyaans'",
	"#INSTID#,122,0,'TK','Turkic/altaic','Turkmen','Turkmeens (Oostturks)'",
	"#INSTID#,123,0,'TL','Oceanic/indonesian','Tagalog','Tagalog'",
	"#INSTID#,124,0,'TN','Negro-african','Setswana','Tswana'",
	"#INSTID#,125,0,'TO','Oceanic/indonesian','Tonga','Tonga'",
	"#INSTID#,126,0,'TR','Turkic/altaic','Turkish','Turks'",
	"#INSTID#,127,0,'TS','Negro-african','Tsonga','Tsonga'",
	"#INSTID#,128,0,'TT','Turkic/altaic','Tatar','Tatar'",
	"#INSTID#,129,0,'TW','Negro-african','Twi','Twi'",
	"#INSTID#,130,0,'UK','Slavic','Ukrainian','Oekra&Iuml;ens'",
	"#INSTID#,131,0,'UR','Indian','Urdu','Urdu'",
	"#INSTID#,132,0,'UZ','Turkic/altaic','Uzbek','Uzbeeks'",
	"#INSTID#,133,0,'VI','Asian','Vietnamese','Vietnamees'",
	"#INSTID#,134,0,'VO','International aux.','Volapuk','Volap&uuml;k'",
	"#INSTID#,135,0,'WO','Negro-african','Wolof','Wolof'",
	"#INSTID#,136,0,'XH','Negro-african','Xhosa','Xhosa'",
	"#INSTID#,137,1,'YI','Germanic','Yiddish','Jiddish'",
	"#INSTID#,138,0,'YO','Negro-african','Yoruba','Yorouba'",
	"#INSTID#,139,0,'ZA','Asian','Zhuang','Zhuang'",
	"#INSTID#,140,0,'ZH','Asian','Chinese','Chinees'",
	"#INSTID#,141,0,'ZU','Negro-african','Zulu','Zulu'"
  )
)
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
