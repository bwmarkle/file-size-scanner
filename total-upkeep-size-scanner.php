<?php

function run_command( $cmd ) {
	$tmp_file = 'total-upkeep-size-scanner.tmp';

	$cmd .= ' > ' . $tmp_file;

	system( $cmd );

	$file = file_get_contents( $tmp_file );

	unlink( $tmp_file );

	return $file;
}

function timeout_run() {
	$tmp_file = 'total-upkeep-timeout-test.tmp';

	if ( file_exists( $tmp_file ) ) {
		unlink( $tmp_file );
	}

	$start = time();

	$duration = 0;

	// Set the max test time to 10 minutes.
	$max_test_time = 60 * 10;
	ini_set('max_execution_time', $max_test_time);

	while ( $duration <= $max_test_time ) {
		if ( time() - $start !== $duration ) {
			$cmd = 'echo ' . $duration . ' >> ' . $tmp_file;
			echo '<pre>' . $cmd . '</pre>';
			system( $cmd );
		}

		$duration = time() - $start;
	}
}
?>

<h1>Total Upkeep Size Scanner</h1>

<p>Initial release. This will only work on Linux.</p>

<ul>
	<li><a href="?task=biggest_files">List 50 biggest files.</a></li>
	<li><a href="?task=size_per_extension">Size per extension</a></li>
	<li><a href="?task=timeout_run">Timeout - Run</a></li>
	<li><a href="?task=timeout_view">Timeout - View</a></li>
	<li><a href="?task=find_file&name=*error_log*">Find error logs</a></li>
</ul>

<hr />

<?php
	$task = empty( $_GET['task'] ) ? null : $_GET['task'];

	switch( $task ) {
		case 'biggest_files':

			$results = run_command( 'du -ah | sort -n -r | head -n 50' );
			echo '<pre>' . print_r( $results, 1 ) . '</pre>';

			break;


		case 'size_per_extension':

			$extensions = run_command( "find . -type f | perl -ne 'print $1 if m/\.([^.\/]+)$/' | sort -u" );
			$extensions = explode( "\n", $extensions );

			// Sanitize. Only allow [A-Za-z0-9_] in extensions.
			foreach ( $extensions as $extension ) {
				$extension = preg_replace( '/[^ \w-]/', '', $extension );

				if ( empty( $extension ) ) {
					continue;
				}

				$cmd     = "find . -iname '*.$extension' -print0 | du -ch --files0-from=- | tail -1";
				$results = run_command( $cmd );

				echo '<p>Extension: <a href="?task=all_per_extension&extension=' . $extension . '">' . $extension . '</a></p>';
				echo '<pre>' . print_r( $results, 1 ) . '</pre>';
				echo '<hr />';
			}

			break;


		case 'all_per_extension':

			if ( empty( $_GET['extension'] ) ) {
				echo '<p>Missing extension.</p>';
				die();
			}

			$extension = $_GET['extension'];
			$cmd       = "find . -iname '*.$extension' -print0 | du -c --files0-from=- | sort -k1 -n";
			$results   = run_command( $cmd );

			echo '<p>Extension: <a href="?task=all_per_extension&extension=' . $extension . '">' . $extension . '</a></p>';
			echo '<pre>' . print_r( $results, 1 ) . '</pre>';
			echo '<hr />';

			break;


		case 'timeout_run':

			timeout_run();

			break;


		case 'timeout_view':

			$tmp_file = 'total-upkeep-timeout-test.tmp';
			$file     = file_get_contents( $tmp_file );

			echo '<pre>' . $file . '</pre>';

			break;


		case 'find_file':

			if ( empty( $_GET['name'] ) ) {
				die( 'Missing file name' );
			}

			$name    = escapeshellcmd( $_GET['name'] );
			$name = str_replace( '\*', '*', $name );
			$cmd     = 'find ~ -name "' . $name . '" -exec ls -lh {} \;';
			$results = run_command( $cmd );

			echo '<pre>' . $results . '</pre>';

			break;
	}