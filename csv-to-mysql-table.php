<?php

ini_set('max_execution_time', 3000); 

$servername = "localhost";
$username = "";
$password = "";
$dbname = "";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} 

function onlyAlphanumeric($text) {
 	return preg_replace("[^A-Za-z]", "", $text);
}

if($_POST['table']) {
	
	$table = $_REQUEST['table'];
	$fields = $_REQUEST['fields'];

	if(!isset($table)) {
		die('Error: required field.');
	}
	if($_FILES['file_upload']['error'] > 0){
		die('An error ocurred when uploading.');
	}

	$field_list = explode(',',$fields);

	// build our table dynamically.
	$createtable = "CREATE TABLE IF NOT EXISTS `".$table."` (";
	for($j=0;$j<=count($field_list);$j++) {
		if($field_list[$j] != '') {
			$createtable .= "`".$field_list[$j]."` varchar(255) ";
			if($j != count($field_list)-1) {
				$createtable .= ",";
			}
		}
	}
	$createtable .= "\n";
	$createtable .= ") ";
	$createtable .= " ENGINE=MyISAM DEFAULT CHARSET=utf8;";

	$conn->query($createtable) or die(mysqli_error($conn));

	$file_url = str_replace("\\", "/", $_FILES['file_upload']['tmp_name']);

if($file_url) {
$querys = <<<eof
    LOAD DATA LOCAL INFILE '$file_url'
     REPLACE
     INTO TABLE $table
     FIELDS TERMINATED BY ',' 
	 ENCLOSED BY ''
     LINES TERMINATED BY '\n'
    ($fields)
eof;
$query = mysqli_query($conn, $querys) or die (mysqli_error($conn)); 
} else {
	die('error file processing');
}

// Alter table, add custom columns.
$alttable = 'ALTER TABLE '.$table.' ADD COLUMN `id` int(11) NOT NULL AUTO_INCREMENT primary key FIRST;';
$conn->query($alttable) or die(mysqli_error($conn));

}     
        
?>

<style>
span, input {
	margin:10px;
}
</style>

<fieldset>
	<form action='' enctype='multipart/form-data'  method='post'>
    	 	<span>File: <input type='file' name='file_upload'></span> <br>
	 	<span>Table Name: <input type="text" name="table" value=""></span>
		<span>Fields: <input type="text" name="fields" value=""> (apple,pears,lemons)</span>
     		<input type='submit' name='Submit' value='Upload CSV' />
	</form>
</fieldset>
