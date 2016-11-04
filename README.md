![Neostrada](https://www.neostrada.nl/asset/nx/images/logo.png) 
=================

# Neostrada WHMCS module

Easily connect WHMCS to the Neostrada API using the Neostrada WHMCS plugin and your [API credentials](https://www.neostrada.nl/mijn-account/api.html) to automatically register and manage domains.

## Installation
Follow this short step-by-step guide to install the WHMCS module.

### Step 1
Download the [Neostrada WHMCS module](https://github.com/neostrada/neostrada-whmcs/archive/master.zip). Upload the `neostrada` directory to the `/modules/registrars` directory.

### Step 2
Login to the WHMCS administration panel. Go to 'Setup > Products/Services > Domain Registrars' and look for 'Neostrada'. Click on 'Activate' and enter your API credentials.

### Step 3
Go to 'Setup > Products/Services > Domain Pricing'. Enter the extensions you want to sell to your customers and give them a price.

## WHOIS
WHMCS uses pre-defined WHOIS servers by default. If you want to use the Neostrada API to perform domain availability checks, follow the steps below.

### Step 1
Edit the file `neostrada_whois.php` and replace your API key and secret with `[your_apikey]` and `[your_apisecrey]` respectively. Upload the file to the directory where WHMCS is installed.

### Step 2
#### WHMCS 7.X or later
If you're using WHMCS 7 or later, download the file `/resources/domains/dist.whois.json` from your hosting account. Search for the extesions you want to check and remove them from the file. Then edit the file and put the code below on top, after `[`.

```
{
    "extensions": ".extension1,.extension2,.extension3",
    "uri": "http://yourdomain.extension/whmcs_directory/neostrada_whois.php?domain=",
    "available": "free"
},
```

Replace `.extension1,.extension2,.extension3` with the extensions you want to check. For example: `.nl,.be,.de,.com,.org,.net`. Save and upload the file when you're done.

#### WHMCS 6.X or earlier
If you're using WHMCS 6 or earlier, download the file `/includes/whoisservers.php` from your hosting account. Edit the file and replace the following code with the extension you want to sell:

```
.extension|http://yourdomain.extension/whmcs_directory/neostrada_whois.php?domain=|free
```

For example, if you want to check the extension `.nl`, you replace the line:

`.nl|whois.domain-registry.nl|is free`

With:

`.nl|http://yourdomain.extension/whmcs_directory/neostrada_whois.php?domain=|free`

Save and upload the file when you're done.

## License
[BSD (Berkeley Software Distribution) License](http://www.opensource.org/licenses/bsd-license.php).
Copyright (c) 2014, Avot Media BV

## Support
[www.neostrada.nl](https://www.neostrada.nl) - support@neostrada.nl
