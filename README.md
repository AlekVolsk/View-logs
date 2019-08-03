# View logs

![Version](https://img.shields.io/badge/VERSION-1.3.7-0366d6.svg?style=for-the-badge)
![Joomla](https://img.shields.io/badge/joomla-3.2+-1A3867.svg?style=for-the-badge)
![Php](https://img.shields.io/badge/php-5.6+-8892BF.svg?style=for-the-badge)

_description in Russian [here](README.ru.md)_

## Component view the saved logs of core and extensions Joomla

**Scope**:

- reading log files and displaying their contents in a tabular form in the admin panel
- autoexpand json-string message when viewing log in admin panel,<br>
(upd 1.1.0) correct json output with deep nesting of objects,<br>
(upd 1.1.1) collapse (accordion) of json-message block to save screen space
- ability to download the log file in CVS format (two options: classic and specially for opening in MS-Excel)
- ability to delete log file
- (upd 1.1.0) correct reading of log files with non-standard columns
- (upd 1.2.0) reading PHP error log file (provided that it is installed in php.ini and available for reading from the site)
- (upd 1.3.0) archiving a log file to an archive with a log file name + current datetime (assuming the php-zip extension is connected), the archive is saved to the site folder specified in the component settings, by default `/tmp`, where optionally deleting the original file after archiving is also configured

**Disadvantage**: the log file is read and displayed entirely, if it is large, it will take time, create a load on resources and traffic, so<br>
**Recommendation for extension developers**: with intensive logging provide avtorezina logs into parts, task types, period, either, but that logs your not weighed megatons

![screen](https://image.prntscr.com/image/pbf3-h1UT8G8QvcGtZ3Hbw.png)

About how the native extension to use logging, see the Joomla documentation: https://docs.joomla.org/Using_JLog#Logging_a_specific_log_file
