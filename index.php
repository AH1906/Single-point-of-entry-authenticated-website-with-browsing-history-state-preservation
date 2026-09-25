<?php
/*	Web Programming Using PHP Cwk2 Task 4 - MVC single point of entry with saved state Cookie data
	See corresponding coursework specification
*/
require_once 'includes/functions.php';  #user defined function library
require_once 'includes/config.php';  	#database connection returns as $pdo
session_start();

#Add your MVC controller PHP code here
/*
You will need to:
1. Detect login and validate user credentials, setting $_GET URL parameter and $_SESSION entries if valid
2. Determine whether to display the login or logout form, setting [+placeholders+] appropriately
3. Detect if the user pressed logout and if so, set last page viewed and current style cookies and destroy session
4. Detect if user has changed the style SELECT option; if so update $_SESSION and refresh page with new style
5. Check URL view parameter; if set load that page content and reload page; else use default home page
6. Build the relevant NAV options depending on whether a user is logged in or not
7. Render the page template in browser with selected form, style, nav and page content
*/

#View is set if a page is selected, else defaulted to the home view
if (isset($_GET['view'])) {
	 $view = $_GET['view']; 
} elseif (isset($_SESSION['view'])) {
	$view = $_SESSION['view'];
} else {
	$view = 'home'; 
}

#The session view is set to the current view
$_SESSION['view'] = $view;

#If the user enters a url for page1 or page2 in the login page, still defaults to home
if (!isset($_SESSION['userName']) AND ($view === 'page1' OR $view === 'page2')) {
	$view = 'home';
}

#Logout processed, save the current page and selected style in cookies, then destroy the session
if (isset($_POST['logout'])) {
	logoutUser($view, $_SESSION['style'] ?? 'plain');
	exit;
}

#If a style is clicked, the stylesheet is updated and reload the current page
if (isset($_POST['style'])) {
	updateStyle();
	header('Location: index.php?view='. $view);
	exit;
}

#Initialise all placeholders to empty strings
$placeholders = clearData();

#Validate the submitted login details and redirect to the user's saved page if successful
if (isset($_POST['login'])) {
	$validForm = validateForm($pdo, $_POST);
	$validData = $validForm[0];
	$cleanData = $validForm[1];

	if ($validData){
		loginUser();
		header('Location: index.php?view='. $_SESSION['view']);
		exit;
	} else {
		$placeholders = $validForm[2];
	}
}

#Html navigation is generated
$nav = buildNav();
#Set the css style used in the session
$css = $_SESSION['style'] ?? 'plain';

#Switch statement used to deal with view urls and home pages
switch ($view) {
	case 'home' :
		include 'views/home.php';
		break;
	case 'page1' :
		include 'views/page1.php';
		break;
	case 'page2' :
		include 'views/page2.php';
		break;
	default:
		include 'views/404.php'; #Any other view inputted will lead to 404 error
}

#Determine whether to display the logout form or the login form
if (isset($_SESSION['userName'])) {
	
	#Page template for the logout form is set to replace the placeholders, meaning the user is logged in
	$form = file_get_contents('html/logoutFormTemplate.html');
	$placeholders['[+loggedInName+]'] = $_SESSION['userName'];
	#Switch statement used to ensure the tylesheet is chosen correctly and display the expected css file
	switch ($_SESSION['style']) {
		case 'plain' :
			$placeholders['[+plainSelected+]'] = 'selected';
			$placeholders['[+lightSelected+]'] = '';
			$placeholders['[+darkSelected+]'] = '';
			break;
		case 'light' :
			$placeholders['[+lightSelected+]'] = 'selected';
			$placeholders['[+plainSelected+]'] = '';
			$placeholders['[+darkSelected+]'] = '';
			break;
		case 'dark' :
			$placeholders['[+darkSelected+]'] = 'selected';
			$placeholders['[+plainSelected+]'] = '';
			$placeholders['[+lightSelected+]'] = '';
			break;
	}
	$form = str_replace(array_keys($placeholders), array_values($placeholders), $form);
} else { #Else the login form template is set to replace the placeholders, therefore the user will have to log in
		$form = file_get_contents('html/loginFormTemplate.html');
		$form = str_replace(array_keys($placeholders), array_values($placeholders), $form);
}

#Overall page template to replace the placeholders with the main contents, such as the style, navigation
$htmlPage = file_get_contents('html/pageTemplate.html');
echo str_replace(['[+title+]', '[+style+]', '[+loginOutForm+]', '[+nav+]', '[+heading+]', '[+content+]'], [$headTitle, $css, $form, $nav, $viewHeading, $content], $htmlPage);
?>