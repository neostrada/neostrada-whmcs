![Neostrada](https://www.neostrada.nl/asset/nx/images/logo.png) 
=================

# Neostrada WHMCS plugin #

Easily connect WHMCS to the Neostrada API using the Neostrada WHMCS plugin and your [API credentials](https://www.neostrada.nl/mijn-account/api.html) to automatically register and manage domainnames.
If you are using the new updated version of WHMCS you will need to upload the directory neostrada_V2.0 into modules/registrars of your WHMCS. You do not need to download the neostrada directory viewed above.

## Installation ##
Download the [Neostrada WHMCS plugin](https://github.com/neostrada/neostrada-whmcs/archive/master.zip) or checkout the repository and include the plugin in your WHMCS and follow the setup instructions.

## Whois ##
WHMCS uses pre-defined whois servers by default. This means that when you install the plugin, the whois will not automatically switch to the plugin whois method.

To use the plugin whois method, please copy neostrada_whois.php to your public folder and configure it to use your [API credentials](https://www.neostrada.nl/mijn-account/api.html).
Then edit [includes/whoisservers.php](http://docs.whmcs.com/Domains_Configuration#Adding_Additional_WHOIS_Services) and change the extensions you wish to whois via the plugin.

The required format is: .extension|/neostrada_whois.php?domain=|HTTPREQUEST-free

## License ##
[BSD (Berkeley Software Distribution) License](http://www.opensource.org/licenses/bsd-license.php).
Copyright (c) 2014, Avot Media BV

## Support ##
[www.neostrada.nl](https://www.neostrada.nl) - support@neostrada.nl