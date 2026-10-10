<?php
/*
 * Shared security helpers.
 * Include this file at the VERY TOP of a page (before any HTML is printed):
 *
 *     require_once __DIR__ . '/includes/security.php';
 *     require_admin();      // admin-only page
 *     require_staff();      // employee page (admins may open it too)
 */

# ---------- Safe session ----------
if (session_status() === PHP_SESSION_NONE) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
          || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $https,  # only send the cookie over https when the site uses https
        'httponly' => true,    # JavaScript cannot read the session cookie
        'samesite' => 'Lax',   # helps block requests from other websites
    ]);
    session_start();
}

# Do not show technical PHP errors to visitors (they reveal file paths and SQL)
ini_set('display_errors', '0');

# ---------- Output ----------
# Use e() whenever printing data that came from the user or the database
function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

# ---------- Login checks ----------
function is_admin()
{
    return !empty($_SESSION['admin_id']);
}

function is_employee()
{
    return !empty($_SESSION['employee_id']);
}

# Admin-only pages
function require_admin()
{
    if (!is_admin()) {
        header('Location: admin_login.php');
        exit;
    }
}

# Employee pages (admins are also allowed in)
function require_staff()
{
    if (!is_employee() && !is_admin()) {
        header('Location: employ_login.php');
        exit;
    }
}

# Call right after a successful login (stops session-fixation attacks)
function login_session_refresh()
{
    session_regenerate_id(true);
}

# ---------- CSRF protection ----------
# Put  echo csrf_field();  inside every POST form (inside PHP tags),
# and call  csrf_check();  before handling the form.
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_check()
{
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(400);
        exit('Your session has expired. Please go back, refresh the page and try again.');
    }
}

# ---------- Passwords ----------
# Checks a password against the stored value.
# Old accounts saved as plain text still work once, and are then
# upgraded to a secure hash automatically.
function verify_and_upgrade_password($dbc, $table, $id_column, $id, $input, $stored)
{
    $info = password_get_info((string)$stored);
    $is_hash = !empty($info['algo']);

    if ($is_hash) {
        $ok = password_verify($input, $stored);
    } else {
        $ok = hash_equals((string)$stored, (string)$input); # legacy plain-text password
    }

    if ($ok && (!$is_hash || password_needs_rehash($stored, PASSWORD_DEFAULT))) {
        $new_hash = password_hash($input, PASSWORD_DEFAULT);
        # $table and $id_column come from our own code, never from the user
        $stmt = mysqli_prepare($dbc, "UPDATE `$table` SET password = ? WHERE `$id_column` = ?");
        mysqli_stmt_bind_param($stmt, 'si', $new_hash, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    return $ok;
}

# ---------- Login rate limit (simple, per session) ----------
# Blocks a browser for 5 minutes after 5 wrong passwords in a row.
function login_blocked($key)
{
    $until = $_SESSION['login_block'][$key] ?? 0;
    return $until > time();
}

function login_failed($key)
{
    $_SESSION['login_fail'][$key] = ($_SESSION['login_fail'][$key] ?? 0) + 1;
    if ($_SESSION['login_fail'][$key] >= 5) {
        $_SESSION['login_block'][$key] = time() + 300;
        $_SESSION['login_fail'][$key] = 0;
    }
}

function login_succeeded($key)
{
    unset($_SESSION['login_fail'][$key], $_SESSION['login_block'][$key]);
}
