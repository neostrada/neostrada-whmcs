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
define('API_KEY', '[your_apikey]');
define('API_SECRET', '[your_apisecret]');
/**
 * Neostrada :: whois
 */
function neostrada_whois($Domain)
{
	$RV = FALSE;
	list ($DomainName, $Extension) = explode('.', $Domain, 2);
	if (($Result = neostrada_api('whois', array(
		'domain'	=> $DomainName,
		'extension'	=> $Extension
	))) !== FALSE) {
		$RV = ((int)$Result['code'] === 210 ? TRUE : FALSE);
	}
	return $RV;
}
/**
 * Neostrada :: api
 */
function neostrada_api($Action, array $Parameters = array())
{
	$RV = FALSE;
	if (($cURL = curl_init()) !== FALSE) {
		curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($cURL, CURLOPT_SSLVERSION, 1);
        curl_setopt($cURL, CURLOPT_URL, API_HOST.'?api_key='.API_KEY.'&action='.$Action.'&'.http_build_query($Parameters).'&api_sig='.neostrada_apisignature($Action, $Parameters).'&referer=WHMCS');
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
function neostrada_apisignature($Action, array $Parameters = array())
{
	$APISig = API_SECRET.API_KEY.'action'.$Action;
	foreach ($Parameters AS $Key => $Value) $APISig.= $Key.$Value;
	return md5($APISig);
}
/**
 * Execute whois
 */
if (array_key_exists('domain', $_GET) && strlen($_GET['domain']) > 0 && $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
	echo (neostrada_whois(filter_var($_GET['domain'], FILTER_SANITIZE_URL)) ? 'free' : 'registered');
}
exit;
?>