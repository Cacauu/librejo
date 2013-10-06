<?php
session_name('Librejo');
session_start();
session_destroy();
echo "<p>Logged out successfully!</p>";
?>