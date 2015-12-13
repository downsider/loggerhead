<?php
/**
 * @package loggerhead
 * @copyright Copyright © 2015 Danny Smart
 */

namespace Downsider\Loggerhead\Format;

use Silktide\Reposition\Metadata\EntityMetadata;
use Silktide\Reposition\Repository\AbstractRepository;
use Silktide\Reposition\Tests\QueryBuilder\EntityMetadataTest;

class FieldRepository extends AbstractRepository
{

    protected $collectionName = "field";

    protected function configureMetadata()
    {
        $this->entityMetadata->addRelationshipMetadata(
            "Downsider\\Loggerhead\\Format\\Format",
            [
                EntityMetadata::METADATA_RELATIONSHIP_TYPE => EntityMetadata::RELATIONSHIP_TYPE_MANY_TO_ONE,
                EntityMetadata::METADATA_RELATIONSHIP_PROPERTY => "format",
                EntityMetadata::METADATA_RELATIONSHIP_OUR_FIELD => "format_id"
            ]
        );

        $this->entityMetadata->addRelationshipMetadata(
            "Downsider\\Loggerhead\\Format\\Template",
            [
                EntityMetadata::METADATA_RELATIONSHIP_TYPE => EntityMetadata::RELATIONSHIP_TYPE_MANY_TO_ONE,
                EntityMetadata::METADATA_RELATIONSHIP_PROPERTY => "template",
                EntityMetadata::METADATA_RELATIONSHIP_OUR_FIELD => "template_id"
            ]
        );

        parent::configureMetadata();
    }

} 