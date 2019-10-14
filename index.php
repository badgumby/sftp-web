<?php

// Enable PHP error reporting
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);

// Get user IP
$userIP = $_SERVER['REMOTE_ADDR'];
$pageName = $_SERVER['PHP_SELF'];

// Check for log count
if (isset($_GET['logs']) && is_numeric($_GET['logs']) && $_GET['logs'] < 5001) {
  $logCount = $_GET['logs'];
} else {
  $logCount = "20";
}

error_log("$userIP - $pageName");

$hostname = exec("cat /etc/hostname");
$uptime = exec("uptime -p");
$upSince = exec("uptime -s");
$date = exec("date");
$release = exec("head -1 /etc/system-release");
$kernel = exec("uname -r");

// For Ubuntu use:
// $ipAddr = exec("hostname -i | awk '{print $3}'");
$ipAddr = exec("hostname -i");

// Change to match your partition
$diskSpace = exec("df -h | grep /dev/mapper/centos-home | awk '{print \"<b>Total Size: </b>\" $2 \" | <b>Used: </b>\" $3 \" | <b>Available: </b>\" $4 \" | <b>Percent Used: </b>\" $5}'");

// Get ssh logged in users
exec("ps axo user:20,pid,ppid,pcpu,pmem,stime,stat,time,command | grep '[s]shd:.*@notty'", $users);

// Read SSH Log
// Re-configure /etc/rsyslog.conf to have "local5.*" use "/var/log/ssh/ssh.log"
// Re-configure /etc/ssh/sshd_config to use "LOCAL5"
exec("tail -n $logCount /var/log/ssh/ssh.log", $sshLogs);

?>

<html>
<head>
  <title>System Info - <?php echo $hostname ;?></title>
  <link href="https://fonts.googleapis.com/css?family=Roboto&amp;display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/style.css">
</head>
<body>
  <h1><?php echo strtoupper($hostname);?></h1>
  <div class="mainDiv">
  <h2>System Information</h2>
  <table border="1">
    <tr><td><b>Hostname</b></td><td><?php echo $hostname; ?></td></tr>
    <tr><td><b>IP Address</b></td><td><?php echo $ipAddr; ?></td></tr>
    <tr><td><b>Uptime</b></td><td><?php echo $uptime; ?> </td></tr>
    <tr><td><b>Last Reboot</b></td><td><?php echo $upSince; ?> </td></tr>
    <tr><td><b>System Time</b></td><td><?php echo $date; ?> </td></tr>
    <tr><td><b>OS Release</b></td><td><?php echo $release; ?></td></tr>
    <tr><td><b>Kernel Version</b></td><td><?php echo $kernel; ?></td></tr>
    <tr><td><b>Disk Space</b></td><td><?php echo $diskSpace; ?></td></tr>
  </table>
  <h2>Logged in User(s)</h2>
  <table border="1">
    <tr>
      <th>User ID</th>
      <th>Process ID</th>
      <th>Parent Process ID</th>
      <th>CPU Usage (%)</th>
      <th>Memory Usage (%)</th>
      <th>Start Time</th>
      <th>Process Status</th>
      <th>CPU Time</th>
      <th>Command</th>
    </tr>
    <?php
    foreach ($users as $user) {
      $userSplit = preg_split('/\s+/', $user);
      ?>
      <tr>
        <td>
          <?php echo $userSplit[0]; ?>
        </td>
        <td>
          <?php echo $userSplit[1]; ?>
        </td>
        <td>
          <?php echo $userSplit[2]; ?>
        </td>
        <td>
          <?php echo $userSplit[3]; ?>
        </td>
        <td>
          <?php echo $userSplit[4]; ?>
        </td>
        <td>
          <?php echo $userSplit[5]; ?>
        </td>
        <td>
          <?php echo $userSplit[6]; ?>
        </td>
        <td>
          <?php echo $userSplit[7]; ?>
        </td>
        <td>
          <?php echo $userSplit[8]; echo $userSplit[9]; ?>
        </td>
      </tr>
      <?php
    }
    ?>
 </table>
 <h2>Recent Log (<?php echo $logCount;?>)</h2>
 <div class="logDiv">
   <table class="logTable" border="1">
   <?php
   foreach ($sshLogs as $log) {
     ?>

     <tr>
       <td>
         <?php echo $log; ?>
       </td>
     </tr>

     <?php
   }
   ?>
  </table>
  </div>
  To see more than the last 20 log entries, add the 'logs' variable to the address. Example: <a href="https://<?php echo $_SERVER['SERVER_NAME'];?>/?logs=100">100 Entries (max 5000)</a>
</div>
</body>
</html>
