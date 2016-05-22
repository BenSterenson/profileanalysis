<?php
include_once("Attributes.php");

define("DEFAULT_API_KEY", 'd45fd466-51e2-4701-8da8-04351c872236');
define("DEFAULT_API_SECRET", '171e8465-f548-401d-b63b-caf0dc28df5f');
define("DEFAULT_API_URL",'http://www.betafaceapi.com/service.svc');
define("DEFAULT_POLL_INTERVAL",1);

define("D_PROXY_IP",'31.168.236.236');
define("D_PROXY_PORT",'8080');


class betaFaceApi
{
    var $api_key;
    var $api_secret;
    var $api_url;
    var $poll_interval;
    var $image_Attributes;
    var $log_level = 1;
    var $countConnect = 0;
    var $proxy_use = 0;
    var $proxy_port;
    var $proxy_ip;


    function _betaFaceApi($api_key,$api_secret,$api_url,$poll_interval,$PhotoId)
    {
        $this->api_key = $api_key;
        $this->api_secret= $api_secret;
        $this->api_url = $api_url;
        $this->poll_interval = $poll_interval;
        $this->image_Attributes = new Attributes($PhotoId);
       
    }
    
    function betaFaceApi($PhotoId)
    {
        $this->api_key = DEFAULT_API_KEY;
        $this->api_secret= DEFAULT_API_SECRET;
        $this->api_url = DEFAULT_API_URL;
        $this->poll_interval = DEFAULT_POLL_INTERVAL; 
        $this->image_Attributes = new Attributes($PhotoId);
        $this->proxy_port = D_PROXY_PORT;
        $this->proxy_ip = D_PROXY_IP;
        return true;
    }
	
	function convertTextToBool($value){
		if(!(strcmp($value,"no"))){
			return 0;
		}
		return 1;
	}

    /**
     * Get face info from BetaFace API by face_uid
     * @param type $face_uid
     * @return type
     */
    function get_face_info($face_uid)
    { 
        $result = $this->api_call('GetFaceImage', array('face_uid' => $face_uid));
        while(!$result['ready'])
        {
            sleep($this->poll_interval);
            $result = $this->api_call('GetFaceImage', array('face_uid' => $face_uid));
        }
        return $result;
    }

    /**
     * Uploads an image to BetaFace API, waits for it to be processed 
     * by polling each poll_interval seconds, and then assigns a person_id
     * (alpha-numberic + '.') to that image.
     * @param type $url
     * @param type $person_id
     * @return boolean
     */
    function get_Image_attributes($url,$proxyUSE)
    {
        $this->proxy_use = $proxyUSE;
        // Step 1: upload image to service and get image ID
        $image__url = $url;
        $params = array("img_url" => $image__url,"original_filename" => $image__url);
        $result = $this->api_call('UploadNewImage_Url', $params);


        if(!$result)
        {
            $this->logger("API call to upload image failed!");
            return false;
        } 
     
        // Step 2: keep polling the GetImageInfo endpoint until the processing of the uploaded image is ready.

        $img_uid = $result['img_uid'];
        $result = $this->api_call('GetImageInfo', array('image_uid' => $img_uid));
        if($result == -1){
            return -1;
        }
        while(!$result['ready'])
        {
            if($this->countConnect == 60)
                return -1;
            sleep($this->poll_interval);
            $result = $this->api_call('GetImageInfo', array('image_uid' => $img_uid));
            $this->countConnect++;
        }
       
        if($result['face_uid'])
            $face_uids = $result['face_uid'];
        
        
        //this gets the face attributes

        $result = $this->api_call('GetFaceImage', array('face_uid' => $face_uids));
        while(!$result['ready'])
        {
            sleep($this->poll_interval);
            $result = $this->api_call('GetFaceImage', array('face_uid' => $face_uids));
        }
   
       
        return $result;
    }
    
        
    function recognize_faces($url, $namespace)
    {
         // Step 1: upload image url
        $image__url = $url;
        $params = array("image_url" => $image__url,"original_filename" => $url);
        $result = $this->api_call('UploadNewImage_Url', $params);
        if(!$result)
        {
            $this->logger("API call to upload image failed!");
            return false;
        } 
        
        // Step 2: keep polling the GetImageInfo endpoint until the processing of the uploaded image is ready.
        $img_uid = $result['img_uid'];
        $result = $this->api_call('GetImageInfo', array('image_uid' => $img_uid));
        
        while(!$result['ready'])
        {
            sleep($this->poll_interval);
            $result = $this->api_call('GetImageInfo', array('image_uid' => $img_uid));
        }
        
        if($result['face_uid'])
            $face_uid = $result['face_uid'];
        
        // Step 3: Start a face recognition job
        $params = array('face_uid' => $face_uid, 'namespace' => 'all@'.$namespace);        
        $result = $this->api_call('Faces_Recognize', $params);
        
        // Step 4: Wait for the recognition job to finish
        $params = array('recognize_job_id' => $result['recognize_job_id']);
        $result = $this->api_call('GetRecognizeResult', $params);   
        while(!$result['ready'])
        {
            sleep($this->poll_interval);
            $result = $this->api_call('GetRecognizeResult', $params);
        }                
        
        return $result['matches'];
    }
    
    /**
     * Make an API call to a given endpoint, with given params.
     * This will actually fetch the template from request_templates/endpoint,
     * render it replacing params into XML, POST the
     * data with headers: content_type = application/xml to the BetaFace API,
     * fetch the response and possibly parse it if there is a function
     * available.

     * Returns a dictionary of parsed stuff from the response, or false
     * if the request failed.
     * @param type $endpoint
     * @param type $params
     * @return boolean
     */
    function api_call($endpoint,$params)
    {
        $api_call_params = array_merge(array('api_key'=>$this->api_key,'api_secret'=>$this->api_secret),$params);
        
        $template_name = getcwd()."/request_templates/$endpoint.xml";
        $request_data = $this->render_template($template_name, $api_call_params);
        $url = $this->api_url . '/' . $endpoint;
        $this->logger("Making HTTP request to $url");
        $headers[] = "Content-Type: application/xml";
        

        //ob_start();
        //open curl connection 
        $ch = curl_init();

        //set the url, POST vars, POST data and headers
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$request_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        //curl_setopt($ch, CURLOPT_FRESH_CONNECT , 1);
        //curl_setopt($ch, CURLOPT_FORBID_REUSE , 1);
        //curl_setopt($ch, CURLOPT_COOKIESESSION , 1);

        if($this->proxy_use == 1){
            echo "using proxy";
            curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxy_port);
            curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy_ip);
        }

        $result = curl_exec($ch);
        //ob_end_clean();

        if(!$result)
            $this->logger("Response empty from API");
        
        curl_close($ch);
        
        // Check if parser method exists, and call it with the response from API 
        if (method_exists($this, 'parse_'.$endpoint))
        {
            $this->logger("Using custom response parser for endpoint $endpoint");
            try
            {
                $parsed_result = $this->{'parse_'.$endpoint}($result);
            } catch (Exception $e)
            {
                $this->logger("Error while parsing response: $e");
                return false;
            }
        }
        else
        {
            $this->logger("Custom parsing failed for endpoint $endpoint");
            return false;
        }
     
        return $parsed_result;        
    }
    
    /**
     * Log for debugging
     * @param type $text
     * @param type $level
     */
    function logger($text,$level=0)
    {
        if($this->log_level>$level)
            echo $text."<BR>";
    }

    
    
    
    function render_template($template_file,$context)
    {
        $xml_model = file_get_contents($template_file);
        foreach($context as $param => $value)
        {
            $xml_model = str_replace("{{".$param."}}", $value, $xml_model);
        } 
        return $xml_model;
    }
    
    /**
     * Parse the response from API for UploadNewImage_Url method call.
     * @param type $response
     * @return boolean
     */
    function parse_UploadNewImage_Url($response)
    {
        $response_xml = simplexml_load_string($response);
        
        $img_uid = $response_xml->xpath('.//img_uid');
        if (count($img_uid) == 0)
            return false;
        $result['img_uid'] = $img_uid[0];
        
        $ready = $response_xml->xpath(".//int_response");
    
        if (count($ready) == 0)
            return false;
        
        $result['ready'] = (trim($ready[0]) == '0');
        return $result;
    }
    
    /**
     * Parse the response from API for GetImageInfo method call.
     * @param type $response
     * @return boolean
     */
    function parse_GetImageInfo($response)
    {
        $response_xml = simplexml_load_string($response);
       
        $ready = $response_xml->xpath(".//int_response");
        if (count($ready) == 0)
            return false;
        
        $result['ready'] = (trim($ready[0]) == '0');

        # If not ready yet, stop parsing at 'ready'
        if (!$result['ready'])
            return $result;

        # Otherwise, see if we have faces
        $face_uids = $response_xml->xpath(".//faces/FaceInfo/uid");
        if (count($face_uids) == 0)
        {
            $this->logger("No faces found in image!");
            return -1;
        }
        $result['face_uid'] = trim($face_uids[0]);
        return $result;
    }

    /**
     * Parse the response from API for GetFaceImage method call.
     * @param type $response
     * @return boolean
     */
    function parse_GetFaceImage($response)
    {
        $response_xml = simplexml_load_string($response);
        
        
        $tags = $response_xml->xpath(".//face_info/tags/TagInfo");
        
        $UpdateDate = date("Y-m-d:H:i:s");
       
        $this->image_Attributes->setUpdateDate($UpdateDate);
        $this->image_Attributes->setUpdatedByUser(0);


        for($x=0; $x < count($tags); $x++){
            $name = trim($tags[$x]->name);
        
            $value = trim($tags[$x]->value);
            
            switch($name){
                case "age":
                    $this->image_Attributes->setAge($value);
                    break;
                case "gender":
					if (strcmp(strtolower($value), "male") == 0) {
						$this->image_Attributes->setGender(1);
					}
					else {
						$this->image_Attributes->setGender(0);
					}
                    break;
                case "color hair":
                    $this->image_Attributes->setHairColor($value);
                    break;
                case "color eyes":
                    $this->image_Attributes->setEyeColor($value);
                    break;
                case "beard":
                    $this->image_Attributes->setHasBeard($this->convertTextToBool($value));
                    break;
                case "glasses":
                        $this->image_Attributes->setHasGlasses($this->convertTextToBool($value));
                    break;
                case "expression":
					if (strcmp(strtolower($value), "smile") == 0) {
						$this->image_Attributes->setHasSmile(1);
					}
					else {
						$this->image_Attributes->setHasSmile(0);
					}
                    break;
            }
         
        }
        
        $ready = $response_xml->xpath(".//int_response");
       
        if (count($ready) == 0)
            return false;
        
        $result['ready'] = (trim($ready[0]) == '0');
        
        // If not ready yet, stop parsing at 'ready'
        if (!$result['ready'])
            return $result;

        // Otherwise, see if we have face info
        $face_info = $response_xml->xpath(".//face_info");
        if (count($face_info) == 0)
        {
            $this->logger("No face info found!");
            return $result;
        }
        $result['face_info'] = $face_info[0];
        return $result;
    }    
    
    /**
     * Parse the response from API for Faces_Regognize method call. (GetRecognizeResult)
     * @param type $response
     * @return boolean
     */
    function parse_Faces_Recognize($response)
    {
        $response_xml = simplexml_load_string($response);
        $recognize_job_id = $response_xml->xpath(".//recognize_uid");
        if (count($recognize_job_id) == 0)
            return false;

        $result['recognize_job_id'] = trim($recognize_job_id[0]);        
        return $result;
    } 
       
    /**
     * Parse the response from API for GetRecognizeResult method call.
     * @param type $response
     * @return boolean
     */
    function parse_GetRecognizeResult($response)
    {
        $response_xml = simplexml_load_string($response);
        $ready = $response_xml->xpath(".//int_response");
     
        if (count($ready) == 0)
            return false;
        
        $result['ready'] = (trim($ready[0]) == '0');

        // If not ready yet, stop parsing at 'ready'
        if (!$result['ready'])
            return $result;
        
        $matching_persons = $response_xml->xpath(".//faces_matches/FaceRecognizeInfo/matches/PersonMatchInfo");
        if (count($matching_persons) == 0)
        {   
            $this->logger("No matching persons found for image!");
            return false;
        }
        foreach($matching_persons as $matching_person)
        {
            $person_name = trim($matching_person->person_name);
            $confidence = trim($matching_person->confidence);
            $result["matches"][$person_name] = $confidence;
        }
        return $result;
    }
}
?>
