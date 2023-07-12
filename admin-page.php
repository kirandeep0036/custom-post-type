<?php

class Gust_Post
{

    public function index()
    {
        $this->init_hooks();
    }

    public function init_hooks()
    {
        // self::$initiated = true;
        add_action('admin_menu', array($this, 'guest_post_admin_menu'), 5, 1);
        add_action('init', array($this, 'resgiter_post_type'), 1, 1);
        add_action('wp_enqueue_scripts', array($this, 'gust_post_enqueue_files'), 10, 3);
        add_action('admin_enqueue_scripts', array($this, 'gust_post_enqueue_files'), 10, 5);
        add_action('init', array($this, 'create_guestPostType_taxonomies'), 0);
        add_shortcode('add_guest_post_form', array($this, 'create_Guest_Post_Form_shortcode'));
        add_shortcode('list_of_posts', array($this, 'show_posts_list'));
    }

    public function gust_post_enqueue_files()
    {
        wp_enqueue_script('ajax-script', CUSTOM_POST_TYPE_PLUGIN_DIR . 'assets/js/jquery.min.js', array('jquery'));
        wp_register_script('custom-functions', CUSTOM_POST_TYPE_PLUGIN_DIR . 'assets/js/custom-functions.js?' . time());
        wp_enqueue_script('custom-functions');
        wp_enqueue_style('guest-post', CUSTOM_POST_TYPE_PLUGIN_DIR . 'assets/css/gues-posts.css?' . time());
        wp_localize_script('ajax-script', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    }

    // Guest Post Setting page function
    public function guest_post_contents()
    {
        if (isset($_POST['save'])) {
            array_pop($_POST);
            update_option('custom_post_options', $_POST);
        }
        $postOptions = get_option('custom_post_options');
        $postTItle = $postOptions ? $postOptions['post_name'] : 'Guest Posts';
        $postSlug = $postOptions ? $postOptions['post_slug'] : 'guest_posts';
        $categoryName = $postOptions ? $postOptions['category_name'] : 'guest_posts';
        $categorySlug = $postOptions ? $postOptions['category_slug'] : 'guest_posts';

        $html = "<form method='post'><table cellpadding='10' bgcolor='#f2f2f2'><tbody>";
        $html .= '<tr><td><h2>Shortcode to display posts listing on the page: </h2></td><td>[list_of_posts]</td></tr>';
        $html .= '<tr><td colspan="2"><h2>Custom Post Type Settings</h2></tr>';
        if ($postOptions) {
            $html .= '<tr><td>Post Name: <td><td><input type="text" name="post_name" value="' . $postTItle . '"></td></tr>';
            $html .= '<tr><td>Post Slug: <td><td><input type="text" name="post_slug" value="' . $postSlug . '"></td></tr>';
            $html .= '<tr><td>Category Name: <td><td><input type="text" name="category_name" value="' . $categoryName . '"></td></tr>';
            $html .= '<tr><td>Category Slug: <td><td><input type="text" name="category_slug" value="' . $categorySlug . '"></td></tr>';
        } else {
            $html .= '<tr><td>Post Name: <td><td><input type="text" name="post_name" required></td></tr>';
            $html .= '<tr><td>Post Slug: <td><td><input type="text" name="post_slug" required></td></tr>';
            $html .= '<tr><td>Category Name: <td><td><input type="text" name="category_name" required></td></tr>';
            $html .= '<tr><td>Category Slug: <td><td><input type="text" name="category_slug" required></td></tr>';
        }
        $html .= '<tr><td colspan="2"><input type="submit" name="save" class="components-button editor-post-trash is-secondary is-destructive" value="Save"></td></tr>';
        $html .= '</tbody></html</form>';
        echo $html;

    }

    // Function to register custom post type Guest Posts
    public function resgiter_post_type()
    {

        $supports = array(
            'title',
            // post title
            'editor',
            // post content
            'author',
            // post author
            'thumbnail',
            // featured images
            'excerpt',
            // post excerpt
            // 'custom-fields', // custom fields
            // 'comments', // post comments
            'revisions', // post revisions
            // 'post-formats', // post formatsss
        );

        $postOptions = get_option('custom_post_options');
        $postTItle = $postOptions ? $postOptions['post_name'] : 'Guest Posts';
        $postSlug = $postOptions ? $postOptions['post_slug'] : 'Guest Posts';

        $labels = array(
            'name' => _x($postTItle, 'plural'),
            'singular_name' => _x($postSlug, 'singular'),
            'menu_name' => _x($postTItle, 'admin menu'),
            'name_admin_bar' => _x($postTItle, 'admin bar'),
            'add_new' => _x('Add New', 'add new'),
            'add_new_item' => __('Add New'),
            'new_item' => __('New'),
            'edit_item' => __('Edit '),
            'view_item' => __('View'),
            'all_items' => __('All'),
            'search_items' => __("Search $postTItle"),
            'not_found' => __("No $postTItle found"),
        );

        $args = array(
            'supports' => $supports,
            'labels' => $labels,
            'public' => true,
            'query_var' => true,
            'rewrite' => array('slug' => $postSlug),
            'has_archive' => true,
            'hierarchical' => false,
            'menu_icon' => _("dashicons-admin-page"),
        );

        register_post_type($postSlug, $args);
    }

    public function create_guestPostType_taxonomies()
    {
        $postOptions = get_option('custom_post_options');
        $postSlug = $postOptions ? $postOptions['post_slug'] : 'guest_posts';
        $categoryName = $postOptions ? $postOptions['category_name'] : 'Guest Category';
        $categorySlug = $postOptions ? $postOptions['category_slug'] : 'guest_category';

        $labels = array(
            'name' => _x('Guest Categories', 'taxonomy general name', 'textdomain'),
            'singular_name' => _x($categoryName, 'taxonomy singular name', 'textdomain'),
            'search_items' => __("Search " . $categoryName, 'textdomain'),
            'all_items' => __('All ' . $categoryName . 's', 'textdomain'),
            'parent_item' => __('Parent ' . $categoryName, 'textdomain'),
            'parent_item_colon' => __('Parent ' . $categoryName, 'textdomain'),
            'edit_item' => __('Edit ' . $categoryName, 'textdomain'),
            'update_item' => __('Update ' . $categoryName, 'textdomain'),
            'add_new_item' => __('Add New Category', 'textdomain'),
            'new_item_name' => __('New Category Name', 'textdomain'),
            'menu_name' => __($categoryName, 'textdomain'),
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => $categorySlug),
        );

        register_taxonomy($categorySlug, array($postSlug), $args);
    }

    public function guest_post_admin_menu()
    {
        $postOptions = get_option('custom_post_options');
        $postTItle = $postOptions ? $postOptions['post_name'] : 'Guest Posts';
        $postSlug = $postOptions ? $postOptions['post_slug'] : 'guest_posts';

        add_menu_page(
            __($postTItle . ' Setting', 'my-textdomain'),
            __($postTItle . ' Posts Setting', 'my-textdomain'),
            'manage_options',
            'guest_post_setting',
            array($this, 'guest_post_contents'),
            //callback function setting page
            'dashicons-admin-settings',
            30
        );

    }

    public function add_featured_img($data, $postId)
    {

        $image_url = $data['tmp_name']; // Define the image URL here
        $image_name = $data['name'];
        $upload_dir = wp_upload_dir(); // Set upload folder
        $image_data = file_get_contents($image_url); // Get image data
        $unique_file_name = wp_unique_filename($upload_dir['path'], $image_name); // Generate unique name
        $filename = basename($unique_file_name); // Create image file name

        // Check folder permission and define file location
        if (wp_mkdir_p($upload_dir['path'])) {
            $file = $upload_dir['path'] . '/' . $filename;
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }

        // Create the image  file on the server
        file_put_contents($file, $image_data);

        // Check image file type
        $wp_filetype = wp_check_filetype($filename, null);

        // Set attachment data
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'pending',
        );

        // Create the attachment
        $attach_id = wp_insert_attachment($attachment, $file, $postId);

        // Include image.php
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
        wp_update_attachment_metadata($attach_id, $attach_data);
        set_post_thumbnail($postId, $attach_id);
    }

    public function show_posts_list()
    {
        $postOptions = get_option('custom_post_options');
        $postSlug = $postOptions['post_slug'] ? $postOptions['post_slug'] : 'guest_posts';

        ob_start();
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

        $args = array('post_type' => array($postSlug), 'post_status' => 'publish', 'posts_per_page' => 10, 'paged' => $paged);

        $loop = new WP_Query($args);

        if ($loop->have_posts()):
            while ($loop->have_posts()):
                $loop->the_post();?>
										<div class="pindex">
											<div class="ptitle">

												<h2><a href="<?php the_permalink(get_the_ID());?>"><?php echo get_the_title(); ?></a></h2>
												<div class="thumnailofPost">
													<?php if (has_post_thumbnail(get_the_ID())) {
                    echo get_the_post_thumbnail(get_the_ID(), 'medium_large', ['class' => 'img-responsive responsive--full', 'title' => get_the_title()]);
                } else {?>
														<img src="<?php echo plugin_dir_url(__FILE__); ?>/assets/imgs/default.jpg" alt="thumbnail">
													<?php }?>

												</div>
												<p>
													<?php echo get_the_excerpt(); ?>
												</p>
											</div>
										</div>
									<?php endwhile;

        endif;
        ?>
		<nav class="pagination">
			<?php $this->pagination_bar($loop);?>
		</nav>

			<?php
wp_reset_postdata();
        $output = ob_get_clean();
        return $output;

    }

    // Pagination function
    public function pagination_bar($custom_query)
    {
        $total_pages = $custom_query->max_num_pages;
        $big = 999999999; // need an unlikely integer

        if ($total_pages > 1) {
            $current_page = max(1, get_query_var('paged'));

            echo paginate_links(
                array(
                    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                    'format' => '?paged=%#%',
                    'current' => $current_page,
                    'total' => $total_pages,
                )
            );
        }
    }

    //Create Guest Post Function
    public function create_Guest_Post_Form_shortcode()
    {

        if (is_user_logged_in()) {
            // your code for logged in user

            $html = '<div class="outer-section"><img src="' . CUSTOM_POST_TYPE_PLUGIN_DIR . 'assets/imgs/loader.gif" class="loader"><div id="alert-notification"></div>';
            $html .= '<form id="createPostFrom" method="POST" enctype="multipart/form-data">';
            $html .= '<div class="input-group"><label>Title <span class="required">*</span></label><input id="title" type="text" class="form-controle input" name="title"></div>';
            $html .= '<div class="input-group"><label>Description</label><textarea id="desc" class="form-constol" name="description" rows="4" cols="50"></textarea></div>';
            $html .= '<div class="input-group"><label>Excerpt</label><textarea id="excerpt" class="form-constol" name="excerpt" rows="4" cols="50"></textarea></div>';
            $html .= '<div class="input-group"><label>Select Post Type <span class="required">*</span></label><select class="form-controle" id="post_type" name="post_type"> <option value="">Select Post Type</option>';

            // Get custom post types
            $args = array(
                'public' => true,
                '_builtin' => false,
            );

            $output = 'objects';
            $operator = 'and';

            $post_types = get_post_types($args, $output, $operator);

            foreach ($post_types as $key => $value) {
                $html .= '<option value="' . $value->name . '">' . $value->label . '</option>';
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

}

?>