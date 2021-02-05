<?php
/**
 * @package MancStudentsPlugin
 */
/*
Plugin Name: Manc Students Plugin
Plugin URI: https://github.com/mansi-raja5/manc-students-plugin
Description: This plugin stores students data.
Version: 1.0.0
Author: Mansi Raja
Author URI: https://github.com/mansi-raja5
License: GPLv2 or later
Text Domain: manc-students-plugin
 */

defined('ABSPATH') or die('Sorry You are not allowed to enter herr!');

if (!class_exists('MancStudentsPlugin')) {

    class MancStudentsPlugin
    {
        public $plugin;
        public static $subjects = [1 => 'Maths', 2 => 'Physics', 3 => 'Chemistry', 4 => 'English'];
        public static $gender = ['M' => 'Male', 'F' => 'Female', 'O' => 'Other'];

        public function __construct()
        {
            $this->plugin = plugin_basename(__FILE__);
        }

        public function register()
        {
            add_action('admin_enqueue_scripts', array($this, 'enqueue'));

            add_action('admin_menu', array($this, 'add_admin_pages'));

            add_filter("plugin_action_links_$this->plugin", array($this, 'settings_link'));

            add_shortcode('manc-student-list', array($this, 'manc_student_list'));
        }

        public function manc_student_list()
        {
            ob_start();
            global $wpdb;
            $table_name = $wpdb->prefix . 'students';
            $students = $wpdb->get_results("SELECT * FROM $table_name");
            echo '<link href="'.plugins_url('/assets/css/bootstrap.min.css', __FILE__).'" rel="stylesheet">';
			echo '<link href="'.plugins_url('/assets/css/frontend/students.css', __FILE__).'" rel="stylesheet">';
            require_once plugin_dir_path(__FILE__) . 'templates/manc-student-list.php';
            return ob_get_clean();
        }

        public function settings_link($links)
        {
            $settings_link = '<a href="admin.php?page=manc_students_plugin">Settings</a>';
            array_push($links, $settings_link);
            return $links;
        }

        public function add_admin_pages()
        {
            add_menu_page('Manc Students Plugin', 'Students', 'manage_options', 'manc_students_plugin', array($this, 'student_list'), 'dashicons-buddicons-buddypress-logo', 110);
            add_submenu_page('manc_students_plugin', __('Add new', 'manc_students_plugin'), __('Add new', 'manc_students_plugin'), 'manage_options', 'student_form', array($this, 'student_form'));
            add_submenu_page('manc_students_plugin', __('Shortcodes', 'manc_students_plugin'), __('Shortcodes', 'manc_students_plugin'), 'manage_options', 'student_ss', array($this, 'student_shortcodes'));
        }

        public function student_shortcodes()
        {
            require_once plugin_dir_path(__FILE__) . 'templates/student-shortcodes.php';
        }

        public function student_list()
        {
            global $wpdb;
            require_once plugin_dir_path(__FILE__) . 'inc/student-list.php';
            $studentList = new Student_List();
            $studentList->prepare_students();

            $message = '';
            if ('delete' === $studentList->current_action()) {
                $count = isset($_REQUEST['id']) && is_array($_REQUEST['id']) ? count($_REQUEST['id']) : '';
                $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Student Details is deleted: %d', 'manc_students_plugin'), $count) . '</p></div>';
            }
            require_once plugin_dir_path(__FILE__) . 'templates/student-list.php';
        }

        public function student_form()
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'students';

            $message = '';
            $notice = '';

            // student default value
            $default = array(
                'id' => 0,
                'name' => '',
                'age' => '',
                'gender' => '',
                'subjects' => '',
            );

            if (isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'student-form.php')) {
                $student = shortcode_atts($default, $_REQUEST);
                $student_valid = $this->validate_student($student);
                $student['subjects'] = implode(',', $student['subjects']);
                if ($student_valid === true) {
                    if ($student['id'] == 0) {
                        $result = $wpdb->insert($table_name, $student);
                        $student['id'] = $wpdb->insert_id;
                        if ($result) {
                            $message = __('Student was successfully saved', 'manc_students_plugin');
                        } else {
                            $notice = __('There was an error while saving student details', 'manc_students_plugin');
                        }
                    } else {
                        $result = $wpdb->update($table_name, $student, array('id' => $student['id']));
                        if ($result) {
                            $message = __('Student was successfully updated', 'manc_students_plugin');
                        } else {
                            $notice = __('There was an error while updating student details', 'manc_students_plugin');
                        }
                    }
                } else {
                    $notice = $student_valid;
                }
            } else {
                $student = $default;
                if (isset($_REQUEST['id'])) {
                    $student = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
                    if (!$student) {
                        $student = $default;
                        $notice = __('Student Details not found', 'manc_students_plugin');
                    }
                }
            }

            $studentSubjects = isset($student['subjects']) ? explode(",", $student['subjects']) : [];
            require_once plugin_dir_path(__FILE__) . 'templates/student-form.php';
        }

        public function validate_student($student)
        {
            $messages = array();
            if (empty($student['name'])) {
                $messages[] = __('Name is required', 'manc_students_plugin');
            }

            if (!ctype_digit($student['age'])) {
                $messages[] = __('Age in wrong format', 'manc_students_plugin');
            }

            if (empty($messages)) {
                return true;
            }

            return implode('<br />', $messages);
        }

        public function enqueue()
        {
            wp_enqueue_style('bootstrap', plugins_url('/assets/css/bootstrap.min.css', __FILE__));
            wp_enqueue_style('bootstrap-select-css', plugins_url('/assets/css/bootstrap-select.css', __FILE__));

            wp_enqueue_script('student-jquery', plugins_url('/assets/js/jquery.min.js', __FILE__));
            wp_enqueue_script('bootstrap-bundle', plugins_url('/assets/js/bootstrap.bundle.min.js', __FILE__));
            wp_enqueue_script('bootstrap-select-js', plugins_url('/assets/js/bootstrap-select.min.js', __FILE__));
        }

        public function activate()
        {
            require_once plugin_dir_path(__FILE__) . 'inc/manc-students-plugin-activate.php';
            MancStudentsPluginActivate::activate();
            $this->runSql();
        }

        public function runSql()
        {
            global $wpdb;
            global $manc_students_db_version;

            $table_name = $wpdb->prefix . 'students';

            // sql to create student table
            $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(100) NOT NULL,
				`age` int(11) NOT NULL,
				`gender`  char(1) NOT NULL,
				`subjects` text NOT NULL,
				PRIMARY KEY (`id`)
			);";

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql);

            add_option('manc_students_db_version', $manc_students_db_version);
        }
    }

    $mancStudentsPlugin = new MancStudentsPlugin();
    $mancStudentsPlugin->register();

    // activation
    register_activation_hook(__FILE__, array($mancStudentsPlugin, 'activate'));

    // deactivation
    require_once plugin_dir_path(__FILE__) . 'inc/manc-students-plugin-deactivate.php';
    register_deactivation_hook(__FILE__, array('MancStudentsPluginDeactivate', 'deactivate'));

}
