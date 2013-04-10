<?php

    // Input your info here:
	$name = "Ergin Dervisoglu";
	$email = 'ergin.dervisoglu@docusign.com';
	$integratorKey = '...';
	$password = '...';

	// user credentials
	include_once("creds.php");
	
	// credentials stored in helper file
	SetIntegratorKeyAndPassword();
	
	// Note: Login to the Console and create a template first, then copy its template Id into the following string.  Make sure
	// your template has at least one template role defined, which you will reference below in the body of your request.
	$templateId = "55A80182-2E9F-435D-9B16-FD1E1C0F9D74";
	
	// construct the authentication header:
	$header = "<DocuSignCredentials><Username>" . $email . "</Username><Password>" . $password . "</Password><IntegratorKey>" . $integratorKey . "</IntegratorKey></DocuSignCredentials>";
	
	/////////////////////////////////////////////////////////////////////////////////////////////////
	// STEP 1 - Login (to retrieve baseUrl and accountId)
	/////////////////////////////////////////////////////////////////////////////////////////////////
	$url = "https://demo.docusign.net/restapi/v2/login_information";
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-DocuSign-Authentication: $header"));
	
	$json_response = curl_exec($curl);
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	
	if ( $status != 200 ) {
		echo "error calling webservice, status is:" . $status;
		exit(-1);
	}
	
	$response = json_decode($json_response, true);
	$accountId = $response["loginAccounts"][0]["accountId"];
	$baseUrl = $response["loginAccounts"][0]["baseUrl"];
	curl_close($curl);
	
	// --- display results
	echo "\naccountId = " . $accountId . "\nbaseUrl = " . $baseUrl . "\n";
	
	/////////////////////////////////////////////////////////////////////////////////////////////////
	// STEP 2 - Create an envelope using composite templates
	/////////////////////////////////////////////////////////////////////////////////////////////////                                                                   
	
	$data_string = file_get_contents("example3.json");  
	
	echo "\n\n$data_string\n\n";
	
	$curl = curl_init($baseUrl . "/envelopes" );
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
		'Content-Type: application/json',
		'Content-Length: ' . strlen($data_string),
		"X-DocuSign-Authentication: $header" )                                                                       
	);
	
	$json_response = curl_exec($curl);
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	if ( $status != 201 ) {
		echo "error calling webservice, status is:" . $status . "\nerror text is --> ";
		print_r($json_response); echo "\n";
		exit(-1);
	}
	
	$response = json_decode($json_response, true);
	$envelopeId = $response["envelopeId"];
	
	// --- display results
	echo "Document is sent! Envelope ID = " . $envelopeId . "\n\n"; 
?>