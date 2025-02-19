<?php
// session_check.php
function ensure_session_started() {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        session_start();
    }
}