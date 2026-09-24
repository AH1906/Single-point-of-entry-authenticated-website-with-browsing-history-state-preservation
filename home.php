<?php
$headTitle = 'Home Page';
$viewHeading = htmlHeading('Home Page', 2);
$content = htmlParagraph('This assignment requires a user to login using a login ID and a password stored in a MySQL table you created in Task 3 of this coursework.');
$content .= htmlParagraph('After a successful login for the first time, the user should land on the home page, with a plain style and be able to access and freely browse an additional two page views.');
$content .= htmlParagraph('After a successful login, the user should also be able to select 3 different CSS style themes [plain, light and dark] which will change the background colour of the web pages.');
$content .= htmlParagraph('On logging out the current page being viewed and style set should be saved as Cookies for up to one year.');
$content .= htmlParagraph('On subsequent logins the user should land on the last page viewed, with the last style set.');
$content .= htmlParagraph('If you are running this website from : https://titan.dcs.bbk.ac.uk/~abutt20/p1/cwk2/task4');
$content .= htmlParagraph('You can use the following test username: abdulbutt20 password: Battersea2006!');
?>
