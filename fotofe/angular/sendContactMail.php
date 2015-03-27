<?php
    $mailRecipient  = 'pfischtr@gmail.com'; //'info@foto-ch.ch';     // form data will be sent to this address
    $mailSubject    = "FotoCH - Kontaktformular";

    $dataIsValid    = true;
    $errors         = array();  	// array to hold validation errors
    $data 			= array(); 		// array to pass back data
    $errorMsg       = 'Oups, something is missing here!';

    // Process form data
	if (empty($_POST['name'])) {
		$errors['name'] = $errorMsg;
        $dataIsValid = false;
    }
    else {
        $name = $_POST['name'];
    }

	if (empty($_POST['email'])) {
		$errors['email'] = $errorMsg;
        $dataIsValid = false;
    }
    else {
        $mailHeader = 'From: ' . $_POST['email'] . "\r\n";
    }

    if (empty($_POST['message'])) {
        $errors['message'] = $errorMsg;
        $dataIsValid = false;
    }
    else {
        if (!empty($name)) {
            $mailMessage = "Nachricht von: $name\n\n" . htmlspecialchars($_POST['message']);
        }
    }

    // Send email
    if (empty($errors)) {
        if ( mail($mailRecipient, $mailSubject, $mailMessage, $mailHeader) ) {
            $data['success'] = true;
            $data['message'] = 'Thank you for your message!';
        } else {
            $data['success'] = false;
            $data['message'] = 'Oups, something went wrong!';
        }
    }
    else {
        $data['success'] = false;
        $data['errors']  = $errors;
    }

    // return all data back to the client
	echo json_encode($data);
?>