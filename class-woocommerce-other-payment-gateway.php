<?php 
ob_start();

add_action( 'added_post_meta', 'mp_sync_on_product_save', 10, 4 );
add_action( 'updated_post_meta', 'mp_sync_on_product_save', 10, 4 );
function mp_sync_on_product_save( $meta_id, $post_id, $meta_key, $meta_value ) {
    if ( $meta_key == '_edit_lock' ) { // we've been editing the post
        if ( get_post_type( $post_id ) == 'product' ) { // we've been editing a product
            $_product = wc_get_product( $post_id );
			   //$variations = $product->get_available_variations();
			    $option="woocommerce_billecta_payment_settings";
$out=get_option( $option, $default = false );

			  // $_pf = new WC_Product_Factory(); 
			  // $_pro $this->init_settings();duct = $_pf->get_product($post_id);
			   global $wpdb;
			   $CreditorPublicId=$out['CreditorPublicId'];
			   $product_id=$post_id;
			 $mode=$out['mode'];
			
				 if($mode=="yes")
				 {
					$apiurl= "https://api.billecta.com";
				 }else{
					 
					$apiurl="https://apitest.billecta.com";
					 
				 }

			  // $apiurl="https://apitest.billecta.com";
			  	 $user= $out['username'];
  $pass=$out['password'];
		//print_r($_POST);
		  $authentication = base64_encode("$user:$pass");
		// $this->init_settings();
		 	//echo   get_option( 'username' );
			//		   get_option( 'password' );
			//			 get_option( 'CreditorPublicId' );
		// exit;
		 	   $title= $_product->get_title();
 $price=$_product->get_price();
$desc= $_product->get_description();
$status= $_product->get_status();
if($status=="publish"){
	$status=true;
	
}else{
	$status=false;
	
}
function getall_products($url,$request,$authentication)
    {
       //$authentication = base64_encode("dag@kreativinsikt.se:Stockholm66");
	   $ch = curl_init($url);
        $options = array(
                CURLOPT_RETURNTRANSFER => true,         // return web page
                CURLOPT_HEADER         => false,        // don't return headers
                CURLOPT_FOLLOWLOCATION => false,         // follow redirects
               // CURLOPT_ENCODING       => "utf-8",           // handle all encodings
			   
                CURLOPT_AUTOREFERER    => true,         // set referer on redirect
                CURLOPT_CONNECTTIMEOUT => 20,          // timeout on connect
                CURLOPT_TIMEOUT        => 20,          // timeout on response
                CURLOPT_POST            => 0,    // this are my post vars
                CURLOPT_SSL_VERIFYHOST => 0,            // don't verify ssl
                CURLOPT_SSL_VERIFYPEER => false,        //
                CURLOPT_VERBOSE        => 1,
                CURLOPT_HTTPHEADER     => array(
				"Authorization: Basic $authentication",
                    "Content-Type: application/json")    
        );
		//print_r($options);
        curl_setopt_array($ch,$options);
        $data = curl_exec($ch);
	
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        //echo $curl_errno;
       // echo $curl_error;
        curl_close($ch);
        return $data;
    }
	
	
 $get_allproduct=$apiurl."/v1/products/productbyexternalid/$CreditorPublicId/?externalid=$product_id";
 $jsonData_allproduct=array();
 $jsonData_allproduct=json_encode($jsonData_allproduct);
 $all_pro=getall_products($get_allproduct,$jsonData_allproduct,$authentication);
$all_products=json_decode($all_pro);
//print_r($all_products);
 $ProductPublicId=$all_products->ProductPublicId;
if(!empty($ProductPublicId)){
 $ch = curl_init($apiurl."/v1/products/product");
  
	   $data=array(
  "ProductPublicId"=> $ProductPublicId,
  "CreditorPublicId"=> $CreditorPublicId,
  "ArticleNumber"=> $product_id,
  "ProductExternalId"=> $product_id,
  "Description"=> $title,
  "Units"=> "pairs",
  "IsActive"=> $status,
  "UnitPrice"=> $price,
  "VAT"=> 10,
  "BookKeepingAccount"=> 3000,
  "BookKeepingSalesEUAccount"=> 3510,
  "BookKeepingSalesEUVATAccount"=> 3610,
  "BookKeepingSalesNonEUAccount"=> 3710,
  "BookKeepingPurchaseAccount"=> 4000,
  "ProductType"=> "Service"
);
$request=json_encode($data);
//print_r($request);
//echo "<br><br>";
//Use the CURLOPT_PUT option to tell cURL that
//this is a PUT request.
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
//We want the result / output returned.
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Accept: application/json',
				"Authorization: Basic $authentication",
				"Content-Type: application/json",
			"Content-Length:". strlen($request)
					
					));
					
 
 $response = curl_exec($ch);
 print_r($response);
// exit;
            // do something with this product
}
	//	echo $product->['data:protected']>name;
	//		exit;
        }
    }
}

function my_enqueue() {
   
  wp_enqueue_script('billect_color', plugin_dir_url(__FILE__) . 'color/jscolor.js');
   // wp_enqueue_script( 'my_custom_script', get_template_directory_uri() . '/myscript.js' );
}
add_action( 'admin_enqueue_scripts', 'my_enqueue' );
add_filter( 'woocommerce_order_button_text', 'woo_custom_order_button_text1'); 
 function woo_custom_order_button_text1($text) {
	  global $wpdb;
	
	 //$k=new WC_Billecta_Payment_Gateway;
	 $m= get_option( 'woocommerce_billecta_payment_settings' );
	 
	  $color_on_button=$m['color_on_button'];
	 if($m['enabled']=="yes")
	 {
		 $text_on_button=$m['text_on_button'];
		if(!empty( $text_on_button)){
			 $text=$text_on_button;
			
		}
		if(!empty( $color_on_button)){
			 $color=$color_on_button;
			
		}?>
		<style>
		.woocommerce .place-order input[type=submit] {
    background: #<?php echo $color;?>;
    border-color: red;
}
		</style>
		<?php
    
	 }
	  return __( $text, 'woocommerce' );
}
class WC_Other_Payment_Gateway extends WC_Payment_Gateway{
	public function __construct(){
		$this->id = 'billecta_payment';
		$this->method_title = __('Billecta Payment','woocommerce-billecta-payment-gateway');
		$this->title = __('Billecta Payment','woocommerce-billecta-payment-gateway');
		$this->has_fields = true;
		$this->init_form_fields();
		$this->init_settings();
		$this->enabled = $this->get_option('enabled');
		$this->title = $this->get_option('title');
		$this->description = $this->get_option('description');
	
		
		add_action('woocommerce_update_options_payment_gateways_'.$this->id, array($this, 'process_admin_options'));
	}
	public function init_form_fields(){
		
				$this->form_fields = array(
					'enabled' => array(
					'title' 		=> __( 'Enable/Disable', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'checkbox',
					'label' 		=> __( 'Enable Custom Payment', 'woocommerce-billecta-payment-gateway' ),
					'default' 		=> 'yes'
					),
					'title' => array(
						'title' 		=> __( 'Plugin Title', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'text',
						'description' 	=> __( 'This controls the title', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( 'Custom Payment', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),
					'description' => array(
						'title' => __( 'Customer Message', 'woocommerce-billecta-payment-gateway' ),
						'type' => 'textarea',
						'css' => 'width:500px;',
						'default' => 'None of the other payment options are suitable for you? please drop us a note about your favourable payment option and we will contact you as soon as possible.',
						'description' 	=> __( 'The message which you want it to appear to the customer in the checkout page.', 'woocommerce-billecta-payment-gateway' ),
					),
					
					
	'username' => array(
					'title' 		=> __( 'Username', 'woocommerce-billecta-payment-gateway' ),
					'description' 	=> __( 'Please enter the Username that you used to logged in into the billecta', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'text',
					'label' 		=> __( 'Username Custom Payment', 'woocommerce-billecta-payment-gateway' ),
					'desc_tip'		=> true,
					),
		'password' => array(
					'title' 		=> __( 'Password', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'password',
					'description' 	=> __( 'Please enter the password that you used to logged in into the billecta', 'woocommerce-billecta-payment-gateway' ),
					'label' 		=> __( 'Password Custom Payment', 'woocommerce-billecta-payment-gateway' ),
					'desc_tip'		=> true,
					),
					
	'CreditorPublicId' => array(
					'title' 		=> __( 'CreditorPublicId', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'text',
					'description' 	=> __( 'Please enter the CreditorPublicId', 'woocommerce-billecta-payment-gateway' ),
					'label' 		=> __( 'CreditorPublicId ', 'woocommerce-billecta-payment-gateway' ),
					'desc_tip'		=> true,
					),
					 
					'num_of_days_option1' => array(
					'title' 		=> __( 'Select number days for each Invoice:', 'woocommerce-billecta-payment-gateway' ),
					'description' => 'This is Global setting for all the invoice',
					'type' 			=> 'select',
					 'options' => array(
 '2'        => __( '2', 'woocommerce-billecta-payment-gateway' ),
      '3'        => __( '3', 'woocommerce-billecta-payment-gateway' ),
      '4'       => __( '4', 'woocommerce-billecta-payment-gateway' ),
      '5'  => __( '5', 'woocommerce-billecta-payment-gateway' ),
      '6' => __( '6', 'woocommerce-billecta-payment-gateway' ),
	  '7'  => __( '7', 'woocommerce-billecta-payment-gateway' ),
	  '8'  => __( '8', 'woocommerce-billecta-payment-gateway' ),
	  '9'  => __( '9', 'woocommerce-billecta-payment-gateway' ),
	  '10'  => __( '10', 'woocommerce-billecta-payment-gateway' ),
	  '11'  => __( '11', 'woocommerce-billecta-payment-gateway' ),
	  '12'  => __( '12', 'woocommerce-billecta-payment-gateway' ),
	  '13'  => __( '13', 'woocommerce-billecta-payment-gateway' ),
	  '14'  => __( '14', 'woocommerce-billecta-payment-gateway' ),
	  '15'  => __( '15', 'woocommerce-billecta-payment-gateway' ),
	  '16'  => __( '16', 'woocommerce-billecta-payment-gateway' ),
	  '17'  => __( '17', 'woocommerce-billecta-payment-gateway' ),
	  '18'  => __( '18', 'woocommerce-billecta-payment-gateway' ),
	    '19'  => __( '19', 'woocommerce-billecta-payment-gateway' ),
		  '20'  => __( '20', 'woocommerce-billecta-payment-gateway' ),
    ),
					'label' 		=> __( 'Partial payment Enable Custom Payment', 'woocommerce-billecta-payment-gateway' ),
					'default' 		=> 'no'
					),
					
						'Full_payment' => array(
					'title' 		=> __( '1. Full Payment', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'checkbox',
					'label' 		=> __( 'Full payment Enable Custom Payment', 'woocommerce-billecta-payment-gateway' ),
					'default' 		=> '0'
					),
							'inoice_fee' => array(
						'title' 		=> __( '--Invoice Fee', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the title', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( 'Invice fee', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),
							'discount_fee' => array(
						'title' 		=> __( '--Discount Fee', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'text',
						'description' 	=> __( 'This controls the Discount on the full Payment', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),
					
					
					'partial_payment' => array(
					'title' 		=> __( '2. Partial Payment', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'checkbox',
					
					'label' 		=> __( 'Enable/Disable', 'woocommerce-billecta-payment-gateway' ),
					'default' 		=> 'no'
					),
	
	/*'num_invoices' => array(
					'title' 		=> __( '--Number Invoices', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'select',
					 'options' => array(
 '2'        => __( '2', 'woocommerce-billecta-payment-gateway' ),
      '3'        => __( '3', 'woocommerce-billecta-payment-gateway' ),
      '4'       => __( '4', 'woocommerce-billecta-payment-gateway' ),
      '5'  => __( '5', 'woocommerce-billecta-payment-gateway' ),
      '6' => __( '6', 'woocommerce-billecta-payment-gateway' ),
	  '7'  => __( '7', 'woocommerce-billecta-payment-gateway' ),
	  '8'  => __( '8', 'woocommerce-billecta-payment-gateway' ),
	  '9'  => __( '9', 'woocommerce-billecta-payment-gateway' ),
	  '10'  => __( '10', 'woocommerce-billecta-payment-gateway' ),
	  '11'  => __( '11', 'woocommerce-billecta-payment-gateway' ),
	  '12'  => __( '12', 'woocommerce-billecta-payment-gateway' ),
    ),
					'label' 		=> __( 'Number of invoices option 1 Enable Custom Payment', 'woocommerce-billecta-payment-gateway' ),
					'default' 		=> 'no'
					),*/
					
					'inoice_fee_partial_option1' => array(
						'title' 		=> __( '--invoice_fee', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),
						'registration_fee_option1' => array(
						'title' 		=> __( '--Registration Fee', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),'partial_payment_option1' => array(
					'title' 		=> __( '3. Partial Payment Option', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'checkbox',
					
					'label' 		=> __( 'Partial payment Option2', 'woocommerce-billecta-payment-gateway' ),
					'default' 		=> 'no'
					),
					'inoice_fee_partial_option2' => array(
						'title' 		=> __( '---Invoice fee', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),
						'registration_fee_option2' => array(
						'title' 		=> __( '---Registration fee', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),'partial_payment_option3' => array(
					'title' 		=> __( '4. Partial Payment Option', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'checkbox',
					
					'label' 		=> __( 'Enable/Disable', 'woocommerce-billecta-payment-gateway' ),
					'default' 		=> 'no'
					),
					'inoice_fee_partial_option3' => array(
						'title' 		=> __( '---Invoice fee', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),
						'registration_fee_option3' => array(
						'title' 		=> __( '---Registration fee', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),'partial_payment_option4' => array(
					'title' 		=> __( '5. Partial Payment Option', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'checkbox',
					
					'label' 		=> __( 'Enable/Disable', 'woocommerce-billecta-payment-gateway' ),
					'default' 		=> 'no'
					),
					'inoice_fee_partial_option4' => array(
						'title' 		=> __( '---Invoice fee', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),
						'registration_fee_option4' => array(
						'title' 		=> __( '---Registration fee', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),'partial_payment_option5' => array(
					'title' 		=> __( '6. Partial Payment Option', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'checkbox',
					
					'label' 		=> __( 'Enable/Disable', 'woocommerce-billecta-payment-gateway' ),
					'default' 		=> 'no'
					),
					'inoice_fee_partial_option5' => array(
						'title' 		=> __( '---Invoice fee ', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),
						'registration_fee_option5' => array(
						'title' 		=> __( '---Registration fee ', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),'partial_payment_option6' => array(
					'title' 		=> __( '7. Partial Payment Option', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'checkbox',
					
					'label' 		=> __( 'Enable/Disable', 'woocommerce-billecta-payment-gateway' ),
					'default' 		=> 'no'
					),
					'inoice_fee_partial_option6' => array(
						'title' 		=> __( '---Invoice fee ', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),
						'registration_fee_option6' => array(
						'title' 		=> __( '---Registration fee', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),'partial_payment_option7' => array(
					'title' 		=> __( '8.Partial Payment Option', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'checkbox',
					
					'label' 		=> __( 'Enable/Disable', 'woocommerce-billecta-payment-gateway' ),
					'default' 		=> 'no'
					),
					'inoice_fee_partial_option7' => array(
						'title' 		=> __( '---Invoice fee', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),
						'registration_fee_option7' => array(
						'title' 		=> __( '---Registration fee ', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),'partial_payment_option8' => array(
					'title' 		=> __( '9. Partial Payment Option', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'checkbox',
					
					'label' 		=> __( 'Enable/Disable', 'woocommerce-billecta-payment-gateway' ),
					'default' 		=> 'no'
					),
					'inoice_fee_partial_option8' => array(
						'title' 		=> __( '---Invoice fee ', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),
						'registration_fee_option8' => array(
						'title' 		=> __( '---Registration fee ', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),'partial_payment_option9' => array(
					'title' 		=> __( '10. Partial Payment Option', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'checkbox',
					
					'label' 		=> __( 'Enable/Disable', 'woocommerce-billecta-payment-gateway' ),
					'default' 		=> 'no'
					),
					'inoice_fee_partial_option9' => array(
						'title' 		=> __( '---Invoice fee ', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),
						'registration_fee_option9' => array(
						'title' 		=> __( '---Registration fee ', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),'partial_payment_option10' => array(
					'title' 		=> __( '11. Partial Payment Option', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'checkbox',
					
					'label' 		=> __( 'Enable/Disable', 'woocommerce-billecta-payment-gateway' ),
					'default' 		=> 'no'
					),
					'inoice_fee_partial_option10' => array(
						'title' 		=> __( '---Invoice fee ', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),
						'registration_fee_option10' => array(
						'title' 		=> __( '---Registration fee ', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),'partial_payment_option11' => array(
					'title' 		=> __( '12. Partial Payment Option', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'checkbox',
					
					'label' 		=> __( 'Enable/Disable ', 'woocommerce-billecta-payment-gateway' ),
					'default' 		=> 'no'
					),
					'inoice_fee_partial_option11' => array(
						'title' 		=> __( '---Invoice fee ', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),
						'registration_fee_option11' => array(
						'title' 		=> __( '---Registration fee', 'woocommerce-billecta-payment-gateway' ),
						'type' 			=> 'number',
						'description' 	=> __( 'This controls the Fee Invoice', 'woocommerce-billecta-payment-gateway' ),
						'default'		=> __( '', 'woocommerce-billecta-payment-gateway' ),
						'desc_tip'		=> true,
					),'text_on_button' => array(
					'title' 		=> __( 'Text on Button', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'text',
					'label' 		=> __( 'Text on Button Custom Payment', 'woocommerce-billecta-payment-gateway' ),
					'default' 		=> 'Submit'
					),
					
'color_on_button' => array(
					'title' 		=> __( 'color on button', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'text',
					'class' =>'jscolor',
					'label' 		=> __( 'Color on button Custom Payment', 'woocommerce-billecta-payment-gateway' ),
					'default' 		=> 'red'
					),
	'mode' => array(
					'title' 		=> __( 'Live/Test', 'woocommerce-billecta-payment-gateway' ),
					'type' 			=> 'checkbox',
					'description' 	=> __( 'PLease select the mode fot the api to use', 'woocommerce-billecta-payment-gateway' ),
					'label' 		=> __( 'Check this for live mode(Live:https://api.billecta.com 
					****  Test Url: https://apitest.billecta.com )', 'woocommerce-billecta-payment-gateway' ),
					'desc_tip'		=> true,
					)
					
	
			 );
	}
	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_options() {
		?>
		<h3><?php _e( 'Billecta Payment Settings', 'woocommerce-billecta-payment-gateway' ); ?></h3>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<table class="form-table">
							<?php $this->generate_settings_html();?>
						</table><!--/.form-table-->
					</div>
					<div id="postbox-container-1" class="postbox-container">
	                        <div id="side-sortables" class="meta-box-sortables ui-sortable"> 
	                           
     							
	                            <div class="postbox ">
	                                <div class="handlediv" title="Click to toggle"><br></div>
	                                <h3 class="hndle"><span><i class="dashicons dashicons-editor-help"></i>&nbsp;&nbsp;Plugin Support</span></h3>
	                                <div class="inside">
	                                    <div class="support-widget">
	                                        <p>
	                                        <img style="width: 70%;margin: 0 auto;position: relative;display: inherit;" src="https://wpruby.com/wp-content/uploads/2016/03/wpruby_logo_with_ruby_color-300x88.png">
	                                        <br/>
	                                        Got a Question, Idea, Problem or Praise?</p>
	                                        

	                                    </div>
	                                </div>
	                            </div>
	                       
	                            <div class="postbox rss-postbox">
	    							<div class="handlediv" title="Click to toggle"><br></div>
	    								<h3 class="hndle"><span><i class="fa fa-wordpress"></i>&nbsp;&nbsp;WPRuby Blog</span></h3>
	    								<div class="inside">
											
	    								</div>
	    						</div>

	                        </div>
	                    </div>
                    </div>
				</div>
				<div class="clear"></div>
				<style type="text/css">
				.wpruby_button{
					background-color:#4CAF50 !important;
					border-color:#4CAF50 !important;
					color:#ffffff !important;
					width:100%;
					padding:5px !important;
					text-align:center;
					height:35px !important;
					font-size:12pt !important;
				}
				</style>
				<?php
	}
	public function process_admin_options()
{
	  $this->init_settings();

    $post_data = $this->get_post_data();

 
 $apiurl="";
 $user=$post_data['woocommerce_billecta_payment_username'];
  $pass=$post_data['woocommerce_billecta_payment_password'];
  $credit=$post_data['woocommerce_billecta_payment_CreditorPublicId'];
    $mode=$post_data['woocommerce_billecta_payment_mode'];
	
  
  //updating the product option
  
  
  //End Product
  	 
	print_r($post_data);
		  $authentication = base64_encode("$user:$pass");
		  
		  
  function authenticate($url,$request,$authentication)
    {
       //$authentication = base64_encode("dag@kreativinsikt.se:Stockholm66");
	   $ch = curl_init($url);
        $options = array(
                CURLOPT_RETURNTRANSFER => true,         // return web page
                CURLOPT_HEADER         => false,        // don't return headers
                CURLOPT_FOLLOWLOCATION => false,         // follow redirects
               // CURLOPT_ENCODING       => "utf-8",           // handle all encodings
			   
                CURLOPT_AUTOREFERER    => true,         // set referer on redirect
                CURLOPT_CONNECTTIMEOUT => 20,          // timeout on connect
                CURLOPT_TIMEOUT        => 20,          // timeout on response
                CURLOPT_POST            => 0,    // this are my post vars
                CURLOPT_SSL_VERIFYHOST => 0,            // don't verify ssl
                CURLOPT_SSL_VERIFYPEER => false,        //
                CURLOPT_VERBOSE        => 1,
                CURLOPT_HTTPHEADER     => array(
				"Authorization: Basic $authentication",
                    "Content-Type: application/json")    
        );
		//print_r($options);
        curl_setopt_array($ch,$options);
        $data = curl_exec($ch);
	
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        //echo $curl_errno;
       // echo $curl_error;
        curl_close($ch);
        return $data;
    }
	 if($mode)
				 {
					$apiurl= "https://api.billecta.com";
				 }else{
					 
					 $apiurl="https://apitest.billecta.com";
					 
				 }

	

	
	
 $get_allproduct=$apiurl."/v1/authentication/apiauthenticate";
 $jsonDa=array();
 $jsonDa=json_encode($jsonDa);
$all_pro=authenticate($get_allproduct,$jsonDa,$authentication);
$auth=json_decode($all_pro);

if(empty($auth)){

	
	
	//return $post_data;
   $redirect = add_query_arg('wc_error', __('Your-Api-Username-and-Password-is-not-valid.', 'woocommerce'));
            wp_redirect($redirect);
            exit;
	
}else{
	
	
	
}
    parent::process_admin_options();
 
 

}
	
	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new WC_Order( $order_id );
		$amount=null;
		$vatinc=false;
		if($_POST['payment_method']=='billecta_payment')
		{
			
	
		 $user= $_POST['billecta_payment-username'];
  $pass=$_POST['billecta_payment-payment_password'];
		//print_r($_POST);
		  $authentication = base64_encode("$user:$pass");
	
		$days_option=$_POST['billecta_payment-num_days'];
$option=$_POST['billecta_payment-payment-option'];
	//$full_inovice=$_POST['billecta_payment-inoice_fee'];
	if($option==1){
		 $full_inovice=$_POST['billecta_payment-inoice_fee'];
		 $discount_fee=$_POST['billecta_payment_discount_fee'];
		
		}
	else{
	$opt=$option-1;
		$invoices_per="billecta_payment-num_invoices-par".$opt;
		$invoices_fee="billecta_payment-inoice_fee_partial_par_option".$opt;
		
		$registration="billecta_payment-registration_fee_par_option".$opt;
	$num_parts=$_POST[$invoices_per];
	$full_inovice=$_POST[$invoices_fee];
	$regist_fee=$_POST[$registration];
	}


	
	//$option=$_POST['billecta_payment-payment-option'];
	$tax = new WC_Tax();
	
	  if(wc_prices_include_tax()){
         //   return  WC_Tax::calc_inclusive_tax( $price, $rates );
    $vatIcluded=true;
		}else{
			 $vatIcluded=false;
			}
		

$country_code = '*'; // or populate from order to get applicable rates
$rates = $tax->find_rates( array( 'country' => $country_code ) );

//print_r($rates);
$tax_rate_val=0;
foreach( $rates as $rate ){
    $tax_rate_val = $rate['rate'];
}
			

		$CreditorPublicId=$_POST['billecta_payment-CreditorPublicId'];
		$apiurl=$_POST['billecta_payment-payment_mode'];
		$order = new WC_Order( $order_id );
$items = $order->get_items();

	$order1 = wc_get_order( $order_id );
	//print_r($order1);

		$order_data = $order1->get_data();
		//print_r($order1->get_used_coupons());
		foreach( $order1->get_used_coupons() as $coupon_name ){

    // Retrieving the coupon ID
    $coupon_post_obj = get_page_by_title($coupon_name, OBJECT, 'shop_coupon');
  $coupon_id = $coupon_post_obj->ID;

    // Save an instance of WC_Coupon object in an array(necesary to use WC_Coupon methods)
    $coupons_obj = new WC_Coupon($coupon_id);
$amount=$coupons_obj->get_amount();
    // Now you can get type in your condition
    if ( $coupons_obj->get_discount_type() == 'cash_back_percentage' ){
        // Get the coupon object amount
      echo "coup1".  $coupons_amount1 = $coupons_obj->get_amount();
    }

    // Or use this other conditional method for coupon type
    if( $coupons_obj->is_type( 'cash_back_fixed' ) ){
        // Get the coupon object amount
        echo "coup2".$coupons_amount2 = $coupons_obj->get_amount();
    }
}

	
	//print_r($order_data);
	
	
	
function getall_products($url,$request,$authentication)
    {
       //$authentication = base64_encode("dag@kreativinsikt.se:Stockholm66");
	   $ch = curl_init($url);
        $options = array(
                CURLOPT_RETURNTRANSFER => true,         // return web page
                CURLOPT_HEADER         => false,        // don't return headers
                CURLOPT_FOLLOWLOCATION => false,         // follow redirects
               // CURLOPT_ENCODING       => "utf-8",           // handle all encodings
			   
                CURLOPT_AUTOREFERER    => true,         // set referer on redirect
                CURLOPT_CONNECTTIMEOUT => 20,          // timeout on connect
                CURLOPT_TIMEOUT        => 20,          // timeout on response
                CURLOPT_POST            => 0,    // this are my post vars
                CURLOPT_SSL_VERIFYHOST => 0,            // don't verify ssl
                CURLOPT_SSL_VERIFYPEER => false,        //
                CURLOPT_VERBOSE        => 1,
                CURLOPT_HTTPHEADER     => array(
				"Authorization: Basic $authentication",
                    "Content-Type: application/json")    
        );
		//print_r($options);
        curl_setopt_array($ch,$options);
        $data = curl_exec($ch);
	
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        //echo $curl_errno;
        //echo $curl_error;
        curl_close($ch);
        return $data;
    }
	
	
	
//Creating the product in the billecta
function creating_products($url,$request,$authentication)
    {
     //  $authentication = base64_encode("dag@kreativinsikt.se:Stockholm66");
	   $ch = curl_init($url);
        $options = array(
                CURLOPT_RETURNTRANSFER => true,         // return web page
                CURLOPT_HEADER         => false,        // don't return headers
                CURLOPT_FOLLOWLOCATION => false,         // follow redirects
               // CURLOPT_ENCODING       => "utf-8",           // handle all encodings
			   
                CURLOPT_AUTOREFERER    => true,         // set referer on redirect
                CURLOPT_CONNECTTIMEOUT => 20,          // timeout on connect
                CURLOPT_TIMEOUT        => 20,          // timeout on response
                CURLOPT_POST            => 1,            // i am sending post data
                CURLOPT_POSTFIELDS     => $request,    // this are my post vars
                CURLOPT_SSL_VERIFYHOST => 0,            // don't verify ssl
                CURLOPT_SSL_VERIFYPEER => false,        //
                CURLOPT_VERBOSE        => 1,
                CURLOPT_HTTPHEADER     => array(
				"Authorization: Basic $authentication",
                    "Content-Type: application/json",
					"Content-Length:". strlen($request))    
        );
		//print_r($options);
        curl_setopt_array($ch,$options);
        $data = curl_exec($ch);
	
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        //echo $curl_errno;
        //echo $curl_error;
        curl_close($ch);
        return $data;
    }
	$product_url=$apiurl."/v1/products/products";
$product_public=array();
// Get all the customer
 $get_allcustomer=$apiurl."/v1/debtors/debtors/$CreditorPublicId";
 $jsonData_allcustomer=array();
 $jsonData_allcustomer=json_encode($jsonData_allcustomer);
$all_cust=getall_products($get_allcustomer,$jsonData_allcustomer,$authentication);
$all_custmer=json_decode($all_cust);
//sprint_r($all_custmer);
$cust_me=array();
 $cust_email=$order_data['billing']['email'];
$creat_cutom=0;
	foreach($all_custmer as $cust)
	{
	if($cust->Email==$cust_email)
	{
	$creat_cutom=1;
	
$cut_public=$cust->DebtorPublicId;
	
	}
	}
	if($creat_cutom==0)
	{
		 //$creat_cutom; 
		 $url=$apiurl."/v1/debtors/debtor";
$request_debtors=array( 
  "CreditorPublicId"=> $CreditorPublicId,
  "DebtorExternalId"=>"25",
  "Name"=> $order_data['billing']['first_name'].' '.$order_data['billing']['last_name'],
  "Address"=>$order_data['billing']['address_1'].' '.$order_data['billing']['address_2'],
  "ZipCode"=>$order_data['billing']['postcode'],
  "City"=> $order_data['billing']['city'],
  "CountryCode"=> $order_data['billing']['country'],
  "Email"=>$order_data['billing']['email'],
  "ContactName"=> $order_data['billing']['first_name'],
  "ContactEmail"=> $order_data['billing']['email']);
  $jsonDataDebtors = json_encode($request_debtors);
 
  
$output=creating_products($url,$jsonDataDebtors,$authentication);
$Outcome=json_decode($output,2);
$cut_public=$Outcome['PublicId'];
	//$product_public[]=$Outcome;
		echo "created new customer".$cut_public;
	
		}

	//Registration product

			$opId=$_POST['billecta_payment-payment-option'];
		
		if($opId>1){
					$opId1=$opId-1;
	$price_reg=$_POST['billecta_payment-registration_fee_par_option'.$opId1];
	$product_name_reg="Registartion Fee Partial";
	//$product_reg=array();
			
			}else{
				
					$product_name_reg="Registartion Fee Full";
$price_reg="-".$_POST['billecta_payment_discount_fee'];
	
				
				}
			$active="400".$opId;
				$price_regi_id="option".$opId;
				
$product_reg=array("CreditorPublicId"=>$CreditorPublicId,
	"ArticleNumber"=> $active,
  "ProductExternalId"=> $price_regi_id,
  "Description"=> $product_name_reg,
  "Units"=>  "items",
  "IsActive"=>  true,
  "UnitPrice"=>  $price_reg,
  "VAT"=>   0, 
  "ProductType"=>  "Service",
    "BookKeepingAccount"=> 3000,
  "BookKeepingSalesEUAccount"=> 3510,
  "BookKeepingSalesEUVATAccount"=> 3006,
  "BookKeepingSalesNonEUAccount"=> 3108,
  "BookKeepingPurchaseAccount"=> 4000
  );
	
//print_r($product_reg);
	
	$get_allreg=$apiurl."/v1/products/productbyexternalid/$CreditorPublicId/?externalid=$price_regi_id";
 $jsonData_price_regi=array();
 $jsonData_price_regi=json_encode($jsonData_price_regi);
$objreg=getall_products($get_allreg,$jsonData_price_regi,$authentication);
$all_preg=json_decode($objreg);
print_r($all_preg);
if(empty($all_preg->ProductPublicId))
	{ 
	echo "creating the new registration";
	$jsonData_proreg = json_encode($product_reg);
 //$product_url;
  
 $output1reg=creating_products($product_url,$jsonData_proreg,$authentication);
 
$objreg=json_decode($output1reg,true);
$Regi_pro_public=$objreg->ProductPublicId;
	}else{
	echo "Registered ";
		$Regi_pro_public=$all_preg->ProductPublicId;
		$product_name_reg=$all_preg->Description;
			//$price_reg=$all_preg->UnitPrice;
 
 
 $ch = curl_init($apiurl."/v1/products/product");
  
	   $data=array(
  "ProductPublicId"=> $Regi_pro_public,
  "CreditorPublicId"=> $CreditorPublicId,
  "ArticleNumber"=> $active,
  "ProductExternalId"=> $price_regi_id,
  "Description"=> $product_name_reg,
  "Units"=> "pairs",
  "IsActive"=> true,
  "UnitPrice"=> $price_reg,
  "VAT"=> 25,
  "BookKeepingAccount"=> 3000,
  "BookKeepingSalesEUAccount"=> 3510,
  "BookKeepingSalesEUVATAccount"=> 3610,
  "BookKeepingSalesNonEUAccount"=> 3710,
  "BookKeepingPurchaseAccount"=> 4000,
  "ProductType"=> "Service"
);

$request=json_encode($data);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
//We want the result / output returned.
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Accept: application/json',
				"Authorization: Basic $authentication",
				"Content-Type: application/json",
			"Content-Length:". strlen($request)
					
					));
					
 
 $response = curl_exec($ch);
 
 
 	//print_r($response);
	}

		
	
		
		
		  $SIngproduct_reg=array(
      "SequenceNo"=> 0,
      "Units"=> "pairs",
      "ArticleDescription"=> $product_name_reg,
      "Quantity"=> 1,
	  
      "UnitPrice"=> array
                (
                    "CurrencyCode" => "SEK",
                    "Value" => $price_reg*100,
                    "ValueForView" => $price_reg
                ),
      "DiscountPercentage"=> 0,
      "DiscountType" => "Amount",
      "VAT"=>$tax_rate_val,
       "VatIsIncluded"=> $vatIcluded,
      "Hidden"=> false,
      "TotalIncVAT"=>array(
                            "CurrencyCode" => "SEK",
                            "Value" => 0,
                            "ValueForView" => 0),
	  "ProductPublicId" => $Regi_pro_public
);
		  print_r($SIngproduct_reg);
//exit;
//End Registartion

		
		 $SIngproduct_data=array();
		 $all_Products=array();
		  $quantity_chek=array();
			 $j=0;
		foreach ( $items as $item ) {
			//print_r($item);
		//exit;
 $product_name = $item['name'];
    $product_id = $item['product_id'];
    $product_quantity = $item['quantity'];
 $product_total = floatval($item['subtotal']/$product_quantity);
 $product_subtotal_tax = $item['subtotal_tax'];
 
	 
  $product_variation_id = $item['variation_id'];
  $total_tax= array("CurrencyCode"=> "SEK",
        "Value"=> $product_subtotal_tax,
        "ValueForView"=> $product_subtotal_tax);
		 
        $unit_price=array("CurrencyCode"=> "SEK",
        "Value"=> $product_total*100,
        "ValueForView"=> $product_total);

        $subtract_quantity=floatval($product_quantity/$option);
		
			$quantity_chek[]=array("key"=>$j,"value"=>$subtract_quantity);
			$j++;
if($amount!="null" ){
	$amountk=$amount*$product_quantity;
	$amount1=array("CurrencyCode"=> "SEK",
	"Value"=> $amountk*100,
	"ValueForView" =>$amountk
);
	}else{
		$amount1=null;
		}
		if($vatIcluded==true){
  $SIngproduct_data=array(
      "SequenceNo"=> 0,
      "Units"=> "pairs",
      "ArticleDescription"=> $product_name,
      "Quantity"=> $product_quantity,
	  
      "UnitPrice"=>$unit_price,
      "DiscountAmount"=> $amount1,
      "DiscountPercentage"=> 0,
      "DiscountType"=> "Amount",
      "VAT"=>$tax_rate_val,
       "VatIsIncluded"=> $vatIcluded,
      "Hidden"=> false,
      "TotalIncVAT"=>$total_tax
);
}else{
	  $SIngproduct_data=array(
      "SequenceNo"=> 0,
      "Units"=> "pairs",
      "ArticleDescription"=> $product_name,
      "Quantity"=> $product_quantity,
	  
      "UnitPrice"=>$unit_price,
      "DiscountAmount"=> $amount1,
      "DiscountPercentage"=> 0,
      "DiscountType"=> "Amount",
      "VAT"=>$tax_rate_val,
       "VatIsIncluded"=> $vatIcluded,
      "Hidden"=> false,
     "TotalExclVAT"=>$total_tax);
	 }
	$product_request=array("CreditorPublicId"=>$CreditorPublicId,
	"ArticleNumber"=> $product_id,
  "ProductExternalId"=> $product_id,
  "Description"=> $product_name,
  "Units"=>  "items",
  "IsActive"=>  true,
  "UnitPrice"=>  $product_total,
  "VAT"=>   $tax_rate_val, 
  "ProductType"=>  "Service",
    "BookKeepingAccount"=> 3000,
  "BookKeepingSalesEUAccount"=> 3510,
  "BookKeepingSalesEUVATAccount"=> 3006,
  "BookKeepingSalesNonEUAccount"=> 3108,
  "BookKeepingPurchaseAccount"=> 4000
  );
  $get_allproduct=$apiurl."/v1/products/productbyexternalid/$CreditorPublicId/?externalid=$product_id";
 $jsonData_allproduct=array();
 $jsonData_allproduct=json_encode($jsonData_allproduct);
$all_pro=getall_products($get_allproduct,$jsonData_allproduct,$authentication);
$all_products=json_decode($all_pro);
	// eND OF THE CUSTOMER CHECKING
//print_r($all_products);
//print_r($all_products);
$pro_me=array();
	if(empty($all_products->ProductPublicId))
	{ 
	echo "created new products";
$jsonData_product = json_encode($product_request);
 
  
$output1=creating_products($product_url,$jsonData_product,$authentication);
 
$obj=json_decode($output1,true);
$product_public[]=$obj['PublicId'];
if(!empty($obj['PublicId']))
{
	
  echo "prodtc id".$SIngproduct_data['ProductPublicId']=$obj['PublicId'];
}	//$product_public[]=$Outcome;
	}else{
	 $product_public[]=$all_products->ProductPublicId;
	 $SIngproduct_data['ProductPublicId']=$all_products->ProductPublicId;
	 $pro_descr=$all_products->Description;
		
	}
	$all_Products[]=$SIngproduct_data;
 
}
$all_Products[]=$SIngproduct_reg;
$product_public=array_filter($product_public);
$product_public=array_values($product_public);
		//echo $order_data['currency'];
		//print_r($order_data);
	
		 function CurlSendPostRequest($url,$request,$authentication)
    {
     
	   $ch = curl_init($url);
        $options = array(
                CURLOPT_RETURNTRANSFER => true,         // return web page
                CURLOPT_HEADER         => false,        // don't return headers
                CURLOPT_FOLLOWLOCATION => false,         // follow redirects
               // CURLOPT_ENCODING       => "utf-8",           // handle all encodings
			   
                CURLOPT_AUTOREFERER    => true,         // set referer on redirect
                CURLOPT_CONNECTTIMEOUT => 20,          // timeout on connect
                CURLOPT_TIMEOUT        => 20,          // timeout on response
                CURLOPT_POST            => 1,            // i am sending post data
                CURLOPT_POSTFIELDS     => $request,    // this are my post vars
                CURLOPT_SSL_VERIFYHOST => 0,            // don't verify ssl
                CURLOPT_SSL_VERIFYPEER => false,        //
                CURLOPT_VERBOSE        => 1,
                CURLOPT_HTTPHEADER     => array(
				"Authorization: Basic $authentication",
                    "Content-Type: application/json",
					"Content-Length:". strlen($request))    
        );
		//print_r($options);
        curl_setopt_array($ch,$options);
        $data = curl_exec($ch);
	
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        //echo $curl_errno;
        //echo $curl_error;
        curl_close($ch);
        return $data;
    }
	
	
		
	//	print_r($order_data['billing']);
	
//Creating the first invoice
	//"SplitInvoice":{"UseSplitInvoice":true","NumberOfSplitInvoices":2,"SplitInvoiceDetails":[{"Text":"Invoice 1 of 2","DueDate":"2017-09-19 00:00:00+02:00","PeriodStart":null,"PeriodStop":null,"ReminderDate":null,"SendDate":null,"AmountOfArticle":[{"Key":"0","Value":0.5}]},{"Text":"Invoice 2 of 2","DueDate":"2017-10-19 00:00:00+02:00","PeriodStart":null,"PeriodStop":null,"ReminderDate":null,"SendDate":"2017-10-01 00:00:00+02:00","AmountOfArticle":[{"Key":"0","Value":0.5}]}]}
/*	
$split=array("UseSplitInvoice"=>true,"NumberOfSplitInvoices"=>2,"SplitInvoiceDetails"=>array("0"=>array("Text"=>"Invoice 1 of 2","DueDate"=>"2017-09-19 00:00:00+02:00","PeriodStart"=>null,"PeriodStop"=>null,"ReminderDate"=>null,"SendDate"=>"2017-09-18 00:00:00+02:00","AmountOfArticle"=>array("Key"=>"0","Value"=>0.5)),"1"=>array("Text"=>"Invoice 2 of 2","DueDate"=>"2017-10-19 00:00:00+02:00","PeriodStart"=>null,"PeriodStop"=>null,"ReminderDate"=>null,"SendDate"=>"2017-10-01 00:00:00+02:00","AmountOfArticle"=>array("Key"=>"0","Value"=>0.5))));*/
$splitInvoicedetails=array();
//echo "test".$option."test";
$part=floatval(1/$option);
$qual_count=count($quantity_chek);

$quantity_chek[]=array(  "key" =>$qual_count,
                                            "value"=> $subtract_quantity);

for($i=1;$i<=$option;$i++)
{
	
	if($i==1)
	{
	echo 	$current_date=date("Y-m-d h:i:s");
		
  $NewDate=date('Y-m-d h:i:s', strtotime("+".$days_option." days"));
		
	}else{
		
$current_date=$NewDate;
	echo $NewDate=date('Y-m-d h:i:s', strtotime($current_date."+".$days_option." days"));	
		
	}
	$k=$i-1;
	$splitInvoicedetails[]=array(   'Text' => 'Invoice '.$i.' of '.$option,
        'DueDate' => $NewDate,
        'PeriodStart' => NULL,
        'PeriodStop' => NULL,
        'ReminderDate' => NULL,
        'SendDate' => $current_date,
        'AmountOfArticle' =>  $quantity_chek,);
	
	
}
 $split1=array('UseSplitInvoice' => true,
    'NumberOfSplitInvoices' => 2,
    'SplitInvoiceDetails' =>  $splitInvoicedetails,
  );
  
  if($option==1){
	 $split1=null; 
	  }
$cur=date("Y-m-d h:i:s");
	$url=$apiurl."/v1/invoice/action";
		$records=$all_Products;
$invoice_fee=array(
    "CurrencyCode"=> "SEK",
    "Value"=> $full_inovice*100,
    "ValueForView"=> $full_inovice);
  
$request=array( 
  "CreditorPublicId"=> $CreditorPublicId,
  "DebtorPublicId"=> $cut_public,
  "InvoiceDate"=>$cur,
  "DueDate"=> $NewDate,
"DeliveryDate" =>null,
  "Records"=>$records,
  "OurReference"=> "John Doe",
  "DeliveryMethod"=> "Email",
  "CommunicationLanguage"=> "SV",
  "Message"=> "This is first invoice created by me ",
  "InvoiceFee"=> $invoice_fee,
  "VatIsIncluded"=> $vatIcluded,
  "SendByMailIfEmailNotViewedInDays"=> null,
  "SplitInvoice"=> $split1,
  "InvoicePDF"=>null,
  "CreditingInvoicePublicId"=> null,
  "ExternalReference"=> null
  
  );
  
//print_r($request);
  echo $jsonDataEncoded = json_encode($request);
  // print_r($jsonDataEncoded);
 $output=CurlSendPostRequest($url,$jsonDataEncoded,$authentication);
$Outcome=json_decode($output,2);
//print_r($output);

//$Outcome=json_decode($Outcome['PublicId']);
//exit;
$publicIdcustomer=$Outcome['PublicId'];
//5592765983
//9149854243
$alloutput=explode(";",$publicIdcustomer);
if(!empty($alloutput))
{
foreach($alloutput as $publicIdcustomer)
{	
	 $ch = curl_init($apiurl."/v1/invoice/attest/$publicIdcustomer");
       
//Use the CURLOPT_PUT option to tell cURL that
//this is a PUT request.
curl_setopt($ch, CURLOPT_PUT, true);
curl_setopt($ch, CURLOPT_HEADER, true);
 
//We want the result / output returned.
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Authorization: Basic $authentication"));
 
 
 $response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  
  
if($httpcode==200)
{	
 
}else{	echo 'I am therte4';
	
		$out= array(
			'result'   => 'failure',
    'messages' => "Attesting the invocie is failed"
		);	
}
}
}else{
		echo 'I am therte1';
$out= array(
			'result'   => 'failure',
    'messages' => "Customer is not added into the into the billecta"
		);	
	
}
echo "jhgjg".$httpcode; 
if($httpcode=="200")
{
	echo "I am fine";
$order->update_status('on-hold', __( 'Awaiting payment', 'woocommerce-billecta-payment-gateway' ));
		// Reduce stock levels
		$order->reduce_order_stock();
		if(isset($_POST[ $this->id.'-admin-note']) && trim($_POST[ $this->id.'-admin-note'])!=''){
			$order->add_order_note(esc_html($_POST[ $this->id.'-admin-note']),1);
		}
		// Remove cart
		$woocommerce->cart->empty_cart();
		// Return thankyou redirect
		$out= array(
			'result' => 'success',
			'redirect' => $this->get_return_url( $order )
		);	
		return $out;
		exit();
		
}		

		}else{
				
		$order->update_status('on-hold', __( 'Awaiting payment', 'woocommerce-billecta-payment-gateway' ));
		// Reduce stock levels
		$order->reduce_order_stock();
		if(isset($_POST[ $this->id.'-admin-note']) && trim($_POST[ $this->id.'-admin-note'])!=''){
			$order->add_order_note(esc_html($_POST[ $this->id.'-admin-note']),1);
		}
		// Remove cart
		$woocommerce->cart->empty_cart();
		// Return thankyou redirect
		return array(
			'result' => 'success',
			'redirect' => $this->get_return_url( $order )
		);	
			
			exit();
		}
//end invoice
	//exit;
		// Mark as on-hold (we're awaiting the cheque)
	
	}
	public function payment_fields(){
		?>
       <script>
	   jQuery(document).ready(function(){
		   jQuery(".pay").click(function(){
			   jQuery( ".same" ).each(function( index ) {
 jQuery(this).css('display','none');
});
			   var myId=jQuery(this).attr("id");
			  // alert(myId);
			   jQuery("."+myId).css('display','block');
			   
		   });
		   
		   
	   }
	   );
	   </script>
		<fieldset>
			<p class="form-row form-row-wide">
				<label for="<?php echo $this->id; ?>-admin-note"><?php echo esc_attr($this->description); ?> <span class="required">*</span></label>
				<?php /*?> <textarea id="<?php echo $this->id; ?>-admin-note" class="input-text" type="text" name="<?php echo $this->id; ?>-admin-note"></textarea><?php */?>
			</p>	
             I want to pay:		
             <?php
			// print_r($this);
			 $this->init_settings();
			 
			  $this->full_payment = $this->get_option( 'Full_payment' );
			  
			   $this->discount_fee = $this->get_option( 'discount_fee' );
			  
			   $this->inoice_fee = $this->get_option( 'inoice_fee' );
			    $this->num_invoices = $this->get_option( 'num_invoices' );
				 $this->payment_mode = $this->get_option( 'mode' );
				 if($this->payment_mode=="yes")
				 {
					$this->payment_mode= "https://api.billecta.com";
				 }else{
					 
					 $this->payment_mode="https://apitest.billecta.com";
					 
				 }
				  $this->inoice_fee_partial_option1 = $this->get_option( 'inoice_fee_partial_option1' );
				   $this->registration_fee_option1 = $this->get_option( 'registration_fee_option1' );
				   
		
				   
				  //   $this->payment_mode = $this->get_option( 'payment_mode' );
			
					   $this->payment_username = $this->get_option( 'username' );
					     $this->payment_password = $this->get_option( 'password' );
						  $this->CreditorPublicId = $this->get_option( 'CreditorPublicId' );
						  
			 $this->num_of_days_option1 = $this->get_option( 'num_of_days_option1' );			 
			   
			   
			 
	  $this->partial_payment = $this->get_option( 'partial_payment' );
	   $this->partial_payment_option2 = $this->get_option( 'partial_payment_option1' );
	   
	   		   	  $this->inoice_fee_partial_par_option2 = $this->get_option( 'inoice_fee_partial_option2' );
				   $this->registration_fee_par_option2 = $this->get_option( 'registration_fee_option2' );
				   
	   
	     $this->partial_payment_option3 = $this->get_option( 'partial_payment_option3' );
		  	  $this->inoice_fee_partial_par_option3 = $this->get_option( 'inoice_fee_partial_option3' );
				   $this->registration_fee_par_option3 = $this->get_option( 'registration_fee_option3' );
				   
				   
		 $this->partial_payment_option4 = $this->get_option( 'partial_payment_option4' );
		  	  $this->inoice_fee_partial_par_option4 = $this->get_option( 'inoice_fee_partial_option4' );
				   $this->registration_fee_par_option4 = $this->get_option( 'registration_fee_option4' );
				   
				   			   
		 $this->partial_payment_option5 = $this->get_option( 'partial_payment_option5' );
		  	  $this->inoice_fee_partial_par_option5= $this->get_option( 'inoice_fee_partial_option5' );
				   $this->registration_fee_par_option5 = $this->get_option( 'registration_fee_option5' );
				   
				   			   
		 $this->partial_payment_option6 = $this->get_option( 'partial_payment_option6' );
		  	  $this->inoice_fee_partial_par_option6 = $this->get_option( 'inoice_fee_partial_option6' );
				   $this->registration_fee_par_option6 = $this->get_option( 'registration_fee_option6' );
				   
				   			   
		 $this->partial_payment_option7 = $this->get_option( 'partial_payment_option7' );
		  	  $this->inoice_fee_partial_par_option7 = $this->get_option( 'inoice_fee_partial_option7' );
				   $this->registration_fee_par_option7= $this->get_option( 'registration_fee_option7' );
				   
				   			   
		 $this->partial_payment_option8 = $this->get_option( 'partial_payment_option8' );
		  	  $this->inoice_fee_partial_par_option8 = $this->get_option( 'inoice_fee_partial_option8' );
				   $this->registration_fee_par_option8 = $this->get_option( 'registration_fee_option8' );
				   
				   			   
		 $this->partial_payment_option9 = $this->get_option( 'partial_payment_option9' );
		  	  $this->inoice_fee_partial_par_option9 = $this->get_option( 'inoice_fee_partial_option9' );
				   $this->registration_fee_par_option9 = $this->get_option( 'registration_fee_option9' );
				   
				   			   
		 $this->partial_payment_option10 = $this->get_option( 'partial_payment_option10' );
		  	  $this->inoice_fee_partial_par_option10 = $this->get_option( 'inoice_fee_partial_option10' );
				   $this->registration_fee_par_option10 = $this->get_option( 'registration_fee_option10' );
				   
				   			   
		 $this->partial_payment_option11 = $this->get_option( 'partial_payment_option11' );
		  	  $this->inoice_fee_partial_par_option11 = $this->get_option( 'inoice_fee_partial_option11' );
				   $this->registration_fee_par_option11 = $this->get_option( 'registration_fee_option11' );
		 
		 
	   
			 if($this->full_payment=="yes")
			 {?>
				Full payment <input type="radio"  class="pay" id="<?php echo $this->id; ?>-payment-option1" name="<?php echo $this->id; ?>-payment-option" value="1" checked />
                  <p class="<?php echo $this->id; ?>-payment-option1 same" id="full_content" style="display:block">Invoicing fee <?php echo $this->inoice_fee;?> <input type="hidden" id="<?php echo $this->id;?>-inoice_fee_full" name="<?php echo $this->id;?>-inoice_fee" value="<?php echo $this->inoice_fee;?>"				  />
                  
                <br />  Discount fee <?php echo $this->discount_fee;?>
                  
                  <input type="hidden" id="<?php echo $this->id;?>_discount_fee" name="<?php echo $this->id;?>_discount_fee" value="<?php echo $this->discount_fee;?>"  //>
                  </p>
				<?php }
				 if($this->partial_payment=="yes")
			 {?><br />
				Partial Payment (2 invoices) <input type="radio"  class="pay" id="<?php echo $this->id; ?>-payment_par_option1" name="<?php echo $this->id; ?>-payment-option"  value="2"/>
                 <div id="par_content" class="same <?php echo $this->id; ?>-payment_par_option1" style="display:none">Number of parts <?php echo $this->num_invoices;?> 
                  <input type="hidden" id="<?php echo $this->id;?>-num_invoices1" name="<?php echo $this->id;?>-num_invoices-par1"  value="<?Php echo $this->num_invoices;?>" />
                     <div>Invoicing fee: <?php echo $this->inoice_fee_partial_option1;?>  <input type="hidden" id="<?php echo $this->id;?>-inoice_fee_partial_option1_par" name="<?php echo $this->id;?>-inoice_fee_partial_par_option1"  value="<?Php echo $this->inoice_fee_partial_option1;?>"/>
					 <br />
					  Registration Fee: <?php echo   $this->registration_fee_option1; ?>
					 <input type="hidden" id="<?php echo $this->id;?>-registration_fee_par_option1" name="<?php echo $this->id;?>-registration_fee_par_option1"  value="<?php echo  $this->registration_fee_option1; ?>"/></div>   </div>
                 
				<?php }
				
				if($this->partial_payment_option2=="yes"){
					
					?><br />
						Partial Payment (3 invoices)<input type="radio"  class="pay" id="<?php echo $this->id; ?>-payment_par_option2" name="<?php echo $this->id; ?>-payment-option"  value="3"/>
                 <div id="par_content1" class="same <?php echo $this->id; ?>-payment_par_option2" style="display:none">
				
				Number of parts: 3 
                  <input type="hidden" id="<?php echo $this->id;?>-num_invoices" name="<?php echo $this->id;?>-num_invoices-par2"  value="3" />
                     
					 <div>Invoicing fee: <?php echo $this->inoice_fee_partial_par_option2;?>  <input type="hidden" id="<?php echo $this->id;?>-inoice_fee_partial_option2_par" name="<?php echo $this->id;?>-inoice_fee_partial_par_option2"  value="<?Php echo $this->inoice_fee_partial_par_option2;?>"/> 
					 <br />
					 Registration Fee: <?php echo   $this->registration_fee_par_option2; ?>
					 
					 <input type="hidden" id="<?php echo $this->id;?>-registration_fee_par_option2" name="<?php echo $this->id;?>-registration_fee_par_option2"  value="<?php echo   $this->registration_fee_par_option2; ?>"/></div>   </div>
					<?php
					
					
				}			 if($this->partial_payment_option3=="yes"){
					
					?><br />
						Partial Payment (4 invoices)<input type="radio"  class="pay" id="<?php echo $this->id; ?>-payment_par_option3" name="<?php echo $this->id; ?>-payment-option"  value="4"/>
                 <div id="par_content1" class="same <?php echo $this->id; ?>-payment_par_option3" style="display:none">
				
				Number of parts: 4 
                  <input type="hidden" id="<?php echo $this->id;?>-num_invoices" name="<?php echo $this->id;?>-num_invoices-par3"  value="4" />
                     
					 <div>Invoicing fee: <?php echo $this->inoice_fee_partial_par_option3;?>  <input type="hidden" id="<?php echo $this->id;?>-inoice_fee_partial_option3_par" name="<?php echo $this->id;?>-inoice_fee_partial_par_option3"  value="<?Php echo $this->inoice_fee_partial_par_option3;?>"/>
					 		<br /> 
					 Registration Fee: <?php echo   $this->registration_fee_par_option3; ?>
					 <input type="hidden" id="<?php echo $this->id;?>-registration_fee_par_option3" name="<?php echo $this->id;?>-registration_fee_par_option3"  value="<?php echo   $this->registration_fee_par_option3; ?>"/></div>   </div>
					<?php
					
					
				}	 if($this->partial_payment_option4=="yes"){
					
					?><br />
						Partial Payment (5 invoices)<input type="radio"  class="pay" id="<?php echo $this->id; ?>-payment_par_option4" name="<?php echo $this->id; ?>-payment-option"  value="5"/>
                 <div id="par_content1" class="same <?php echo $this->id; ?>-payment_par_option4" style="display:none">
				
				Number of parts: 5
                  <input type="hidden" id="<?php echo $this->id;?>-num_invoices" name="<?php echo $this->id;?>-num_invoices-par4"  value="5" />
                     
					 <div>Invoicing fee: <?php echo $this->inoice_fee_partial_par_option4;?>  <input type="hidden" id="<?php echo $this->id;?>-inoice_fee_partial_option4_par" name="<?php echo $this->id;?>-inoice_fee_partial_par_option4"  value="<?Php echo $this->inoice_fee_partial_par_option4;?>"/>
					 <br />
					 Registration Fee: <?php echo   $this->registration_fee_par_option4; ?>
					 <input type="hidden" id="<?php echo $this->id;?>-registration_fee_par_option4" name="<?php echo $this->id;?>-registration_fee_par_option4"  value="<?php echo   $this->registration_fee_par_option4; ?>"/></div>   </div>
					<?php
					
					
				}
				if($this->partial_payment_option5=="yes"){
					
					?><br />
						Partial Payment (6 invoices)<input type="radio"  class="pay" id="<?php echo $this->id; ?>-payment_par_option5" name="<?php echo $this->id; ?>-payment-option"  value="6"/>
                 <div id="par_content1" class="same <?php echo $this->id; ?>-payment_par_option5" style="display:none">
				
				Number of parts: 6
                  <input type="hidden" id="<?php echo $this->id;?>-num_invoices" name="<?php echo $this->id;?>-num_invoices-par5"  value="6" />
                     
					 <div>Invoicing fee: <?php echo $this->inoice_fee_partial_par_option5;?>  <input type="hidden" id="<?php echo $this->id;?>-inoice_fee_partial_option5_par" name="<?php echo $this->id;?>-inoice_fee_partial_par_option5"  value="<?Php echo $this->inoice_fee_partial_par_option5;?>"/>
					 <br />
					 Registration Fee: <?php echo   $this->registration_fee_par_option5; ?>
					 <input type="hidden" id="<?php echo $this->id;?>-registration_fee_par_option5" name="<?php echo $this->id;?>-registration_fee_par_option5"  value="<?php echo   $this->registration_fee_par_option5; ?>"/></div>   </div>
					<?php
					
					
				}
				if($this->partial_payment_option6=="yes"){
					
					?><br />
						Partial Payment (7 invoices)<input type="radio"  class="pay" id="<?php echo $this->id; ?>-payment_par_option6" name="<?php echo $this->id; ?>-payment-option"  value="7"/>
                 <div id="par_content1" class="same <?php echo $this->id; ?>-payment_par_option6" style="display:none">
				
				Number of parts: 7
                  <input type="hidden" id="<?php echo $this->id;?>-num_invoices" name="<?php echo $this->id;?>-num_invoices-par6"  value="5" />
                     
					 <div>Invoicing fee: <?php echo $this->inoice_fee_partial_par_option6;?>  <input type="hidden" id="<?php echo $this->id;?>-inoice_fee_partial_option6_par" name="<?php echo $this->id;?>-inoice_fee_partial_par_option6"  value="<?Php echo $this->inoice_fee_partial_par_option6;?>"/>
					 <br />
					 Registration Fee: <?php echo   $this->registration_fee_par_option6; ?>
					 <input type="hidden" id="<?php echo $this->id;?>-registration_fee_par_option6" name="<?php echo $this->id;?>-registration_fee_par_option6"  value="<?php echo   $this->registration_fee_par_option6; ?>"/></div>   </div>
					<?php
					
					
				}
				if($this->partial_payment_option7=="yes"){
					
					?><br />
						Partial Payment (8 invoices)<input type="radio"  class="pay" id="<?php echo $this->id; ?>-payment_par_option7" name="<?php echo $this->id; ?>-payment-option"  value="8"/>
                 <div id="par_content1" class="same <?php echo $this->id; ?>-payment_par_option7" style="display:none">
				
				Number of parts: 8
                  <input type="hidden" id="<?php echo $this->id;?>-num_invoices" name="<?php echo $this->id;?>-num_invoices-par7"  value="8" />
                     
					 <div>Invoicing fee: <?php echo $this->inoice_fee_partial_par_option7;?>  <input type="hidden" id="<?php echo $this->id;?>-inoice_fee_partial_option7_par" name="<?php echo $this->id;?>-inoice_fee_partial_par_option7"  value="<?Php echo $this->inoice_fee_partial_par_option7;?>"/>
					 <br />
					 Registration Fee: <?php echo   $this->registration_fee_par_option4; ?>
					 <input type="hidden" id="<?php echo $this->id;?>-registration_fee_par_option7" name="<?php echo $this->id;?>-registration_fee_par_option7"  value="<?php echo   $this->registration_fee_par_option4; ?>"/></div>   </div>
					<?php
					
					
				}
				if($this->partial_payment_option8=="yes"){
					
					?><br />
						Partial Payment (9 invoices)<input type="radio"  class="pay" id="<?php echo $this->id; ?>-payment_par_option8" name="<?php echo $this->id; ?>-payment-option"  value="9"/>
                 <div id="par_content1" class="same <?php echo $this->id; ?>-payment_par_option8" style="display:none">
				
				Number of parts: 9
                  <input type="hidden" id="<?php echo $this->id;?>-num_invoices" name="<?php echo $this->id;?>-num_invoices-par8"  value="5" />
                     
					 <div>Invoicing fee: <?php echo $this->inoice_fee_partial_par_option8;?>  <input type="hidden" id="<?php echo $this->id;?>-inoice_fee_partial_option8_par" name="<?php echo $this->id;?>-inoice_fee_partial_par_option8"  value="<?Php echo $this->inoice_fee_partial_par_option8;?>"/>
					 <br />
					 Registration Fee: <?php echo   $this->registration_fee_par_option4; ?>
					 <input type="hidden" id="<?php echo $this->id;?>-registration_fee_par_option8" name="<?php echo $this->id;?>-registration_fee_par_option8"  value="<?php echo   $this->registration_fee_par_option8; ?>"/></div>   </div>
					<?php
					
					
				}
				if($this->partial_payment_option9=="yes"){
					
					?><br />
						Partial Payment (10 invoices)<input type="radio"  class="pay" id="<?php echo $this->id; ?>-payment_par_option9" name="<?php echo $this->id; ?>-payment-option"  value="10"/>
                 <div id="par_content1" class="same <?php echo $this->id; ?>-payment_par_option9" style="display:none">
				
				Number of parts:10
                  <input type="hidden" id="<?php echo $this->id;?>-num_invoices" name="<?php echo $this->id;?>-num_invoices-par9"  value="10" />
                     
					 <div>Invoicing fee: <?php echo $this->inoice_fee_partial_par_option9;?>  <input type="hidden" id="<?php echo $this->id;?>-inoice_fee_partial_option9_par" name="<?php echo $this->id;?>-inoice_fee_partial_par_option9"  value="<?Php echo $this->inoice_fee_partial_par_option9;?>"/>
					 <br />
					 Registration Fee: <?php echo   $this->registration_fee_par_option9; ?>
					 <input type="hidden" id="<?php echo $this->id;?>-registration_fee_par_option9" name="<?php echo $this->id;?>-registration_fee_par_option9"  value="<?php echo   $this->registration_fee_par_option9; ?>"/></div>   </div>
					<?php
					
					
				}
				if($this->partial_payment_option10=="yes"){
					
					?><br />
						Partial Payment (11 invoices)<input type="radio"  class="pay" id="<?php echo $this->id; ?>-payment_par_option10" name="<?php echo $this->id; ?>-payment-option"  value="11"/>
                 <div id="par_content1" class="same <?php echo $this->id; ?>-payment_par_option10" style="display:none">
				
				Number of parts: 11
                  <input type="hidden" id="<?php echo $this->id;?>-num_invoices" name="<?php echo $this->id;?>-num_invoices-par10"  value="11" />
                     
					 <div>Invoicing fee: <?php echo $this->inoice_fee_partial_par_option10;?>  <input type="hidden" id="<?php echo $this->id;?>-inoice_fee_partial_option10_par" name="<?php echo $this->id;?>-inoice_fee_partial_par_option10"  value="<?Php echo $this->inoice_fee_partial_par_option10;?>"/>
					 <br />
					 Registration Fee: <?php echo   $this->registration_fee_par_option4; ?>
					 <input type="hidden" id="<?php echo $this->id;?>-registration_fee_par_option10" name="<?php echo $this->id;?>-registration_fee_par_option10"  value="<?php echo   $this->registration_fee_par_option10; ?>"/></div>   </div>
					<?php
					
					
				}
				if($this->partial_payment_option11=="yes"){
					
					?><br />
						Partial Payment (12 invoices)<input type="radio"  class="pay" id="<?php echo $this->id; ?>-payment_par_option11" name="<?php echo $this->id; ?>-payment-option"  value="12"/>
                 <div id="par_content1" class="same <?php echo $this->id; ?>-payment_par_option11" style="display:none">
				
				Number of parts: 12
                  <input type="hidden" id="<?php echo $this->id;?>-num_invoices" name="<?php echo $this->id;?>-num_invoices-par11"  value="12" />
                     
					 <div>Invoicing fee: <?php echo $this->inoice_fee_partial_par_option11;?>  <input type="hidden" id="<?php echo $this->id;?>-inoice_fee_partial_option11_par" name="<?php echo $this->id;?>-inoice_fee_partial_par_option11"  value="<?Php echo $this->inoice_fee_partial_par_option11;?>"/>
					 <br />
					 Registration Fee: <?php echo   $this->registration_fee_par_option11; ?>
					 <input type="hidden" id="<?php echo $this->id;?>-registration_fee_par_option11" name="<?php echo $this->id;?>-registration_fee_par_option11"  value="<?php echo   $this->registration_fee_par_option11; ?>"/></div>   </div>
					<?php
					
					
				}
				
				
				
				
				
			 
			 //echo $this->settings->Full_payment."<div>hhhhhhhhhhhh".$this->Full_payment."</div>";
			 
			 ?>
<input type="hidden" id="<?php echo $this->id;?>-num_days" name="<?php echo $this->id;?>-num_days" value="<?php echo $this->num_of_days_option1;?>"				  />			 
			 <input type="hidden" value="<?php echo $this->payment_username;?>" name="<?php echo $this->id; ?>-username" />
                  <input type="hidden" value="<?php echo $this->payment_password;?>" name="<?php echo $this->id; ?>-payment_password" />
           
             <input type="hidden" value="<?php echo $this->payment_mode;?>" name="<?php echo $this->id; ?>-payment_mode" />
              <input type="hidden" value="<?php echo $this->CreditorPublicId;?>" name="<?php echo $this->id; ?>-CreditorPublicId" />
             
               		
			<div class="clear"></div>
		</fieldset>
		<?php
	}
}
?>
