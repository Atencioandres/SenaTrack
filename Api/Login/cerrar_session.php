<?php
session_start();
session_destroy();
header("Location: /senatrack/login.html");
exit();
?>
