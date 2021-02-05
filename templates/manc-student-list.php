<div class="wrap student-wrap">
	<div class="container">
	    <div class="row">
	        <div class="col-md-12">
				<h2>Student Details</h2>
	            <div class="student-parent">
					<?php
					if (count($students)) :
						foreach ($students as $_student) {
							?>
			                <div class="student-child">
			                    <div class="row">
			                        <div class="col-md-4 col-sm-4">
			                            <img alt="Student" class="profile-photo-lg" src="<?php echo plugins_url('../assets/img/avatar.png', __FILE__)?>">
			                            </img>
			                        </div>
			                        <div class="col-md-8 col-sm-8">
			                            <h5><?php echo $_student->name; ?></h5>
			                            <p>
			                            	<label for="age">Age:</label>
			                            	<i><?php echo $_student->age; ?></i>
			                            	<label for="gender">Gender:</label>
			                            	<i><?php echo isset(self::$gender[$_student->gender]) ? self::$gender[$_student->gender] : ''; ?></i>
			                            </p>
			                            <p class="text-muted">
			                            	<label for="subjects">Subjects:</label>
			                                <?php
			                                $subjects = [];
			                                if ($_student->subjects && $_student->subjects != '') :
				                                foreach (explode(",", $_student->subjects) as $_subjectKey)
				                                {
				                                	$subjects[] = isset(self::$subjects[$_subjectKey]) ? self::$subjects[$_subjectKey] : '';
				                            	}
				                            endif;
				                            echo implode(", ", array_filter($subjects));
			                                ?>
			                            </p>
			                        </div>
			                    </div>
			                </div>
							<?php
						}
					else:
						echo "<p>No Student Details Found!</p>";
					endif;
					?>
	            </div>
	        </div>
	    </div>
	</div>
</div>