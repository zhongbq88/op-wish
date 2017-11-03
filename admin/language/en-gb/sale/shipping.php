<?php
// Heading
$_['heading_title']        = 'Product Returns';

// Text
$_['text_success']         = 'Success: You have modified returns!';
$_['text_list']            = 'Product Return List';
$_['text_add']             = 'Add Product Return';
$_['text_edit']            = 'Edit Product Return';
$_['text_opened']          = 'Opened';
$_['text_unopened']        = 'Unopened';
$_['text_order']           = 'Order Information';
$_['text_product']         = 'Product Information &amp; Reason for Return';
$_['text_history']         = 'History';
$_['text_history_add']     = 'Add History';

// Column
$_['column_return_id']     = 'Return ID';
$_['column_order_id']      = 'Order ID';
$_['column_customer']      = 'Customer';
$_['column_product']       = 'Product';
$_['column_model']         = 'Model';
$_['column_status']        = 'Status';
$_['column_date_added']    = 'Date Added';
$_['column_date_modified'] = 'Date Modified';
$_['column_comment']       = 'Comment';
$_['column_notify']        = 'Customer Notified';
$_['column_action']        = 'Action';

// Entry
$_['entry_customer']       = 'Customer';
$_['entry_order_id']       = 'Order ID';
$_['entry_date_ordered']   = 'Order Date';
$_['entry_firstname']      = 'First Name';
$_['entry_lastname']       = 'Last Name';
$_['entry_email']          = 'E-Mail';
$_['entry_telephone']      = 'Telephone';
$_['entry_product']        = 'Product';
$_['entry_model']          = 'Model';
$_['entry_quantity']       = 'Quantity';
$_['entry_opened']         = 'Opened';
$_['entry_comment']        = 'Comment';
$_['entry_return_reason']  = 'Return Reason';
$_['entry_return_action']  = 'Return Action';
$_['entry_return_status']  = 'Return Status';
$_['entry_notify']         = 'Notify Customer';
$_['entry_return_id']      = 'Return ID';
$_['entry_date_added']     = 'Date Added';
$_['entry_date_modified']  = 'Date Modified';

// Help
$_['help_product']         = '(Autocomplete)';

// Error
$_['error_warning']        = 'Warning: Please check the form carefully for errors!';
$_['error_permission']     = 'Warning: You do not have permission to modify returns!';
$_['error_order_id']       = 'Order ID required!';
$_['error_firstname']      = 'First Name must be between 1 and 32 characters!';
$_['error_lastname']       = 'Last Name must be between 1 and 32 characters!';
$_['error_email']          = 'E-Mail Address does not appear to be valid!';
$_['error_telephone']      = 'Telephone must be between 3 and 32 characters!';
$_['error_product']        = 'Product Name must be greater than 3 and less than 255 characters!';
/*$_['shipping_array']             = array(
                                            "亚洲"=>array(
													0=> array('value'=>"AE",'name'=>'阿联酋 - AE'
													),
													1=> array('value'=>"AF",'name'=>'阿富汗 - AF'),
                 2=> array('value'=>"AM",'name'=>'亚美尼亚 - AM'),
                  3=> array('value'=>"AZ",'name'=>'阿塞拜疆 - AZ'),
                 4=> array('value'=>"BD",'name'=>'孟加拉 - BD'),
                 5=> array('value'=>"BH",'name'=>'巴林 - BH'),
                  6=> array('value'=>"BN",'name'=>'文莱 - BN'),
                  7=> array('value'=>"BT",'name'=>'不丹 - BT'),
                  8=> array('value'=>"CC",'name'=>'科科斯（基林）群岛 - CC'),
                  9=> array('value'=>"CN",'name'=>'中国 - CN'),
                  10=> array('value'=>"GE",'name'=>'格鲁吉亚 - GE'),
                  11=> array('value'=>"HK",'name'=>'香港 - HK'),
                  12=> array('value'=>"ID",'name'=>'印度尼西亚 - ID'),
                  13=> array('value'=>"IL",'name'=>'以色列 - IL'),
                  14=> array('value'=>"IN",'name'=>'印度 - IN'),
                  15=> array('value'=>"IO",'name'=>'英属印度洋领地 - IO'),
                  16=> array('value'=>"IQ",'name'=>'伊拉克 - IQ'),
                  17=> array('value'=>"IR",'name'=>'伊朗 - IR'),
                  18=> array('value'=>"JO",'name'=>'约旦 - JO'),
                  19=> array('value'=>"JP",'name'=>'日本 - JP'),
                  20=> array('value'=>"KG",'name'=>'吉尔吉斯坦 - KG'),
                  21=> array('value'=>"KH",'name'=>'柬埔寨 - KH'),
                  22=> array('value'=>"KP",'name'=>'朝鲜 - KP'),
                  23=> array('value'=>"KR",'name'=>'韩国 - KR'),
                  24=> array('value'=>"KW",'name'=>'科威特 - KW'),
                  25=> array('value'=>"KZ",'name'=>'哈萨克 - KZ'),
                  1=> array('value'=>"LA",'name'=>'老挝 - LA'),
                  1=> array('value'=>"LB",'name'=>'黎巴嫩 - LB'),
                  1=> array('value'=>"LK",'name'=>'斯里兰卡 - LK'),
                  1=> array('value'=>"MM",'name'=>'缅甸 - MM'),
                  1=> array('value'=>"MN",'name'=>'蒙古 - MN'),
                  1=> array('value'=>"MO",'name'=>'澳门 - MO'),
                  1=> array('value'=>"MV",'name'=>'马尔代夫 - MV'),
                  1=> array('value'=>"MY",'name'=>'马来西亚 - MY'),
                  1=> array('value'=>"NP",'name'=>'尼泊尔 - NP'),
                  1=> array('value'=>"OM",'name'=>'阿曼 - OM'),
                  1=> array('value'=>"PH",'name'=>'菲律宾 - PH'),
                  1=> array('value'=>"PK",'name'=>'巴基斯坦 - PK'),
                  1=> array('value'=>"PS",'name'=>'巴勒斯坦 - PS'),
                  1=> array('value'=>"QA",'name'=>'卡塔尔 - QA'),
                  1=> array('value'=>"SA",'name'=>'沙特阿拉伯 - SA'),
                  1=> array('value'=>"SG",'name'=>'新加坡 - SG'),
                  1=> array('value'=>"SY",'name'=>'叙利亚 - SY'),
                  1=> array('value'=>"TH",'name'=>'泰国 - TH'),
                  1=> array('value'=>"TJ",'name'=>'塔吉克斯坦 - TJ'),
                  1=> array('value'=>"TL",'name'=>'东帝汶 - TL'),
                  1=> array('value'=>"TM",'name'=>'土库曼 - TM'),
                  1=> array('value'=>"TR",'name'=>'土耳其 - TR'),
                  1=> array('value'=>"TW",'name'=>'台湾省 - TW'),
                  1=> array('value'=>"UZ",'name'=>'乌兹别克 - UZ'),
                  1=> array('value'=>"VN",'name'=>'越南 - VN'),
                  1=> array('value'=>"YE",'name'=>'也门 - YE')
												)
											)
                                            
);*/