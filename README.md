# Overview
Koreroa (which means talk, speak, discuss in Cook Islands Maori) is the name of our Open Source Language Database Web application  developed by the Cook Islands Internet Action Group.

## Application Platform
The application is a LAMP Stack (Linux Apache PHP MySQL) based web application and is similiar to the one developed by the orginal project here http://www.maori.org.ck/

# Basic Install Instructions
To install the application of your LAMP based web server you'll need to
* Upload and Unzip the source file
* Create a database for the web application 
* Edit the Configuration file

# Detailed Installation
## Upload zip file to your server
* Download the zipped version of the application here https://github.com/ano/Korero/archive/master.zip
* Upload the code to your LAMP server
* Unzip it

## Create your MySQL Database
* Create an empty database 
* Copy, Paste and Execute the SQL code in the database folder, SQL code is located here https://github.com/ano/Korero/blob/master/database/maoridb.sql 

## Edit configuration file
Find and Change the following configuration file's details to connect to the database

Set the details to your databases credentials, example if you
// Database connection info
define("EW_CONN_HOST", 'localhost', TRUE);
define("EW_CONN_PORT", 3306, TRUE);
define("EW_CONN_USER", 'maoridb_user', TRUE);
define("EW_CONN_PASS", 'maoridb_rocks', TRUE);
define("EW_CONN_DB", 'maoridb', TRUE);

# Acknowlegements and Thanks
This project would not have been successfull without the contribution and support of the following individuals and Organisations
Sylvia Cadena - Community Partnerships Specialist: ISIF.ASIA (http://isif.asia/)
Rod Dixon - Director: USP, Cook Islands Campus
Jules Maher - CEO: Telecom Cook Islands

## Project Team
Maureen Hilyard - Ex-PICISOC Chairman
Nga Teinangaro - Teaching and Learning Resource Co-ordinator
Violet Tisam - Community Engagement
