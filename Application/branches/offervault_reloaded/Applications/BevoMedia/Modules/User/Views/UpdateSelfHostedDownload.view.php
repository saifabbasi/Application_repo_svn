<?php
ignore_user_abort(true);
require_once(PATH . 'PhoneHome.class.php');
$ph = new PhoneHome();
$ph->bevo_auth($this->User);


if($ph->updateReady() || isset($_GET['force'])) {
    if($ph->disabled)
        die;
    if($ph->doUpdate())
        echo isset($_GET['force']) ? '' : "<script>parent.location.href = parent.location.href;</script>";
    exit;
}