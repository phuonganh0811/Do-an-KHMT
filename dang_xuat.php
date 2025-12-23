<?php
session_start();
session_destroy();
header("Location: TrangChucopy.php");
exit;
