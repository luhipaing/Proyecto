<?php
session_start();
session_unset();
session_destroy();
header("Location: loadingpage.php");
exit;
?>
