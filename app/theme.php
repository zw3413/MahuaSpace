<?php

	/**
	 * 1.0
	 * @package    Madara
	 * @author     Mangabooth <mangabooth@gmail.com>
	 * @copyright  Copyright (C) 2018 mangabooth.com. All Rights Reserved
	 *
	 * Websites: https://mangabooth.com/
	 */

	namespace App;

	// Prevent direct access to this file
	defined( 'ABSPATH' ) || die( 'Direct access to this file is not allowed.' );

	require( get_template_directory() . '/app/core.php' );

	require( 'lib/walker_mobile_menu.class.php' );

	if ( class_exists( 'WP_MANGA' ) ) {
		/*
		 * check plugin wp-manga active or not.
		 * */
		require( get_template_directory() . '/madara-core/manga-functions.php' );

	}

	/**
	 * Core class.
	 *
	 * @package  Madara
	 * @since    1.0
	 */
	class MadaraStarter extends Madara {

		private static $instance;

		public static function getInstance() {
			if ( null == self::$instance ) {
				self::$instance = new MadaraStarter();
			}

			return self::$instance;
		}

		/**
		 * Initialize Madara Core.
		 *
		 * @return  void
		 */
		public function initialize() {
			add_action( 'template_redirect', array( $this, 'set_content_width' ), 0 );

			parent::initialize();

			if ( class_exists( 'woocommerce' ) ) {
				Plugins\madara_WooCommerce\WooCommerce::initialize();
			}

			/**
			 * Custom template tags and functions for this theme.
			 */
			require( get_template_directory() . '/inc/template-tags.php' );
			require( get_template_directory() . '/inc/extras.php' );
			require( get_template_directory() . '/inc/hooks.php' );
			
			add_action( 'after_setup_theme', array( $this, 'addThemeSupport' ) );
			add_action( 'widgets_init', array( $this, 'registerSidebar' ) );
			add_action( 'after_setup_theme', array( $this, 'registerNavMenus' ) );
			add_action('init', array($this, 'init'));


			add_action( 'wp_enqueue_scripts', array( $this, 'enqueueScripts' ) );

			add_action( 'madara_release_logs', array( $this, 'release_logs' ) );
			add_filter( 'theme_page_templates', array( $this, 'makewp_exclude_page_templates' ) );

		}
		
		function init(){
			if($this->getOption('amp', 'off') == 'on'){
				require( get_template_directory() . '/inc/amp.php' );
			}
		}

		/**
		 * Set the content width in pixels, based on the theme's design and stylesheet.
		 *
		 * Priority 0 to make it available to lower priority callbacks.
		 *
		 * @global int $content_width
		 */
		function set_content_width() {

			$content_width = 980;

			$GLOBALS['content_width'] = apply_filters( 'madara_content_width', $content_width );
		}

		/**
		 * Hides the custom post template for pages on WordPress 4.6 and older
		 *
		 * @param array $post_templates Array of page templates. Keys are filenames, values are translated names.
		 *
		 * @return array Filtered array of page templates.
		 */
		function makewp_exclude_page_templates( $post_templates ) {
			if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
				// unset( $post_templates['page-templates/my-full-width-post-template.php'] );
			}

			return $post_templates;
		}

		/**
		 * Add Theme Support
		 *
		 * @return void
		 */
		function addThemeSupport() {

			load_theme_textdomain( 'madara', get_template_directory() . '/languages' );

			add_theme_support( 'automatic-feed-links' );

			add_theme_support( "title-tag" );

			add_theme_support( 'post-thumbnails' );

			add_theme_support( 'custom-background' );

			add_theme_support( 'custom-header' );

			add_theme_support( 'html5', array(
				'comment-form',
				'comment-list',
				'search-form',
				'gallery',
				'caption',
			) );
			
			add_theme_support( 'wp-block-styles' );
			add_theme_support( 'responsive-embeds' );
			add_theme_support( 'align-wide' );
			add_theme_support( 'align-full' );

			// register thumb sizes
			do_action( 'madara_reg_thumbnail' );
		}

		/**
		 * Madara Sidebar Init
		 *
		 * @since Madara Alpha 1.0
		 */
		function registerSidebar() {
			/*
			 * register WP Manga Main Top Sidebar & WP Manga Main Top Second Sidebar when plugin wp-manga activated.
			 * */
			do_action( 'madara_add_manga_sidebar' );

			$main_sidebar_before_widget = apply_filters( 'madara_main_sidebar_before_widget', '<div class="row"><div id="%1$s" class="widget %2$s"><div class="widget__inner %2$s__inner c-widget-wrap">' );
			$main_sidebar_after_widget  = apply_filters( 'madara_main_sidebar_after_widget', '</div></div></div>' );

			$before_widget = apply_filters( 'madara_sidebar_before_widget', '<div id="%1$s" class="widget %2$s"><div class="widget__inner %2$s__inner c-widget-wrap">' );
			$after_widget  = apply_filters( 'madara_sidebar_after_widget', '</div></div>' );

			$before_title = apply_filters( 'madara_sidebar_before_title', '<div class="widget-heading font-nav"><h5 class="heading">' );
			$after_title  = apply_filters( 'madara_sidebar_after_title', '</h5></div>' );

			register_sidebar( array(
				'name'          => esc_html__( 'Main Sidebar', 'madara' ),
				'id'            => 'main_sidebar',
				'description'   => esc_html__( 'Main Sidebar used by all pages', 'madara' ),
				'before_widget' => $main_sidebar_before_widget,
				'after_widget'  => $main_sidebar_after_widget,
				'before_title'  => $before_title,
				'after_title'   => $after_title,
			) );

			register_sidebar( array(
				'name'          => esc_html__( 'Single Post Sidebar', 'madara' ),
				'id'            => 'single_post_sidebar',
				'description'   => esc_html__( 'Appear in Single Post', 'madara' ),
				'before_widget' => $main_sidebar_before_widget,
				'after_widget'  => $main_sidebar_after_widget,
				'before_title'  => $before_title,
				'after_title'   => $after_title,
			) );

			register_sidebar( array(
				'name'          => esc_html__( 'Search Sidebar', 'madara' ),
				'id'            => 'search_sidebar',
				'description'   => esc_html__( 'Search Sidebar in header', 'madara' ),
				'before_widget' => $before_widget,
				'after_widget'  => $after_widget,
				'before_title'  => $before_title,
				'after_title'   => $after_title,
			) );

			register_sidebar( array(
				'name'          => esc_html__( 'Main Top Sidebar', 'madara' ),
				'id'            => 'top_sidebar',
				'description'   => esc_html__( 'Appear before main content', 'madara' ),
				'before_widget' => $before_widget,
				'after_widget'  => $after_widget,
				'before_title'  => $before_title,
				'after_title'   => $after_title,
			) );

			register_sidebar( array(
				'name'          => esc_html__( 'Main Top Second Sidebar', 'madara' ),
				'id'            => 'top_second_sidebar',
				'description'   => esc_html__( 'Appear before main content', 'madara' ),
				'before_widget' => $before_widget,
				'after_widget'  => $after_widget,
				'before_title'  => $before_title,
				'after_title'   => $after_title,
			) );

			register_sidebar( array(
				'name'          => esc_html__( 'Body Top Sidebar', 'madara' ),
				'id'            => 'body_top_sidebar',
				'description'   => esc_html__( 'Appear before body content', 'madara' ),
				'before_widget' => $before_widget,
				'after_widget'  => $after_widget,
				'before_title'  => $before_title,
				'after_title'   => $after_title,
			) );

			register_sidebar( array(
				'name'          => esc_html__( 'Body Bottom Sidebar', 'madara' ),
				'id'            => 'body_bottom_sidebar',
				'description'   => esc_html__( 'Appear after body content', 'madara' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget__inner %2$s__inner c-widget-wrap">',
				'after_widget'  => '</div></div>',
				'before_title'  => '<div class="widget-title"><div class="c-blog__heading style-2 font-heading"><h4>',
				'after_title'   => '</h4></div></div>',
			) );

			register_sidebar( array(
				'name'          => esc_html__( 'Main Bottom Sidebar', 'madara' ),
				'id'            => 'bottom_sidebar',
				'description'   => esc_html__( 'Appear after main content', 'madara' ),
				'before_widget' => $before_widget,
				'after_widget'  => $after_widget,
				'before_title'  => $before_title,
				'after_title'   => $after_title,
			) );
			
			register_sidebar( array(
				'name'          => esc_html__( 'Footer Sidebar', 'madara' ),
				'id'            => 'footer_sidebar',
				'description'   => esc_html__( 'Appear in Footer', 'madara' ),
				'before_widget' => $before_widget,
				'after_widget'  => $after_widget,
				'before_title'  => $before_title,
				'after_title'   => $after_title,
			) );
		}

		/**
		 * Register Menu Location
		 *
		 * @since Madara Alpha 1.0
		 */
		function registerNavMenus() {
			register_nav_menus( array(
				'primary_menu'   => esc_html__( 'Primary Menu', 'madara' ),
				'secondary_menu' => esc_html__( 'Secondary Menu', 'madara' ),
				'mobile_menu'    => esc_html__( 'Mobile Menu', 'madara' ),
				'user_menu'      => esc_html__( 'User Menu', 'madara' ),
				'footer_menu'    => esc_html__( 'Footer Menu', 'madara' ),
			) );
		}

		/**
		 * Enqueue needed scripts
		 */
		function enqueueScripts() {
			//wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i' );

			if ( $this->getOption( 'loading_fontawesome', 'on' ) == 'on' ) {
				wp_enqueue_style( 'fontawesome', get_parent_theme_file_uri( '/app/lib/fontawesome/web-fonts-with-css/css/all.min.css' ), array(), '5.2.0' );
			}
			if ( $this->getOption( 'loading_ionicons', 'on' ) == 'on' ) {
				wp_enqueue_style( 'ionicons', get_parent_theme_file_uri( '/css/fonts/ionicons/css/ionicons.min.css' ), array(), '4.3.3' );
			}
			if ( $this->getOption( 'loading_ct_icons', 'on' ) == 'on' ) {
				wp_enqueue_style( 'madara-icons', get_parent_theme_file_uri( '/css/fonts/ct-icon/ct-icon.css' ) );
			}

			wp_enqueue_style( 'bootstrap', get_parent_theme_file_uri( '/css/bootstrap.min.css' ), array(), '4.3.1' );
			wp_enqueue_style( 'slick', get_parent_theme_file_uri( '/js/slick/slick.css' ) );
			wp_enqueue_style( 'slick-theme', get_parent_theme_file_uri( '/js/slick/slick-theme.css' ) );
			//Temporary
			wp_enqueue_style( 'loaders', get_parent_theme_file_uri( '/css/loaders.min.css' ) );
			wp_enqueue_style( 'madara-css', get_stylesheet_uri() );

			wp_enqueue_script( 'imagesloaded' );
			wp_enqueue_script( 'slick', get_parent_theme_file_uri( '/js/slick/slick.min.js' ), array( 'jquery' ), '1.7.1', true );
			wp_enqueue_script( 'aos', get_parent_theme_file_uri( '/js/aos.js' ), array(), '', true );
			wp_enqueue_script( 'madara-js', get_parent_theme_file_uri( '/js/template.js' ), array(
				'jquery',
				'bootstrap',
				'shuffle'
			), '1.6.3', true );

			//if ( $this->getOption( 'archive_navigation', 'default' ) == 'ajax' ) {
			wp_enqueue_script( 'madara-ajax', get_parent_theme_file_uri( '/js/ajax.js' ), array( 'jquery' ), '', true );
			//}

			$js_params = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );

			global $wp_query, $wp;

			$js_params['query_vars']  = $wp_query->query_vars;
			$js_params['current_url'] = home_url( $wp->request );

			wp_localize_script( 'madara-js', 'madara', apply_filters( 'madara_js_params', $js_params ) );

			/**
			 * Add Custom CSS
			 */
			require( get_template_directory() . '/css/custom.css.php' );
			$custom_css = madara_custom_CSS();
			wp_add_inline_style( 'madara-css', $custom_css );
		}		

		public function release_logs() {
			?>
            <ul>
				<li>Version 1.6.4 - 2020.06.02<br/>
					<ul>
						<li>#Add: option to show Manga Author instead of post author in Manga meta tags (Theme Options > Manga Detail Page > Manga Single - Meta Tags for Authors)</li>
						<li>#Add: option to manage maximum numbers of bookmarked items to prevent out of memory issue (WP Manga Settings > Single Manga Settings > Maximum Bookmark Items)</li>
						<li>#Update: able to multi-select chapters in admin and delete selected chapters</li>						
						<li>#Update: support Ajax Search in the Search Results page for manga</li>
						<li>#Update: support PHP 7.4.6</li>
						<li>#Fix: bookmark icons are hidden on mobile in Video Chapter</li>
						<li>#Fix: invalid Rating data tag if a manga does not have rating</li>
						<li>#Update: add some hooks & filters in admin to further support add-ons</li>
					</ul>
				</li>
				<li>Version 1.6.3.2 - 2020.04.26<br/>
					<ul>
						<li>#Fix: latest few chapters are deleted if a novel is permanently removed</li>
						<li>#Fix: some minor CSS issues</li>
					</ul>
				</li>
				<li>Version 1.6.3.1 - 2020.04.17<br/>
					<ul>
						<li>#Add: option to turn off lazy-load Chapters List (Theme Options > Manga Detail Page)</li>
						<li>#Fix: button Chapters Sort does not work in lazy-load Chapters List</li>
						<li>#Fix: some minor CSS issues</li>
					</ul>
				</li>
				<li>Version 1.6.3 - 2020.04.09<br/>
					<ul>
						<li>#Update: support .gif thumbnail for Manga Thumbnail</li>
						<li>#Update: add "Upcoming" status for Manga</li>
						<li>#Update: add "Amazon CDN URL" option</li>
						<li>#Update: lazy-load Chapters List, greatly improve loading speed</li>
						<li>#Fix: Google Snippet validation</li>
						<li>#Fix: Chapter & Server selectbox of Video Chapters are hidden on mobile</li>						
						<li>#Fix: search new term in second results page goes wrong</li>
						<li>#Fix: 18+ popup does not show if visitors go to chapter directly</li>						
						<li>#Fix: remove Chapter Content in database if a text chapter is deleted</li>
					</ul>
				</li>
				<li>Version 1.6.2.2 - 2020.01.08<br/>
					<ul>
						<li>#Fix: AMP validation issues</li>
						<li>#Fix: missing chapter navigation in Video Chapter on mobile</li>
						<li>#Improve: load Reading Histories via ajax to prevent caching</li>
					</ul>
				</li>
				<li>Version 1.6.2.1 - 2019.12.22<br/>
					<ul>
						<li>#Fix: bug with existing manga histories</li>
						<li>#Update: language files</li>
					</ul>
				</li>
				<li>Version 1.6.2 - 2019.12.12<br/>
					<ul>
						<li>#Add: option to store Guest reading history</li>
						<li>#Add: support AMP. Enable in Theme Options > AMP</li>
						<li>Update: reduce DB calls to import performance</li>
						<li>#Update: add Index when uploading new single chapter</li>
						<li>#Update: able to use same .zip file structure with multiple-chapters upload when upload single chapter</li>
						<li>#Update: update some outdated external libraries</li>
						<li>#Fix: improve some minor CSS and layout issues</li>
					</ul>
				</li>
				<li>Version 1.6.1.3 - 2019.10.21<br/>
					<ul>
						<li>#Update: Amazon S3 library (use of REST API)</li>
						<li>#Fix: some WP Manga Settings do not work (error in saving settings)</li>
						<li>#Fix: CSS issues</li>
						<li>#Fix: some minor bugs</li>
						<li>#Fix: cannot choose Volume when uploading multiple chapters</li>
					</ul>
				</li>
				<li>Version 1.6.1.2 - 2019.09.30<br/>
					<ul>
						<li>#Improve: Manga Listing shortcode to filter by Manga Type, Manga Status and Following (bookmarked by current user)</li>
						<li>#Improve: require to update Option Tree 2.7.3 for security fix</li>
						<li>#Improve: Next/Prev volume for Novel (Text) chapters</li>
						<li>#Improve: specify Chapter Extend Name when uploading multiple chapters (use "--" separator)</li>
						<li>#Improve: Manga Listing shortcode (Chapter mode) to support Chapter Thumbnail plugin</li>
						<li>#Fix: cannot add shortcodes in Classic Editor</li>
						<li>#Fix: comment pagination links are incorrect</li>
						<li>#Fix: some minor bugs</li>
						<li>#Add: option to order chapter reversely in Manga Detail page (Theme Options > Manga Detail)</li>
						<li>#Fix: Manga Badge overlap with Adult badge</li>
						<li>#Fix: cannot use keyboard navigation in List reading mode</li>
					</ul>
				</li>
				<li>Version 1.6.1.1 - 2019.08.22<br/>
					<ul>
						<li>#Fix: missing Manga Genres in detail page</li>
						<li>#Fix: users cannot change from List Reading style to Paged Reading style</li>
						<li>#Fix: Sticky navigation is overlapped</li>
						<li>#Fix: Manga Comments Count is incorrect in detail page</li>
						<li>#Add: option to turn on Sticky Navigation on mobile (in Theme Options > Manga Reading Layout</li>
					</ul>
				</li>
				<li>Version 1.6.1 - 2019.08.20<br/>
					<ul>
						<li>#Add: able to sort chapters in front-end in Manga Detail page</li>
						<li>#Add: option to exclude Genres, Tags or Authors from Manga Advance Search results (in Theme Options > Search)</li>
						<li>#Add: option to move Manga Badge to before Thumbnail (in Theme Options > Manga Archives Layout)</li>
						<li>#Add: shortcode [manga_grid]</li>
						<li>#Add: option to toggle "Image Gaps" for each Manga</li>
						<li>#Add: option to disable "Click To Scroll" while reading (in Manga > WP Manga Settings</li>
						<li>#Add: option to filter manga by Status in Front-Page template</li>
						<li>#Improve: Manga Advance Search to filter by Adult Content, and choose Genres condition between OR and AND</li>
						<li>#Improve: new "item_layout" option (and new layout value: "Chapters") for [manga_listing] shortcodes</li>
						<li>#Improve: update Heading (H1 --> H6) structure for better SEO</li>
						<li>#Improve: limit Manga Views Ranking cache to 100 top viewed mangas to improve performance</li>
						<li>#Improve: update Manga Single template file structure (for developer)</li>
						<li>#Improve: show chosen image file name when upload user avatar</li>
						<li>#Improve: sticky header for minimal reading layout</li>
						<li>#Fix: ajax-search does not work</li>
						<li>#Fix: cannot open Chapter Navigation selectbox on mobile</li>
					</ul>
				</li>
				<li>Version 1.6.0.1 - 2019.07.19<br/>
					<ul>
						<li>#Fix: error in navigation paged chapter</li>
						<li>#Fix: the "Back to Manga Info" button only works in paged chapter. Now it works for both Manga (paged- and list-type chapter) and Text/Video</li>
						<li>#Fix: search by Status doesn't work</li>
						<li>#Fix: format the All Time Views value to human-friendly</li>
						<li>#Fix: Sort by Rating (and Number of Votes) only work for front-page template. Now it works for Archives Manga page</li>
						<li>#Fix: cannot upload multiple Text/Video Chapters to specific volume</li>
						<li>#Improve: (Madara Shortcodes) get current Manga ID for [manga_chapters] and [manga_info] shortcode if it is not passed in</li>
					</ul>
				</li>
				<li>Version 1.6 - 2019.07.15<br/>
					<ul>
						<li>#Add: Minimal Reading Layout for Chapter Reading page (in Theme Options > WP Manga Reading Layout)</li>
						<li>#Add: Reading Toolbar for Novel type (in front-end)</li>
						<li>#Add: option to change displaying Monthly Views by All Time Views in Manga Info page</li>
						<li>#Add: Random Order in WP Manga Posts widget</li>
						<li>#Add: option to turn off Reading Settings tab in User Settings page</li>
						<li>#Add: (WP Manga Shortcodes) shortcode [manga_info]</li>
						<li>#Add: option to show/hide Chapter Heading (in Theme Options > WP Manga Reading Layout)</li>
						<li>#Add: option to filter Mangas by Genres and Tags for Front-Page template</li>						
						<li>#Add: option to add link back to Manga Info page if users are reading the last page of the last chapter</li>
						<li>#Add: option to disable User Avatar Upload feature</li>
						
						<li>#Improve: click to scroll when reading novel</li>
						<li>#Improve: remove old avatar when users upload new ones</li>
						<li>#Improve: sort by Rating now counts for Number of Votes</li>
						<li>#Improve: open Sign In/Sign Up popup if users are not logged in to comment</li>
						<li>#Improve: add Chapter URL in comment notification email content</li>
						<li>#Improve: count Views by ajax so it works with Cache plugins</li>
						<li>#Improve: visited chapter links have grey color</li>
						<li>#Improve: small issues</li>
						<li>#Fix: WP Manga Slider widget - filter by tag does not work</li>
						<li>#Fix: cannot open mobile navigation bar when reading chapter after prev/next chapter</li>	
					</ul>
				</li>
				<li>Version 1.5.5.3 - 2019.05.29<br/>
					<ul>
						<li>#Add: option to hide Page Title and Page Meta for Front-Page template</li>
						<li>#Improve: use default setting for Chapter Link in WP SEO Sitemap (previously use "List" style</li>
						<li>#Improve: do not need "page/1" suffix in chapter link</li>
						<li>#Improve: failure message when upload to cloud storage</li>
						<li>#Improve: support filter custom badges for Manga</li>
						<li>#Fix: month views is reset</li>
						<li>#Fix: order by week views is incorrect</li>
						<li>#Fix: cannot zip to download if chapter is in cloud storage</li>
						<li>#Fix: cannot set default background for Manga detail page</li>
						<li>#Fix: average star rating is rounded incorrectly</li>
						<li>#Fix: saving reading history is not proper</li>
						<li>#Fix: saving user settings is not proper</li>
					</ul>
				</li>
				<li>Version 1.5.5.1 - 2019.04.22<br/>
					<ul>
						<li>#Fix: unable to upload to cloud</li>
						<li>#Fix: clicking on reading panel causes page load to next chapter</li>
					</ul>
				</li>
				<li>Version 1.5.5 - 2019.04.17<br/>
					<ul>
						<li>#Add: drag & drop Volume Order in admin</li>
						<li>#Add: mark Chapter Status completed/uploading</li>
						<li>#Add: upload more images to chapter without overriding or delete</li>
						<li>#Add: option to turn on/off User Rating and User Bookmark feature</li>
						<li>#Improve: use Short number format for counting</li>
						<li>#Improve: option to hide Volume information in Widget</li>
						<li>#Improve: show Manga badge in slider</li>
						<li>#Improve: support .webp</li>
						<li>#Improve: update some third-party CSS and JS libraries</li>
						<li>#Improve: RTL css</li>
						<li>#Improve: show chapter number for comments in admin</li>
						<li>#Fix: filter Mangas by first character '0'</li>
						<li>#Fix: admin chapter download</li>
						<li>#Fix: Disqus compatibility</li>
					</ul>
				</li>
				<li>Version 1.5.4.7 - 2019.03.21<br/>
					<ul>
						<li>#Add: option to change Login/Register Popup background</li>
						<li>#Fix: CSS bugs</li>
						<li>#Remove Blogspot support</li>
						<li>#Improve: support External Storage (FTP add-on)</li>
					</ul>
				</li>
				<li>Version 1.5.4.6 - 2019.03.18<br/>
					<ul>
						<li>#Fix: CSS bugs regarding ionicons, Posts Slider shortcode</li>
					</ul>
				</li>
				<li>Version 1.5.4.5 - 2019.03.12<br/>
					<ul>
						<li>#Add: option to hide Read First/Read Last/Continue buttons in Manga detail page</li>
						<li>#Improve: recognize duplicated chapter name when importing chapters from cloud</li>
						<li>#Improve: update JS & PHP libs (lazysizes 4.1.6, slick 1.9.0, jscolor 2.05, ClassLoader 3.3.0 (x)
Bootstrap 4.3.1, Ionicons v4.4.4, Shuffle</li>
						<li>#Improve: when input/textarea is focus, disable next/prev chapters by keyboards</li>
						<li>#Improve: center Search & Burger icons on header on mobile</li>
						<li>#Improve: responsive Manga Genres widget</li>
						<li>#Fix: CSS bugs</li>
						<li>#Fix: incorrect next/prev link in Novel (Text Chapter) </li>
						<li>#Fix: update Madara-Shortcodes plugin</li>
					</ul>
				</li>
				<li>Version 1.5.4.4 - 2019.03.07<br/>
					<ul>
						<li>#Fix: CSS bugs</li>
					</ul>
				</li>
				<li>Version 1.5.4.3 - 2019.03.06<br/>
					<ul>
						<li>#Add: option to show different icons for different Manga Type (webtoon/comic, novel, video)</li><li>#Fix: CSS bugs</li>
						<li>#Improve: update FontAwesome, Normalize, Select2, GuzzleHttp to latest version</li>
						<li>#Improve: remove SQL_CACHE requirement. Many servers do not enable this feature by default as it shows no effect on multi-cores system. To enable support for SQL_CACHE in Madara, add "define('WP_MANGA_QUERY_CACHE', 1);" in wp-config.php</li>
						<li>#Improve: fix some errors with slider, and now only slide 1 by 1</li>
						<li>#Fix: reverse order of Prev/Next button for chapters</li>
						<li>#Fix: minor issues</li>
					</ul>
				</li>
				<li>Version 1.5.4.2 - 2019.02.25<br/>
					<ul>
						<li>#Improve: add / (forward splash) to chapter link</li>
						<li>#Improve: add Autoplay option to widget Hero Slider and Popular Mangas</li>
						<li>#Fix: CSS bugs in RTL, Dark theme mode and on mobile</li>
						<li>#Fix: when Images Per Page value is greater than 1, images order are incorrect</li>
						<li>#Fix: first image and last image are not displayed in Paged Reading mode</li>
						<li>#Fix: chapter order in the dropdown is incorrect</li>
						<li>#Fix: Front-Page template cannot filter by Chapter Type</li>
						<li>#Fix: setting for Reading mode does not work in Genres page</li>
						<li>#Fix: chapter link in sitemap has /p/1/ parameter</li>
						<li>#Fix: upload images to Google Photos</li>
						<li>#Fix: comment count for Disqus</li>
					</ul>
				</li>
				<li>Version 1.5.4.1 - 2019.01.25<br/>
					<ul>
						<li>#Fix: images are not loaded when lazy loaded</li>
					</ul>
				</li>
				<li>Version 1.5.4 - 2019.01.24<br/>
					<ul>
						<li>#Add: support Google Photos storage. (will replace Google Picasa on March 15.2019)</li>
						<li>#Improve: lazy loading in reading list style</li>
						<li>#Improve: mark current reading chapter so users can continue reading</li>
						<li>#Fix: various bugs</li>
						<li>#Fix: dark mode setting & css. Remove independent setting for Reading Page</li>
					</ul>
				</li>
				<li>Version 1.5.3.3 - 2019.01.17<br/>
					<ul>
						<li>#Add: option for chapter SEO description and chapter warning text</li>
						<li>#Improve: option to remove a chapter storage</li>
						<li>#Fix: Google Picasa (Blogspot) API does not work. Support until 15.03.2019</li>
						<li>#Fix: Front-Page template settings do not show up in classic editor</li>
						<li>#Fix: dark mode setting & css. Remove independent setting for Reading Page</li>
					</ul>
				</li>
				<li>Version 1.5.3.2 - 2019.01.12<br/>
					<ul>
						<li>#Add: allow to upload banner/wide image for manga to be used in slider</li>
						<li>#Improve: CSS (including Dark Mode schema)</li>
						<li>#Fix: Widget registration in theme</li>
						<li>#Fix: YOAST site-map chapter links error</li>
						<li>#Fix: chapter paged link starts at 1</li>
						<li>#Fix: default chapter image server setting does not affect front-end reading page</li>
						<li>#Fix: pagination for Front Page</li>
						<li>#Fix: remove /p/1/ for non-manga chapters</li>
						<li>#Fix: hide "relevance" filter in archive (not search results) page</li>
					</ul>
				</li>
				<li>Version 1.5.3.1 - 2019.01.08<br/>
					<ul>
						<li>#Fix: search results missing CSS</li>
						<li>#Fix: bug in navigating chapter page</li>
					</ul>
				</li>
				<li>Version 1.5.3 - 2019.01.05<br/>
					<ul>
						<li>#Improve: add Relevance search results</li>
						<li>#Improve: pretty URL for reading paged chapter</li>
						<li>#Improve: save cookie for Adult viewer</li>
						<li>#Improve: support Jetpack Photon API</li>
						<li>#Improve: add custom class, allowing to change Custom Badge color via custom css</li>
						<li>#Fix: incorrect default reading style in notification link</li>
						<li>#Fix: unsubscribe manga</li>
					</ul>
				</li>
				<li>Version 1.5.2.1 - 2018.12.20<br/>
					<ul>
						<li>#Fix: page scrolls when clicking on Video Player chapter</li>
						<li>#Fix: duplicate servers for chapter doesn't work</li>
						<li>#Fix: default theme schema is dark for new users</li>
						<li>#Improve: support chapter folder name with "." (dot) character</li>
					</ul>
				</li>
				<li>Version 1.5.2 - 2018.12.20<br/>
					<ul>
						<li>#Add: My Uploaded Mangas page in User Settings panel</li>
						<li>#Add: option to hide Reading Style selection (in WP Manga Settings page)</li>
						<li>#Add: option to turn on Full Width (No Padding) reading mode on mobile (in Theme Options > Manga Reading Layout</li>
						<li>#Add: option for users to configure Theme Mode in User Settings panel</li>
						<li>#Add: new widget to show all Authors</li>
						<li>#Add: new shortcode to display "My Bookmarked Mangas" (ex: [wp-manga-my-bookmarks column="1|2|3|4|6" style="1|2"])</li>
						<li>#Add: option to change font-size for Text Chapter</li>
						<li>#Add: option to set default video server for Video Chapter</li>
						<li>#Add: option to hide Comments Form for Single Post (Blog) (in Theme Options > Single Post)</li>
						<li>#Improve: support WP 5.0 and add shortcodes to Gutenberg editor</li>
						<li>#Improve: UX on mobile chapter reading. Easier to navigate and read</li>
						<li>#Improve: redirect to 404 if chapter URL does not exist</li>
						<li>#Improve: click to scroll when reading Chapter in listing (all images) mode</li>
						<li>#Improve: shortcode Manga Listing to list Mangas by Author</li>
						<li>#Add: link to read First Chapter | Last Chapter for quick access</li>
						<li>#Fix: minor CSS issues</li>
						<li>#Fix: cannot change notification message template</li>
						<li>#Fix: notice warning on Option Tree plugin</li>
						<li>#Fix: Front Page template does not order manga by Modified Time if choosing Manga Type different than "All"</li>
					</ul>
				</li>
				<li>Version 1.5.1.5 - 2018.11.08<br/>
					<ul>
						<li>#Fix: add Option Tree plugin to the package(due to removal of plugin on WordPress.org</li>
						<li>#Fix: duplicate servers feature</li>
					</ul>
				</li>
				<li>Version 1.5.1.4 - 2018.11.08<br/>
					<ul>
						<li>#Add: option to order Chapters by Custom Index</li>
						<li>#Fix: order Chapters by Name (poor performance)</li>
						<li>#Fix: duplicate servers feature</li>
					</ul>
				</li>
				<li>Version 1.5.1.3 - 2018.11.07<br/>
					<ul>
						<li>#Add: option to turn on Filter by First Characters in title, to build All Manga (A-Z) page</li>
						<li>#Improve: auto scroll to top of image when reading chapter in Paginated style</li>
						<li>#Improve: add conditional tag for checking single Manga and single Chapter</li>
						<li>#Improve: save dark/light reading mode in Single Chapter page for later access</li>
						<li>#Fix: some CSS issues</li>
						<li>#Fix: minor bugs</li>
						<li>#Fix: Related Manga section doesn't work correctly</li>
						<li>#Fix: widget Manga Posts doesn't work with Time Range setting for Trending posts</li>
						<li>#Fix: improve RTL CSS</li>
						<li>#Fix: when saving chapter, images may be deleted (local storage)</li>
					</ul>
				</li>
				<li>Version 1.5.1 - 2018.10.01<br/>
					<ul>
						<li>#Add: option to upload chapters via direct URL</li>
						<li>#Add: Footer sidebar</li>
						<li>#Add: option to change Author, Artist, Release Year, Manga Tag slug</li>
						<li>#Add: option to move Single Reading sidebar to side column for Text Chapter</li>
						<li>#Add: Light on/off button for video chapter</li>
						<li>#Add: support multi-servers for Video Chapter</li>
						<li>#Improve: show Play icon on thumbnail of Video Manga, and link directly to latest chapter</li>
						<li>#Fix: some minor issues</li>
						<li>#Fix: some CSS issues</li>
					</ul>
				</li>
				<li>Version 1.5.0.5 - 2018.09.26<br/>
					<ul>
						<li>#Improve: update Bootstrap 4.+, improve responsiveness of Big Thumbnail layout</li>
						<li>#Improve: support Add-ons WP Manga Custom Fields & WP Manga Chapter Permissions</li>
					</ul>
				</li>
				<li>Version 1.5.0.4 - 2018.09.20<br/>
					<ul>
						<li>#Add: option to hide Genre link in breadcrumbs</li>
						<li>#Improve: cache query "Calculate View Rank" for faster loading</li>
						<li>#Improve: responsiveness of Big Thumbnail layout</li>
						<li>#Fix: bookmark issue</li>
						<li>#Fix: Text Chapter's content is missing line breaks in Edit mode</li>
						<li>#Fix: download chapter does not work</li>
						<li>#Fix: disable User Login/Register if "Anyone Can Register" option is turned off</li>
					</ul>
				</li>
				<li>Version 1.5.0.3 - 2018.09.01<br/>
					<ul>
						<li>#Improve: improvements for faster database query</li>
						<li>#Fix: some minor bugs</li>
						<li>#Fix: invalid MACOSX zip file when uploading single chapter</li>
						<li>#Improve: CSS</li>
						<li>#Add: show Genre description</li>
						<li>#Add: option to insert URL for Manga Badge</li>
						<li>#Add: new Manga Archives layout - Simple List for Front Page</li>
						<li>#Add: new Item Layout property for Manga Listing shortcode</li>
					</ul>
				</li>
				<li>Version 1.5.0.2 - 2018.08.10<br/>
					<ul>
						<li>#Improve: great improvement in performance</li>
					</ul>
				</li>
				<li>Version 1.5.0.1 - 2018.08.06<br/>
					<ul>
						<li>#Fix: bug with YOAST Chapters sitemap</li>
						<li>#Fix: warning error in chapter reading page</li>
					</ul>
				</li>
                <li>Version 1.5 - 2018.08.03<br/>
	<ul>
		<li>#Add: option to turn off Hosting Sever select box from front-end, and use anonymous name for server</li>
		<li>#Add: option to hide word "Manga" (link to All Mangas page) from breadcrumbs</li>
		<li>#Add: option to change mobile header color (in Theme Options > Custom Colors)</li>
		<li>#Add: option for Big Thumbnail layout item</li>
		<li>#Add: RSS Feed link for Chapter</li>
		<li>#Add: option to change "manga-paged" variable when reading chapter</li>
		<li>#Add: option to view larger image when reading chapter</li>
		<li>#Add: option to switch Dark/Light mode for users when reading chapter</li>
		<li>#Add: option to list specific Manga Type (Comic, Novel, Drama) in Front-Page template, shortcode and widgets</li>
		<li>#Improve: support chapter sitemap by YOAST</li>
		<li>#Improve: support manga and chapter meta tags by YOAST. Use "%summary%" placeholder for Novel description</li>
		<li>#Improve: breadcrumbs and Manga Archives slug setting</li>
		<li>#Improve: showing number of people who have bookmarked manga</li>
		<li>#Improve: language translation</li>
		<li>#Improve: disable indexing duplicated chapter paged</li>
		<li>#Improve: convert external data file to database, improve site performance</li>
		<li>#Fix: custom color for half-star and active menu item</li>
		<li>#Fix: minor bugs</li>
	</ul>
</li>
<li>Version 1.4.2 - 2018.06.29<br/>
	<ul>
		<li>#Add: option to change Manga Genre slug</li>
		<li>#Add: option to change Reading Style (Paged or List) in different mangas</li>
		<li>#Add: option to mark "New Chapters" automatically (Theme Options >Manga General Layout > Manga New Chapter Tag)</li>
		<li>#Update: add various hooks and filters for custom content (see <a href="http://demo.mangabooth.com/doc/docs/manga-actions-and-filters/">document</a></li>
		<li>#Update: pre-load images in chapter to improve loading</li>
		<li>#Update: validate image extension when uploading</li>
		<li>#Update: WooCommerce 3.4 template</li>
		<li>#Update: support YOAST SEO title format %chapter%</li>
		<li>#Fix: various issues</li>
		<li>#Fix: Custom Fonts settings</li>
		<li>#Fix: reset password doesn't work</li>
	</ul>
</li>
<li>Version 1.4.1 - 2018.05.16<br/>
	<ul>
		<li>#Fix: bugs when Disqus plugin is not installed</li>
		<li>#Fix: navigation does not work in Front-Page template</li>
		<li>#Fix: search by Manga Status. Improve Search</li>
		<li>#Fix: select Volume and Ajax Next Chapter bug</li>
	</ul>
</li>
<li>Version 1.4 - 2018.05.14<br>
	<ul>
		<li>#Add: option to add chapter image via URL</li>
		<li>#Add: support Flickr storage</li>
		<li>#Add: New Chapter notification</li>
		<li>#Add: support OneSignal Web Push notification when there is new chapter. Require: https://wordpress.org/plugins/onesignal-free-web-push-notifications/</li>
		<li>#Add: option to use both WP Comments and Disqus Comments</li>
		<li>#Add: option to mark Manga 18+ and display a content warning</li>
		<li>#Add: option to turn off "Show More" in Manga summary</li>
		<li>#Add: more Manga Status "On Hold" and "Canceled"</li>
		<li>#Add: new Menu Location for Logged-in User</li>
		<li>#Add: option to manual configure for Chapter SEO meta tags</li>
		<li>#Add: option to turn off "Show More" in Chapter List</li>
		<li>#Add: add various hooks to allow theme customization. Check: http://demo.mangabooth.com/doc/docs/manga-actions-and-filters/</li>
		<li>#Add: option to hide meta tags in single Page & Post</li>

		<li>#Update: improve description meta for chapter reading page</li>
		<li>#Update: better UX for reading page</li>
		<li>#Update: schedule to delete temp folder, to save server storage</li>
		<li>#Update: search Manga by alternative name</li>
		<li>#Update: support Genre & Tag condition for Manga Listing shortcode</li>
		<li>#Update: language file</li>
		<li>#Update: option to choose a Page for User Profile link on menu</li>

		<li>#Fix: option disable Related Manga doesn't work</li>
		<li>#Fix: support UTF-8 content for text chapter</li>
		<li>#Fix: chapter link is incorrect when using multi-language</li>
		<li>#Fix: incorrect Chapter Volume when updating chapter</li>
		<li>#Fix: incorrect chapter order in Video and Text manga</li>
		<li>#Fix: FontAwesome 5-related issues</li>
		<li>#Fix: support Login No Captcha reCaptcha plugin</li>
		<li>#Fix: various bugs</li>
		<li>#Fix: check max file size for big value (GB) </li>
	</ul>
</li>
<li>Version 1.3 - 2018.02.28<br>
	<ul>
		<li>#Add: option for Sticky Chapter Navigation</li>
		<li>#Add: option for turning on Dark mode for Reading page</li>
		<li>#Add: allow to upload multiple Video &amp; Text chapters in back-end</li>
		<li>#Add: option for Ajax Loading Next Image in chapter</li>
		<li>#Add: support Blogger Storage</li>
		<li>#Add: widget Bookmark List</li>
<li>#Add: option to disable comments in Reading page</li>
		<li>#Update: WooCommerce support</li>
		<li>#Update: [for developer] add some filters for managing Ads</li>
		<li>#Update: import meta tags for Chapter</li>
		<li>#Update: link to next chapter when reaching last page in previous chapter</li>
		<li>#Update: able to select Volumn in Reading page</li>
		<li>#Update: show Chapter storages in Chapter List management</li>
		<li>#Update: able to search for Alternative Name</li>
		<li>#Update: FontAwesome 5</li>
		<li>#Update: able to configure heading in Archive page</li>
		<li>#Fix: chapter Content Type does not work correctly with Text tab</li>
		<li>#Fix: special character in Chapter extended name</li>
		<li>#Fix: Manga name in different language</li>
		<li>#Fix: some minor bugs</li>
	</ul>
</li>
<li>Version 1.2 - 2018.02.13<br>
<ul>
<li>#Add: Custom Badge feature (ex. Spoiler tag)</li>
<li>#Add: floating/side ads</li>
<li>#Add: shortcode to display all chapters for a single manga</li>
<li>#Update: option to allow comments on chapter, instead of manga</li>
<li>#Update: support Disqus comments</li>
<li>#Update: add text domain for translation in plugins</li>
<li>#Update: option to remove Select Hosting when there is only 1 option</li>
<li>#Update: support .gif feature image</li>
<li>#Update: navigate to next page when clicking on chapter image</li>
<li>#Update: redirect to login page if visitors go to User Settings page</li>
<li>#Update: upload images to different folder on Amazon storage</li>
<li>#Fix: next &amp; prev chapter buttons in Manga Reading - List Style</li>
<li>#Fix: chapter order by Name</li>
<li>#Fix: orderby option in Front-page template</li>
<li>#Fix: login &amp; register form layout on mobile</li>
</ul>
</li>
<li>Version 1.1 - 2018.01.12<br>
<ul>
<li>#Add: Support using Text &amp; Video iframe in Chapter</li>
<li>#Add: option to change Manga slug</li>
<li>#Add: shortcode Latest/Popular Manga</li>
<li>#Update: URL Friendly for Chapter</li>
<li>#Update: Navigate using keyboard</li>
<li>#Fix: correct language file</li>
</ul>
</li>
<li>Version 1.0 - First release</li>
            </ul>
			<?php
		}
	}


	$madara = MadaraStarter::getInstance();
	$madara->initialize();
