<?php
/*
Plugin Name: bbPress Custom Reply Notifications
Plugin URI: http://pippinsplugins.com/bbpress-custom-reply-notifications
Description: A bbPress extension to customize the email sent to topic subscribers when a new reply is posted
Version: 1.0
Author: Pippin Williamson
Author URI: http://pippinsplugins.com
Contributors: mordauk 
*/


class PW_BBP_Reply_Notifications {


	function __construct() {

		// let's get started

		add_filter( 'bbp_subscription_mail_message', array( __CLASS__, 'reply_message' ), 10, 4 );
		add_filter( 'bbp_subscription_mail_title', array( __CLASS__, 'reply_title' ), 10, 3 );

		add_filter( 'bbp_admin_get_settings_sections', array( __CLASS__, 'settings_section' ), 10 );
		add_filter( 'bbp_admin_get_settings_fields', array( __CLASS__, 'settings_field' ), 10 );

	}


	function reply_message( $message, $reply_id, $topic_id, $user_id ) {


	}

	function reply_title( $reply_id, $topic_id, $user_id ) {


	}

	function settings_section( $sections ) {

		$sections[] = array(
			'bbp_settings_reply_notices' => array(
				'title'    => __( 'Reply Notifications', 'bbpress' ),
				'callback' => 'bbp_admin_setting_callback_reply_notices_section',
				'page'     => 'bbpress'
			)
		);

		return $sections;
	}

	function settings_field( $fields ) {

		$sections['bbp_settings_reply_notices'] = array(
			'_bbp_reply_notice_body' => array(
				'title'             => __( 'Email Body', 'bbpress' ),
				'callback'          => 'pw_bbp_admin_setting_callback_reply_notices_body',
				'sanitize_callback' => 'sanitize_textarea',
				'args'              => array()
			),
			'_bbp_reply_notice_title' => array(
				'title'             => __( 'Email Subject', 'bbpress' ),
				'callback'          => 'pw_bbp_admin_setting_callback_reply_notices_subject',
				'sanitize_callback' => 'sanitize_textfield',
				'args'              => array()
			)
		);

		return $fields;

	}

}

/**
 * Reply Notice Body Field
 *
 * @since 1.0
 *
 * @uses bbp_form_option() To output the option value
 */
function pw_bbp_admin_setting_callback_reply_notices_body() {

	$default = '{author} wrote:

{content}
			
Post Link: {url}

-----------

You are receiving this email because you subscribed to a forum topic.

Login and visit the topic to unsubscribe from these emails.';


	$message = bbp_get_form_option( '_bbp_reply_notice_body', $default );

?>

	<textarea name="_bbp_reply_notice_body" type="number" min="0" step="1" id="_bbp_reply_notice_body"><?php echo esc_textarea( $message ); ?></textarea>
	<label for="_bbp_reply_notice_body"><?php _e( 'seconds', 'bbpress' ); ?></label>

<?php
}

/**
 * Reply Notice Body Subject / Title
 *
 * @since 1.0
 *
 * @uses bbp_form_option() To output the option value
 */
function pw_bbp_admin_setting_callback_reply_notices_subject() {
?>

	<input name="_bbp_reply_notice_title" type="number" min="0" step="1" id="_bbp_reply_notice_title" value="<?php bbp_form_option( '_bbp_reply_notice_title', 'New Reply' ); ?>" class="regular-text" />
	<label for="_bbp_reply_notice_title"><?php _e( 'sbuject', 'bbpress' ); ?></label>

<?php
}

// load our class
$GLOBALS['pw_bbp_reply_notifications'] = new PW_BBP_Reply_Notifications();