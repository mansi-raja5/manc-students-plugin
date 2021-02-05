<h1>Students Plugin</h1>
<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<h2>
		<a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=student_form');?>"><?php _e('Add new', 'manc_students_plugin')?></a>
	</h2>
	<?php echo $message; ?>
	<form id="students-table" method="GET">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
		<?php $studentList->display() ?>
	</form>
</div>