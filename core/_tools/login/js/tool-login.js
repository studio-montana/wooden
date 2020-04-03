/**
 * @package WordPress
 * @version 1.0
 * @author Sébastien Chandonay www.seb-c.com / Cyril Tissot www.cyriltissot.com This file, like this theme, like WordPress, is licensed under the GPL.
 */
jQuery(window).ready(function($) {
	$(".login #login h1 a").attr('title', ToolLogin.url_title);
	$(".login #login h1 a").attr('href', ToolLogin.url_site);
	$('.login-action-register .register.message').html(ToolLogin.message);
	
	// remove user_login text - add placeholder
	var $text = $(".login #login label[for='user_login']").contents().filter(function() {
		return this.nodeType === 3;
	});
	if (!empty($text))
		$text.remove();
	$(".login #login label[for='user_login'] br").remove();
	$(".login #login #user_login").attr("placeholder", ToolLogin.placeholder_login);
	
	// remove user_pass text - add placeholder
	var $text = $(".login #login label[for='user_pass']").contents().filter(function() {
		return this.nodeType === 3;
	});
	if (!empty($text))
		$text.remove();
	$(".login #login label[for='user_pass'] br").remove();
	$(".login #login #user_pass").attr("placeholder", ToolLogin.placeholder_password);
	
	// remove user_email text - add placeholder
	var $text = $(".login #login label[for='user_email']").contents().filter(function() {
		return this.nodeType === 3;
	});
	if (!empty($text))
		$text.remove();
	$(".login #login label[for='user_email'] br").remove();
	$(".login #login #user_email").attr("placeholder", ToolLogin.placeholder_email);
	
	// remove #nav text
	var $text = $(".login #nav").contents().filter(function() {
		return this.nodeType === 3;
	});
	if (!empty($text))
		$text.remove();
	
});
