<?php
/**
 * Contains the form that is used when adding or editing users.
 *
 * @package		ProjectSend
 * @subpackage	Users
 *
 */
?>

<script type="text/javascript">
	$(document).ready(function() {
		$("form").submit(function() {
			clean_form(this);

			is_complete(this.add_user_form_name,'<?php echo $validation_no_name; ?>');
			is_complete(this.add_user_form_user,'<?php echo $validation_no_user; ?>');
			is_complete(this.add_user_form_email,'<?php echo $validation_no_email; ?>');
			is_complete(this.add_user_form_level,'<?php echo $validation_no_level; ?>');
			is_length(this.add_user_form_user,<?php echo MIN_USER_CHARS; ?>,<?php echo MAX_USER_CHARS; ?>,'<?php echo $validation_length_user; ?>');
			is_email(this.add_user_form_email,'<?php echo $validation_invalid_mail; ?>');
			is_alpha(this.add_user_form_user,'<?php echo $validation_alpha_user; ?>');
			
			<?php
				/**
				 * Password validation is optional only when editing a user.
				 */
				if ($user_form_type == 'edit_user' || $user_form_type == 'edit_user_self') {
			?>
					// Only check password if any of the 2 fields is completed
					var password_1 = $("#add_user_form_pass").val();
					//var password_2 = $("#add_user_form_pass2").val();
					if ($.trim(password_1).length > 0/* || $.trim(password_2).length > 0*/) {
			<?php
				}
			?>

						is_complete(this.add_user_form_pass,'<?php echo $validation_no_pass; ?>');
						//is_complete(this.add_user_form_pass2,'<?php echo $validation_no_pass2; ?>');
						is_length(this.add_user_form_pass,<?php echo MIN_PASS_CHARS; ?>,<?php echo MAX_PASS_CHARS; ?>,'<?php echo $validation_length_pass; ?>');
						is_password(this.add_user_form_pass,'<?php $chars = addslashes($validation_valid_chars); echo $validation_valid_pass." ".$chars; ?>');
						//is_match(this.add_user_form_pass,this.add_user_form_pass2,'<?php echo $validation_match_pass; ?>');

			<?php
				/** Close the jquery IF statement. */
				if ($user_form_type == 'edit_user' || $user_form_type == 'edit_user_self') {
			?>
					}
			<?php
				}
			?>

			// show the errors or continue if everything is ok
			if (show_form_errors() == false) { return false; }
		});
	});
</script>

<?php
switch ($user_form_type) {
	case 'new_user':
		$submit_value = __('Add user','cftp_admin');
		$disable_user = false;
		$require_pass = true;
		$form_action = 'users-add.php';
		$extra_fields = true;
		break;
	case 'edit_user':
		$submit_value = __('Save user','cftp_admin');
		$disable_user = true;
		$require_pass = false;
		$form_action = 'users-edit.php?id='.$user_id;
		$extra_fields = true;
		break;
	case 'edit_user_self':
		$submit_value = __('Update account','cftp_admin');
		$disable_user = true;
		$require_pass = false;
		$form_action = 'users-edit.php?id='.$user_id;
		$extra_fields = false;
		break;
}
?>
<form action="<?php echo $form_action; ?>" name="adduser" method="post">
	<ul class="form_fields">
		<li>
			<label for="add_user_form_name"><?php _e('Name','cftp_admin'); ?></label>
			<input type="text" name="add_user_form_name" id="add_user_form_name" class="required" value="<?php echo (isset($add_user_data_name)) ? stripslashes($add_user_data_name) : ''; ?>" />
		</li>
		<li>
			<label for="add_user_form_user"><?php _e('Log in username','cftp_admin'); ?></label>
			<input type="text" name="add_user_form_user" id="add_user_form_user" class="<?php if (!$disable_user) { echo 'required'; } ?>" maxlength="<?php echo MAX_USER_CHARS; ?>" value="<?php echo (isset($add_user_data_user)) ? stripslashes($add_user_data_user) : ''; ?>" <?php if ($disable_user) { echo 'readonly'; } ?> placeholder="<?php _e("Must be alphanumeric",'cftp_admin'); ?>" />
		</li>
		<li>
			<button type="button" class="btn password_toggler pass_toggler_show"><i class="icon-eye-open"></i></button>
			<label for="add_user_form_pass"><?php _e('Password','cftp_admin'); ?></label>
			<input name="add_user_form_pass" id="add_user_form_pass" class="<?php if ($require_pass) { echo 'required'; } ?> password_toggle" type="password" maxlength="<?php echo MAX_PASS_CHARS; ?>" />
			<?php password_notes(); ?>
		</li>
		<li>
			<label for="add_user_form_email"><?php _e('E-mail','cftp_admin'); ?></label>
			<input type="text" name="add_user_form_email" id="add_user_form_email" class="required" value="<?php echo (isset($add_user_data_email)) ? stripslashes($add_user_data_email) : ''; ?>" placeholder="<?php _e("Must be valid and unique",'cftp_admin'); ?>" />
		</li>
		<?php
			if ($extra_fields == true) {
		?>
			<li>
				<label for="add_user_form_level"><?php _e('Role','cftp_admin'); ?></label>
				<select name="add_user_form_level" id="add_user_form_level">
					<option value="9" <?php echo (isset($add_user_data_level) && $add_user_data_level == '9') ? 'selected="selected"' : ''; ?>><?php echo USER_ROLE_LVL_9; ?></option>
					<option value="8" <?php echo (isset($add_user_data_level) && $add_user_data_level == '8') ? 'selected="selected"' : ''; ?>><?php echo USER_ROLE_LVL_8; ?></option>
					<option value="7" <?php echo (isset($add_user_data_level) && $add_user_data_level == '7') ? 'selected="selected"' : ''; ?>><?php echo USER_ROLE_LVL_7; ?></option>
				</select>
			</li>
			<li>
				<label for="add_user_form_active"><?php _e('Active (user can log in)','cftp_admin'); ?></label>
				<input type="checkbox" name="add_user_form_active" id="add_user_form_active" <?php echo (isset($add_user_data_active) && $add_user_data_active == 1) ? 'checked="checked"' : ''; ?> />
			</li>
		<?php
			}
		?>
	</ul>

	<div class="inside_form_buttons">
		<button type="submit" name="submit" class="btn btn-wide btn-primary"><?php echo $submit_value; ?></button>
	</div>

	<?php
		if ($user_form_type == 'new_user') {
			$msg = __('This account information will be e-mailed to the address supplied above','cftp_admin');
			echo system_message('info',$msg);
		}
	?>
</form>