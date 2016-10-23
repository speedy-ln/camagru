<?php

/**
 * Created by IntelliJ IDEA.
 * User: Lebelo Nkadimeng
 * Date: 2016/10/22
 * Time: 2:45 PM
 */
/**
 * Description of APIClient
 *
 * @author Lebelo Nkadimeng
 */
require_once 'settings.php';
class CurlClient
{
    private $apiUrl = API_URL;
    private $postData;
    private $response;
    private $header;
    private $http_code;
    public $post = 1;

    /**
     * Create an instance of the APIClient.
     * @param array|bool $postData Example: array("action"=>"foo", "api_key"=>"abc123")
     * @param bool|string $url URL of the API to call. Not mandatory, but if set, API
     *  URL will be replaced with this one
     */
    function __construct($url = API_URL, $postData = FALSE) {

        $this->apiUrl = $url;
        if ($postData !== FALSE)
            $this->postData = $postData;
    }

    /**
     * @return mixed
     */
    public function getHeader(){
        return $this->header;
    }

    /**
     * @return mixed
     */
    public function getHttpCode(){
        return $this->http_code;
    }

    public function getApiUrl() {
        return $this->apiUrl;
    }

    public function setApiUrl($apiUrl) {
        $this->apiUrl = $apiUrl;
    }

    public function getResponse() {
        return $this->response;
    }

    public function setResponse($response) {
        $this->response = $response;
    }

    public function getPostData() {
        return $this->postData;
    }

    public function setPostData($postData) {
        $this->postData = $postData;
    }

    public function executePost() {
        $curl = curl_init($this->apiUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->postData);
        if($this->post == 0){
            curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
        } else {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        }
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);//return header and body

        $curl_response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($curl_response, 0, $header_size);
        $body = substr($curl_response, $header_size);
        $this->http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->setResponse($curl_response);
        $this->header = $header;

        return $body;
    }

    public function send_request($method){
        $method = strtolower($method);
        switch ($method){
            case "post":
                if (!empty($this->postData)) {
                    $this->postData['api_key'] = "46000d8feb6807d361e82aa2ff1e3c43";
                    $postfields = http_build_query($this->getPostData());
                    $context =
                        array("http"=>
                            array(
                                "method" => "POST",
                                "header" => "Expect: \r\n",
                                "content" => $postfields));
                }
                break;
            case "get":
                if (!empty($this->postData)) {
                    $this->postData['api_key'] = "46000d8feb6807d361e82aa2ff1e3c43";
                    $postfields = http_build_query($this->getPostData());
                    $context =
                        array("http"=>
                            array(
                                "method" => "get",
                                "header" => "Expect: \r\n",
                                "content" => $postfields));

                }
                break;
        }
        $context = stream_context_create($context);
        $response = file_get_contents($this->getApiUrl()    , false, $context);
        return $response;
    }

}