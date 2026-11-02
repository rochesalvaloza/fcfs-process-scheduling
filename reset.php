<?php
// Clear the saved form inputs and previous scheduling results.
session_start();
$_SESSION = [];
session_destroy();

header('Location: index.php');
exit;
