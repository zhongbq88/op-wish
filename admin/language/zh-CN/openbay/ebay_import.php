<?php
// Heading
$_['heading_title']                     = '导入项目';
$_['text_openbay']                      = 'OpenBay Pro';
$_['text_ebay']                         = 'eBay';

// Text
$_['text_sync_import_line1']            = '<strong>注意!</strong> 这将导入您的所有 eBay 商品，并在您的商店建立一个产品分類结构。 但我还是建议您在运行此选项前删除所有分類和商品. <br />该产品分類结构是正常的 eBay 商品分類，而不是您的店铺的商品分類 (如果您有一个 eBay 商店)。 您可以在不影响 eBay 商品的情况下重新命名，删除和编辑已导入的商品分类。';
$_['text_sync_import_line3']            = '您需要确保您的伺服器可以接受和处理大量的POST 数据。 1000个 eBay 商品约有40Mb大小, 您需要计算出您所需要的大小。 如果您调用失败，那么很可能您设置过小。您的PHP内存限制需约 128Mb。';
$_['text_sync_server_size']             = '目前您的服务器可以接受: ';
$_['text_sync_memory_size']             = '您的 PHP 内存限制： ';
$_['text_import_confirm']				= '这将导入您的所有 eBay 商品作为新商品，您是否确定? 这将不能还原! 请确定您已做了备份!';
$_['text_import_notify']				= '您的导入请求已经被送往处理. 在导入时，每1000个项目约需1小时。';
$_['text_import_images_msg1']           = '图像正在从 eBay等待导入/复制。如果数量不会再降低，请刷新此页。';
$_['text_import_images_msg2']           = '点击此处';
$_['text_import_images_msg3']           = '请等等. 可以在 <a href="http://shop.openbaypro.com/index.php?route=information/faq&topic=8_45"target="_blank"> 这里</a>找到为什么发生这种情况的更多信息';

// Entry
$_['entry_import_item_advanced']        = '获得进阶数据';
$_['entry_import_categories']         	= '导入类别';
$_['entry_import_description']			= '导入物料描述';
$_['entry_import']						= '导入 eBay 项目';

// Buttons
$_['button_import']						= '导入';

// Help
$_['help_import_item_advanced']        	= '如果导入重量、 尺寸、 ISBN 和其他将需要 10 倍时间。';
$_['help_import_categories']         	= '从eBay的分类在您的商店构建一个商品分類结构';
$_['help_import_description']         	= '这将导入全部内容包括 HTML、 访问计数器等';

// Error
$_['error_import']                   	= '加载失败';
$_['error_maintenance']					= '你的店是在维护模式下。导入将会失败 ！';
$_['error_ajax_load']					= '未能连接到服务器';
$_['error_validation']					= '您需要注册您的 API标记和启用模块。';