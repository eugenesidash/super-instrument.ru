<?php 
class ControllerUnishopColor extends Controller {

	public function index() {
		$token = '';
		
		if(isset($this->session->data['token'])) {
			$token = $this->session->data['token'];
		}
		
		if($token) {
			header('Content-type: text/css');
		
			$this->load->model('unishop/setting');
			$settings = $this->model_unishop_setting->getSetting();
			
			$main_color = ((isset($settings['custom_color_schema']) && isset($settings['custom_color_schema_color'])) ? $settings['custom_color_schema_color'] : '');
		
			$style = '';
			$style .= 'a, a:hover,'; 
			$style .= 'a:visited,'; 
			$style .= 'a:focus,';
			$style .= 'h1.heading span,'; 
			$style .= 'h2.heading span,'; 
			$style .= 'h3.heading span,'; 
			$style .= 'h4.heading span,';
			$style .= '#logo a > span,';
			$style .= '#top-links ul li a,';
			$style .= '#top .btn i,'; 
			$style .= '#top img,'; 
			$style .= '#top strong,';
			$style .= 'header .phone .fa-phone,';
			$style .= 'header .phone + .dropdown-menu .open_callback,';
			$style .= '#search .btn-lg,';
			$style .= '#search .cat_id button,';
			$style .= '.dropdown-menu li > a i,';
			$style .= '.breadcrumb i,';
			$style .= '#search_phrase span,';
			$style .= '#menu_wrap #menu.menu2 #category, #menu_wrap #menu.menu2 .btn-navbar,';
			$style .= '#menu_wrap #search .btn,';
			$style .= '#menu_wrap #phone .phone > div,';
			$style .= '#menu_wrap #account button i,';
			$style .= '#menu_wrap #cart > button i,';
			$style .= '.menu_links a,';
			$style .= '.home_banners > div > div i,';
			$style .= '.product_banners > div > div i,';
			$style .= '#list-view, #grid-view, #price-view,';
			$style .= '.rating i,';
			$style .= '#product .product_button .btn';
			$style .= '{color:#'.$main_color.';}';

			$style .= '#top-links ul li a:hover{color:#666}';
			$style .= '#menu:before {display:block;position:absolute;top:0;right:0;bottom:0;left:0;content:\'\';background: linear-gradient(to bottom, rgba(255, 255, 255, .3) 0%, transparent 100%);}';
			$style .= '#menu_wrap #menu:before {background:none;}';
			$style .= '#menu .nav > li > a{text-shadow:0 -1px 0 rgba(0, 0, 0, .5);}';
			$style .= '.menu_links a:hover {color:#666;}';
			$style .= '#cart.show.fly2 > .btn {animation: shadow 2s infinite ease-in-out;}';
			$style .= '@-webkit-keyframes shadow {from {box-shadow:0 0 0;} 50% {box-shadow:0 0 30px #'.$main_color.'} to {box-shadow:0 0 0;}}';
			$style .= '.slideshow .title:after {border-color:#'.$main_color.' transparent #'.$main_color.' #'.$main_color.';}';

			$style .= '#menu,';
			$style .= '#cart > .btn span,';
			$style .= '.slideshow .title,';
			$style .= '#manufacturer_module .heading,';
			$style .= '#manufacturer_module .heading:after,';
			$style .= '#filterpro_box .ui-slider .ui-slider-handle,'; 
			$style .= '#filterpro_box label input[type=\'checkbox\']:checked + span,'; 
			$style .= '#filterpro_box label input[type=\'radio\']:checked + span,';
			$style .= '.quick_order:hover,'; 
			$style .= '.quick_order:focus,'; 
			$style .= '.quick_order:active,';
			$style .= '.product-thumb:hover .cart .btn,'; 
			$style .= '.product-thumb .cart .in_cart,';
			$style .= '.pagination .active span,';
			$style .= 'input[type=\'radio\']:checked + label,'; 
			$style .= '.product-thumb input[type=\'checkbox\']:checked + label,';
			$style .= '#product input[type=\'radio\']:checked + label,'; 
			$style .= '#product input[type=\'checkbox\']:checked + label,';
			$style .= '.fly_callback,';
			$style .= '.fly_callback2';			
			$style .= '{background:#'.$main_color.' !important}';
		
			$style .= '.fly_callback:after {border-color:#'.$main_color.';}';

			$style .= '.product-thumb:hover .cart .btn, .product-thumb .cart .in_cart{border-color:#'.$main_color.'}';
			$style .= '.list-group a {color:#777;background:#f5f5f5;}';
			$style .= '.list-group a.active, .list-group a.active:hover, .list-group a:hover{color:#'.$main_color.'}';

			$style .= '.btn-primary, .btn-primary:hover, .btn-primary:active, .btn-primary:focus {position:relative;background:#'.$main_color.' !important; border:solid 1px #'.$main_color.' !important;}';
			$style .= '.btn-primary:after {display:block;position:absolute;top:0;bottom:0;left:0;right:0;content:\'\';transition: all ease-in-out .2s}';
			$style .= '.btn-primary:hover:after {background:rgba(0, 0, 0, .1)}';
			$style .= '.quick_order:hover, .quick_order:focus, .quick_order:active{border:solid 1px #'.$main_color.' !important; color:#fff !important; text-shadow: 0 1px 0 rgba(0, 0, 0, .3)}';
			
			$style .= '.product_sticker {background:#'.$main_color.'}';
			$style .= '.product_sticker span{border-color:#'.$main_color.' transparent #'.$main_color.' #'.$main_color.'}';

			$style .= 'input[type=\'radio\']:checked + label,';
			$style .= '.product-thumb input[type=\'checkbox\']:checked + label';
			$style .= '{border:solid 1px #'.$main_color.' !important}';
		
			$style .= '#product input[type=\'radio\']:checked + label,';
			$style .= '#product input[type=\'checkbox\']:checked + label';
			$style .= '{border:solid 1px #'.$main_color.'}';
			
			$style .= '#product .product_button .btn{}';

			$style .= '.pagination{margin:0;}';
			$style .= '.pagination a{font-weight:500;color:#'.$main_color.' !important; padding:6px 14px !important}';
			$style .= '.pagination .active span{border:solid 1px #'.$main_color.' !important;font-weight:500; padding:6px 14px !important}';

			$style .= 'footer {color:#eee;border-top:solid 3px #'.$main_color.'}';
			$style .= 'footer:before{background:#'.$main_color.'; opacity:.7}';
			$style .= 'footer hr{border-top:1px solid rgba(0, 0, 0 , .2);border-bottom:solid 1px rgba(255, 255, 255 , .2);margin:10px 0 15px;}';
			$style .= 'footer a{color:#eee;}';

			$style .= '@media (min-width:992px){#menu .nav > li > .dropdown-menu > div > ul > li > a {font-size:1.0em;color:#'.$main_color.';font-weight:500;} #menu.menu2 .nav li:hover > a {color:#'.$main_color.';}}';
			$style .= '@media (max-width:992px){#menu .nav{background:#'.$main_color.'}}';
		
			if(!file_exists(DIR_APPLICATION.'view/theme/unishop/stylesheet/color_schema.css')) {
				$style_file = fopen(DIR_APPLICATION.'view/theme/unishop/stylesheet/color_schema.css', 'x');
			} else {
				$style_file = fopen(DIR_APPLICATION.'view/theme/unishop/stylesheet/color_schema.css', 'w');
			}
			fwrite($style_file, $style);
			fclose($style_file);
		} else {
			$this->response->redirect($this->url->link('error/not_found', '', 'SSL'));
		}
	}
}
?>