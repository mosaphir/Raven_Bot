<?php

ini_set('log_errors', 1);
ini_set('error_log', 'php-error.log');

ignore_user_abort(true);
set_time_limit(0);

$data = file_get_contents('php://input');

exec("nohup php bot.php '".base64_encode($data)."' > /dev/null 2>/dev/null &");

?>