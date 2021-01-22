<?php
global $db,$sugar_config;

if(isset($_REQUEST['key']) && !empty($_REQUEST['key'])){
	//Create Key for Validation
	$key = md5($sugar_config['validation_key']);
	if($key != $_REQUEST['key']){
		sugar_die('Un-Authorised Access');
	}
}else{
	sugar_die('Un-Authorised Access');
}


/**
 * Entry Point to get Users Details for perticular Instance.
 */
// Get Georaphical Filter Value
$config_sql = "SELECT `value` FROM config WHERE `name`='geo_filter' AND `category`='instance'";
$config_query = $db->query ( $config_sql );
$config_result = $db->fetchByAssoc ( $config_query );
$instance_filter = 'Client';
if (trim ( $config_result ['value'] ) == 'project_location') {
	$instance_filter = 'Project';
}

// Get All Users of instance
$userCountSQL = "SELECT count(users.id) ctt";
$userSelectSQL = "SELECT users.id,users.first_name,users.last_name,users.user_name,users.is_admin,role.name user_role,ea.email_address user_email";
$fromSQL = " FROM users 
			   	LEFT JOIN acl_roles_users ru ON ru.user_id=users.id AND ru.deleted=0
				LEFT JOIN acl_roles role ON role.id=ru.role_id AND role.deleted=0
				LEFT JOIN email_addr_bean_rel eabr ON eabr.bean_id = users.id AND eabr.bean_module = 'Users' AND eabr.primary_address='1' AND eabr.deleted=0
				LEFT JOIN email_addresses ea ON ea.id = eabr.email_address_id AND ea.deleted=0 				
			   	WHERE users.deleted=0 AND users.user_name <> ''";

// Pagination
$userCountSQL = $userCountSQL . $fromSQL;
$userCountQuery = $db->query ( $userCountSQL );
$countResult = $db->fetchByAssoc ( $userCountQuery );

$max_per_page = 10;
$totalRecord = $countResult ['ctt'];
$no_of_pages = ceil ( $totalRecord / $max_per_page );

$page = 1;
if (isset ( $_REQUEST ['page'] )) {
	$page = $_REQUEST ['page'];
}

$offset = $max_per_page * $page;

if (($totalRecord <= $max_per_page) || ($page == $no_of_pages)) {
	$offset = $totalRecord;
}

$currentRecord = ($max_per_page * $page) - $max_per_page;
$currentRecordPage = $currentRecord + 1;

$limitSQL = " LIMIT $currentRecord, $max_per_page";
$userSQL = $userSelectSQL . $fromSQL . $limitSQL;
$userQuery = $db->query ( $userSQL );
?>
<table width="100%" cellspacing="0" cellpadding="0" border="0"
	class="list view">
	<tbody>
		<tr height="20" style="background-color:#929798;">
			<th width="15%" scope="col" style="text-align: center;"><span style="white-space: normal;">First
					Name</span></th>
			<th width="15%" scope="col"><span style="white-space: normal;">Last
					Name</span></th>
			<th width="15%" scope="col"><span style="white-space: normal;">User
					Name</span></th>
			<th width="15%" scope="col"><span style="white-space: normal;">Email</span></th>
			<th width="15%" scope="col"><span style="white-space: normal;">Role</span></th>
			<th width="15%" scope="col"><span style="white-space: normal;">User
					Filters</span></th>
			<th width="15%" scope="col"><span style="white-space: normal;">&nbsp;</span></th>
			<th width="15%" scope="col"><span style="white-space: normal;">&nbsp;</span></th>
		</tr>
		<tr role="presentation" class="pagination" style="background-color:#c2c3c4 none repeat scroll 0 0;">
			<td align="right" colspan="20">
				<table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tbody>
						<tr>
							<td align="left"></td>
							<td align="right" nowrap="">
								<button type="button" name="listViewStartButton" title="Start"
									class="button" onclick="getInstanceUsers(1);"
									<?php if($page==1) { echo "disabled"; }?>>
									<span class="suitepicon suitepicon-action-first" style="line-height: 2.4;"></span>
								</button>&nbsp;&nbsp;
								<button type="button" name="listViewPrevButton" title="Previous"
									class="button"
									onclick="getInstanceUsers(<?php echo $page-1; ?>);"
									<?php if($page==1) { echo "disabled"; }?>>
									<span class="suitepicon suitepicon-action-left" style="line-height: 2.4;"></span>
								</button>&nbsp;&nbsp;<span class="pageNumbers">(<?php echo $currentRecordPage;?> - <?php echo $offset; ?> of <?php echo $totalRecord; ?>)</span>&nbsp;&nbsp;
								<button type="button" name="listViewNextButton" title="Next"
									class="button"
									onclick="getInstanceUsers(<?php echo $page+1; ?>);"
									<?php if($page == $no_of_pages) { echo "disabled"; }?>>
									<span class="suitepicon suitepicon-action-right" style="line-height: 2.4;"></span>
								</button>&nbsp;&nbsp;
								<button type="button" name="listViewEndButton" title="End"
									class="button"
									onclick="getInstanceUsers(<?php echo $no_of_pages; ?>);"
									<?php if($page==$no_of_pages) { echo "disabled"; }?>>
									<span class="suitepicon suitepicon-action-last" style="line-height: 2.4;"></span>
								</button>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
<?php while($userRow = $db->fetchByAssoc($userQuery)) { ?>
<tr height="20" class="oddListRowS1">
			<td valign="top" class="" scope="row"><span sugar="slot17b"><?php echo $userRow['first_name']; ?></span></td>
			<td valign="top" class="" scope="row"><span sugar="slot18b"><?php echo $userRow['last_name']; ?></span></td>
			<td valign="top" class="" scope="row"><span sugar="slot19b"><?php echo $userRow['user_name']; ?></span></td>
			<td valign="top" class="" scope="row"><span sugar="slot20b"><?php echo $userRow['user_email']; ?></span></td>
			<td valign="top" class="" scope="row"><span sugar="slot21b"><?php echo $userRow['user_role']; ?></span></td>
			<td valign="top" class="" scope="row"><span sugar="slot22b"><a
					href="#"
					onclick="getUserFilters('<?php echo $userRow['id'] ?>','<?php echo $userRow['is_admin']; ?>');"><?php echo ($userRow['is_admin']==1)?"Target Class":$instance_filter; ?></a></span></td>
			<td valign="top" class="" scope="row"><span sugar="slot23b"><a
					href="#"
					onclick="changePassword('<?php echo $userRow['user_name'] ?>');">Reset
						Password</a></span></td>
			<td scope="row" valign="top" class="inlineButtons"><span sugar="slot24b">
					<ul id="<?php echo $userRow['id'] ?>" class="clickMenu " name=""><li class="single">
					<div style="display: inline" id="<?php echo $userRow['id'] ?>">
					<a href="#" onclick="deleteUser('<?php echo base64_encode(md5(urlencode($userRow['id']))) ?>');" class="listViewTdToolsS1">&nbsp;Delete</a>&nbsp;</div></li></ul></span></td>
		</tr>
<?php } ?>
</tbody>
</table>



