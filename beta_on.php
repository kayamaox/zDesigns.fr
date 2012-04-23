<?php
session_start();
$_SESSION['beta'] = true;
header('Location: ./');
?>