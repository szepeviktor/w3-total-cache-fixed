<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<p>
    Jump to:
    <a href="admin.php?page=w3tc_general"><?php _e( 'Main Menu', 'w3-total-cache' ); ?></a> |
    <a href="admin.php?page=w3tc_extensions"><?php _e( 'Extensions', 'w3-total-cache' ); ?></a>
</p>
<p>
    NewRelic extension is currently <?php
if ( $config->is_extension_active_frontend( 'newrelic' ) )
	echo '<span class="w3tc-enabled">enabled</span>';
else
	echo '<span class="w3tc-disabled">disabled</span>';
?>.
<p>

<form action="admin.php?page=w3tc_monitoring" method="post">
<div class="metabox-holder">
    <?php Util_Ui::postbox_header( __( 'Application Settings', 'w3-total-cache' ), '', 'application' ); ?>
    <?php if ( $application_settings ): ?>
    <table class="form-table">
        <tr>
         <th>
             <label>Application ID:</label>
         </th>
        <td>
            <?php esc_attr_e( $application_settings['application-id'] )?>
        </td>
        </tr>
        <tr>
            <th>
                <label>Application name:</label>
            </th>
            <td>
                <?php esc_attr_e( $application_settings['name'] )?>
            </td>
        </tr>
        <tr>
            <th>
                <label for="alerts-enabled">Alerts enabled:</label>
            </th>
            <td>
                <input name="alerts-enabled]" type="hidden" value="false" />
                <input id="alerts-enabled" name="application[alerts_enabled]"
                    type="checkbox" value="1" <?php checked( $application_settings['alerts-enabled'], 'true' ) ?> <?php Util_Ui::sealing_disabled( 'newrelic' ) ?>/>
            </td>
        </tr>
        <tr>
            <th>
                <label for="app-apdex-t">Application ApDex Threshold:</label>
            </th>
            <td>
                <input id="app-apdex-t" name="application[app_apdex_t]" type="text"
                    value="<?php echo esc_attr( $application_settings['app-apdex-t'] )?>"
                    <?php Util_Ui::sealing_disabled( 'newrelic' ) ?> />
            </td>
        </tr>
        <tr>
            <th>
                <label for="rum-apdex-t"><acronym title="Real User Monitoring">RUM</acronym> ApDex Threshold:</label>
            </th>
            <td>
                <input id="rum-apdex-t" name="application[rum_apdex_t]" type="text"
                    value="<?php echo esc_attr( $application_settings['rum-apdex-t'] )?>"
                    <?php Util_Ui::sealing_disabled( 'newrelic' ) ?>/>
            </td>
        </tr>
        <tr>
            <th>
                <label for="rum-enabled"><acronym title="Real User Monitoring">RUM</acronym> enabled:</label>
            </th>
            <td>
                <input name="application[rum_enabled]" type="hidden" value="false"
                    <?php Util_Ui::sealing_disabled( 'newrelic' ) ?> />
                <input id="rum-enabled" name="application[rum_enabled]"
                    type="checkbox" value="1"
                    <?php checked( $application_settings['rum-enabled'], 'true' ) ?>
                    <?php Util_Ui::sealing_disabled( 'newrelic' ) ?>/>
            </td>
        </tr>
    </table>
    <p class="submit">
        <?php echo Util_Ui::nonce_field( 'w3tc' ); ?>
        <input type="submit" name="w3tc_save_new_relic"
            class="w3tc-button-save button-primary"
            <?php Util_Ui::sealing_disabled( 'newrelic' ) ?>
            value="Save New Relic settings" />
    </p>
    <?php elseif ( empty( $application_settings ) ): ?>
    <p><span class="description"><?php echo sprintf( __( 'Application settings could not be retrieved. New Relic may not be properly configured, <a href="%s">review the settings</a>.', 'w3-total-cache' ), network_admin_url( 'admin.php?page=w3tc_general#monitoring' ) ) ?></span></p>
    <?php else: ?>
    <p><?php _e( 'Application settings are only visible when New Relic is enabled', 'w3-total-cache', 'w3-total-cache' ) ?></p>
    <?php endif; ?>
    <?php Util_Ui::postbox_footer(); ?>
    </form>
    <form action="admin.php?page=w3tc_monitoring" method="post">

    <?php Util_Ui::postbox_header( __( 'Dashboard Settings', 'w3-total-cache' ), '', 'dashboard' ); ?>
    <table class="form-table">
        <tr>
            <th>
                <label for="newrelic_cache_time"><?php
_e( 'Cache time:', 'w3-total-cache' )
?></label></th>
            <td><input id="newrelic_cache_time" name="extension__newrelic__cache_time"
                type="text" value="<?php echo esc_attr( $config->get_integer( array( 'newrelic', 'cache_time', 5 ) ) ) ?>"
                <?php Util_Ui::sealing_disabled( 'newrelic' ) ?> />
                <p><span class="description">
                    <?php _e( 'How many minutes data retrieved from New Relic should be stored. Minimum is 1 minute.', 'w3-total-cache' ) ?>
                    </span>
                </p>
            </td>
        </tr>
    </table>
    <?php Util_Ui::button_config_save( 'extension_newrelic_dashboard' ); ?>
    <?php Util_Ui::postbox_footer(); ?>

    <?php Util_Ui::postbox_header( __( 'Behavior Settings', 'w3-total-cache' ), '', 'behavior' ); ?>
    <table  class="form-table">
        <tr>
            <th colspan="2">
                <?php
Util_Ui::checkbox( '',
	Util_Ui::config_key_to_http_name( array( 'newrelic', 'accept.logged_roles' ) ),
	$config->get_boolean( array( 'newrelic', 'accept.logged_roles' ) ),
	$config->is_sealed( 'newrelic' ) );
_e( 'Use <acronym title="Real User Monitoring">RUM</acronym> only for following user roles', 'w3-total-cache' )
?></label><br />
                <span class="description"><?php
_e( 'Select user roles that <acronym title="Real User Monitoring">RUM</acronym> should be enabled for:', 'w3-total-cache' )
?></span>

                <div id="newrelic_accept_roles">
                    <?php $saved_roles = $config->get_array( array( 'newrelic', 'accept.roles' ) ); ?>
                    <input type="hidden" name="newrelic___accept__roles" value="" /><br />
                    <?php foreach ( get_editable_roles() as $role_name => $role_data ) : ?>
                    <input type="checkbox" name="newrelic___accept__roles[]" value="<?php echo $role_name ?>"
                        <?php checked( in_array( $role_name, $saved_roles ) ) ?>
                        id="role_<?php echo $role_name ?>"
                        <?php Util_Ui::sealing_disabled( 'newrelic' ) ?> />
                    <label for="role_<?php echo $role_name ?>"><?php echo $role_data['name'] ?></label>
                    <?php endforeach; ?>
                </div>
            </th>
        </tr>
        <tr>
            <th>
                <label for="newrelic_include_rum"><?php
_e( 'Include <acronym title="Real User Monitoring">RUM</acronym> in compressed or cached pages:', 'w3-total-cache' )
?></label>
            </th>
            <td>
                <input name="extension__newrelic__include_rum" type="hidden" value="0"
                    <?php Util_Ui::sealing_disabled( 'newrelic' ) ?> />
                <input id="newrelic_include_rum" name="extension__newrelic__include_rum"
                    type="checkbox" value="1"
                    <?php checked( $config->get_boolean( array( 'newrelic', 'include_rum' ) ) ) ?>
                    <?php Util_Ui::sealing_disabled( 'newrelic' ) ?> />
                <p><span class="description">
                <?php _e( 'This enables inclusion of <acronym title="Real User Monitoring">RUM</acronym> when using Page Cache together with Browser Cache gzip or when using Page Cache with Disc: Enhanced', 'w3-total-cache' )?>
                </span>
                </p>
            </td>
        </tr>
        <tr>
            <th>
                <label for="newrelic_use_php_function"><?php
_e( 'Use <acronym title=\"Hypertext Preprocessor\">PHP</acronym> function to set application name:', 'w3-total-cache' )
?></label></th>
            <td>
                <?php if ( Util_Environment::is_wpmu() ): ?>
                <input id="newrelic_use_php_function" name="extension__newrelic__use_php_function" type="checkbox" value="1" checked="checked" disabled="disabled" />
                    <p><span class="description">
                        <?php _e( 'This is required when using New Relic on a network install to set the proper names for sites.', 'w3-total-cache' ) ?></span></p>
                <?php else: ?>
                <input name="extension__newrelic__use_php_function" type="hidden" value="0" />
                <input id="newrelic_use_php_function" name="extension__newrelic__use_php_function" type="checkbox" value="1" <?php checked( $config->get_boolean( array( 'newrelic', 'use_php_function' ) ) ) ?>/>
                    <p><span class="description">
                       <?php _e( 'Enable this to dynamically set proper application name. (See New Relic <a href="https://newrelic.com/docs/php/per-directory-settings">Per-directory settings</a> for other methods.', 'w3-total-cache' ) ?></span>
                    </p>
                <?php endif ?>
            </td>
        </tr>
        <tr>
            <th>
                <label for="newrelic_enable_xmit"><?php
		_e( 'Enable XMIT:', 'w3-total-cache' )
		?></label>
            </th>
            <td><input name="" type="hidden" value="0" />
            <input id="newrelic_enable_xmit" name="extension__newrelic__enable_xmit" type="checkbox" value="1" <?php checked( $config->get_boolean( array( 'newrelic', 'enable_xmit' ) ) ) ?> <?php Util_Ui::sealing_disabled( 'newrelic' ) ?>/>
                <p><span class="description"><?php _e( sprintf( 'Enable this if you want to record the metric and transaction data (until the name is changed using PHP function), specify a value of true for this argument to make the agent send the transaction to the daemon. There is a slight performance impact as it takes a few milliseconds for the agent to dump its data. <em>From %s</em>',
				'<a href="https://newrelic.com/docs/php/the-php-api">New Relic PHP API doc</a>' )
			, 'w3-total-cache' )?></span></p>
            </td>
        </tr>
    </table>
    <?php Util_Ui::button_config_save( 'extension_newrelic_behaviour' ); ?>
    <?php Util_Ui::postbox_footer(); ?>
    </form>
</div>
<?php if ( $view_metric ):?>
<table>
<?php foreach ( $metric_names as $metric ):?>
    <tr>
        <th style="text-align: right"><strong><?php echo $metric->name ?></strong></th>
        <td><?php echo implode( ', ', $metric->fields ) ?></td>
    </tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
