<?php

if (!defined('ABSPATH')) {
	die('-1');
}
function create_questionnaire_posttype()
{
	$post['questions'] = array(
		'labels'  => array(
			'all_items'           => 'Questions',
			'menu_name'           => 'Questions',
			'singular_name'       => 'Question',
			'edit_item'           => 'Edit Question',
			'add_new'             => 'New Question',
			'add_new_item'        => 'New Question',
			'view_item'           => 'View Question',
			'items_archive'       => 'Question Archive',
			'search_items'        => 'Search Question',
			'not_found'           => 'No Question found.',
			'not_found_in_trash'  => 'No Question found in trash.'
		),
		'label' => 'Questions',
		'public' => false,
		'supports' => array('title'),
		'publicly_queryable' => false,
		'exclude_from_search' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'has_archive' => false,
		'menu_icon' => 'dashicons-list-view',
		'rewrite' => false,
		'query_var' => false,
	);
	$post['departments'] = array(
		'labels'  => array(
			'all_items'           => 'All Department',
			'menu_name'           => 'Department',
			'singular_name'       => 'Department',
			'edit_item'           => 'Edit Department',
			'add_new'             => 'New Department',
			'add_new_item'        => 'New Department',
			'view_item'           => 'View Department',
			'items_archive'       => 'Department Archive',
			'search_items'        => 'Search Department',
			'not_found'           => 'No Department found.',
			'not_found_in_trash'  => 'No Department found in trash.'
		),
		'label' => 'Department',
		'public' => false,
		'publicly_queryable' => false,
		'exclude_from_search' => false,
		'show_ui' => true,
		'show_in_menu' => 'edit.php?post_type=questions',
		'has_archive' => false,
		'rewrite' => false,
		'query_var' => false,
	);
	foreach ($post as $key => $args) {
		register_post_type($key, $args);
		add_action('add_meta_boxes', function () use ($key) {
			questionnaire_post_class_meta_box($key);
		});
	}
	create_questionnaire_tables();
}
function create_questionnaire_tables()
{
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	/*
	$table_name = $wpdb->prefix . 'questions';
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " ( id INT NOT NULL AUTO_INCREMENT, post_id TEXT NOT NULL,maping_question_id TEXT NOT NULL, PRIMARY KEY  (id) ) ". $charset_collate .";";
		dbDelta($sql);
	}
*/
	$table_name = $wpdb->prefix . 'responces';
	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " ( id INT NOT NULL AUTO_INCREMENT, value TEXT NOT NULL, score TEXT NOT NULL, image TEXT, PRIMARY KEY  (id) ) " . $charset_collate . ";";
		dbDelta($sql);
	} 
	$table_name = $wpdb->prefix . 'tf_reviewsystem_responces';
	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " ( id INT NOT NULL AUTO_INCREMENT, tf_reviewsystem_id INT NOT NULL, company_id INT NOT NULL,location_id INT NOT NULL,review_for INT NOT NULL,review_by INT NOT NULL,`comments` longtext NOT NULL, is_offline BOOLEAN,datetime TIMESTAMP, PRIMARY KEY  (id) ) " . $charset_collate . ";";
		dbDelta($sql);
	}
	$table_name = $wpdb->prefix . 'tf_reviewsystem_responce_details';
	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " ( id INT NOT NULL AUTO_INCREMENT, tf_reviewsystem_responce_id INT NOT NULL, question_id INT NOT NULL,score INT,responce TEXT,category_id INT NOT NULL, PRIMARY KEY  (id) ) " . $charset_collate . ";";
		dbDelta($sql);
	}
}
function UpdateOrCreateQuestion($post_id, $question)
{
	$question_id = $question['question_id'];
	global $wpdb;
	$image = "";
	if (isset($question['question']) && !empty($question['question'])) {
		$value = $question['question'];
	}
	if (isset($question['question_image']) && !empty($question['question_image'])) {
		$image = $question['question_image'];
	}
	if (isset($question['text_length']) && !empty($question['text_length'])) {
		$text_length = $question['text_length'];
	}
	if (isset($question['type']) && !empty($question['type'])) {
		$responces_type = $question['type'];
	}
	if (isset($question['option']) && !empty($question['option'])) {
		$option = $question['option'];
	}
	if (isset($question['languages']) && !empty($question['languages'])) {
		$languages = $question['languages'];
	}
	$table_name = $wpdb->prefix . 'questions';
	$values = array(
		'post_id' => $post_id,
		'maping_question_id' => $question_id
	);
	$question = replacedata($table_name, $values);
	if (empty($question)) {
		$wpdb->replace(
			$table_name,
			array(
				'post_id' => $post_id,
				'maping_question_id' => $question_id
			),
			array(
				'%s',
				'%s'
			)
		);
	}
	$questionId = $wpdb->insert_id;
	// if ($responces_type == 'responce') {
	// 	if (is_array($option)) {
	// 		foreach ($option as $key => $optvalue) {
	// 			$optionId = UpdateOrCreateResponces($questionId, $optvalue);
	// 			UpdateOrCreateResponcesLanguages($optionId, $key, $languages);
	// 		}
	// 	}
	// }
	// UpdateOrCreateQuestionLanguages($questionId, $languages);
	return true;
}
/*
function UpdateOrCreateResponces($questionId, $value, $score = 0, $image = "")
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'responces';
	$values = array(
		'value' => $value,
		'score'		=> $score,
		'image'		=> $image
	);
	$responces = replacedata($table_name, $values);
	if (empty($responces)) {
		$wpdb->replace(
			$table_name,
			array(
				'value' => $value,
				'score'		=> $score,
				'image'		=> $image
			),
			array(
				'%s',
				'%s',
				'%s'
			)
		);
	}
	$responce_id = $wpdb->insert_id;
	UpdateOrCreateMappingResponces($questionId, $responce_id);
	return $responce_id;
}
function UpdateOrCreateMappingResponces($questionId, $responce_id)
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'mapping_responces';
	$values = array(
		'question_id' => $questionId,
		'responce_id' => $responce_id
	);
	$mapping_responces = replacedata($table_name, $values);
	if (empty($mapping_responces) && $questionId != 0 && $responce_id != 0) {
		$wpdb->replace(
			$table_name,
			array(
				'question_id' => $questionId,
				'responce_id' => $responce_id
			),
			array(
				'%s',
				'%s'
			)
		);
	}
	return $responce_id;
}
function UpdateOrCreateQuestionLanguages($questionId, $languages)
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'question_languages';
	foreach ($languages as $key => $value) {
		$values = array(
			'value' => $value['question'],
			'question_id' => $questionId,
			'code'		=> $key
		);
		$question_languages = replacedata($table_name, $values);
		if (empty($question_languages)) {
			$wpdb->replace(
				$table_name,
				array(
					'value' => $value['question'],
					'question_id' => $questionId,
					'code'		=> $key
				),
				array(
					'%s',
					'%s',
					'%s'
				)
			);
		}
	}
	return true;
}
function UpdateOrCreateResponcesLanguages($optionId, $keyId, $languages)
{
	global $wpdb;
	$responcelang = array();
	foreach ($languages as $key => $lang) {
		if (isset($lang['option'][$keyId]) && !empty($lang['option'][$keyId])) {
			$table_name = $wpdb->prefix . 'responces_languages';
			$values = array(
				'value' => $lang['option'][$keyId],
				'responce_id' => $optionId,
				'code'		=> $key
			);
			$responces_languages = replacedata($table_name, $values);
			if (empty($responces_languages)) {
				$wpdb->replace(
					$table_name,
					array(
						'value' => $lang['option'][$keyId],
						'responce_id' => $optionId,
						'code'		=> $key
					),
					array(
						'%s',
						'%s',
						'%s'
					)
				);
			}
		}
	}
	return true;
}
*/
function questionnaire_post_class_meta_box($post)
{
	add_meta_box(
		$post . '-post-class',
		esc_html__("Questionnaire", 'cmtf_reviewsystem'),
		$post . '_post_class_meta_box',
		$post
	);
}
function save_department_meta_fields($dept_id, $post)
{
	if ($post->post_type == 'departments') {
		if ($_POST) {
			$category_image = '';
			$category_image = get_post_meta($dept_id, 'category_image');
			if ($category_image != '') {
				update_post_meta($dept_id, 'category_image', $_POST['category_image']);
			} else {
				add_post_meta($dept_id, 'category_image', $_POST['category_image']);
			}
			global $wpdb;
			$table_name = $wpdb->prefix . $post->post_type;
			$values = array(
				'post_id' => $post->ID
			);
			$responces_languages = replacedata($table_name, $values);
			if (empty($responces_languages)) {
				$wpdb->replace(
					$table_name,
					array(
						'title' => $post->post_title,
						'post_id' => $post->ID
					),
					array(
						'%s'
					)
				);
			} else {
				$wpdb->update(
					$table_name,
					array(
						'title' => $post->post_title,
					),
					array(
						'post_id' => $post->ID,
					)
				);
			}
			wp_redirect(admin_url('edit.php?post_type=departments'));
			exit();
		}
	}
}
function save_questionnaire_meta_fields($question_bank_id, $post)
{
	if ($post->post_type == 'questions') {
		if ($_POST) {
			$department = get_post_meta($question_bank_id, 'department');
			if ($department != '') {
				update_post_meta($question_bank_id, 'department', $_POST['department']);
			} else {
				add_post_meta($question_bank_id, 'department', $_POST['department']);
			}
			$questions = get_post_meta($question_bank_id, 'questions');
			/**/
			if ($questions != '') {
				update_post_meta($question_bank_id, 'questions', $_POST['questions']);
			} else {
				add_post_meta($question_bank_id, 'questions', $_POST['questions']);
			}
			$question_image = '';
			$question_image = get_post_meta($question_bank_id, 'question_image');
			if ($question_image != '') {
				update_post_meta($question_bank_id, 'question_image', $_POST['question_image']);
			} else {
				add_post_meta($question_bank_id, 'question_image', $_POST['question_image']);
			}
			$text_length = 150;
			if ($_POST['text_length'] == '') {
				$_POST['text_length'] = 150;
			}
			$text_length = get_post_meta($question_bank_id, 'text_length');
			if ($text_length != '') {
				update_post_meta($question_bank_id, 'text_length', $_POST['text_length']);
			} else {
				add_post_meta($question_bank_id, 'text_length', $_POST['text_length']);
			}
			if (isset($_POST['questions']) && !empty($_POST['questions'])) {
				$questions = $_POST['questions'];
				foreach ($questions as $key => $question) {
					UpdateOrCreateQuestion($question_bank_id, $question);
				}
			}
			global $wpdb;
			$table_name = $wpdb->prefix . $post->post_type;
			$values = array(
				'post_id' => $post->ID
			);
			$responces_languages = replacedata($table_name, $values);
			if (empty($responces_languages)) {
				$wpdb->replace(
					$table_name,
					array(
						'title' => $post->post_title,
						'post_id' => $post->ID,
						'category_id' => $_POST['department']
					),
					array(
						'%s'
					)
				);
			} else {
				$wpdb->update(
					$table_name,
					array(
						'title' => $post->post_title,
						'category_id' => $_POST['department']
					),
					array(
						'post_id' => $post->ID,
					)
				);
			}
			wp_redirect(admin_url('edit.php?post_type=questions'));
			exit();
			/*echo '<pre>';	
		   	print_r($_POST);
		   	die;*/
		}
	}
}
function replacedata($table_name, $values)
{
	global $wpdb;
	$where_query = array();
	foreach ($values as $key => $value) {
		if (!empty($value)) {
			$where_query[] = "" . $key . "='" . $value . "'";
		}
	}
	$where_query_text = " WHERE " . implode(' AND ', $where_query);
	$data = $wpdb->get_results("SELECT * FROM `" . $table_name . "` " . $where_query_text . ";");
	return $data;
}
function getLanguages()
{
	return array(
		'ar' => 'Arabic',
		'hi' => 'Hindi',
		'ur' => 'Urdu',
		'ta' => 'Tamil',
		'ml' => 'Malayalam',
		'bn' => 'Bengali'
	);
}
function getQuestionsByDepartment($department)
{
	$args = array(
		'post_type' => 'questions',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'meta_query'	=> array(
			array(
				'key'       => 'department',
				'value'     => $department,
			),
		),
	);
	$postids = array();
	$query = new WP_Query($args);
	$questions = "";
	if (!empty($query->posts)) {
		foreach ($query->posts as $key => $value) {
			$postids[] = $value->ID;
			$title = $value->post_title;
			$questions_data = get_post_meta($value->ID, 'questions', true);
			$options = isset(unserialize($questions_data)['option']) ? unserialize($questions_data)['option'] : "";
			if (!empty($questions_data)) {
				$questions_array = unserialize($questions_data);
				if (!empty($questions_array)) {
					array_values($questions_array);
					array_unshift($questions_array, "");
					unset($questions_array[0]);
				}
			}
			$questions_options = array();
			$type = 'Options';
			if ($questions_array['type'] == 'checkbox') {
				$type = 'SA(4) / A (3) / D (3) / SD(1)';
			} else if ($questions_array['type'] == "yes/no") {
				$type = "Yes(4) / No(1)";
			} else if ($questions_array['type'] == "rcheckbox") {
				$type = "SD(4)/ D (3)/ A (3) / SA(1)";
			} else if ($questions_array['type'] == "rating") {
				$type = "SD(1)/ D(1.5)/ SWD(2) / NAND(2.5) /SWA(3) / A(3.5) / SA(4)";
			} else if ($questions_array['type'] == "openended") {
				$type = "Open Ended";
			} else {
				$questions_options = $options;
			}
			$questions .= SurveyQuestionHTML($value->ID, $title, $type, $questions_options);
		}
	}
	wp_send_json_success(['data' => $questions, 'ids' => $postids]);
}
function SurveyQuestionHTML($id, $title, $type, $options = null)
{
	$html = "";
	$html .= '<div class="ui styled fluid accordion" id="' . $id . '">';
	$html .= '<div class=" title">';
	$html .= '<i class="dropdown icon"></i>';
	$html .= $title;
	$html .= '</div>';
	$html .= '<div class=" extra content">';
	$html .= '<table class="ui celled table"><thead><tr><th>Type</th>';
	if ($type == "Options") {
		$html .= '<th>Options</th>';
	}
	$html .= '</tr></thead>';
	$html .= '<tbody>';
	$html .= '<tr>';
	$html .= '<td>' . $type . '</td>';
	if ($type == "Options") {
		$html .= '<td>';
		$html .= '<ul style="margin:0;">';
		if (!empty($options)) {
			foreach ($options as $key => $opt) {
				$html .= '<li>' . $opt . '</li>';
			}
		}
		$html .= '</ul>';
		$html .= '</td>';
	}
	$html .= '</tr>';
	$html .= '</tbody></table></div></div>';
	return $html;
}
function departments_post_class_meta_box($department)
{
?>
	<div class="ui container">
		<div class="ui segment">
			<div class="ui loading form">
				<div class="ui fluid form">
					<div class="field">
						<input id="category_json" value='<?php base64_encode(json_encode($department)); ?>' type="hidden">
						<div class="form-field term-group">
							<input type="hidden" id="category-image-id" name="category_image" class="custom_media_url" value="<?php $department->category_image ?>">
							<div id="category-image-wrapper">
								<img src="<?php wp_get_attachment_image_src($department->category_image)[0] ?>" style="max-width: 100%;max-height: 100%;">
							</div>
							<p>
								<input type="button" class="ui green button button-secondary ct_tax_media_button" id="ct_tax_media_button" name="ct_tax_media_button" value="Add Image" <?php echo ($department->category_image == '') ? '' : 'style="display: none;"' ?>>
								<input type="button" class="ui red button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="Remove Image" <?php echo ($department->category_image == '') ? 'style="display: none;"' : '' ?>>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			var myplugin_media_upload;
			$('#ct_tax_media_button').click(function(e) {
				e.preventDefault();
				if (myplugin_media_upload) {
					myplugin_media_upload.open();
					return;
				}
				myplugin_media_upload = wp.media.frames.file_frame = wp.media({
					multiple: false,
					library: {
						type: ['video', 'image']
					},
				});
				myplugin_media_upload.on('select', function() {
					$('#ct_tax_media_button').hide();
					$('#ct_tax_media_remove').show();
					var attachments = myplugin_media_upload.state().get('selection').map(
						function(attachment) {
							attachment.toJSON();
							return attachment;
						});
					var i;
					for (i = 0; i < attachments.length; ++i) {
						$('#category-image-wrapper').append(
							'<div class="myplugin-image-preview"><img src="' +
							attachments[i].attributes.url + '" style="max-width: 100%;max-height: 100%;" ></div>'
						);
						$('#category-image-id').val(attachments[i].id);
					}
				});
				myplugin_media_upload.open();
			});
			$("body").on("click", "#ct_tax_media_remove", function() {
				$("#category-image-id").val(""), $("#category-image-wrapper").html("");
				$('#ct_tax_media_button').show();
				$('#ct_tax_media_remove').hide();
			})
		});
	</script>
<?php
}
function questions_post_class_meta_box($questions)
{
	wp_nonce_field(basename(__FILE__), 'questions_post_class_meta_box_nonce');
	$questions_array = get_post_meta($questions->ID, 'questions', true);
	$department = get_post_meta($questions->ID, 'department', true);
	$img_id = get_post_meta($questions->ID, 'question_image', true);
	$text_length = get_post_meta($questions->ID, 'text_length', true);
	if ($text_length == '') {
		$text_length = 150;
	}
	$src = wp_get_attachment_image_src($img_id, 'full')[0];
	$question_languages = base64_encode(json_encode(getLanguages()));
	$questions_count = 0;
	//$questions_array = array();
	if (!empty($questions_data)) {
		//$questions_array = unserialize($questions_data);
		if (!empty($questions_array)) {
			array_values($questions_array);
			array_unshift($questions_array, "");
			unset($questions_array[0]);
			$questions_count = count($questions_array);
		}
	}
	$departments = get_posts([
		'post_type' => 'departments',
		'post_status' => 'publish',
		'numberposts' => -1
	]);
?>
	<div class="ui container">
		<div class="ui loading form">
			<div class="ui segment">
				<div class="field">
					<input type="hidden" id="question_languages" value="<?php $question_languages ?>">
					<label>Select Department</label>
					<div class="ui fluid selection dropdown" required>
						<input type="hidden" name="department" value="<?php $department ?>">
						<i class="dropdown icon"></i>
						<div class="default text">Select Department</div>
						<div class="menu">
							<?php foreach ($departments as $key => $value) { ?>
								<div class="item" data-value="<?php $value->ID ?>"><?php $value->post_title ?></div>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="fields">
					<div class="questions" id="questions">
						<div class="content">
							<div class="ui segment transition">
								<div class="ui fluid form">
									<div class="field">
										<input id="question_json" value='<?php base64_encode(json_encode($questions_array)); ?>' type="hidden">
										<div class="form-field term-group">
											<p>
											<div class="form-field term-group">
												<input type="hidden" id="question-image-id" name="question_image" class="custom_media_url" value="<?php $questions->question_image ?>">
												<div id="question-image-wrapper">
													<img src="<?php $src ?>" style="max-width: 100%;max-height: 100%;">
												</div>
												<!-- <p>
													<input type="button" class="ui green button button-secondary qt_tax_media_button" id="qt_tax_media_button" name="qt_tax_media_button" value="Add Image" <?php echo ($questions->question_image == '') ? '' : 'style="display: none;"' ?>>
													<input type="button" class="ui red button button-secondary qt_tax_media_remove" id="qt_tax_media_remove" name="qt_tax_media_remove" value="Remove Image" <?php echo ($questions->question_image == '') ? 'style="display: none;"' : '' ?>>
												</p> -->
											</div>
										</div>
									</div>
									<br>
									<div class="field">
										<label>Type</label>
										<div class="ui fluid search selection dropdown" id="questionstype">
											<input name="questions[type]" type="hidden" value="<?php $questions_array['type'] ?>" required>
											<i class="dropdown icon"></i>
											<input class="search" autocomplete="off" tabindex="0">
											<div class="default text">Type</div>
											<div class="menu" tabindex="-1">
												<div class="item" data-value="yes/no" data-option="false" data-open="false">Yes(4) / No(1)</div>
												<!-- <div class="item" data-value="checkbox" data-option="false" data-open="false" title="Strongly Agree(4) / Agree(3) / Disagree(2) / Strongly Disagree(1)">SA(4) / A (3) / D (2) / SD(1)</div>
												<div class="item" data-value="rcheckbox" data-option="false" data-open="false" title="Strongly Disagree(4)/ Disagree(3) /Agree(2)  /Strongly Agree(1) ">SD(4) / D (3) / A (2) / SA(1)</div>
												<div class="item" data-value="rating" data-option="false" data-open="false" title="Strongly Disagree(1) / Disagree(1.5) / Somewhat Disagree(2) / Neither agree nor disagree(2.5) /Somewhat Agree(3) /Agree(3.5) /Strongly Agree(4)">SD(1)/ D(1.5)/ SWD(2) / NAND(2.5) /SWA(3) / A(3.5) / SA(4)</div> -->
												<div class="item" data-value="openended" data-option="false" data-open="true" title="openended">Open Ended</div>
												<!-- <div class="item" data-value="responce" data-option="true" data-open="false">Options</div> -->
											</div>
										</div>
										<br>
										<!-- <div class="ui styled fluid accordion" id="open_length">
											<div class="title active"><i class="icon dropdown"></i>Length</div>
											<div class="content field active" id="lengthcontainer">
												<input type="number" id="open_length" name="text_length" value="<?php $text_length ?>" min="0">
											</div>
										</div> -->
										<br>
										<div class="ui styled fluid accordion" id="accordion">
											<div class="title active"><i class="icon dropdown"></i>Options</div>
											<div class="content field active" id="optionscontainer">
												<input type="hidden" id="option_count" value="0">
												<div class="options" id="options">
												</div>
												<div class="field">
													<button type="button" class="ui facebook small button new-question-options" onclick="addOption()" id="option">Add Option</button>
												</div>
											</div>
										</div>
									</div>
									<!-- <h4 class="ui top attached block header">Languages</h4> -->
									<!-- <div class="ui attached segment">
										<div class="ui top attached tabular menu">
											<a class="item active" data-tab="ar">Arabic</a>
											<a class="item" data-tab="hi">Hindi</a>
											<a class="item" data-tab="ur">Urdu</a>
											<a class="item" data-tab="ta">Tamil</a>
											<a class="item" data-tab="ml">Malayalam</a>
											<a class="item" data-tab="bn">Bengali</a>
										</div>
										<div class="ui bottom attached tab segment active" data-tab="ar">
											<label>Question:</label>
											<br>
											<?php if (isset($questions_array['languages']) && !empty($questions_array['languages'])) { ?>
												<textarea rows="2" name="questions[languages][ar][question]"><?php $questions_array['languages']['ar']['question'] ?></textarea>
											<?php } else { ?>
												<textarea rows="2" name="questions[languages][ar][question]"></textarea>
											<?php } ?>
											<div id="optionlanggroup" style="margin-top:12px;">
												<h4 class="ui top attached block header">Options</h4>
												<div class="ui attached segment">
													<div class="field" id="arlangcontainer"></div>
												</div>
											</div>
										</div>
										<div class="ui bottom attached tab segment" data-tab="hi">
											<label>Question:</label>
											<br>
											<?php if (isset($questions_array['languages']) && !empty($questions_array['languages'])) { ?>
												<textarea rows="2" name="questions[languages][hi][question]"><?php $questions_array['languages']['hi']['question'] ?></textarea>
											<?php } else { ?>
												<textarea rows="2" name="questions[languages][hi][question]"></textarea>
											<?php } ?>
											<div id="optionlanggroup" style="margin-top:12px;">
												<h4 class="ui top attached block header">Options</h4>
												<div class="ui attached segment">
													<div class="field" id="hilangcontainer"></div>
												</div>
											</div>
										</div>
										<div class="ui bottom attached tab segment" data-tab="ur">
											<label>Question:</label>
											<br>
											<?php if (isset($questions_array['languages']) && !empty($questions_array['languages'])) { ?>
												<textarea rows="2" name="questions[languages][ur][question]"><?php $questions_array['languages']['ur']['question'] ?></textarea>
											<?php } else { ?>
												<textarea rows="2" name="questions[languages][ur][question]"></textarea>
											<?php } ?>
											<div id="optionlanggroup" style="margin-top:12px;">
												<h4 class="ui top attached block header">Options</h4>
												<div class="ui attached segment">
													<div class="field" id="urlangcontainer"></div>
												</div>
											</div>
										</div>
										<div class="ui bottom attached tab segment" data-tab="ta">
											<label>Question:</label>
											<br>
											<?php if (isset($questions_array['languages']) && !empty($questions_array['languages'])) { ?>
												<textarea rows="2" name="questions[languages][ta][question]"><?php $questions_array['languages']['ta']['question'] ?></textarea>
											<?php } else { ?>
												<textarea rows="2" name="questions[languages][ta][question]"></textarea>
											<?php } ?>
											<div id="optionlanggroup" style="margin-top:12px;">
												<h4 class="ui top attached block header">Options</h4>
												<div class="ui attached segment">
													<div class="field" id="talangcontainer"></div>
												</div>
											</div>
										</div>
										<div class="ui bottom attached tab segment" data-tab="ml">
											<label>Question:</label>
											<br>
											<?php if (isset($questions_array['languages']) && !empty($questions_array['languages'])) { ?>
												<textarea rows="2" name="questions[languages][ml][question]"><?php $questions_array['languages']['ml']['question'] ?></textarea>
											<?php } else { ?>
												<textarea rows="2" name="questions[languages][ml][question]"></textarea>
											<?php } ?>
											<div id="optionlanggroup" style="margin-top:12px;">
												<h4 class="ui top attached block header">Options</h4>
												<div class="ui attached segment">
													<div class="field" id="mllangcontainer"></div>
												</div>
											</div>
										</div>
										<div class="ui bottom attached tab segment" data-tab="bn">
											<label>Question:</label>
											<br>
											<?php if (isset($questions_array['languages']) && !empty($questions_array['languages'])) { ?>
												<textarea rows="2" name="questions[languages][bn][question]"><?php $questions_array['languages']['bn']['question'] ?></textarea>
											<?php } else { ?>
												<textarea rows="2" name="questions[languages][bn][question]"></textarea>
											<?php } ?>
											<div id="optionlanggroup" style="margin-top:12px;">
												<h4 class="ui top attached block header">Options</h4>
												<div class="ui attached segment">
													<div class="field" id="bnlangcontainer"></div>
												</div>
											</div>
										</div>

									</div> -->
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function(e) {
				var val = e('#questionstype').dropdown('get value');
				if (val == "responce") {
					e("#accordion").show();
					e("#optionlanggroup").show();
				} else {
					e("#accordion").hide();
					e("#optionlanggroup").hide();
				}
				if (val == "openended") {
					e("#open_length").show();
				} else {
					e("#open_length").hide();
				}
				e(".menu .item").tab(), e(".ui.accordion").accordion(), e("#question").on("keyup", function() {
					"" == e("textarea#question").val() ? e("#title").text("Question") : e("#title").text(e("textarea#question").val())
				}), e("#questionstype").dropdown({
					onChange: function(o, n, t) {
						if (1 == t.data("open")) {
							e("#open_length").show();
						} else {
							e("#open_length").hide();
						}
						if (0 == t.data("option")) {
							e("#accordion").hide();
							e("#optionlanggroup").hide();
							var i;
							for (i = 1; i < jQuery("#option_count").val() + 1; i++) {
								deleteOption(i);
							}
						} else {
							if (e("#option_count").val() == 0) {
								e("#option").click();
								e("#optiondelete").hide();
							}
							e("#accordion").show();
							e("#optionlanggroup").show();
						}
					}
				})
			});
		</script>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				var myplugin_media_upload;
				$('#qt_tax_media_button').click(function(e) {
					e.preventDefault();
					if (myplugin_media_upload) {
						myplugin_media_upload.open();
						return;
					}
					myplugin_media_upload = wp.media.frames.file_frame = wp.media({
						multiple: false,
						library: {
							type: ['video', 'image']
						},
					});
					myplugin_media_upload.on('select', function() {
						$('#qt_tax_media_button').hide();
						$('#qt_tax_media_remove').show();
						var attachments = myplugin_media_upload.state().get('selection').map(
							function(attachment) {
								attachment.toJSON();
								return attachment;
							});
						var i;
						for (i = 0; i < attachments.length; ++i) {
							$('#question-image-wrapper').append(
								'<div class="myplugin-image-preview"><img src="' +
								attachments[i].attributes.url + '" style="max-width: 100%;max-height: 100%;" ></div>'
							);
							$('#question-image-id').val(attachments[i].id);
						}
					});
					myplugin_media_upload.open();
				});
				$("body").on("click", "#qt_tax_media_remove", function() {
					$("#question-image-id").val(""), $("#question-image-wrapper").html("");
					$('#qt_tax_media_button').show();
					$('#qt_tax_media_remove').hide();
				})
			});
		</script>
	<?php
}
add_action('save_post', 'save_questionnaire_meta_fields', 10, 2);
add_action('save_post', 'save_department_meta_fields', 10, 2);
add_action('init', 'create_questionnaire_posttype');

//hooks
add_action('show_user_profile', 'Add_user_fields');
add_action('edit_user_profile', 'Add_user_fields');
add_action('user_new_form', 'Add_user_fields');

function Add_user_fields($user)
{
	$departments = get_posts([
		'post_type' => 'departments',
		'post_status' => 'publish',
		'numberposts' => -1,

	]);
	?>
		<h3>Extra fields</h3>
		<table class="form-table">
			<tr>
				<th><label for="text">Employee Id</label></th>
				<td>
					<?php
					// get test saved value
					$saved = esc_attr(get_the_author_meta('employee_id', $user->ID));
					?>
					<input type="text" name="employee_id" id="employee_id" value="<?php echo $saved; ?>" class="regular-text" /><br />
				</td>
			</tr>
			<tr>
				<th><label for="dropdown">Departments</label></th>
				<td>
					<select name="user_department" id="user_department">
						<?php
						//get dropdown saved value
						$selected = get_the_author_meta('user_department', $user->ID);
						foreach ($departments as $key => $value) {
						?><option value="<?php $value->ID ?>" <?php echo ($selected == $value->ID) ?  'selected="selected"' : '' ?>><?php $value->post_title ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
		</table>
	<?php } ?>
	<?php
	add_action('personal_options_update', 'save_user_fields');
	add_action('edit_user_profile_update', 'save_user_fields');
	
	add_action('user_register', 'save_user_fields');
	function save_user_fields($user_id)
	{
		if (!current_user_can('edit_user', $user_id)) {
			return false;
		}
		//save text field
		update_usermeta($user_id, 'employee_id', $_POST['employee_id']);
		//save dropdown
		update_usermeta($user_id, 'user_department', $_POST['user_department']);
	}
	
	function prevent_duplicate_departments($data, $postarr) {
		// Check if it's the right post type
		if ($data['post_type'] == 'departments') {
			// Check for duplicate title
			$existing_post = get_page_by_title($data['post_title'], 'OBJECT', 'departments');
			// If a post with the same title exists, prevent saving
			if ($existing_post && $existing_post->ID != $postarr['ID']) {
				// Display an error message
				add_filter('redirect_post_location', function ($location) {
					remove_filter('redirect_post_location', __FILTER__);
					return add_query_arg('message', 12, $location);
				});
				// Set an error message
				$error_message = __('Error: Duplicate post title. Please choose a unique title.', 'your-text-domain');
				add_action('admin_notices', function () use ($error_message) {
					echo '<div class="error"><p>' . esc_html($error_message) . '</p></div>';
				});
				// Prevent saving
				$data['post_status'] = 'draft'; // You can customize this behavior
			}
		}
		return $data;
	}
	add_filter('wp_insert_post_data', 'prevent_duplicate_departments', 10, 2);