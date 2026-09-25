<?php
#ADD YOUR USER DEFINED FUNCTIONS HERE
function databaseExists($pdo, $table) {
	#Checks if the specified table in database exists
	$sql = "SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?";
	#If a matching table is found, return true;
	try {
		$stmt=$pdo->prepare($sql);
		$stmt->execute([$table]);
		return (bool) $stmt->fetch();
	} catch (PDOException $e) {
		$errorCode = $e->getCode();
		$errorMessage = $e->getMessage();
		echo "$errorCode : $errorMessage"; #Database error messages displayed;
		return false;
	}
}

function usernameExists($pdo, $username) {
	#Checks whether the username supplied exists in the usersTable table
	$sql = "SELECT * FROM usersTable WHERE username = ?";
	#Return the matching user record if the username exists
	try {
		$stmt = $pdo->prepare($sql);
		$stmt->execute([$username]);
		$user = $stmt->fetch();
		return $user;
	} catch (PDOException $e) {
		$errorCode = $e->getCode();
		$errorMessage = $e->getMessage();
		echo "$errorCode : $errorMessage";
		return false;
	}
}

function clearData() {
	#Clear values for each placeholder
	$placeholders = ['[+uName+]' => '',
						'[+loginError+]' => '',
						'[+plainSelected+]' => '',
						'[+lightSelected+]' => '',
						'[+darkSelected+]' => '',
						'[+loggedInName+]' => '',
						];
	
	return $placeholders;
}

function validateForm($pdo, $formData) {
	#Initialise validation flag, an array for storing and placeholders initialised with no values
	$validData = true;
	$cleanData = array();
	$placeholders = clearData();
	#Then placeholders for the username can store the value for the username entered
	$placeholders['[+uName+]'] = trim(htmlentities($formData['userName']));
	
	#Validate if database exists, then if username exists
	if (databaseExists($pdo, 'usersTable')) {
		$user = usernameExists($pdo, $formData['userName']);
		if ($user !== false) {
			if ($user['password'] === $formData['password'])  { #Compare password in the database and the current password being entered
				$cleanData['username'] = trim(htmlentities($formData['userName']));
				$cleanData['password'] = trim(htmlentities($formData['password']));
			} else { #Else data is invalid and placeholders for error messages are filled in, in the form of paragraphs
				$validData = false;
				$placeholders['[+loginError+]'] = htmlParagraph('Incorrect password');
			}
		} else {
			$validData = false;
			$placeholders['[+loginError+]'] = htmlParagraph('User unknown');
		}
	} else {
		$validData = false;
		$placeholders['[+loginError+]'] = htmlParagraph('No database table found!');
	}
	
	return [$validData, $cleanData, $placeholders]; #Return if data is valid, the array containing the username and password, and updated placeholders
}

function loginUser() {
	#Session's username should equal the sanitised entered username, along with default stylesheet in the session
	$username = trim($_POST['userName']);
	
	$_SESSION['userName'] = $username;
	$_SESSION['style'] = 'plain';
	
	#Read cookies to restore the user's last page and stylesheet selected from cookies
	if (isset($_COOKIE[$username . '_view'])) {
		$_SESSION['view'] = $_COOKIE[$username .'_view'];
	} else {
		$_SESSION['view'] = 'home';
	}
	
	if (isset($_COOKIE[$username . '_style'])) {
		$_SESSION['style'] = $_COOKIE[$username .'_style'];
	} else {
		$_SESSION['style'] = 'plain';
	}
}

function logoutUser($view, $style) {
	$username = trim($_SESSION['userName']);
	
	#Set the time for a year
	$time = time() + (365 * 24 * 60 * 60);
	
	#Cookies are set to store the view and style data, for a year
	setcookie($username . '_view', $view, $time);
	setcookie($username . '_style', $style, $time);
	
	#The whole session is cleared
	$_SESSION = array();
	
	#Delete session cookie data
	if (ini_get("session.use_cookies")) {
		$yesterday = time() - (24 * 60 * 60);
		$params = session_get_cookie_params();
		setcookie(session_name(), '', $yesterday, $params["path"], $params["domain"],$params["secure"], $params["httponly"]);
	}
	
	#Destroy session and reload page to the home page
	session_destroy();
	header('Location: index.php');
	exit;
}

function buildNav() {
	#Build HTML navigation menu
	$html = '<nav>';
	
	#If a session contains username data (so logged in), set the navigation links for all available pages
	if (isset($_SESSION['userName'])) {
		$html .= '<ul>';
		$html .= '<li><a href="index.php?view=home">Home Page</a></li>';
		$html .= '<li><a href="index.php?view=page1">Page 1</a></li>';
		$html .= '<li><a href="index.php?view=page2">Page 2</a></li>';
		$html .= '</ul>';
	} else { #Else display only the home page link
		$html .= '<ul>';
		$html .= '<li><a href="index.php?view=home">Home Page</a></li>';
		$html .= '</ul>';
	}
	$html .= '</nav>';
	return $html;
}

function updateStyle() {
	#Update the selected stylesheet stored in the session
	if ($_POST['style'] === 'light') {
		$_SESSION['style'] = 'light';
	} elseif ($_POST['style'] === 'dark') {
		$_SESSION['style'] = 'dark';
	} else {
		$_SESSION['style'] = 'plain';
	}
	
	return $_SESSION['style'];
}

#HTML UTILITY FUNCTIONS

function htmlHeading($text, $level) {
	$heading = trim(strtolower($text));
	#Return sanitised text with chosen heading level
	switch ($level) {
		case 1 :
		case 2 :
			$heading = ucwords($heading);
			break;
		case 3 :
		case 4 :
		case 5 :
		case 6 :
			$heading = ucfirst($heading);
			break;
		default: #traps unknown heading level exception
			$heading = '<FONT COLOR="#ff0000">Unknown heading level:' . $level . '</FONT>';
		}
	return '<h' . $level . '>' . htmlentities($heading) . '</h' . $level .  '>';
}

function htmlParagraph($text) {
	#Return sanitised paragraph text
	return '<p>' . htmlentities(trim($text)) . '</p>';
}


#Debugging functions

function displayArray($array, $name) {
	return '<pre>' . $name . print_r($array,true) .	'</pre>';
}
?>