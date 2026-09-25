<?php
$host = 'mysqlsrv.dcs.bbk.ac.uk';
$db   = 'abutt20db'; 	#your MySQL database name goes here
$user = 'abutt20';		#your ITS loginID
$pass = 'bbkmysql';		#password
$charset = 'utf8mb4';	#DO NOT CHANGE
#do not alter anything below this line
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
		$pdo = new PDO($dsn, $user, $pass, $options);
	} catch (PDOException $e) {
		$errorCode = $e->getCode();
		$errorMessage = $e->getMessage();
		echo htmlParagraph("Database Connection Error : $errorCode : $errorMessage");
		echo htmlParagraph("A valid Database Connection is required to run this website.");
		exit();
}

?>