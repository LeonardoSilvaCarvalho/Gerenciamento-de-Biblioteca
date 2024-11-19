How to Run the System Locally
1.	Prerequisites:
 o	PHP 7.4+.
 o	MySQL 5.7+.
 o	Composer.

2.	Steps:
 o	Create a folder in the C: directory with the name you want, then click on the repository inside it.
 
 o	Clone the repository:
  git clone <url-do-repositorio>
 
 o	Install dependencies:
  composer install
 
 o	Configure the database in /App/ Connection.php.
   Start an apache server and access Mysql, and import the database into it, just configure your credentials in the Connection.php file
 
 o	Start the server: 
  If you have git bash installed, go to the project's public folder and open git bash then run the command:
   php -S localhost:8080
  After that just access localhost:8080 in your browser.
  If not, use the command prompt to go to the project's public folder and pass the above field to start the server.

 
