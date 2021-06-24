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
    
    public function getRowAttachments($sheet_id, $row_id)
    {
        $url = $this->url . self::SHEETS . "$sheet_id/rows/$row_id/attachments";
        try {
            $results = json_decode($this->client()->get($url)->getBody()->getContents());
            dd($results);
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
}
