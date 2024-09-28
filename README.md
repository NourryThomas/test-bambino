# test-bambino

Environment : 

Localhost

Requirements :

PHP 8.3
cURL activate
ssl enabled and configured

Launch (example) : 

php -S localhost:8000

Informations : 

Impossible to delete books, admin privilege required.

How to :

How to add author in cli :
- use cmd : php src/cli/add_author.php --token=TOKEN_KEY where TOKEN_KEY is valid access token
- follow instructions on cmd

How to enable cURL : 
 - In php.ini, add :
    extension=curl
    extension_dir = "ext"

How to enable ssl : 
 - download cacert.pem : https://curl.se/ca/cacert.pem
 - move to {your-path-to-php}/extras/ssl
 - In php.ini, add curl.cainfo and openssl.cafile with the path to cacert.pem
