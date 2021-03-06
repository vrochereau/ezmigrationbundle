<?php

namespace Kaliop\eZMigrationBundle\Core\Matcher;

use Kaliop\eZMigrationBundle\API\Collection\ContentCollection;

class ContentMatcherDirectLoad extends ContentMatcher
{
    /**
     * Override the parent's implementation to use the repository instead of the Search Service to load Contents when
     * specified by Id or RemoteId.
     * This has the advantage of not going through Solr, and hence having less problems with transactions and indexation delay.
     *
     * @param array $conditions
     * @return ContentCollection
     */
    public function matchContent(array $conditions)
    {
        $match = reset($conditions);
        if (count($conditions) === 1 && in_array(($key = key($conditions)), array(self::MATCH_CONTENT_ID, self::MATCH_CONTENT_REMOTE_ID))) {
            $match = (array)$match;
            $contents = array();
            switch ($key) {
                case self::MATCH_CONTENT_ID:
                    foreach($match as $contentId) {
                        $content = $this->repository->getContentService()->loadContent($contentId);
                        $contents[$content->id] = $content;
                    }
                    break;
                case self::MATCH_CONTENT_REMOTE_ID:
                    foreach($match as $contentRemoteId) {
                        $content = $this->repository->getContentService()->loadContentByRemoteId($contentRemoteId);
                        $contents[$content->id] = $content;
                    }
                    break;
            }
            return new ContentCollection($contents);
        }

        return parent::matchContent($conditions);
    }
}