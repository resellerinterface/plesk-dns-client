# plesk-dns-client

## Requirements
- Plesk Interface Version 18 or greater
- PHP 8.2
- ResellerInterface API-Module
- ResellerInterface API-User

## Installation (2 methods)

### Method #1 - CLI
- download the extensions zip file
- upload the extensions zip file to your server
- execute the following command `plesk bin extension -i <PATH-TO-UPLOADED-EXTENSION-ZIP>`
- go to your plesk interface and configure the extension under "my extensions"

### Method #2 - Plesk Interface
- download the extensions zip file
- go to your plesk interface and open the extensions page
- on the "my extensions" tab click on the "extension upload" button
- upload the extension zip file
- configure the extension

You see no upload button? Then add the following code to your `panel.ini`
```
[ext-catalog]
extensionUpload = true
```