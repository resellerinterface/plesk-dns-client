<?php
$messages = [
	'pageTitle'      => "ResellerInterface - DNS",
	'pageTitleIndex' => "API-Access",

	'titleApiCredentials' => "API-Credentials",
	'descApiCredentials'  => "To synchronize the DNS to the ResellerInterface-Nameservers, you need an API user with DNS rights.",
	'descApiModules'      => "Required module: API, user roles incl. rights management (optional)",
	'descApiModulesHint'  => "ResellerInterface » Management » Modules",
	'descApiRights'       => "Required Rights: manage zone header (api.dns.manageSOA), manange zone records (api.dns.manageRecords)",
	'descApiRightsHint'   => "ResellerInterface » Management » Rights",

	'formLabelApiResellerID'  => "Customer number",
	'formLabelApiUsername'    => "Username",
	'formLabelApiPassword'    => "Password",
	'formLabelApiBackupZone'  => "DNS Backup",
	'formLabelApiStagingMode' => "Staging-Mode",
	'formLabelApiSubmit'      => "Test & save",

	'formDescApiResellerID'  => "K12345",
	'formDescApiBackupZone'  => "Create a backup of the DNS before synchronization",
	'formDescApiStagingMode' => "Send API calls to the staging system",

	'apiResponse' => "API-Response",
];
