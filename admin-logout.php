<?php
session_name('gf_admin_session');
session_start();
session_destroy();
header('Location: admin-login.php');
exit;
