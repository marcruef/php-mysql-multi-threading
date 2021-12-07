<?php

require 'inc_config.php';
require 'inc_db_threading.php';

//Unset query array
unset($query);

//Prepare queries
$queries['users']		= 'SELECT * FROM tbl_users WHERE id>10 AND id<100000';
$queries['sessions']	= 'SELECT * FROM tbl_sessions WHERE user IN (1, 2, 3) AND timestamp>0 AND active=1';
$queries['posts']		= 'SELECT * FROM tbl_posts WHERE body LIKE "*Foobar*"';

//Execute query threads in parallel
$db_threads = db_thread($queries);

//Load all results
$users		= $db_threads['users'];
$sessions	= $db_threads['sessions'];
$posts		= $db_threads['posts'];

//Show all results
print_r($users);
print_r($sessions);
print_r($posts);

?>
