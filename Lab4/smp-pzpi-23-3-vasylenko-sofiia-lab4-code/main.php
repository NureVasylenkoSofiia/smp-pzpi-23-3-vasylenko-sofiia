<?php
session_start();
include 'header.php';

$page = $_GET['page'] ?? 'index';

$allowed_pages = ['index', 'products_content', 'basket_content', 'login', 'profile'];

if (in_array($page, $allowed_pages)) {
    if (in_array($page, ['profile', 'basket']) && !isset($_SESSION['login'])) {
        include 'page404.php';
    } else {
        include "$page.php";
    }
} else {
    include 'page404.php';
}

include 'footer.php';
