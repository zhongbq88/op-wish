<?php
// Heading
$_['heading_title']					= 'First Data EMEA 网页服务 API';

// Text
$_['text_firstdata_remote']			= '<img src="view/image/payment/firstdata.png"alt="First Data"title="First Data"style="border: 1px solid #EEEEEE;"/>';
$_['text_payment']					= '支付管理';
$_['text_success']					= '成功: 您已修改了First Data帐户详细信息 ！';
$_['text_edit']                     = '编辑First Data EMEA网页服务 API';
$_['text_card_type']				= '信用卡类型';
$_['text_enabled']					= '已启用';
$_['text_merchant_id']				= '商店ID';
$_['text_subaccount']				= '子帐户';
$_['text_user_id']					= '用户 ID';
$_['text_capture_ok']				= '成功捕获';
$_['text_capture_ok_order']			= '已成功捕获，订单状态更新到成功 -已解决';
$_['text_refund_ok']				= '成功退款';
$_['text_refund_ok_order']			= '已成功退款，订单状态更新为已退款';
$_['text_void_ok']					= '已成功取消订单，订单状态更新为无效';
$_['text_settle_auto']				= '銷售模式';
$_['text_settle_delayed']			= '準備身份验证';
$_['text_mastercard']				= '万事达卡';
$_['text_visa']						= 'Visa';
$_['text_diners']					= 'Diners';
$_['text_amex']						= '美国运通';
$_['text_maestro']					= 'Maestro';
$_['text_payment_info']				= '支付信息';
$_['text_capture_status']			= '款项已捕获';
$_['text_void_status']				= '款项已无效';
$_['text_refund_status']			= '款项已退款';
$_['text_order_ref']				= '订单 ref';
$_['text_order_total']				= '授权总数';
$_['text_total_captured']			= '捕获总数';
$_['text_transactions']				= '交易记录';
$_['text_column_amount']			= '金额';
$_['text_column_type']				= '类型';
$_['text_column_date_added']		= '已创建';
$_['text_confirm_void']				= '你确定要取消款项？';
$_['text_confirm_capture']			= '你确定要捕获款项？';
$_['text_confirm_refund']			= '你确定要退还款项？';

// Entry
$_['entry_certificate_path']		= '证书路径';
$_['entry_certificate_key_path']	= '专用密钥的路径';
$_['entry_certificate_key_pw']		= '专用密钥密码';
$_['entry_certificate_ca_path']		= 'CA 路径';
$_['entry_merchant_id']				= '商店ID';
$_['entry_user_id']					= '用户 ID';
$_['entry_password']				= '密码';
$_['entry_total']					= '总计';
$_['entry_sort_order']				= '排序顺序';
$_['entry_geo_zone']				= '地理区域';
$_['entry_status']					= '状态';
$_['entry_debug']					= '调试日志记录';
$_['entry_auto_settle']				= '结算类型';
$_['entry_status_success_settled']	= '成功-已解决';
$_['entry_status_success_unsettled'] = '成功 - 没解决';
$_['entry_status_decline']			 = '拒绝';
$_['entry_status_void']				 = '已无效';
$_['entry_status_refund']			 = '已退款';
$_['entry_enable_card_store']		 = '启用卡存储代幣';
$_['entry_cards_accepted']			 = '卡类型已接受';

// Help
$_['help_total']					 = '需要最低结帐金额才能启动支付模组';
$_['help_certificate']				 = '证书和专用钥密应存储在您的公共网页文件夹之外';
$_['help_card_select']				 = '将他们重定向之前要求用户选择其卡类型';
$_['help_notification']				 = '您需要提供此 URL 給First Data 获得付款通知';
$_['help_debug']					 = '启用调试会将敏感数据写到日志文件。除非另有指示，应该设定为停用。';
$_['help_settle']					 = '如果您使用pre-auth操作，您必须 3-5 天內完成post- auth 操作否则为您的交易将被取消';

// Tab
$_['tab_account']					 = 'API 信息';
$_['tab_order_status']				 = '订单状态';
$_['tab_payment']					 = '支付管理设置';

// Button
$_['button_capture']				= '捕获模式';
$_['button_refund']					= '退款';
$_['button_void']					= '无效';

// Error
$_['error_merchant_id']				= '必须输入商店ID';
$_['error_user_id']					= '必须输入用户 ID ';
$_['error_password']				= '必须输入密码';
$_['error_certificate']				= '必须输入证书路径';
$_['error_key']						= '必须输入证书密钥';
$_['error_key_pw']					= '必须输入证书密钥密码';
$_['error_ca']						= '必须输入证书颁发机构 (CA)';