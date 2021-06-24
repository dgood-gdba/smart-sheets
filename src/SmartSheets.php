<?php

namespace DgoodGdba\SmartSheets;

use DgoodGdba\SmartSheets\Endpoints\AttachmentEndpoints;
use DgoodGdba\SmartSheets\Endpoints\EventEndpoints;
use DgoodGdba\SmartSheets\Endpoints\RowEndpoints;
use DgoodGdba\SmartSheets\Endpoints\SheetEndpoints;
use DgoodGdba\SmartSheets\Endpoints\UserEndpoints;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;

final class SmartSheets
{
    use AttachmentEndpoints;
    use EventEndpoints;
    use SheetEndpoints;
    use RowEndpoints;
    use UserEndpoints;
    
    private string $bearer;
    private string $url;
    
    #EndPoints
    const EVENTS = 'events/';
    const SEARCH = 'search/';
    const SHEETS = 'sheets/';
    const USERS = 'users/';
    
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->bearer = config('smart-sheets.api_token');
        $this->url = 'https://api.smartsheet.com/2.0/';
    }
    
    /**
     * Allow manual setting of bearer token
     *
     * @param string $api_token
     */
    public function setBearerToken(string $api_token)
    {
        $this->bearer = $api_token;
    }
    
    /**
     * allow manual setting of the url
     *
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }
    
    /**
     * Creates and returns a clean client
     *
     * @param string $contentType
     * @param array $additionHeaders
     * @return \GuzzleHttp\Client
     */
    private function client(string $contentType = 'application/json', array $additionHeaders = []): \GuzzleHttp\Client
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->bearer,
            'Content-Type' => $contentType
        ];
        
        foreach ($additionHeaders as $key => $value) {
            $headers[$key] = $value;
        }
        
        return new \GuzzleHttp\Client([
            'headers' => $headers
        ]);
    }
    
    /**
     * @param $status
     * @param $message
     * @param array|null $data
     * @return array
     */
    private function respond($status, $message, array $data = null): array
    {
        $return = [
            'status' => $status,
            'message' => $message,
        ];
        if ($data) {
            foreach ($data as $key => $value) {
                $return[$key] = $value;
            }
        }
        return $return;
    }
}
