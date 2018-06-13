<?php $progid="D-Board";
 $progver = 1;
session_start();
// error_reporting(E_ERROR+E_PARSE+E_CORE_ERROR+E_COMPILE_ERROR+E_RECOVERABLE_ERROR);
 error_reporting(E_ALL - E_NOTICE); 
 set_time_limit(60);
 ini_set('session.gc_maxlifetime',300);
 ini_set('session.gc_probability',1);
 ini_set('session.gc_divisor',1);?>
