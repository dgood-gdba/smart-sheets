<?php

namespace DgoodGdba\SmartSheets\Endpoints;

trait UserEndpoints
{
    /**
     * Gets the sheet version without loading the entire sheet.
     *
     * @param $sheet_id
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUsers($page = 1, $pageSize = 100, $includeAll = false): array
    {
        if ($includeAll) {
            $includeAll = 'true';
        }

        $url = $this->url . self::USERS . "?page=$page&pageSize=$pageSize&includeAll=$includeAll";;
        try {
            $results = json_decode($this->client()->get($url)->getBody()->getContents());
            return $this->respond(200, 'Success.', [
                'users' => $results->data,
            ]);
        } catch (\Exception $e) {
            return $this->respond(500, 'Unable to load users.', ['exception' => $e->getMessage()]);
        }
    }
}
