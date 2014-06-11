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
 * Neostrada :: getConfigArray
 */
function neostrada_getConfigArray()
{
	return array(
		'Username'			=> array(
			'Type'			=> 'text',
			'Size'			=> '20',
			'Description'	=> 'Enter your Neostrada API key'
		),
		'Password'			=> array(
			'Type'			=> 'text',
			'Size'			=> '40',
			'Description'	=> 'Enter your Neostrada API secret'
		)
	);
}
/**
 * Neostrada :: GetNameservers
 */
function neostrada_GetNameservers($params)
{
	$RV = array();
	if (($Result = neostrada_api($params['Username'], $params['Password'], 'getnameserver', array(
		'domain'	=> $params['sld'],
		'extension'	=> $params['tld']
	))) !== FALSE) {
		if ((int)$Result['code'] === 200 && array_key_exists('nameservers', $Result) && is_array($Result['nameservers'])) {
			if (array_key_exists(0, $Result['nameservers'])) $RV['ns1'] = $Result['nameservers'][0];
			if (array_key_exists(1, $Result['nameservers'])) $RV['ns2'] = $Result['nameservers'][1];
			if (array_key_exists(2, $Result['nameservers'])) $RV['ns3'] = $Result['nameservers'][2];
		} else {
			$RV['error'] = '[NEOSTRADA] Could not get nameservers for domain';
		}
	} else {
		$RV['error'] = '[NEOSTRADA] Could not connect to server or could not parse result, try again later';
	}
	return $RV;
}
/**
 * Neostrada :: SaveNameservers
 */
function neostrada_SaveNameservers($params)
{
	$RV = array();
	if (($Result = neostrada_api($params['Username'], $params['Password'], 'nameserver', array(
		'domain'	=> $params['sld'],
		'extension'	=> $params['tld'],
		'ns1'		=> $params['ns1'],
		'ns2'		=> $params['ns2'],
		'ns3'		=> $params['ns3']
	))) !== FALSE) {
		if ((int)$Result['code'] !== 200) {
			$RV['error'] = '[NEOSTRADA] Could not save nameservers for domain';
		}
	} else {
		$RV['error'] = '[NEOSTRADA] Could not connect to server or could not parse result, try again later';
	}
	return $RV;
}
/**
 * Neostrada :: SaveRegistrarLock
 */
function neostrada_SaveRegistrarLock($params)
{
	$RV = array();
	if (($Result = neostrada_api($params['Username'], $params['Password'], 'lock', array(
		'domain'	=> $params['sld'],
		'extension'	=> $params['tld'],
		'lock'		=> ($params['lockenabled'] ? 1 : 0)
	))) !== FALSE) {
		if ((int)$Result['code'] !== 200) {
			$RV['error'] = '[NEOSTRADA] Domain locking not supported for this extension';
		}
	} else {
		$RV['error'] = '[NEOSTRADA] Could not connect to server or could not parse result, try again later';
	}
	return $RV;
}
/**
 * Neostrada :: RegisterDomain
 */
function neostrada_RegisterDomain($params)
{
	$RV = array();
	if (($HolderResult = neostrada_api($params['Username'], $params['Password'], 'holder', array(
		'holderid'		=> 0,
		'sex'			=> 'M',
		'firstname'		=> $params['firstname'],
		'center'		=> '',
		'lastname'		=> $params['lastname'],
		'street'		=> $params['address1'],
		'housenumber'	=> ' ',
		'hnpostfix'		=> '',
		'zipcode'		=> $params['postcode'],
		'city'			=> $params['city'],
		'country'		=> strtolower($params['country']),
		'email'			=> $params['email']
	))) !== FALSE) {
		if ((int)$HolderResult['code'] === 200) {
			if (($Result = neostrada_api($params['Username'], $params['Password'], 'register2', array(
				'domain'	=> $params['sld'],
				'extension'	=> $params['tld'],
				'holderid'	=> (int)$HolderResult['holderid'],
				'period'	=> 1,
				'webip'		=> '',
				'packageid'	=> 0,
				'ns1'		=> $params['ns1'],
				'ns2'		=> $params['ns2'],
				'ns3'		=> $params['ns3']
			))) !== FALSE) {
				if ((int)$Result['code'] !== 200) {
					$RV['error'] = '[NEOSTRADA] Could not register domain';
				}
			} else {
				$RV['error'] = '[NEOSTRADA] Could not connect to server or could not parse result, try again later';
			}
		} else {
			$RV['error'] = '[NEOSTRADA] Could not create contact';
		}
	} else {
		$RV['error'] = '[NEOSTRADA] Could not connect to server or could not parse result, try again later';
	}
	return $RV;
}
/**
 * Neostrada :: TransferDomain
 */
function neostrada_TransferDomain($params)
{
	$RV = array();
	if (($HolderResult = neostrada_api($params['Username'], $params['Password'], 'holder', array(
		'holderid'		=> 0,
		'sex'			=> 'M',
		'firstname'		=> $params['firstname'],
		'center'		=> '',
		'lastname'		=> $params['lastname'],
		'street'		=> $params['address1'],
		'housenumber'	=> ' ',
		'hnpostfix'		=> '',
		'zipcode'		=> $params['postcode'],
		'city'			=> $params['city'],
		'country'		=> strtolower($params['country']),
		'email'			=> $params['email']
	))) !== FALSE) {
		if (($Result = neostrada_api($params['Username'], $params['Password'], 'transfer2', array(
			'domain'	=> $params['sld'],
			'extension'	=> $params['tld'],
			'authcode'	=> $params['transfersecret'],
			'holderid'	=> (int)$HolderResult['holderid'],
			'webip'		=> '',
			'ns1'		=> $params['ns1'],
			'ns2'		=> $params['ns2'],
			'ns3'		=> $params['ns3']
		))) !== FALSE) {
			if ((int)$Result['code'] !== 200) {
				$RV['error'] = '[NEOSTRADA] Could not transfer domain';
			}
			if ((int)$Result['code'] === 504) $RV['error'] = '[NEOSTRADA] Auth token missing';
		} else {
			$RV['error'] = '[NEOSTRADA] Could not connect to server or could not parse result, try again later';
		}
	} else {
		$RV['error'] = '[NEOSTRADA] Could not connect to server or could not parse result, try again later';
	}
	return $RV;
}
/**
 * Neostrada :: SaveContactDetails
 */
function neostrada_SaveContactDetails($params)
{
	$RV = array();
	if (($HolderResult = neostrada_api($params['Username'], $params['Password'], 'holder', array(
		'holderid'		=> 0,
		'sex'			=> 'M',
		'firstname'		=> $params["contactdetails"]["Registrant"]["First Name"],
		'center'		=> '',
		'lastname'		=> $params["contactdetails"]["Registrant"]["Last Name"],
		'street'		=> $params["contactdetails"]["Registrant"]["Address 1"],
		'housenumber'	=> ' ',
		'hnpostfix'		=> '',
		'zipcode'		=> $params["contactdetails"]["Registrant"]["ZIP Code"],
		'city'			=> $params["contactdetails"]["Registrant"]["City"],
		'country'		=> strtolower($params["contactdetails"]["Registrant"]["Country"]),
		'email'			=> $params["contactdetails"]["Registrant"]["Email Address"]
	))) !== FALSE) {
		if ((int)$HolderResult['code'] === 200) {
			if (($Result = neostrada_api($params['Username'], $params['Password'], 'modify', array(
				'domain'	=> $params['sld'],
				'extension'	=> $params['tld'],
				'holderid'	=> (int)$HolderResult['holderid']
			))) !== FALSE) {
				if ((int)$Result['code'] !== 200) {
					$RV['error'] = '[NEOSTRADA] Could not modify domain';
				}
			} else {
				$RV['error'] = '[NEOSTRADA] Could not connect to server or could not parse result, try again later';
			}
		} else {
			$RV['error'] = '[NEOSTRADA] Could not create contact';
		}
	} else {
		$RV['error'] = '[NEOSTRADA] Could not connect to server or could not parse result, try again later';
	}
	return $RV;
}
/**
 * Neostrada :: GetEPPCode
 */
function neostrada_GetEPPCode($params)
{
	$RV = array();
	if (($Result = neostrada_api($params['Username'], $params['Password'], 'gettoken', array(
		'domain'	=> $params['sld'],
		'extension'	=> $params['tld']
	))) !== FALSE) {
		if ((int)$Result['code'] === 200) {
			if (strlen($Result['token']) > 0) $RV['eppcode'] = $Result['token'];
		} else {
			$RV['error'] = '[NEOSTRADA] Domain auth token not set or not supported';
		}
	} else {
		$RV['error'] = '[NEOSTRADA] Could not connect to server or could not parse result, try again later';
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
?>