<?php

namespace DgoodGdba\SmartSheets\Endpoints;

use Carbon\Carbon;

trait RowEndpoints
{
    
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
        $results = json_decode($this->client()->request('post', $url, [
            'json' => $params
        ])->getBody()->getContents());
        return $this->respond(200, 'Success.', [
            'result' => $results->result,
            'message' => $results->message
        ]);
    }
    
    /**
     * @param string $sheet_id
     * @param array $params
     * @return array
     */
    public function updateRow(string $sheet_id, array $params): array
    {
        $url = $this->url . self::SHEETS . "$sheet_id/rows";
        $results = json_decode($this->client()->request('put', $url, [
            'json' => $params
        ])->getBody()->getContents());
        return $this->respond(200, 'Success.', [
            'result' => $results->result,
            'message' => $results->message
        ]);
    }
    
    public function deleteRow(string $sheet_id, int|array $rows)
    {
        if (!is_array($rows)) {
            $rows = [$rows];
        }
        $url = $this->url . self::SHEETS . "$sheet_id/rows?ids=" . implode(',', $rows) . "&ignoreRowsNotFound=true";
        $results = json_decode($this->client()->request('delete', $url)->getBody()->getContents());
        return $this->respond(200, 'Success.', [
            'result' => $results->result,
            'message' => $results->message
        ]);
    }
}
