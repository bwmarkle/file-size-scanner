<?php
/*
 * System Zip Test.
 *
 * If a backup process fails, it can be difficult to troubleshoot why it is failing. Is it something
 * on the server causing a problem, something in WordPress, a plugin? Who knows.
 *
 * This is a simple test script attempts to zip an entire WordPress directory. If this file can successfully
 * make the zip file, then we can rule out php / the server when troubleshooting and focus on WordPress
 * and any plugins that are installed.
 *
 * Instructions:
 * 1. Edit the $wordpress_abspath below.
 * 2. Run the script. It should output something like so:
 * ---------------
 * zip -qr /home/user/public_html/test-backup-1596562475.zip /home/user/public_html/
 * RUNNING... Done in 108.33079218864 seconds!
 * ---------------------
 * 3. Delete the backup that was created!
 */

// Create the command that will create a zip of our WordPress directory.
$wordpress_abspath = '/home/zarkaa5/lelllo.com';
$zip_filepath      = $wordpress_abspath . '/' . 'test-backup-' . time() . '.zip';
$cmd               = 'zip -qr ' . $zip_filepath . ' ' . $wordpress_abspath;

// Show the user the command and that we're running it.
echo '<pre>' . $cmd . '</pre>';
echo '<p>RUNNING... ';

// Run the command, time how long it takes.
$start_time = microtime( true );
system( $cmd );
$total_time =  microtime( true ) - $start_time;

// We're done. Show how long the zip took.
echo 'DONE in ' . $total_time . ' seconds!</p>';

?>
