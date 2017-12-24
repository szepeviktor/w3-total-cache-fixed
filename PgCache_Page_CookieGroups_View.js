jQuery(function() {


function w3tc_cookiegroups_clear() {
	if (!jQuery('#cookiegroups li').size()) {
		jQuery('#cookiegroups_empty').show();
	} else {
		jQuery('#cookiegroups_empty').hide();
	}
}



jQuery('#w3tc_cookiegroup_add').click(function() {
	var group = prompt('Enter group name (only "0-9", "a-z", "_" symbols are allowed).');

	if (group !== null) {
		group = group.toLowerCase();
		group = group.replace(/[^0-9a-z_]+/g, '_');
		group = group.replace(/^_+/, '');
		group = group.replace(/_+$/, '');

		if (group) {
			var exists = false;

			jQuery('.cookiegroup_name').each(function() {
				if (jQuery(this).html() == group) {
					alert('Group already exists!');
					exists = true;
					return false;
				}
			});

			if (!exists) {
				var li = jQuery('<li id="cookiegroup_' + group + '">' +
					'<table class="form-table">' +
					'<tr>' +
					'<th>Group name:</th>' +
					'<td><span class="cookiegroup_number">' + (jQuery('#cookiegroups li').size() + 1) + '.</span> ' +
					'<span class="cookiegroup_name">' + group + '</span> ' +
					'<input type="button" class="button cookiegroup_delete" value="Delete group" /></td>' +
					'</tr>' +
					'<tr>' +
					'<th><label for="cookiegroup_' + group + '_enabled">Enabled:</label></th>' +
					'<td>' +
					'<input id="cookiegroup_' + group + '_enabled" type="checkbox" name="cookiegroups[' +
					group + '][enabled]" value="1" checked="checked" /></td>' +
					'</tr>' +
					'<tr>' +
					'<th><label for="cookiegroup_' + group + '_cache">Cache:</label></th>' +
					'<td>' +
					'<input id="cookiegroup_' + group + '_cache" type="checkbox" name="cookiegroups[' +
					group + '][cache]" value="1" checked="checked" /></td></tr>' +
					'<tr>' +
					'<th><label for="cookiegroups_' + group + '_cookies">Cookies:</label></th>' +
					'<td><textarea id="cookiegroups_' + group + '_cookies" name="cookiegroups[' +
					group + '][cookies]" rows="10" cols="50"></textarea><br />' +
					'<span class="description">Specify the cookies for this group. Values like \'cookie\', \'cookie=value\', and cookie[a-z]+=value[a-z]+are supported. Remember to escape special characters like spaces, dots or dashes with a backslash. Regular expressions are also supported.</span></td></tr>' +
					'</table></li>');
				var select = li.find('select');

				jQuery('#cookiegroups').append(li);
				w3tc_cookiegroups_clear();
				window.location.hash = '#cookiegroup_' + group;
				li.find('textarea').focus();
			}
		} else {
			alert('Empty group name!');
		}
	}
});

jQuery('.w3tc_cookiegroup_delete').live('click', function() {
	if (confirm('Are you sure want to delete this group?')) {
		jQuery(this).parents('#cookiegroups li').remove();
		w3tc_cookiegroups_clear();
		w3tc_beforeupload_bind();
	}
});

w3tc_cookiegroups_clear();

// add sortable
if (jQuery.ui && jQuery.ui.sortable) {
	jQuery('#cookiegroups').sortable({
		axis: 'y',
		stop: function() {
			jQuery('#cookiegroups').find('.cookiegroup_number').each(function(index) {
				jQuery(this).html((index + 1) + '.');
			});
		}
	});
}


});
