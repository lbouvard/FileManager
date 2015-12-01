<?php
/**
 * Show the list of current users.
 *
 * @package		ProjectSend
 * @subpackage	Users
 *
 */
$footable = 1;
$allowed_levels = array(9);
require_once('sys.includes.php');

$active_nav = 'users';

$page_title = __('Users administration','cftp_admin');;
include('header.php');
?>

<script type="text/javascript">
	$(document).ready( function() {
		$("#do_action").click(function() {
			var checks = $("td>input:checkbox").serializeArray(); 
			if (checks.length == 0) { 
				alert('<?php _e('Please select at least one user to proceed.','cftp_admin'); ?>');
				return false; 
			}
			else {
				var action = $('#users_actions').val();
				if (action == 'delete') {
					var msg_1 = '<?php _e("You are about to delete",'cftp_admin'); ?>';
					var msg_2 = '<?php _e("users. Are you sure you want to continue?",'cftp_admin'); ?>';
					if (confirm(msg_1+' '+checks.length+' '+msg_2)) {
						return true;
					} else {
						return false;
					}
				}
			}
		});

	});
</script>

<div id="main">
	<h2><?php echo $page_title; ?></h2>

<?php

	/**
	 * Apply the corresponding action to the selected users.
	 */
	if(isset($_POST['users_actions'])) {
		/** Continue only if 1 or more users were selected. */
		if(!empty($_POST['users'])) {
			$selected_users = $_POST['users'];
			$users_to_get = mysql_real_escape_string(implode(',',array_unique($selected_users)));

			/**
			 * Make a list of users to avoid individual queries.
			 */
			$sql_user = $database->query("SELECT id, name FROM tbl_users WHERE id IN ($users_to_get)");
			while($data_user = mysql_fetch_array($sql_user)) {
				$all_users[$data_user['id']] = $data_user['name'];
			}

			$my_info = get_user_by_username(get_current_user_username());
			$affected_users = 0;

			switch($_POST['users_actions']) {
				case 'activate':
					/**
					 * Changes the value on the "active" column value on the database.
					 * Inactive users are not allowed to log in.
					 */
					foreach ($selected_users as $work_user) {
						$this_user = new UserActions();
						$hide_user = $this_user->change_user_active_status($work_user,'1');
					}
					$msg = __('The selected users were marked as active.','cftp_admin');
					echo system_message('ok',$msg);
					$log_action_number = 27;
					break;

				case 'deactivate':
					/**
					 * Reverse of the previous action. Setting the value to 0 means
					 * that the user is inactive.
					 */
					foreach ($selected_users as $work_user) {
						/**
						 * A user should not be able to deactivate himself
						 */
						if ($work_user != $my_info['id']) {
							$this_user = new UserActions();
							$hide_user = $this_user->change_user_active_status($work_user,'0');
							$affected_users++;
						}
						else {
							$msg = __('You cannot deactivate your own account.','cftp_admin');
							echo system_message('error',$msg);
						}
					}

					if ($affected_users > 0) {
						$msg = __('The selected users were marked as inactive.','cftp_admin');
						echo system_message('ok',$msg);
						$log_action_number = 28;
					}
					break;

				case 'delete':		
					foreach ($selected_users as $work_user) {
						/**
						 * A user should not be able to delete himself
						 */
						if ($work_user != $my_info['id']) {
							$this_user = new UserActions();
							$delete_user = $this_user->delete_user($work_user);
							$affected_users++;
						}
						else {
							$msg = __('You cannot delete your own account.','cftp_admin');
							echo system_message('error',$msg);
						}
					}
					
					if ($affected_users > 0) {
						$msg = __('The selected users were deleted.','cftp_admin');
						echo system_message('ok',$msg);
						$log_action_number = 16;
					}
				break;
			}

			/** Record the action log */
			foreach ($selected_users as $user) {
				$new_log_action = new LogActions();
				$log_action_args = array(
										'action' => $log_action_number,
										'owner_id' => $global_id,
										'affected_account_name' => $all_users[$user]
									);
				$new_record_action = $new_log_action->log_action_save($log_action_args);
			}
		}
		else {
			$msg = __('Please select at least one user.','cftp_admin');
			echo system_message('error',$msg);
		}
	}

	$database->MySQLDB();
	$cq = "SELECT * FROM tbl_users WHERE level != '0'";

	/** Add the search terms */	
	if(isset($_POST['search']) && !empty($_POST['search'])) {
		$search_terms = mysql_real_escape_string($_POST['search']);
		$cq .= " AND (name LIKE '%$search_terms%' OR user LIKE '%$search_terms%' OR email LIKE '%$search_terms%')";
		$no_results_error = 'search';
	}

	/** Add the role filter */	
	if(isset($_POST['role']) && $_POST['role'] != 'all') {
		$role_filter = $_POST['role'];
		$cq .= " AND level='$role_filter'";
		$no_results_error = 'filter';
	}
	
	/** Add the status filter */	
	if(isset($_POST['status']) && $_POST['status'] != 'all') {
		$status_filter = $_POST['status'];
		$cq .= " AND active='$status_filter'";
		$no_results_error = 'filter';
	}

	$cq .= " ORDER BY name ASC";
	
	$sql = $database->query($cq);
	$count = mysql_num_rows($sql);
?>

	<div class="form_actions_left">
		<div class="form_actions_limit_results">
			<form action="users.php" name="users_search" method="post" class="form-inline">
				<input type="text" name="search" id="search" value="<?php if(isset($_POST['search']) && !empty($_POST['search'])) { echo $_POST['search']; } ?>" class="txtfield form_actions_search_box" />
				<button type="submit" id="btn_proceed_search" class="btn btn-small"><?php _e('Search','cftp_admin'); ?></button>
			</form>

			<form action="users.php" name="users_filters" method="post" class="form-inline">
				<select name="role" id="role" class="txtfield">
					<option value="all"><?php _e('All roles','cftp_admin'); ?></option>
					<option value="9"><?php _e('System Administrator','cftp_admin'); ?></option>
					<option value="8"><?php _e('Account Manager','cftp_admin'); ?></option>
					<option value="7"><?php _e('Uploader','cftp_admin'); ?></option>
				</select>

				<select name="status" id="status" class="txtfield">
					<option value="all"><?php _e('All statuses','cftp_admin'); ?></option>
					<option value="1"><?php _e('Active','cftp_admin'); ?></option>
					<option value="0"><?php _e('Inactive','cftp_admin'); ?></option>
				</select>
				<button type="submit" id="btn_proceed_filter_clients" class="btn btn-small"><?php _e('Filter','cftp_admin'); ?></button>
			</form>
		</div>
	</div>

	<form action="users.php" name="users_list" method="post" class="form-inline">
		<div class="form_actions_right">
			<div class="form_actions">
				<div class="form_actions_submit">
					<label><?php _e('Selected users actions','cftp_admin'); ?>:</label>
					<select name="users_actions" id="users_actions" class="txtfield">
						<option value="activate"><?php _e('Activate','cftp_admin'); ?></option>
						<option value="deactivate"><?php _e('Deactivate','cftp_admin'); ?></option>
						<option value="delete"><?php _e('Delete','cftp_admin'); ?></option>
					</select>
					<button type="submit" id="do_action" name="proceed" class="btn btn-small"><?php _e('Proceed','cftp_admin'); ?></button>
				</div>
			</div>
		</div>
		<div class="clear"></div>

		<div class="form_actions_count">
			<p><?php _e('Showing','cftp_admin'); ?>: <span><?php echo $count; ?> <?php _e('users','cftp_admin'); ?></span></p>
		</div>

		<div class="clear"></div>

		<?php
			if (!$count) {
				switch ($no_results_error) {
					case 'search':
						$no_results_message = __('Your search keywords returned no results.','cftp_admin');;
						break;
					case 'filter':
						$no_results_message = __('The filters you selected returned no results.','cftp_admin');;
						break;
				}
				echo system_message('error',$no_results_message);
			}
		?>

		<table id="users_tbl" class="footable" data-page-size="<?php echo FOOTABLE_PAGING_NUMBER; ?>">
			<thead>
				<tr>
					<th class="td_checkbox" data-sort-ignore="true">
						<input type="checkbox" name="select_all" id="select_all" value="0" />
					</th>
					<th data-sort-initial="true"><?php _e('Full name','cftp_admin'); ?></th>
					<th data-hide="phone"><?php _e('Log in username','cftp_admin'); ?></th>
					<th data-hide="phone"><?php _e('E-mail','cftp_admin'); ?></th>
					<th data-hide="phone"><?php _e('Role','cftp_admin'); ?></th>
					<th><?php _e('Status','cftp_admin'); ?></th>
					<th data-hide="phone, tablet" data-type="numeric"><?php _e('Added on','cftp_admin'); ?></th>
					<th data-hide="phone" data-sort-ignore="true"><?php _e('Actions','cftp_admin'); ?></th>
				</tr>
			</thead>
			<tbody>
			
			<?php
				while($row = mysql_fetch_array($sql)) {
					$date = date(TIMEFORMAT_USE,strtotime($row['timestamp']));
				?>
				<tr>
					<td>
						<?php if ($row["id"] != '1') { ?>
							<input type="checkbox" name="users[]" value="<?php echo $row["id"]; ?>" />
						<?php } ?>
					</td>
					<td><?php echo html_entity_decode($row["name"]); ?></td>
					<td><?php echo html_entity_decode($row["user"]); ?></td>
					<td><?php echo html_entity_decode($row["email"]); ?></td>
					<td><?php
						switch(html_entity_decode($row["level"])) {
							case '9': echo USER_ROLE_LVL_9; break;
							case '8': echo USER_ROLE_LVL_8; break;
							case '7': echo USER_ROLE_LVL_7; break;
						}
					?>
					</td>
					<td>
						<?php
							$status_hidden	= __('Inactive','cftp_admin');
							$status_visible	= __('Active','cftp_admin');
							$label			= ($row['active'] === '0') ? $status_hidden : $status_visible;
							$class			= ($row['active'] === '0') ? 'important' : 'success';
						?>
						<span class="label label-<?php echo $class; ?>">
							<?php echo $label; ?>
						</span>
					</td>
					<td data-value="<?php echo strtotime($row['timestamp']); ?>">
						<?php echo $date; ?>
					</td>
					<td>
						<a href="users-edit.php?id=<?php echo $row["id"]; ?>" class="btn btn-primary btn-small"><?php _e('Edit','cftp_admin'); ?></a>
					</td>
				</tr>
						
				<?php
				}
			
				$database->Close();
			?>
			
			</tbody>
		</table>

		<div class="pagination pagination-centered hide-if-no-paging"></div>
	</form>

</div>

<?php include('footer.php'); ?>