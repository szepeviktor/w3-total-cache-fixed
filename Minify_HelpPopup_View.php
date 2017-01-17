<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<div class="w3tc-overlay-logo"></div>
<header>
</header>
<div class="w3tchelp_content">
    <h3>Hang on!</h3>
	<p>
		In the best case, the usage of minify optimization is a trial and
		error process, it's <em>not</em> an "instant on" or "set it and forget it"
		optimization technique.
	</p>
	<p>
		There are lots of reasons why minify cannot work for all sites under
		all circumstances and they have nothing to do with W3 Total Cache:
		Your site's content, your server(s), your plugins and your theme
		are all unique, that means that minify cannot automatically work for everyone.
	</p>

    <h3>What is minification exactly?</h3>
	<ul class="w3tchelp_content_list">
		<li>
			Minification is a process of reducing the file size to improve user experience 
			and it requires testing in order to get it right &mdash; as such it doesn't work for everyone.
		</li>
		<li>
			The interactions and dependencies of <acronym title="Cascading Style Sheet">CSS</acronym> or <acronym>JS</acronym> on each
			other can be complex. Themes and plugins are typically created by various developers 
			and can be combined in millions of combinations. As a result, W3 Total Cache cannot
			take all of those nuances into account, it just does the operation and
			let's you tune to what degree it does it, it doesn't "validate" the result
			or know if it's good or bad; a human must do that.
		</li>
	</ul>

	<h3>Still want to get started? Now for the Pro' tips:</h3>
	<ol class="w3tchelp_content_list">
		<li>
			Start with minify for your <acronym title="Cascading Style Sheet">CSS</acronym> using auto mode first. 
			If you have any issues at that step, contact your developer(s) and report a bug. They should be able to 
			point you in the right direction or correct the issue in a future update.
		</li>
		<li>
			Once <acronym title="Cascading Style Sheet">CSS</acronym> is optimized, try <acronym>JS</acronym> minification. If auto
			mode doesn't work for you, be sure to check the web browsers
			error console to quickly confirm that the optimization isn't
			working. If the JavaScript is working, you can either make
			additional optimizations for user experience like experimenting
			with embed locations etc or further reducing file
			size etc. However, if you're having errors try 
			the "combine only" option and if that still generates errors,
			there are bugs in the code of your theme or plugins or both that
			prevent minification of <acronym>JS</acronym> from working automatically.
		</li>
	</ol>

    <div>
    	<input type="submit" class="btn w3tc-size image btn-primary outset save palette-turquoise "
    	value="I Understand the Risks">
    	<?php
echo Util_Ui::button_link( 'Do It For Me',
	'admin.php?page=w3tc_support', false,
	'btn w3tc-size image btn-primary outset save palette-turquoise w3tc-button-ignore-change' );
?>
    </div>
</div>
