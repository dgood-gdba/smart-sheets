<?php

namespace DgoodGdba\SmartSheets\Endpoints;

use Carbon\Carbon;

trait SheetEndpoints
{
    /**
     * Creates a copy of the specified sheet.
     *
     * @param $sheet_id
     * @param string $include
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function copySheet($sheet_id, $include = 'attachments,cellLinks,data,discussions,filters,forms,ruleRecipients,rules,shares'
    ): array {
        $url = $this->url . self::SHEETS . "copy?include=$include";
        try {
            $results = json_decode($this->client()->post($url)->getBody()->getContents());
            return $this->respond(201, 'Sheet was copies successfully.', [
                'accessLevel' => $results->result->accessLevel,
                'id' => $results->result->id,
                'name' => $results->result->name,
                'permalink' => $results->result->permalink,
            ]);
        } catch (\Exception $e) {
            return $this->respond(500, 'Copy has failed.', ['exception' => $e->getMessage()]);
        }
    }
    
    /**
     * Deletes the sheet specified in the URL.
     *
     * @param $sheet_id
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteSheet($sheet_id): array
    {
        $url = $this->url . self::SHEETS . "$sheet_id";
        try {
            $results = json_decode($this->client()->delete($url)->getBody()->getContents());
            return $this->respond(202, 'Sheet was deleted successfully.');
        } catch (\Exception $e) {
            return $this->respond(500, 'Delete has failed.', ['exception' => $e->getMessage()]);
        }
    }
    
    /**
     * Gets the sheet version without loading the entire sheet.
     *
     * @param $sheet_id
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSheetVersion($sheet_id): array
    {
        $url = $this->url . self::SHEETS . "$sheet_id/version";
        try {
            $results = json_decode($this->client()->get($url)->getBody()->getContents());
            return $this->respond(200, 'Success.', [
                'version' => $results->version,
            ]);
        } catch (\Exception $e) {
            return $this->respond(500, 'Unable to get sheet version.', ['exception' => $e->getMessage()]);
        }
    }
    
    /**
     * Gets the sheet version without loading the entire sheet.
     *
     * @param $sheet_id
     * @param string $include
     * @param null $columnIds
     * @param null $filterId
     * @param null $rowIds
     * @param null $rowNumbers
     * @param Carbon|null $rowsModifiedSince
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSheet($sheet_id, $include = 'objectValue', $columnIds = null, $filterId = null, $rowIds = null, $rowNumbers = null, Carbon $rowsModifiedSince = null): array
    {
        $url = $this->url . self::SHEETS . "$sheet_id?level=2&include=$include";
        
        if ($columnIds) {
            $url .= "&columnIds=$columnIds";
        }
        if ($filterId) {
            $url .= "&filterId=$filterId";
        }
        if ($rowIds) {
            $url .= "&rowIds=$rowIds";
        }
        if ($rowNumbers) {
            $url .= "&rowNumbers=$rowNumbers";
        }
        if ($rowsModifiedSince) {
            $url .= "&rowsModifiedSince=" . $rowsModifiedSince->toIso8601String();
        }
        
        try {
            $results = json_decode($this->client()->get($url)->getBody()->getContents());
            return $this->respond(200, 'Success.', [
                'name' => $results->name,
                'version' => $results->version,
                'rowCount' => $results->totalRowCount,
                'columns' => $results->columns,
                'rows' => $results->rows
            ]);
        } catch (\Exception $e) {
            return $this->respond(500, 'Unable to get sheet version.', ['exception' => $e->getMessage()]);
        }
    }
    
    /**
     * Gets the sheet in the format specified, based on the sheet Id.
     *
     * @param $sheet_id
     * @param string $type
     * @param string $paper_size
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSheetAs($sheet_id, string $type = 'pdf', string $paper_size = 'letter'): array
    {
        switch ($type) {
            case 'csv':
                return $this->getSheetAsCSV($sheet_id);
            case 'pdf':
                return $this->getSheetAsPDF($sheet_id, $paper_size);
            case 'excel':
                return $this->getSheetAsExcel($sheet_id);
        }
    }
    
    /**
     * Gets the sheet in the format specified, based on the sheet Id.
     *
     * @param $sheet_id
     * @param string $paper_size
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSheetAsCSV($sheet_id): array
    {
        $url = $this->url . self::SHEETS . "$sheet_id";
        try {
            $csv = $this->client(additionHeaders: ['Accept' => 'text/csv'])->get($url)->getBody()->getContents();
            $sheet = str_getcsv($csv, "\n");
            foreach ($sheet as &$row) {
                $row = str_getcsv($row);
            }
            return $this->respond(200, 'Success.', [
                'records' => $sheet,
            ]);
        } catch (\Exception $e) {
            return $this->respond(500, 'Unable to download sheet as csv.', ['exception' => $e->getMessage()]);
        }
    }
    
    /**
     * Gets the sheet in the format specified, based on the sheet Id.
     *
     * @param $sheet_id
     * @param string $paper_size
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSheetAsPDF($sheet_id, $paper_size = 'letter'): array
    {
        $paper_size = strtoupper($paper_size);
        $url = $this->url . self::SHEETS . "$sheet_id?paperSize=$paper_size";
        try {
            $pdf = $this->client(additionHeaders: ['Accept' => 'application/pdf'])->get($url)->getBody()->getContents();
            
            return $this->respond(200, 'Success.', [
                'pdf' => $pdf,
            ]);
        } catch (\Exception $e) {
            return $this->respond(500, 'Unable to download sheet as csv.', ['exception' => $e->getMessage()]);
        }
    }
    
    /**
     * Gets the sheet in the format specified, based on the sheet Id.
     *
     * @param $sheet_id
     * @param string $paper_size
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSheetAsExcel($sheet_id): array
    {
        $url = $this->url . self::SHEETS . "$sheet_id";
        try {
            $pdf = $this->client(additionHeaders: ['Accept' => 'application/vnd.ms-excel'])->get($url)->getBody()->getContents();
            
            return $this->respond(200, 'Success.', [
                'excel' => $pdf,
            ]);
        } catch (\Exception $e) {
            return $this->respond(500, 'Unable to download sheet as csv.', ['exception' => $e->getMessage()]);
        }
    }
    
    /**
     * Gets a summarized list of all sheets owned by the members of the organization account.
     *
     * @param int $page
     * @param int $pageSize
     * @param false $includeAll
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listOrgSheet($page = 1, $pageSize = 100, $includeAll = false): array
    {
        if ($includeAll) {
            $includeAll = 'true';
        }
        $url = $this->url . self::USERS . self::SHEETS . "?page=$page&pageSize=$pageSize&includeAll=$includeAll";
        try {
            $results = json_decode($this->client()->get($url)->getBody()->getContents());
            return $this->respond(200, 'Success.', [
                'page' => $results->pageNumber,
                'pages' => $results->totalPages,
                'total' => $results->totalCount,
                'records' => $results->data
            ]);
        } catch (\Exception $e) {
            return $this->respond(500, 'Unable to access endpoint.', ['exception' => $e->getMessage()]);
        }
    }
    
    /**
     * Gets a list of all sheets that the user has access to in alphabetical order by name. The list contains an
     * abbreviated Sheet object for each sheet.
     *
     * @param int $page
     * @param int $pageSize
     * @param false $includeAll
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listSheets($page = 1, $pageSize = 100, $includeAll = false): array
    {
        $url = $this->url . self::SHEETS . "?page=$page&pageSize=$pageSize&includeAll=$includeAll";
        try {
            $results = json_decode($this->client()->get($url)->getBody()->getContents());
            return $this->respond(200, 'Success.', [
                'page' => $results->pageNumber,
                'pages' => $results->totalPages,
                'total' => $results->totalCount,
                'records' => $results->data
            ]);
        } catch (\Exception $e) {
            return $this->respond(500, 'Unable to access endpoint.', ['exception' => $e->getMessage()]);
        }
    }
    
    /**
     * Moves the specified sheet to a new location.
     *
     * @param $sheet_id
     * @param $destination_id
     * @param string $destination_type
     * @return array
     */
    public function moveSheet($sheet_id, $destination_id, $destination_type = 'folder'): array
    {
        dd('NOT YET READY!!!');
    }
    
    /**
     * Searches a sheet for the specified text.
     *
     * @param $sheet_id
     * @param string $query
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function searchSheet($sheet_id, string $query): array
    {
        $query = http_build_query(['query' => $query]);
        $url = $this->url . self::SEARCH . self::SHEETS . "$sheet_id?$query";
        try {
            $results = json_decode($this->client()->get($url)->getBody()->getContents());
            return $this->respond(200, 'Success.', [
                'results' => $results->results,
            ]);
        } catch (\Exception $e) {
            return $this->respond(500, 'Unable to get sheet version.', ['exception' => $e->getMessage()]);
        }
    }
    
    /**
     * Searches a sheet for the specified text.
     *
     * @param $sheet_id
     * @param string $email
     * @param string $subject
     * @param string $message
     * @param bool $ccSelf
     * @param string $format
     * @param string $paper
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendSheetByEmail($sheet_id, string $email, string $subject, string $message, bool $ccSelf = false, string $format = 'pdf', string $paper = 'LETTER'): array
    {
        $url = $this->url . self::SHEETS . "$sheet_id/emails";
        try {
            $results = json_decode($this->client()->post($url, [
                'json' => [
                    'sendTo' => $email,
                    'subject' => $subject,
                    'message' => $message,
                    'ccMe' => $ccSelf,
                    'format' => $format,
                    'formatDetails' => [
                        'paperSize' => strtoupper($paper)
                    ]
                ]
            ])->getBody()->getContents());
            return $this->respond(200, 'Success.', [
                'results' => $results->results,
            ]);
        } catch (\Exception $e) {
            return $this->respond(500, 'Unable to get sheet version.', ['exception' => $e->getMessage()]);
        }
    }
    
    /**
     * @param string $sheet_id
     * @param array $data
     * @param bool $toTop
     * @return array
     */
    public function addRow(string $sheet_id, array $data, bool $toTop = true): array
    {
        $params = [
            'toTop' => $toTop,
            'cells' => $data
        ];
        $url = $this->url . self::SHEETS . "$sheet_id/rows";
        $results = $this->client()->request('post', $url, [
            'json' => $params
        ])->getBody()->getContents();
        dd($results);
        return $this->respond(200, 'Success.', [
            'status' => $results->statusCode,
            'message' => $results->reasonPhrase
        ]);
    }
}
