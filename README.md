# plesk-dns-client
The extension allows you to manage the DNS records of your domains in Plesk.
[further description](DESCRIPTION.md)

## Requirements
- Plesk Interface Version 18.0.48 or greater
- PHP 8.2 or greater
- ResellerInterface API-Module
- ResellerInterface API-User

## Installation (2 methods)

### Method #1 - CLI
- download the [extensions zip](https://github.com/resellerinterface/plesk-dns-client/releases/latest) file
- upload the extensions zip file to your server
- execute the following command `plesk bin extension -i <PATH-TO-UPLOADED-EXTENSION-ZIP>`
- go to your plesk interface and configure the extension under "my extensions"

### Method #2 - Plesk Interface
- download the [extensions zip](https://github.com/resellerinterface/plesk-dns-client/releases/latest) file
- go to your plesk interface and open the extensions page
- on the "my extensions" tab click on the "extension upload" button
- upload the extension zip file
- configure the extension

You see no upload button? Then add the following code to your [panel.ini](https://docs.plesk.com/en-US/obsidian/administrator-guide/plesk-administration/panelini-configuration-file.78509/)
```
[ext-catalog]
extensionUpload = true
```

## Changelog
[see CHANGES.md](CHANGES.md)