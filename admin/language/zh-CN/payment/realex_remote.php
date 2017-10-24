<?php
// Heading
$_['heading_title']					= 'Realex Remote';

// Text
$_['text_payment']					= '支付管理';
$_['text_success']					= '成功: 您已修改了 Realex 帐户的详细信息 ！';
$_['text_edit']                     = '编辑 Realex Remote';
$_['text_card_type']				= '信用卡类型';
$_['text_enabled']					= '已启用';
$_['text_use_default']				= '使用默认值';
$_['text_merchant_id']				= '商戶 ID';
$_['text_subaccount']				= '子帐户';
$_['text_secret']					= '共用密钥';
$_['text_card_visa']				= 'Visa';
$_['text_card_master']				= 'Mastercard';
$_['text_card_amex']				= 'American Express';
$_['text_card_switch']				= 'Switch/Maestro';
$_['text_card_laser']				= 'Laser';
$_['text_card_diners']				= 'Diners';
$_['text_capture_ok']				= '已成功捕获';
$_['text_capture_ok_order']			= '已成功捕获，订单状态更新为成功 -已解决';
$_['text_rebate_ok']				= '已成功回赠';
$_['text_rebate_ok_order']			= '已成功回赠，订单状态更新为已回赠';
$_['text_void_ok']					= '已成功取消订单，订单状态更新为无效';
$_['text_settle_auto']				= '自动';
$_['text_settle_delayed']			= '延迟';
$_['text_settle_multi']				= '多种';
$_['text_ip_message']				= '您必须提供您的服务器 IP 地址到您的 Realex 帐户管理员才能启动';
$_['text_payment_info']				= '支付信息';
$_['text_capture_status']			= '款项已捕获';
$_['text_void_status']				= '款项已无效';
$_['text_rebate_status']			= '款项已回赠';
$_['text_order_ref']				= '订单参考';
$_['text_order_total']				= '授权总数';
$_['text_total_captured']			= '捕获总数';
$_['text_transactions']				= '交易记录';
$_['text_confirm_void']				= '您确定要取消款项？';
$_['text_confirm_capture']			= '您确定要捕获款项？';
$_['text_confirm_rebate']			= '您确定要回赠款项？';
$_['text_realex_remote']			= '<a target="_BLANK"href="http://www.realexpayments.co.uk/partner-refer?id=opencart"> <img src="view/image/payment/realex.png"alt="Realex"title="Realex"style="border: 1px solid #EEEEEE;"/></a>';

// Column
$_['text_column_amount']			= '金额';
$_['text_column_type']				= '类型';
$_['text_column_date_added']		= '已创建';

// Entry
$_['entry_merchant_id']				= '商戶 ID';
$_['entry_secret']					= '共用密钥';
$_['entry_rebate_password']			= '回赠密码';
$_['entry_total']					= '总计';
$_['entry_sort_order']				= '排序顺序';
$_['entry_geo_zone']				= '地理区域';
$_['entry_status']					= '状态';
$_['entry_debug']					= '调试日志记录';
$_['entry_auto_settle']				= '结算类型';
$_['entry_tss_check']				= 'TSS 检查';
$_['entry_card_data_status']		= '卡信息日志记录';
$_['entry_3d']						= '启用 3D secure';
$_['entry_liability_shift']			= '接受非责任转移方案';
$_['entry_status_success_settled']	= '成功-已解决';
$_['entry_status_success_unsettled'] = '成功 - 没解决';
$_['entry_status_decline']			= '拒绝';
$_['entry_status_decline_pending']	= '拒绝 - offline auth';
$_['entry_status_decline_stolen']	= '拒绝-丢失或被盗卡';
$_['entry_status_decline_bank']		= '拒绝-银行错误';
$_['entry_status_void']				= '已无效';
$_['entry_status_rebate']			= '已回赠';

// Help
$_['help_total']					= '需要最低结帐金额才能启动支付模组';
$_['help_card_select']				= '将他们重定向之前要求用户选择其卡类型';
$_['help_notification']				= '您需要提供此 URL 到 Realex 来获取付款通知';
$_['help_debug']					= '启用调试会将敏感数据写到日志文件。除非另有指示，应该设定为停用。';
$_['help_liability']				= '接受责任就意味着用户失败 3D secure时，你依然会接受付款。';
$_['help_card_data_status']			= '记录最后 4 个卡位数字、 过期、 名称、 类型和发行银行信息';

// Tab
$_['tab_api']					    = 'API 的详细信息';
$_['tab_account']				    = '帐户';
$_['tab_order_status']				= '订单状态';
$_['tab_payment']					= '支付管理设置';

// Button
$_['button_capture']				= '捕获';
$_['button_rebate']					= '退稅/退款';
$_['button_void']					= '无效';

// Error
$_['error_merchant_id']				= '必须输入商戶 ID';
$_['error_secret']					= '要求共享机密';