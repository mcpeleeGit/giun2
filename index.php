<?php
// 로깅: index.php 시작
error_log("=== index.php START ===");
error_log("REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET'));
error_log("SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET'));

require_once 'bootstrap.php';
error_log("bootstrap.php loaded");

require_once 'helpers.php';
error_log("helpers.php loaded");

require_once 'Router.php';
error_log("Router.php loaded");

require_once 'routes/web.php';
error_log("routes/web.php loaded");

error_log("=== index.php END, calling Router::dispatch() ===");
Router::dispatch();
