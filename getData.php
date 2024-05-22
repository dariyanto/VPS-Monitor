<?php
	$tmp = null;

	$data = array(
		"memory" => array_map(
			function($value) {
				return (int)$value / 1000000;
			},
			explode(" ", exec("free | grep 'Mem:' | awk {'print $2\" \"$3\" \"$4\" \"$6'}"))
		),
		"CPUDetail" => trim(exec("sed -n 's/^cpu\s//p' /proc/stat")),
		"CPU" => array(),
		"storage" => array(
			"total" => disk_total_space("/") / 1000000000, 
			"free" => disk_free_space("/") / 1000000000, 
			"used" => (disk_total_space("/") - disk_free_space("/")) / 1000000000
		),
		"network" => array_map('intval', explode(" ",exec("cat /proc/net/dev | grep 'eth0:' | awk {'print $2\" \"$3\" \"$10\" \"$11'}"))),
		"uptime" => (int)exec("cut -d. -f1 /proc/uptime"),
		"OS" => exec("cat /etc/*-release | grep 'PRETTY_NAME' | cut -d \\\" -f2")
	);

	exec("cat /proc/cpuinfo | grep -i 'model name\|cpu cores\|cpu mhz'", $tmp);

	foreach($tmp as $line)
	{
		list($key, $value) = explode(":", $line);
		$data["CPU"][] = array(trim($key), trim($value));
	}

	if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	        header('Access-Control-Allow-Origin: *');
	        header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
	        header('Access-Control-Allow-Headers: token, Content-Type');
	        header('Access-Control-Max-Age: 1728000');
	        header('Content-Length: 0');
	        header('Content-Type: text/plain');
	        die();
	    }
	
	    header('Access-Control-Allow-Origin: *');
	    header('Content-Type: application/json');

	echo json_encode($data);
?>
