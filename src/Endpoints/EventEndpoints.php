<?php

namespace DgoodGdba\SmartSheets\Endpoints;

trait EventEndpoints
{
    public function getEvents($since = null, $offset = null, $max = 10000)
    {
        $url = $this->url . self::EVENTS . "?maxCount=$max";
        if ($since) {
            $url .= "&since=$since";
        }
        if ($offset) {
            $url .= "&streamPosition=$offset";
        }
        try {
            $results = json_decode($this->client()->get($url)->getBody()->getContents());
            return $this->respond(200, 'Success.', [
                'events' => $results->data,
                'nextStreamPosition' => $results->nextStreamPosition,
                'moreAvailable' => $results->moreAvailable
            ]);
        } catch (\Exception $e) {
            return $this->respond(500, 'Unable to get sheet version.', ['exception' => $e->getMessage()]);
        }
    }
}
