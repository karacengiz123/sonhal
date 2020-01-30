
#!/usr/bin/php -q
<?php

//exit;
$a=file_get_contents("https://beyazmasa153.ibb.istanbul/last-online-control-cron");
$b=file_get_contents("https://beyazmasa153.ibb.istanbul/last-register-control-cron");
echo $b;
?>

