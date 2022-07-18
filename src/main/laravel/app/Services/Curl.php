<?php

namespace App\Services;

class Curl
{
    private $curl;

    private string $url;

    private array $headers;

    private $body;

    private bool $post = true;

    private bool $returnTransfer = true;

    private array $options = [];

    private bool $sslVerfify = true;

    public function __construct()
    {
       $this->setCurl();
    }

    protected function setCurl() {
        $this->curl = curl_init();
    }


    public function setOption($key, $value = null): self
    {
        if (is_array($key)):
            foreach ($key as $curlKey => $curlOption):
                curl_setopt($this->curl, $curlKey, $curlOption);
            endforeach;
        else:
            curl_setopt($this->curl, $key, $value);
        endif;


        return $this;
    }

    /**
     * @param bool $post
     * @return Curl
     */
    public function setPost(bool $post): Curl
    {
        $this->post = $post;
        return $this;
    }


    /**
     * @param bool $returnTransfer
     * @return Curl
     */
    public function setReturnTransfer(bool $returnTransfer): Curl
    {
        $this->returnTransfer = $returnTransfer;
        return $this;
    }


    /**
     * @param array $headers
     * @return Curl
     */
    public function setHeaders(array $headers): Curl
    {
        $this->headers = $headers;
        return $this;
    }


    /**
     * @param mixed $body
     * @return Curl
     */
    public function setBody($body): self
    {
        $this->body = $body;
        return $this;
    }


    /**
     * @param string $url
     * @return Curl
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }


    /**
     * @return $this
     */
    public function disableSslVerification(): self
    {
        $this->sslVerfify = false;
        return $this;
    }

    public function execute()
    {

        $this->verifyCurlExecution();
        $this->prepareCurl();

        $result = curl_exec($this->curl);

        curl_close($this->curl);

        return $result;
    }

    private function verifyCurlExecution()
    {

    }

    protected function prepareCurl()
    {

        curl_setopt($this->curl, CURLOPT_URL, $this->url);
        curl_setopt($this->curl, CURLOPT_POST, $this->post);
        if($this->headers){
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
        }



        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, $this->returnTransfer);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        if(!$this->sslVerfify):
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        endif;
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->body);

    }


}
