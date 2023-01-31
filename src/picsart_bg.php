<?php

// Define the API key
define('PA_API_KEY', 'Your API Key');

/**
 * Removes the background from an image using the Picsart API.
 *
 * @param string $input The input image as a URL or binary data.
 * @param string $input_type The type of input: 'url' or 'binary'.
 * @return array|WP_Error The API response or a WP_Error object on failure.
 */
function pa_bg($input, $input_type = 'url') {
  // Set the API endpoint URL
  $endpoint = 'https://api.picsart.io/tools/1.0/removebg';

  // Set the cURL options
  $options = array(
    CURLOPT_URL => $endpoint,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => array(
      'accept: application/json',
      'x-picsart-api-key: ' . PA_API_KEY,
    ),
  );

  // Check if the input is a URL or binary data
  if ($input_type === 'url') {
    $options[CURLOPT_POSTFIELDS] = array(
      'output_type' => 'cutout',
      'format' => 'PNG',
      'image_url' => $input,
    );
  } else {

    // Create a temporary file and write the binary data to it
     $temp_file = tmpfile();
    fwrite($temp_file, $input);

    // Create a CURLFile object from the temporary file
    $curl_file = curl_file_create(stream_get_meta_data($temp_file)['uri']);

    $options[CURLOPT_POSTFIELDS] = array(
      'output_type' => 'cutout',
      'format' => 'PNG',
      'image' => $curl_file,
    );
    $options[CURLOPT_HTTPHEADER][] = 'Content-Type: multipart/form-data';
  }

  // Initialize the cURL session
  $ch = curl_init();
  curl_setopt_array($ch, $options);

  // Execute the cURL request
  $response = curl_exec($ch);

  // Check for cURL errors
  if (curl_errno($ch)) {
    // Close the cURL session
    curl_close($ch);

    // Return a WP_Error object with the error message
    return new WP_Error('pa_remove_background_curl_error', curl_error($ch));
  }

  // Close the cURL session
  curl_close($ch);

  // Decode the API response
  $response_data = json_decode($response);

  // Return the output image URL
  return $response_data->data->url;
}
