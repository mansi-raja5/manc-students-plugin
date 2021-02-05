<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<h1>
		Students Details
		<a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=manc_students_plugin');?>"><?php _e('back to list', 'manc_students_plugin')?></a>
	</h1>
	<hr/>

	<?php if (!empty($notice)): ?>
		<div id="notice" class="error"><p><?php echo $notice ?></p></div>
	<?php endif;?>

	<?php if (!empty($message)): ?>
		<div id="message" class="updated"><p><?php echo $message ?></p></div>
	<?php endif;?>

	<form id="form" method="POST">
		<input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
		<input type="hidden" name="id" value="<?php echo $student['id'] ?>"/>
		<div class="metabox-holder" id="poststuff">
			<div id="post-body">
				<div id="post-body-content">
					<div class="form-group row">
						<label for="name" class="col-sm-2 col-form-label"><?php _e('Sudent Name', 'manc_students_plugin')?></label>
						<div class="col-sm-10">
							<input name="name" type="text" value="<?php echo esc_attr($student['name'])?>" size="50" class="form-control" placeholder="<?php _e('Enter Student Name', 'manc_students_plugin')?>" required>
						</div>
					</div>
					<div class="form-group row">
						<label for="age" class="col-sm-2 col-form-label"><?php _e('Age', 'manc_students_plugin')?></label>
						<div class="col-sm-10">
							<input name="age" type="number" value="<?php echo esc_attr($student['age'])?>" size="50" class="form-control" placeholder="<?php _e('Enter Student Age', 'manc_students_plugin')?>" required>
						</div>
					</div>

					<div class="form-group row">
						<label for="gender" class="col-sm-2 col-form-label"><?php _e('Gender', 'manc_students_plugin')?></label>
						<div class="col-sm-10">
							<?php
								foreach (self::$gender as $_genderKey => $_genderValue) {
								?>
								<div class="form-check-inline">
									<label class="form-check-label" for="<?php echo $_genderValue; ?>" ><input id="<?php echo $_genderValue; ?>" class="form-check-input" type="radio" name="gender" value = "<?php echo $_genderKey; ?>" <?php echo $student['gender'] === $_genderKey ? 'checked' : ''; ?> required ><?php echo $_genderValue; ?></label>
								</div>
								<?php
							}
							?>

						</div>
					</div>
					<div class="form-group row">
						<label for="subjects" class="col-sm-2 col-form-label"><?php _e('Subjects', 'manc_students_plugin')?></label>
						<div class="col-sm-10">
							<select class="form-control" name="subjects[]" multiple data-live-search="true" required>
								<?php
								foreach (self::$subjects as $_subjectKey => $_subjectValue) {
									?>
									<option value="<?php echo $_subjectKey; ?>" <?php echo in_array($_subjectKey, $studentSubjects) ? 'selected' : ''; ?> ><?php echo $_subjectValue; ?></option>
									<?php
								}
								?>
							</select>
						</div>
					</div>
					<hr/>
					<div class="form-group row">
						<div class="col-sm-2">
						</div>
						<div class="col-sm-10">
							<input type="submit" value="<?php _e('Save', 'manc_students_plugin')?>" class="btn btn-primary" name="submit">
							<button type="button" class="btn btn-secondary"><?php _e('Cancel', 'manc_students_plugin')?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$('select').selectpicker();
</script>