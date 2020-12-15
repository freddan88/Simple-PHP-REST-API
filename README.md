# Simple PHP Email API

> Post formdata and send emails by using the built in mail function in PHP

[Code License: MIT](https://choosealicense.com/licenses/mit/)

### There are two versions in this repository

-   [Version using csrv and sessions](#version-using-csrv)
-   [Version not using csrv and sessions](#version-not-using-csrv)

| My Links: |                                                               |
| --------- | ------------------------------------------------------------- |
| WebPage:  | [leemann.se/fredrik](http://www.leemann.se/fredrik)           |
| YouTube:  | [youtube.com/FreLee54](https://www.youtube.com/user/FreLee54) |
| GitHub:   | [github.com/freddan88](https://github.com/freddan88)          |
|           |                                                               |

### Tested with

-   [PHP](https://www.php.net)
-   [Apache](https://www.apache.org)
-   [Postman](https://www.postman.com)
-   [Axios](https://www.npmjs.com/package/axios)
-   [React](https://reactjs.org)

#### OBS! Only tested with web servers like Apache and PHP:s built in

> In order to send emails php.ini needs to be configured to use a mail-server or sendmail-program

### Links

-   [How to Sendmail in PHP - Dibya Sahoo](https://pepipost.com/tutorials/sendmail-in-php-complete-guide)
-   [PHP.NET: .INI Configuration for emails](https://www.php.net/manual/en/mail.configuration.php)
-   [Send email from Localhost - phpBasics](https://www.youtube.com/watch?v=4_NP_WYFmIM&list=LLr-xGBx3NL3VGbdjDL4BuNw&index=2&t=0s)
-   [Download sendmail for Windows](https://www.glob.com.au/sendmail)

### Example code client (axios)

-   [Javascript client code using csrv](client_code/app_csrf.js)
-   [Javascript client code not using csrv](client_code/app.js)

---

## Version using csrv

### Functionalities:

1. Security checks of api key and origin
2. Will generate and send CSRF-token to client
3. Validation and sanitization of data from user
4. Error messages are sent back to client as json

### API endpoints

| Endpoint | Request Method | Description                   |
| -------- | -------------- | ----------------------------- |
| /token   | POST           | Generate and send csrf-token  |
| /mail    | POST           | Send email and validate token |
| /end     | POST           | End current php session       |

##### Don't know if /end is needed but added it anyway

### Installation

> Code located in the "server_code" directory

1. Rename index.csrv.php to index.php
2. Rename config.example.php to config.php
3. Configure allowed domains in config.php
4. Configure valid api key in config.php
5. Upload all files to your webserver

### Generate keys here

-   [Secure Password Generator](https://passwordsgenerator.net)
-   [Secure Password & Keygen Generator](https://randomkeygen.com)

## Version not using csrv

### Functionalities:

1. Security checks of api key and origin
2. Validation and sanitization of data from user
3. Error messages are sent back to client as json

### API endpoints

| Endpoint | Request Method | Description                   |
| -------- | -------------- | ----------------------------- |
| /mail    | POST           | Send email and validate token |

### Installation

> Code located in the "server_code" directory

1. Rename index.key.php to index.php
2. Rename config.example.php to config.php
3. Configure allowed domains in config.php
4. Configure valid api key in config.php
5. Upload all files to your webserver

### Generate keys here

-   [Secure Password Generator](https://passwordsgenerator.net)
-   [Secure Password & Keygen Generator](https://randomkeygen.com)
