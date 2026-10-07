<?php
/*
Plugin Name: Simple To-Do List
Description: A simple To-Do List plugin for WordPress.
Version: 1.0
Author: Habiba
*/

function simple_todo_create_table() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'simple_todo';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        task text NOT NULL,
        completed tinyint(1) DEFAULT 0,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta($sql);
}

register_activation_hook(__FILE__, 'simple_todo_create_table');


function simple_todo_menu() {

    add_menu_page(
        'To-Do List',
        'To-Do List',
        'manage_options',
        'simple-todo-list',
        'simple_todo_page'
    );
}

add_action('admin_menu', 'simple_todo_menu');


function simple_todo_page() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'simple_todo';
    if (isset($_GET['complete_task'])) {

    $task_id = intval($_GET['complete_task']);

    $wpdb->update(
        $table_name,
        array('completed' => 1),
        array('id' => $task_id)
    );
}
    if (isset($_GET['delete_task'])) {

    $task_id = intval($_GET['delete_task']);

    $wpdb->delete(
        $table_name,
        array('id' => $task_id)
    );
}

    if (isset($_POST['add_task'])) {

        $task = sanitize_text_field($_POST['todo_task']);

        if (!empty($task)) {

            $wpdb->insert(
                $table_name,
                array(
                    'task' => $task
                )
            );
        }
    }

    ?>

    <div class="wrap">

        <h1>My To-Do List</h1>

        <form method="post">

            <input
                type="text"
                name="todo_task"
                placeholder="Enter your task"
            >

            <button
                type="submit"
                name="add_task"
                class="button button-primary"
            >
                Add Task
            </button>

        </form>
        <h2>Your Tasks</h2>

<?php

$tasks = $wpdb->get_results(
    "SELECT * FROM $table_name ORDER BY id DESC"
);

if ($tasks) {

    echo '<ul>';

//    foreach ($tasks as $task) {

//     echo '<li>';

//     // echo esc_html($task->task);

//     // echo ' <a href="?page=simple-todo-list&delete_task=' . $task->id . '">Delete</a>';
//     echo esc_html($task->task);

// echo ' <a href="?page=simple-todo-list&complete_task=' . $task->id . '">Complete</a>';

// echo ' | <a href="?page=simple-todo-list&delete_task=' . $task->id . '">Delete</a>';

//     echo '</li>';
// }
foreach ($tasks as $task) {

    echo '<li>';

    if ($task->completed == 1) {

        echo '<s>' . esc_html($task->task) . '</s>';
        echo ' - Completed ✓';

    } else {

        echo esc_html($task->task);

        echo ' <a href="?page=simple-todo-list&complete_task=' . $task->id . '">Complete</a>';
    }

    echo ' | <a href="?page=simple-todo-list&delete_task=' . $task->id . '">Delete</a>';

    echo '</li>';
}
    echo '<ul class="simple-todo-list">';

} else {

    echo '<p>No tasks yet.</p>';
}

?>

    </div>

    <?php
}
function simple_todo_styles() {
    ?>
    <style>
        .simple-todo-list {
            background: #fff;
            padding: 20px;
            max-width: 600px;
            border: 1px solid #ddd;
        }

        .simple-todo-list li {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .simple-todo-list a {
            margin-left: 10px;
        }
    </style>
    <?php
}

add_action('admin_head', 'simple_todo_styles');