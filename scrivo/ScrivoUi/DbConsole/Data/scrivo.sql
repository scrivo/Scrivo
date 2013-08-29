CREATE TABLE application_definition (
  instance_id INT(11) NOT NULL DEFAULT '0',
  application_definition_id INT(11) NOT NULL DEFAULT '0',
  page_definition_id INT(11) NOT NULL DEFAULT '0',
  title VARCHAR(50) NOT NULL DEFAULT '',
  description VARCHAR(255) NOT NULL DEFAULT '',
  action VARCHAR(250) DEFAULT NULL,
  type INT(2) DEFAULT '1',
  PRIMARY KEY  (instance_id,application_definition_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE asset (
  instance_id INT(11) NOT NULL DEFAULT '0',
  asset_id INT(11) NOT NULL DEFAULT '0',
  parent_id INT(11) NOT NULL DEFAULT '0',
  sequence_no INT(11) NOT NULL DEFAULT '0',
  type INT(11) NOT NULL DEFAULT '0',
  size INT(11) NOT NULL DEFAULT '0',
  date_created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  date_modified DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  title VARCHAR(255) NOT NULL DEFAULT '',
  location VARCHAR(255) NOT NULL DEFAULT '',
  mime_type VARCHAR(64) NOT NULL DEFAULT '',
  date_online DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  date_offline DATETIME DEFAULT NULL,
  PRIMARY KEY  (instance_id,asset_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE page_property_html (
  instance_id INT(11) NOT NULL DEFAULT '0',
  page_id INT(11) NOT NULL DEFAULT '0',
  version INT(11) NOT NULL DEFAULT '0',
  page_definition_tab_id INT(11) NOT NULL DEFAULT '0',
  raw_html MEDIUMTEXT NOT NULL,
  html MEDIUMTEXT NOT NULL,
  PRIMARY KEY  (instance_id,page_id,page_definition_tab_id,version)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE database_version (
  version_no INT(11) NOT NULL,
  date_updated DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (version_no)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE page (
  instance_id INT(11) NOT NULL DEFAULT '0',
  page_id INT(11) NOT NULL DEFAULT '0',
  version INT(11) NOT NULL DEFAULT '0',
  parent_id INT(11) NOT NULL DEFAULT '0',
  sequence_no INT(11) NOT NULL DEFAULT '0',
  type INT(11) NOT NULL DEFAULT '0',
  page_definition_id INT(11) NOT NULL DEFAULT '0',
  language_id INT(11) NOT NULL DEFAULT '0',
  date_created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  date_modified DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  title VARCHAR(255) DEFAULT NULL,
  description TEXT NOT NULL,
  keywords TEXT NOT NULL,
  javascript TEXT NOT NULL,
  stylesheet TEXT NOT NULL,
  date_online DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  date_offline DATETIME DEFAULT NULL,
  has_staging INT(1) DEFAULT '0',
  PRIMARY KEY  (instance_id,page_id,version)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE page_property (
  instance_id INT(11) NOT NULL DEFAULT '0',
  page_id INT(11) NOT NULL DEFAULT '0',
  version INT(11) NOT NULL DEFAULT '0',
  page_property_definition_id INT(11) NOT NULL DEFAULT '0',
  value MEDIUMTEXT NOT NULL,
  PRIMARY KEY  (instance_id,page_id,page_property_definition_id,version)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE object_role (
  instance_id INT(11) NOT NULL DEFAULT '0',
  role_id INT(11) NOT NULL DEFAULT '0',
  page_id INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (instance_id,role_id,page_id),
  KEY DOCUMENT_ROLE_ROLE_ID (instance_id,role_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE id_label (
  instance_id INT(11) NOT NULL,
  id INT(11) NOT NULL,
  label VARCHAR(32) NOT NULL,
  PRIMARY KEY  (instance_id,id),
  UNIQUE KEY ID_LABEL_LABEL (instance_id,label)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE instance (
  instance_id INT(11) NOT NULL DEFAULT '0',
  www_root VARCHAR(250) NOT NULL DEFAULT '',
  document_root VARCHAR(250) NOT NULL DEFAULT '',
  description TEXT NOT NULL,
  PRIMARY KEY  (instance_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE language (
  instance_id INT(11) NOT NULL DEFAULT '0',
  language_id INT(11) NOT NULL DEFAULT '0',
  short_list INT(11) NOT NULL DEFAULT '0',
  iso_code VARCHAR(25) NOT NULL DEFAULT '',
  family VARCHAR(50) NOT NULL DEFAULT '',
  name_en VARCHAR(50) NOT NULL DEFAULT '',
  name_nl VARCHAR(50) NOT NULL DEFAULT '',
  PRIMARY KEY  (instance_id,language_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE item_list (
  instance_id INT(11) NOT NULL DEFAULT '0',
  page_id INT(11) NOT NULL DEFAULT '0',
  version INT(11) NOT NULL DEFAULT '0',
  page_definition_tab_id INT(11) NOT NULL DEFAULT '0',
  folder_id INT(11) NOT NULL DEFAULT '0',
  item_list_id INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (instance_id,item_list_id,version)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE list_item (
  instance_id INT(11) NOT NULL DEFAULT '0',
  list_item_id INT(11) NOT NULL DEFAULT '0',
  version INT(11) NOT NULL DEFAULT '0',
  parent_id INT(11) NOT NULL DEFAULT '0',
  link_id INT(11) NOT NULL DEFAULT '0',
  sequence_no INT(11) NOT NULL DEFAULT '0',
  list_item_definition_id INT(11) NOT NULL DEFAULT '0',
  item_list_id INT(11) NOT NULL DEFAULT '0',
  title VARCHAR(255) NOT NULL DEFAULT '',
  date_created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  date_offline DATETIME DEFAULT NULL,
  date_online DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  date_modified DATETIME DEFAULT '0000-00-00 00:00:00',
  page_id INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (instance_id,list_item_id,version)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE list_item_property_definition (
  instance_id INT(11) NOT NULL DEFAULT '0',
  list_item_property_definition_id INT(11) NOT NULL DEFAULT '0',
  application_definition_id INT(11) NOT NULL DEFAULT '0',
  list_item_definition_id INT(11) NOT NULL DEFAULT '0',
  sequence_no INT(11) NOT NULL DEFAULT '0',
  type VARCHAR(10) NOT NULL DEFAULT '',
  type_data TEXT NOT NULL,
  php_key VARCHAR(50) NOT NULL DEFAULT '',
  title VARCHAR(50) NOT NULL DEFAULT '',
  in_list INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (instance_id,list_item_property_definition_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE parent_list_item_definitions (
  instance_id INT(11) NOT NULL DEFAULT '0',
  list_item_definition_id INT(11) NOT NULL DEFAULT '0',
  parent_list_item_definition_id INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (instance_id,list_item_definition_id,parent_list_item_definition_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE list_item_definition (
  instance_id INT(11) NOT NULL DEFAULT '0',
  list_item_definition_id INT(11) NOT NULL DEFAULT '0',
  sequence_no INT(11) NOT NULL DEFAULT '0',
  application_definition_id INT(11) NOT NULL DEFAULT '0',
  page_definition_id INT(11) NOT NULL DEFAULT '0',
  title VARCHAR(255) NOT NULL DEFAULT '',
  icon VARCHAR(255) NOT NULL DEFAULT '',
  php_key VARCHAR(50) NOT NULL DEFAULT '',
  title_width INT(11) NOT NULL DEFAULT '0',
  title_label VARCHAR(50) DEFAULT '',
  PRIMARY KEY  (instance_id,list_item_definition_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE list_item_property (
  instance_id INT(11) NOT NULL DEFAULT '0',
  list_item_id INT(11) NOT NULL DEFAULT '0',
  list_item_property_definition_id INT(11) NOT NULL DEFAULT '0',
  version INT(11) NOT NULL DEFAULT '0',
  value MEDIUMTEXT,
  page_id INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (instance_id,list_item_id,list_item_property_definition_id,version)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE login_events (
  instance_id INT(11) NOT NULL DEFAULT '0',
  date_login DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  user_id INT(11) NOT NULL DEFAULT '0',
  user_satus INT(11) NOT NULL DEFAULT '0',
  return_code INT(11) NOT NULL DEFAULT '0',
  remote_address VARCHAR(15) NOT NULL DEFAULT '',
  access_key VARCHAR(255) NOT NULL DEFAULT '',
  KEY login_events (instance_id,access_key)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE modified_touched (
  instance_id INT(11) NOT NULL,
  modified_id INT(11) NOT NULL,
  date_modified_touched INT(1) NOT NULL,
  touched_id INT(11) NOT NULL,
  touched_type INT(1) NOT NULL,
  PRIMARY KEY  (instance_id,modified_id,touched_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE pretty_url (
  instance_id INT(11) NOT NULL,
  type INT(1) NOT NULL,
  page_id INT(11) NOT NULL,
  original_url VARCHAR(250) NOT NULL,
  translated_url TEXT NOT NULL,
  PRIMARY KEY  (instance_id,page_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE role (
  instance_id INT(11) NOT NULL DEFAULT '0',
  role_id INT(11) NOT NULL DEFAULT '0',
  type INT(11) NOT NULL DEFAULT '0',
  title VARCHAR(50) NOT NULL DEFAULT '',
  description TEXT NOT NULL,
  PRIMARY KEY  (instance_id,role_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE search_index (
  instance_id INT(11) NOT NULL DEFAULT '0',
  page_id INT(11) NOT NULL DEFAULT '0',
  url VARCHAR(255) NOT NULL DEFAULT '',
  type VARCHAR(64) NOT NULL DEFAULT '',
  data MEDIUMTEXT NOT NULL,
  title VARCHAR(255) NOT NULL DEFAULT '',
  description TEXT NOT NULL,
  body_text MEDIUMTEXT NOT NULL,
  PRIMARY KEY  (instance_id,URL,type),
  FULLTEXT KEY url (url)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE search_words (
  instance_id INT(11) NOT NULL,
  search_word VARCHAR(255) NOT NULL,
  date_search DATETIME NOT NULL,
  remote_address VARCHAR(15) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE seq (
  seq INT(11) NOT NULL auto_increment,
  PRIMARY KEY  (seq)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE page_definition (
  instance_id INT(11) NOT NULL DEFAULT '0',
  page_definition_id INT(11) NOT NULL DEFAULT '0',
  title VARCHAR(50) NOT NULL DEFAULT '',
  description TEXT NOT NULL,
  action VARCHAR(255) NOT NULL DEFAULT '',
  search_index_rule VARCHAR(64) NOT NULL DEFAULT '',
  config_only INT(11) NOT NULL DEFAULT '0',
  type_set VARCHAR(64) NOT NULL DEFAULT '',
  default_tab_id INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (instance_id,page_definition_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE page_definition_tab (
  instance_id INT(11) NOT NULL DEFAULT '0',
  page_definition_tab_id INT(11) NOT NULL DEFAULT '0',
  page_definition_id INT(11) NOT NULL DEFAULT '0',
  sequence_no INT(11) NOT NULL DEFAULT '0',
  title VARCHAR(50) NOT NULL DEFAULT '',
  php_key VARCHAR(50) NOT NULL DEFAULT '',
  css_selector VARCHAR(50) NOT NULL DEFAULT '',
  page_css VARCHAR(255) NOT NULL DEFAULT '',
  stylesheet TEXT NOT NULL,
  application_definition_id INT(11) DEFAULT NULL,
  css_id VARCHAR(25) NOT NULL DEFAULT '',
  initial_content MEDIUMTEXT NOT NULL,
  PRIMARY KEY  (instance_id,page_definition_tab_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE page_definition_hints (
  instance_id INT(11) NOT NULL DEFAULT '0',
  parent_page_definition_id INT(11) NOT NULL DEFAULT '0',
  page_definition_id INT(11) NOT NULL DEFAULT '0',
  MAX_NO_OF_CHILDREN INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (instance_id,parent_page_definition_id,page_definition_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE page_property_definition (
  instance_id INT(11) NOT NULL DEFAULT '0',
  page_property_definition_id INT(11) NOT NULL DEFAULT '0',
  page_definition_id INT(11) NOT NULL DEFAULT '0',
  sequence_no INT(11) NOT NULL DEFAULT '0',
  type VARCHAR(10) NOT NULL DEFAULT '',
  type_data TEXT NOT NULL,
  php_key VARCHAR(50) NOT NULL DEFAULT '',
  title VARCHAR(50) NOT NULL DEFAULT '',
  in_list INT(11) NOT NULL DEFAULT '0',
  page_definition_tab_id INT(11) DEFAULT '0',
  PRIMARY KEY  (instance_id,page_property_definition_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE ui_lang (
  instance_id INT(11) NOT NULL DEFAULT '0',
  iso_code char(8) NOT NULL DEFAULT '',
  description VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY  (instance_id,iso_code)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE user (
  instance_id INT(11) NOT NULL DEFAULT '0',
  user_id INT(11) NOT NULL DEFAULT '0',
  status INT(11) NOT NULL DEFAULT '0',
  user_code VARCHAR(50) NOT NULL DEFAULT '',
  password VARCHAR(255) NOT NULL DEFAULT '',
  given_name VARCHAR(50) NOT NULL DEFAULT '',
  family_name_prefix VARCHAR(50) NOT NULL DEFAULT '',
  family_name VARCHAR(100) NOT NULL DEFAULT '',
  email_address VARCHAR(255) NOT NULL DEFAULT '',
  custom_data MEDIUMTEXT,
  PRIMARY KEY  (instance_id,user_id),
  KEY USER_USERCODE (instance_id,user_code)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE user_change_password (
  instance_id INT(11) NOT NULL,
  user_id INT(11) NOT NULL,
  date_request DATETIME NOT NULL,
  password VARCHAR(50) NOT NULL,
  access_key VARCHAR(255) NOT NULL,
  email_address VARCHAR(255) NOT NULL,
  PRIMARY KEY  (instance_id,user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE user_role (
  instance_id INT(11) NOT NULL DEFAULT '0',
  role_id INT(11) NOT NULL DEFAULT '0',
  user_id INT(11) NOT NULL DEFAULT '0',
  is_publisher INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (instance_id,role_id,user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  