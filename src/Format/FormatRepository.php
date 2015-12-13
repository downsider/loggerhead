<?php
/**
 * @package loggerhead
 * @copyright Copyright © 2015 Danny Smart
 */

namespace Downsider\Loggerhead\Format;

use Silktide\Reposition\Metadata\EntityMetadata;
use Silktide\Reposition\Repository\AbstractRepository;

class FormatRepository extends AbstractRepository
{

    protected $collectionName = "format";

    protected $includeRelationshipsByDefault = [
        "this" => true,
        "template" => ["template_fields"]
    ];

    protected function configureMetadata()
    {
        $this->entityMetadata->addRelationshipMetadata(
            "Downsider\\Loggerhead\\Format\\Template",
            [
                EntityMetadata::METADATA_RELATIONSHIP_TYPE => EntityMetadata::RELATIONSHIP_TYPE_MANY_TO_ONE,
                EntityMetadata::METADATA_RELATIONSHIP_PROPERTY => "template",
                EntityMetadata::METADATA_RELATIONSHIP_OUR_FIELD => "template_id",
                EntityMetadata::METADATA_RELATIONSHIP_ALIAS => "template"
            ]
        );

        $this->entityMetadata->addRelationshipMetadata(
            "Downsider\\Loggerhead\\Format\\Field",
            [
                EntityMetadata::METADATA_RELATIONSHIP_TYPE => EntityMetadata::RELATIONSHIP_TYPE_ONE_TO_MANY,
                EntityMetadata::METADATA_RELATIONSHIP_PROPERTY => "fields",
                EntityMetadata::METADATA_RELATIONSHIP_THEIR_FIELD => "format_id",
                EntityMetadata::METADATA_RELATIONSHIP_ALIAS => "format_fields"
            ]
        );

        parent::configureMetadata();
    }

} 