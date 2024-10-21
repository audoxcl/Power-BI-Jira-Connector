<?php

/* #########################
* This code was developed by:
* Audox Ingeniería SpA.
* website: www.audox.com
* email: info@audox.com
######################### */

function writeLog($content){
	file_put_contents("log.txt", "\n".date("Y-m-d H:i:s: ").print_r($content, true), FILE_APPEND);
}

function auth($headers) {
    $headers = array_change_key_case($headers);
    list($type, $authorization) = explode(" ", $headers["authorization"]);

    $valid_tokens = [
		'FREETOKEN',
        'JIRA_TOKEN1',
        'JIRA_TOKEN2',
        'JIRA_TOKEN3',
    ];

    return ($type === "Bearer" && in_array($authorization, $valid_tokens));
}

function get_records($object, $params) {
    $base_url = 'https://'.$params['domain'].'.atlassian.net/rest/api/2/';
    $headers = [
        'Authorization: Basic ' . base64_encode($params['email'] . ':' . $params['api_token']),
        'Content-Type: application/json',
    ];

    foreach(['domain', 'email', 'api_token'] as $param){
        $params_aux[$param] = $params[$param];
        unset($params[$param]);
    }
    
    // Build the Jira API URL for searching issues
    if($object === "users") $url = $base_url . 'users/search?' . http_build_query($params);
    elseif($object === "issues") $url = $base_url . 'search?' . http_build_query($params);
    else $url = $base_url . $object;
    $params = array_merge($params, $params_aux);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($output, true);

    // Check for errors in the Jira API response
    if (isset($result['errorMessages'])) {
        return ["error" => implode(", ", $result['errorMessages'])];
    }

    // Parse the results from the Jira response
    if (empty($result[$object])) return $result;
    else {
        $records = [];
        foreach ($result[$object] as $record) $records[] = $record;
    }

    // Handle pagination in Jira API (if there are more results)
    if ( ($result['startAt'] + count($result[$object])) < $result['total'] ) {
        $params['startAt'] = $result['startAt'] + count($result[$object]);
        $records = array_merge($records, get_records($object, $params));
    }

    return $records;
}

function main(array $args) {
    // Get headers
    $headers = isset($args['http']['headers']) ? $args['http']['headers'] : getallheaders();

    // Authorization check
    if (function_exists('auth') && !auth($headers)) {
        $error = json_encode(["error_code" => "401", "error_description" => "Unauthorized"]);
        return isset($args['http']['headers']) ? ["body" => $error] : print($error);
    }

    // Filter parameters
    $params = array_filter($args, 'is_scalar');
    $params['startAt'] = 0;

    // Extract 'action' and 'object'
    foreach (["action", "object"] as $value) {
        ${$value} = $params[$value];
        unset($params[$value]);
    }

    // Handle 'getRecords' action
    if ($action === "getRecords") {
        $result = json_encode(get_records($object, $params));
    }

    // Return or echo the result
    return isset($args['http']['headers']) ? ["body" => $result] : print($result);
}

main($_REQUEST);

?>