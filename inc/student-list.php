<?php
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
class Student_List extends WP_List_Table
{
    public $subjects;

    public function __construct()
    {
        global $status, $page;
        parent::__construct(['singular' => 'student', 'plural' => 'students']);
    }

    public function column_default($student, $column_name)
    {
        return $student[$column_name];
    }

    public function column_age($student)
    {
        return '<em>' . $student['age'] . '</em>';
    }

    public function column_gender($student)
    {
        return isset(MancStudentsPlugin::$gender[$student['gender']]) ? MancStudentsPlugin::$gender[$student['gender']] : '';
    }

    public function column_subjects($student)
    {
        $subjectAry = explode(",", $student['subjects']);
        $subjects = [];
        foreach ($subjectAry as $_subjectKey) {
            $subjects[] = isset(MancStudentsPlugin::$subjects[$_subjectKey]) ? MancStudentsPlugin::$subjects[$_subjectKey] : '';
        }
        return implode(", ", array_filter($subjects));
    }
    /**
     *
     * @param $student - row (key, value array)
     * @return HTML
     */
    public function column_name($student)
    {
        $actions = array(
            'edit' => sprintf('<a href="?page=student_form&id=%s">%s</a>', $student['id'], __('Edit', 'manc_students_plugin')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $student['id'], __('Delete', 'manc_students_plugin')),
        );

        return sprintf('%s %s',
            $student['name'],
            $this->row_actions($actions)
        );
    }

    /**
     * @param $student - row (key, value array)
     * @return HTML
     */
    public function column_cb($student)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $student['id']
        );
    }

    /**
     * @return array
     */
    public function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'name' => __('Name', 'manc_students_plugin'),
            'age' => __('Age', 'manc_students_plugin'),
            'gender' => __('Gender', 'manc_students_plugin'),
            'subjects' => __('Subjects', 'manc_students_plugin'),
        );
        return $columns;
    }

    /**
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'name' => array('name', true),
            'age' => array('age', false),
        );
        return $sortable_columns;
    }

    /**
     * @return array
     */
    public function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete',
        );
        return $actions;
    }

    /**
     *
     */
    public function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'students';

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) {
                $ids = implode(',', $ids);
            }

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }

    /**
     *
     */
    public function prepare_students()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'students';

        $per_page = 5;

        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = [$columns, $hidden, $sortable];

        $this->process_bulk_action();

        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] - 1) * $per_page) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'name';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ));
    }
}
