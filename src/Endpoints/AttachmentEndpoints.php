<?php

namespace DgoodGdba\SmartSheets\Endpoints;

trait AttachmentEndpoints
{
    private function clearRowsAttachments($ss, $sheet_id, $row_id)
    {
        $data = $this->getRowAttachments($sheet_id, $row_id);
        foreach ($data->data as $attachment) {
            $this->deleteAttachment($sheet_id, $attachment->id);
        }
    }
    
    public function getRowAttachments($sheet_id, $row_id, $page = 1, $pageSize = 100)
    {
        $url = $this->url . self::SHEETS . "$sheet_id/rows/$row_id/attachments?page$page&pageSize=$pageSize";
        try {
            $results = json_decode($this->client()->get($url)->getBody()->getContents());
            return $this->respond(200, 'Success.', [
                'pageNumber' => $results->pageNumber,
                'pageSize' => $results->pageSize,
                'totalPages' => $results->totalPages,
                'totalCount' => $results->totalCount,
                'data' => $results->data
            ]);
        } catch (\Exception $e) {
            return $this->respond(500, 'Unable to get sheet version.', ['exception' => $e->getMessage()]);
        }
    }
    
    public function deleteAttachment($sheet_id, $attachment_id)
    {
        $url = $this->url . self::SHEETS . "$sheet_id/attachments/$attachment_id";
        try {
            $this->client()->delete($url)->getBody()->getContents();
        } catch (\Exception $e) {
            return $this->respond(500, 'Unable to get sheet version.', ['exception' => $e->getMessage()]);
        }
    }
    
}
