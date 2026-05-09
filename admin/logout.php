<?php
// ============================================================
// admin/logout.php - Logout Admin
// ============================================================
require_once '../config/config.php';

session_destroy();
redirect(APP_URL . '/admin/login.php');
