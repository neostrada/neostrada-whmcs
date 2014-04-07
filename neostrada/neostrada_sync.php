<?php
/**
 * Copyright (c) 2014, Avot Media BV
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR AND CONTRIBUTORS ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
 * DAMAGE.
 *
 * @license     Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @author      Avot Media BV <api@neostrada.nl>
 * @copyright   Avot Media BV
 * @link        http://www.avot.nl / http://www.neostrada.nl
 */
define('API_HOST', 'https://api.neostrada.nl/');
/**
 * Include required WHMCS files
 */
require dirname(__FILE__).'/../../../dbconnect.php';
require ROOTDIR.'/includes/functions.php';
require ROOTDIR.'/includes/registrarfunctions.php';
/**
 * Get registrar config
 */
$aConfig = getregistrarconfigoptions('neostrada');
/**
 * Neostrada :: GetExpirationDate
 */
function neostrada_GetExpirationDate($Username, $Password, $Domain, $Extension)
{
	$RV = FALSE;
	if (($Result = neostrada_api($Username, $Password, 'getexpirationdate', array(
		'domain'	=> $Domain,
		'extension'	=> $Extension
	))) !== FALSE) {
		if ((int)$Result['code'] === 200) {
			if (strlen($Result['expirationdate']) > 0) $RV = $Result['expirationdate'];
		}
	}
	return $RV;
}
/**
 * Neostrada :: api
 */
function neostrada_api($Username, $Password, $Action, array $Parameters = array())
{
	$RV = FALSE;
	if (($cURL = curl_init()) !== FALSE) {
		curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($cURL, CURLOPT_URL, API_HOST.'?api_key='.$Username.'&action='.$Action.'&'.http_build_query($Parameters).'&api_sig='.neostrada_apisignature($Username, $Password, $Action, $Parameters).'&referer=WHMCS');
		curl_setopt($cURL, CURLOPT_HEADER, 0);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
		if (($Data = curl_exec($cURL)) !== FALSE) {
			if (($XML = @simplexml_load_string($Data)) !== FALSE) {
				$RV = array();
				foreach ($XML->attributes() AS $AV) $RV[strtolower($AV->getName())] = trim((string)$AV);
				foreach ($XML->children() AS $CV) {
					if (count($CV->children()) > 0) {
						foreach ($CV->children() AS $CCV) $RV[strtolower($CV->getName())][] = trim((string)$CCV);
					} else {
						$RV[strtolower($CV->getName())] = trim((string)$CV);
					}
				}
			}
		}
		curl_close($cURL);
	}
	return $RV;
}
/**
 * Neostrada :: apisignature
 */
function neostrada_apisignature($Username, $Password, $Action, array $Parameters = array())
{
	$APISig = $Password.$Username.'action'.$Action;
	foreach ($Parameters AS $Key => $Value) $APISig.= $Key.$Value;
	return md5($APISig);
}
/**
 * Retrieve Neostrada domains from database
 */
$Domains = array();
if (($Query = mysql_query('SELECT domain FROM tbldomains WHERE registrar = "neostrada"')) !== FALSE) {
	while ($Row = mysql_fetch_assoc($Query)) $Domains[] = trim(strtolower($Row['domain']));
}
if (count($Domains) > 0) {
	foreach ($Domains as $Domain) {
		list ($DomainName, $Extension) = explode ('.', $Domain, 2);
		if (($ExpirationDate = neostrada_GetExpirationDate($aConfig['Username'], $aConfig['Password'], $DomainName, $Extension)) !== FALSE) {
			mysql_query('UPDATE tbldomains SET expirydate = "'.mysql_real_escape_string($ExpirationDate).'", nextduedate = "'.mysql_real_escape_string($ExpirationDate).'" WHERE domain = "'.mysql_real_escape_string($Domain).'" LIMIT 1');
		}
		usleep(51);
	}
}
?>