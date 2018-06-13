# View logs

### Component view the saved logs of core and extensions Joomla

**Scope**:

- reading log files and displaying their contents in a tabular form in the admin panel

- autoexpand json-string message when viewing log in admin panel, <br>(upd 1.1.0) correct json output with deep nesting of objects, <br>(upd 1.1.1) collapse (accordion) of json-message block to save screen space

- ability to download the log file in CVS format (two options: classic and specially for opening in MS-Excel)

- ability to delete log file

- (upd 1.1.0) correct reading of log files with non-standard columns

- (upd 1.2.0) reading PHP error log file (provided that it is installed in php.ini and available for reading from the site)

**Requirements**:

- Joomla 3.2 or later (com_ajax involved)

- PHP 5.6 or later

**Disadvantage**: the log file is read and displayed entirely, if it is large, it will take time, create a load on resources and traffic, so <br>**Recommendation for extension developers**: with intensive logging provide avtorezina logs into parts, task types, period, either, but that logs your not weighed megatons

<img src="https://image.prntscr.com/image/pbf3-h1UT8G8QvcGtZ3Hbw.png">

About how the native extension to use logging, see the Joomla documentation: https://docs.joomla.org/Using_JLog#Logging_a_specific_log_file
