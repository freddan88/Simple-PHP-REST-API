# Simple PHP Email API

[Code License: MIT](https://choosealicense.com/licenses/mit/)

| My Links: |                                                               |
| --------: | ------------------------------------------------------------- |
|  WebPage: | [leemann.se/fredrik](http://www.leemann.se/fredrik)           |
|  YouTube: | [youtube.com/FreLee54](https://www.youtube.com/user/FreLee54) |
|   Donate: | [paypal.me/freddan88](https://www.paypal.me/freddan88)        |
|   GitHub: | [github.com/freddan88](https://github.com/freddan88)          |

#### Post formdata and send emails by using the built in function in PHP

### Functionalities:

1. Security checks of api key and origin
2. Will generate and send CSRF-token to client
3. Validation and sanitization of data from user
4. Error messages are sent back to client as json

### Tested with

-   [PHP](https://www.php.net)
-   [Apache](https://www.apache.org)
-   [Postman](https://www.postman.com)
-   [Axios](https://www.npmjs.com/package/axios)
-   [React](https://reactjs.org)

### API endpoints

| Endpoint | Request Method | Description                   |
| -------- | -------------- | ----------------------------- |
| /session | POST           | End current php session       |
| /token   | POST           | Generate and send csrf-token  |
| /mail    | POST           | Send email and validate token |

#### OBS! Don't know if /session is needed but added it anyway

### Installation

1. Rename app.conf.example.php to app.conf.php
2. Configure allowed domains in app.conf.php
3. Configure valid api key in app.conf.php
4. Upload all files to your webserver

### Generate keys here

-   [Secure Password Generator](https://passwordsgenerator.net)
-   [Secure Password & Keygen Generator](https://randomkeygen.com)

#### OBS! Only tested with web servers like Apache and PHP:s built in

### Example code client

```Javascript
console.log("Coming soon!")
```
