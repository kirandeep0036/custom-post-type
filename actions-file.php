<?php 


	class Gust_Post{


		public function index(){
			$this->init_hooks();
		}


		public function init_hooks() {
			// self::$initiated = true;
			add_action( 'init',  array( $this, 'resgiter_post_type' ),10, 1);
			add_action( 'admin_menu',  array( $this, 'guest_post_admin_menu' ),10, 2);
			add_action( 'wp_enqueue_scripts', array( $this, 'gust_post_enqueue_files' ),10, 3);
			add_action( 'admin_enqueue_scripts', array( $this, 'gust_post_enqueue_files' ),10, 5);
			add_action( 'wp_ajax_nopriv_get_data', array( $this, 'create_new_post' ),10, 5);
			add_action( 'wp_ajax_get_data', array( $this, 'create_new_post' ),10, 5);
			add_shortcode( 'add_guest_post_form', array( $this, 'create_Guest_Post_Form_shortcode' ),10, 5);
			add_shortcode( 'list_of_posts', array( $this, 'show_posts_list' ),10, 5);
		}

		public function gust_post_enqueue_files() {
		    wp_enqueue_script( 'ajax-script', GUEST_POSR_PLUGIN_DIR_URL. 'assets/js/jquery.min.js', array('jquery') );
		    wp_register_script( 'custom-functions', GUEST_POSR_PLUGIN_DIR_URL. 'assets/js/custom-functions.js?'.time() );
		    wp_enqueue_script( 'custom-functions');
		    wp_enqueue_style( 'guest-post', GUEST_POSR_PLUGIN_DIR_URL. 'assets/css/gues-posts.css?'.time() );
		    wp_localize_script( 'ajax-script', 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		}
		
		// Function to register custom post type Guest Posts
		public function resgiter_post_type(){

				$supports = array(
					'title', // post title
					'editor', // post content
					'author', // post author
					'thumbnail', // featured images
					'excerpt', // post excerpt
					// 'custom-fields', // custom fields
					// 'comments', // post comments
					'revisions', // post revisions
					// 'post-formats', // post formatsss
				);

				$labels = array(
					'name' => _x('Guest Posts', 'plural'),
					'singular_name' => _x('guest_posts', 'singular'),
					'menu_name' => _x('Gust Posts', 'admin menu'),
					'name_admin_bar' => _x('Guest Post', 'admin bar'),
					'add_new' => _x('Add New', 'add new'),
					'add_new_item' => __('Add New'),
					'new_item' => __('New'),
					'edit_item' => __('Edit '),
					'view_item' => __('View'),
					'all_items' => __('All'),
					'search_items' => __('Search Gust Posts'),
					'not_found' => __('No Gust Posts found.'),
				);

				$args = array(
					'supports' => $supports,
					'labels' => $labels,
					'public' => true,
					'query_var' => true,
					'rewrite' => array('slug' => 'guest_posts'),
					'has_archive' => true,
					'hierarchical' => false,
				);

				register_post_type('guest_posts', $args);
		}


		public function guest_post_admin_menu() {
			add_menu_page(
				__( 'Gust Posts Setting', 'my-textdomain' ),
				__( 'Gust Posts Setting', 'my-textdomain' ),
				'manage_options',
				'guest_post_setting',
				array('Gust_Post', 'guest_post_contents'), //callback function setting page
				'dashicons-schedule',
				2
			);
		}


		// Guest Post Setting page function
		public function guest_post_contents() {

				$html 	=    "<H2>Guest Post Shortcodes and Settings</h2><br><br><table cellpadding='10' bgcolor='#f2f2f2'><tbody>";
				$html 	.=	'<tr><td><b>Front end Guest Post create form shortcode: </b></td><td>[add_guest_post_form]</td></tr>';

				echo $html;
		}
		

		//Create Guest Post Function
		public function create_Guest_Post_Form_shortcode(){

			if ( is_user_logged_in() ) {
			   // your code for logged in user 
			

			$html = '<div class="outer-section"><img src="'.GUEST_POSR_PLUGIN_DIR_URL.'assets/imgs/loader.gif" class="loader"><div id="alert-notification"></div>';
			$html .= '<form id="createPostFrom" method="POST" enctype="multipart/form-data">';
			$html .=   '<div class="input-group"><label>Title <span class="required">*</span></label><input id="title" type="text" class="form-controle input" name="title"></div>';
			$html .=   '<div class="input-group"><label>Description</label><textarea id="desc" class="form-constol" name="description" rows="4" cols="50"></textarea></div>';
			$html .=   '<div class="input-group"><label>Excerpt</label><textarea id="excerpt" class="form-constol" name="excerpt" rows="4" cols="50"></textarea></div>';
			$html .=	'<div class="input-group"><label>Select Post Type <span class="required">*</span></label><select class="form-controle" id="post_type" name="post_type"> <option value="">Select Post Type</option>';

		   // Get custom post types
		   	$args = array(
		       'public'   => true,
		       '_builtin' => false,
		    );

		    $output = 'objects';  $operator = 'and'; 

		    $post_types = get_post_types( $args, $output, $operator); 

			foreach ($post_types as $key => $value) {
		       $html .= '<option value="'.$value->name.'">'.$value->label. '</option>';
		    }
			
			$html .= '</select></div>';
			$html .= '<div class="input-group"><label>Featured image</label><input type="file" id="featured_img" name="file" class="form-control"></div>';
			$html .= '<input type="hidden" name ="action" value="get_data">';
			$html .= '<div class="input-group"><button href="javascript:void(0)" id="createform_submit_btn" class="btn btn_primary">Submit</button></div>';
			$html .= '</form></div>';

			} else {
			   $html = "Your should logged in to access this form!";
			}

			return $html;
		}

		public function add_featured_img($data, $postId){

			  	$image_url        = $data['tmp_name']; // Define the image URL here
			    $image_name       = $data['name'];
			    $upload_dir       = wp_upload_dir(); // Set upload folder
			    $image_data       = file_get_contents($image_url); // Get image data
			    $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
			    $filename         = basename( $unique_file_name ); // Create image file name

			    // Check folder permission and define file location
			    if( wp_mkdir_p( $upload_dir['path'] ) ) {
			        $file = $upload_dir['path'] . '/' . $filename;
			    } else {
			        $file = $upload_dir['basedir'] . '/' . $filename;
			    }

			    // Create the image  file on the server
			    file_put_contents( $file, $image_data );

			    // Check image file type
			    $wp_filetype = wp_check_filetype( $filename, null );

			    // Set attachment data
			    $attachment = array(
			        'post_mime_type' => $wp_filetype['type'],
			        'post_title'     => sanitize_file_name( $filename ),
			        'post_content'   => '',
			        'post_status'    => 'pending'
			    );

			    // Create the attachment
			    $attach_id = wp_insert_attachment( $attachment, $file, $postId );

			    // Include image.php
			    require_once(ABSPATH . 'wp-admin/includes/image.php');

			    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
			    wp_update_attachment_metadata( $attach_id, $attach_data );
			    set_post_thumbnail( $postId, $attach_id );
		}


		// Craeted post
		public function create_new_post() {

			parse_str($_POST['formData'], $_POST);

			if(isset($_POST['action'])){

				if($_POST['title']  == ''  ||  $_POST['post_type'] == '' ){
					$response =  array('response' => 'faild', 'message' => 'Please select required fields!');
					echo json_encode($response );
					die();
				}

				if($_FILES['file']['name']){
					$allowed = array('gif', 'png', 'jpg');
					$filename = $_FILES['file']['name'];
					$ext = pathinfo($filename, PATHINFO_EXTENSION);

					if (!in_array($ext, $allowed)) {
					    $response =  array('response' => 'faild', 'message' => 'Please upload a valid type image!');
						echo json_encode($response );
						die();
					}
				}

				// Get current user data
				$current_user_data = wp_get_current_user();
				$userID = $current_user_data->data->ID;
				$userEmail = $current_user_data->data->user_email;


				$new_post = array(
					'post_title' => $_POST['title'],
					'post_content' => $_POST['description'],
					'post_excerpt' => $_POST['excerpt'],
					'post_status' => 'pending',
					'post_date' => date('Y-m-d H:i:s'),
					'post_author' => $userID,
					'post_type' => $_POST['post_type'],
				);

				$post_id = wp_insert_post($new_post);

				// Check if post created succesfully
				if($post_id){

					$userEmail = 'sadhrao006@gmail.com';

					//Call featured image upload function
					Gust_Post::add_featured_img($_FILES['file'], $post_id);

					$subject = 'New Post added nitification on '.get_site_url();
					$message = 'Hello, \n there is a new post added on your website. Please review and publish it';

					//Call Send Email function
					$this->send_email($userEmail, $subject, $message);

					$response =  array('response' => 'success', 'message' => 'Post created successfully!');
					echo json_encode($response);
				}

			}
			wp_die();
		}

		// Email Send
		public function send_email($email, $subject, $message){
			
			$to = $email;
			$subject = $subject;
			$body = $message;
			$headers = array('Content-Type: text/html; charset=UTF-8');
			wp_mail( $to, $subject, $body, $headers );

		}


		public function show_posts_list(){

			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			
			$args = array('post_type' => array('guest_posts', 'post'), 'post_status' => 'pending', 'posts_per_page' => 10, 'paged' => $paged );

			$loop = new WP_Query($args );

			if ( $loop->have_posts() ) :
		        while ( $loop->have_posts() ) : $loop->the_post(); ?>
		            <div class="pindex">
		                <div class="ptitle">
		                    <h2><a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
		                </div>
		            </div>
		        <?php endwhile;
		        
		    endif;
		    ?>
		    <nav class="pagination">
		        <?php $this->pagination_bar( $loop ); ?>
		    </nav>

		    <?php
		    wp_reset_postdata();

		}

		// Pagination function
		public function pagination_bar( $custom_query ) {
		    $total_pages = $custom_query->max_num_pages;
		    $big = 999999999; // need an unlikely integer

		    if ($total_pages > 1){
		        $current_page = max(1, get_query_var('paged'));

		        echo paginate_links(array(
		            'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		            'format' => '?paged=%#%',
		            'current' => $current_page,
		            'total' => $total_pages,
		        ));
		    }
		}

	}

	




 ?>