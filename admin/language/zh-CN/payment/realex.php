<?php
// Heading
$_['heading_title']					 = 'Realex Redirect';

// Text
$_['text_payment']				  	 = '支付管理';
$_['text_success']					 = '成功: 您已修改了 Realex 帐户的详细信息 ！';
$_['text_edit']                      = '编辑Realex Redirect';
$_['text_live']						 = '在线模式(Live)';
$_['text_demo']						 = '演示';
$_['text_card_type']				 = '信用卡类型';
$_['text_enabled']					 = '已启用';
$_['text_use_default']				 = '使用默认值';
$_['text_merchant_id']				 = '商戶 ID';
$_['text_subaccount']				 = '子帐户';
$_['text_secret']					 = '共用密钥';
$_['text_card_visa']				 = 'Visa';
$_['text_card_master']				 = 'MasterCard';
$_['text_card_amex']				 = 'American Express';
$_['text_card_switch']				 = 'Switch/Maestro';
$_['text_card_laser']				 = 'Laser';
$_['text_card_diners']				 = 'Diners';
$_['text_capture_ok']				 = '已成功捕获';
$_['text_capture_ok_order']			 = '已成功捕获，订单状态更新为成功 -已解决';
$_['text_rebate_ok']				 = '已成功回赠';
$_['text_rebate_ok_order']			 = '已成功回赠，订单状态更新为已回赠';
$_['text_void_ok']					 = '已成功取消订单，订单状态更新为无效';
$_['text_settle_auto']				 = '自动';
$_['text_settle_delayed']			 = '延迟';
$_['text_settle_multi']				 = '多种';
$_['text_url_message']				 = '您必须向您的 Realex 帐户管理员提供商店 URL 才能启动';
$_['text_payment_info']				 = '支付信息';
$_['text_capture_status']			 = '款项已捕获';
$_['text_void_status']				 = '款项已无效';
$_['text_rebate_status']			 = '款项已回赠';
$_['text_order_ref']				 = '订单参考';
$_['text_order_total']				 = '授权总数';
$_['text_total_captured']			 = '捕获总数';
$_['text_transactions']				 = '交易记录';
$_['text_column_amount']			 = '金额';
$_['text_column_type']				 = '类型';
$_['text_column_date_added']		 = '已创建';
$_['text_confirm_void']				 = '您确定要取消款项？';
$_['text_confirm_capture']			 = '您确定要捕获款项？';
$_['text_confirm_rebate']			 = '您确定要回赠款项？';
$_['text_realex']					 = '<a target="_BLANK"href="http://www.realexpayments.co.uk/partner-refer?id=opencart"> <img src="view/image/payment/realex.png"alt="Realex"title="Realex"style="border: 1px solid #EEEEEE;"/></a>';

// Entry
$_['entry_merchant_id']				 = '商戶 ID';
$_['entry_secret']					 = '共用密钥';
$_['entry_rebate_password']			 = '回赠密码';
$_['entry_total']					 = '总计';
$_['entry_sort_order']				 = '排序顺序';
$_['entry_geo_zone']				 = '地理区域';
$_['entry_status']					 = '状态';
$_['entry_debug']					 = '调试日志记录';
$_['entry_live_demo']				 = '在线/演示';
$_['entry_auto_settle']				 = '结算类型';
$_['entry_card_select']				 = '选择信用卡';
$_['entry_tss_check']				 = 'TSS 检查';
$_['entry_live_url']				 = '在线连接 URL';
$_['entry_demo_url']				 = '演示连接 URL';
$_['entry_status_success_settled']	 = '成功-已解决';
$_['entry_status_success_unsettled'] = '成功 - 未解决';
$_['entry_status_decline']			 = '拒绝';
$_['entry_status_decline_pending']	 = '拒绝 - offline auth';
$_['entry_status_decline_stolen']	 = '拒绝-丢失或被盗卡';
$_['entry_status_decline_bank']		 = '拒绝-银行错误';
$_['entry_status_void']				 = '已无效';
$_['entry_status_rebate']			 = '已回赠';
$_['entry_notification_url']		 = '通知 URL';

// Help
$_['help_total']					 = '需要最低结帐金额才能启动支付模组';
$_['help_card_select']				 = '将他们重定向之前要求用户选择信用卡类型';
$_['help_notification']				 = '您需要提供此 URL 到 Realex 来获取付款通知';
$_['help_debug']					 = '启用调试会将敏感数据写到日志文件。除非另有指示，应该设定为停用。';
$_['help_dcc_settle']				 = '如果您的子账户是启用DCC，您必须使用自动结算';

// Tab
$_['tab_api']					     = 'API 的详细信息';
$_['tab_account']		     		 = '帐户';
$_['tab_order_status']				 = '订单状态';
$_['tab_payment']					 = '支付管理设置';
$_['tab_advanced']					 = '进阶';

// Button
$_['button_capture']				 = '捕获模式';
$_['button_rebate']					 = '退稅/退款';
$_['button_void']					 = '无效';

// Error
$_['error_merchant_id']				 = '必须输入商戶 ID';
$_['error_secret']					 = '必须输入共用密钥';
$_['error_live_url']				 = '必需填写在线URL';
$_['error_demo_url']				 = '必需填写演示URL';
$_['error_data_missing']			 = '数据丢失';
$_['error_use_select_card']			 = '您必需启用"选择卡"来路由子帐户的卡类型';