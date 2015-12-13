<?php
/**
 * @package loggerhead
 * @copyright Copyright © 2015 Danny Smart
 */

namespace Downsider\Loggerhead\Format;

use Silktide\Reposition\Metadata\EntityMetadata;
use Silktide\Reposition\Repository\AbstractRepository;

class TemplateRepository extends AbstractRepository
{

    protected $collectionName = "template";

    protected function configureMetadata()
    {
        $this->entityMetadata->addRelationshipMetadata(
            "Downsider\\Loggerhead\\Format\\Format",
            [
                EntityMetadata::METADATA_RELATIONSHIP_TYPE => EntityMetadata::RELATIONSHIP_TYPE_ONE_TO_MANY,
                EntityMetadata::METADATA_RELATIONSHIP_PROPERTY => "formats",
                EntityMetadata::METADATA_RELATIONSHIP_THEIR_FIELD => "template_id"
            ]
        );

        $this->entityMetadata->addRelationshipMetadata(
            "Downsider\\Loggerhead\\Format\\Field",
            [
                EntityMetadata::METADATA_RELATIONSHIP_TYPE => EntityMetadata::RELATIONSHIP_TYPE_ONE_TO_MANY,
                EntityMetadata::METADATA_RELATIONSHIP_PROPERTY => "fields",
                EntityMetadata::METADATA_RELATIONSHIP_THEIR_FIELD => "template_id",
                EntityMetadata::METADATA_RELATIONSHIP_ALIAS => "template_fields"
            ]
        );

        parent::configureMetadata();
    }

} 