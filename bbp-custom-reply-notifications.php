<?php
/*
Plugin Name: bbPress Custom Topic & Reply Notifications
Plugin URI: http://pippinsplugins.com/bbpress-custom-reply-notifications
Description: A bbPress extension to customize the email sent to forum & topic subscribers when a new topic or reply is posted
Version: 1.1
Author: Pippin Williamson
Author URI: http://pippinsplugins.com
Contributors: mordauk
*/


class PW_BBP_Topic_Reply_Notifications {


	function __construct() {

		// let's get started

		add_filter( 'bbp_forum_subscription_mail_message', array( __CLASS__, 'topic_message' ), 10, 4 );
		add_filter( 'bbp_forum_subscription_mail_title',   array( __CLASS__, 'topic_title'   ), 10, 4 );
		add_filter( 'bbp_subscription_mail_message',       array( __CLASS__, 'reply_message' ), 10, 4 );
		add_filter( 'bbp_subscription_mail_title',         array( __CLASS__, 'reply_title'   ), 10, 4 );

		add_action( 'admin_init', array( __CLASS__, 'topic_settings' ), 100 );
		add_action( 'admin_init', array( __CLASS__, 'reply_settings' ), 100 );

	}


	function topic_message( $message, $topic_id, $forum_id, $user_id ) {

		$topic_content 	= strip_tags( bbp_get_topic_content( $topic_id ) );
		$topic_url     	= bbp_get_topic_permalink( $topic_id );
		$topic_author	= bbp_get_topic_author_display_name( $topic_id );

		$custom_message = get_option( '_bbp_topic_notice_body' );

		$message = $custom_message ? $custom_message : $message;

		$message = str_replace( '{author}',  $topic_author,  $message );
		$message = str_replace( '{content}', $topic_content, $message );
		$message = str_replace( '{url}',     $topic_url,     $message );


		return $message;
	}

	function topic_title( $title, $topic_id, $forum_id, $user_id ) {

		$custom_title = get_option( '_bbp_topic_notice_title' );
		$message      = $custom_title ? $custom_title : $message;
		$topic_title  = strip_tags( bbp_get_topic_title( $topic_id ) );
		$title 		  = str_replace( '{title}', $topic_title, $title );

		return $title;
	}

	function reply_message( $message, $reply_id, $topic_id, $user_id ) {

		$reply_content 	= strip_tags( bbp_get_reply_content( $reply_id ) );
		$reply_url     	= bbp_get_reply_url( $reply_id );
		$reply_author	= bbp_get_reply_author_display_name( $reply_id );

		$custom_message = get_option( '_bbp_reply_notice_body' );

		$message = $custom_message ? $custom_message : $message;

		$message = str_replace( '{author}',  $reply_author,  $message );
		$message = str_replace( '{content}', $reply_content, $message );
		$message = str_replace( '{url}',     $reply_url,     $message );


		return $message;
	}

	function reply_title( $title, $reply_id, $topic_id, $user_id ) {

		$custom_title = get_option( '_bbp_reply_notice_title' );
		$message      = $custom_title ? $custom_title : $message;
		$topic_title  = strip_tags( bbp_get_topic_title( $topic_id ) );
		$title 		  = str_replace( '{title}', $topic_title, $title );

		return $title;
	}

	function topic_settings() {

		add_settings_section( 'bbp_settings_topic_notices', __( 'Topic Notifications', 'bbpress' ), array( __CLASS__, 'topic_section_callback' ), 'bbpress' );

		add_settings_field( '_bbp_topic_notice_body', __( 'Email Body', 'bbpress' ), array( __CLASS__, 'topic_body_callback' ), 'bbpress', 'bbp_settings_topic_notices' );
		add_settings_field( '_bbp_topic_notice_title', __( 'Email Subject', 'bbpress' ), array( __CLASS__, 'topic_title_callback' ), 'bbpress', 'bbp_settings_topic_notices' );

		register_setting( 'bbpress', '_bbp_topic_notice_body' );
		register_setting( 'bbpress', '_bbp_topic_notice_title' );
	}

		function topic_section_callback() {
	?>

		<p><?php _e( 'Forum settings for new topic notifications', 'bbpress' ); ?></p>

	<?php
	}

	function reply_settings() {

		add_settings_section( 'bbp_settings_reply_notices', __( 'Reply Notifications', 'bbpress' ), array( __CLASS__, 'reply_section_callback' ), 'bbpress' );

		add_settings_field( '_bbp_reply_notice_body', __( 'Email Body', 'bbpress' ), array( __CLASS__, 'reply_body_callback' ), 'bbpress', 'bbp_settings_reply_notices' );
		add_settings_field( '_bbp_reply_notice_title', __( 'Email Subject', 'bbpress' ), array( __CLASS__, 'reply_title_callback' ), 'bbpress', 'bbp_settings_reply_notices' );

		register_setting( 'bbpress', '_bbp_reply_notice_body' );
		register_setting( 'bbpress', '_bbp_reply_notice_title' );

	}


	function reply_section_callback() {
	?>

		<p><?php _e( 'Forum settings for new reply notifications', 'bbpress' ); ?></p>

	<?php
	}


	function topic_body_callback() {

		$default = '{author} wrote:

{content}

Post Link: {url}

-----------

You are receiving this email because you subscribed to a forum.

Login and visit the forum to unsubscribe from these emails.';


		$message = bbp_get_form_option( '_bbp_topic_notice_body', $default );

	?>

		<textarea name="_bbp_topic_notice_body" class="large-text" rows="15" id="_bbp_topic_notice_body"><?php echo esc_textarea( $message ); ?></textarea>
		<label for="_bbp_topic_notice_body"><?php _e( 'Email message sent to forum subscribers when a new topic is posted', 'bbpress' ); ?></label>

	<?php
	}


	function topic_title_callback() {

		$default_title = '[' . get_option( 'blogname' ) . '] {title}';
	?>

		<input name="_bbp_topic_notice_title" type="text" id="_bbp_topic_notice_title" value="<?php bbp_form_option( '_bbp_topic_notice_title', $default_title ); ?>" class="regular-text" />
		<label for="_bbp_topic_notice_title"><?php _e( 'The subject of the notification email', 'bbpress' ); ?></label>

	<?php
	}

	function reply_body_callback() {

		$default = '{author} wrote:

{content}

Post Link: {url}

-----------

You are receiving this email because you subscribed to a forum topic.

Login and visit the topic to unsubscribe from these emails.';


		$message = bbp_get_form_option( '_bbp_reply_notice_body', $default );

	?>

		<textarea name="_bbp_reply_notice_body" class="large-text" rows="15" id="_bbp_reply_notice_body"><?php echo esc_textarea( $message ); ?></textarea>
		<label for="_bbp_reply_notice_body"><?php _e( 'Email message sent to topic subscribers when a new reply is posted', 'bbpress' ); ?></label>

	<?php
	}


	function reply_title_callback() {

		$default_title = '[' . get_option( 'blogname' ) . '] {title}';
	?>

		<input name="_bbp_reply_notice_title" type="text" id="_bbp_reply_notice_title" value="<?php bbp_form_option( '_bbp_reply_notice_title', $default_title ); ?>" class="regular-text" />
		<label for="_bbp_reply_notice_title"><?php _e( 'The subject of the notification email', 'bbpress' ); ?></label>

	<?php
	}

}

// load our class
$GLOBALS['pw_bbp_topic_reply_notifications'] = new PW_BBP_Topic_Reply_Notifications();
