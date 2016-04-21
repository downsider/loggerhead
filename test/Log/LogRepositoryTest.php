<?php
/**
 * @package loggerhead
 * @copyright Copyright © 2015 Danny Smart
 */

namespace Downsider\Loggerhead\Test\Log;

use Downsider\Loggerhead\Log\LogRepository;
use Silktide\Reposition\Metadata\EntityMetadata;

class LogRepositoryTest extends \PHPUnit_Framework_TestCase {

    public function testMetadataCreation()
    {
        $table = "table";

        $fields = [
            "id" => "int",
            "name" => "string",
            "description" => "text",
            "value" => "float"
        ];

        $field = \Mockery::mock("Downsider\\Loggerhead\\Format\\Field");
        $field->shouldReceive("getFieldName")->andReturnValues(array_keys($fields));
        $field->shouldReceive("getType")->andReturnValues($fields);

        /** @var \Mockery\Mock|\Downsider\Loggerhead\Format\Format $format */
        $format = \Mockery::mock("Downsider\\Loggerhead\\Format\\Format");
        $format->shouldReceive("getName")->andReturn("format");
        $format->shouldReceive("getCollection")->andReturn($table);
        $format->shouldReceive("getTemplate")->once()->andReturn(null);
        $format->shouldReceive("getFields->toArray")->once()->andReturn([$field, $field, $field, $field]);

        $metadata = \Mockery::mock("Silktide\\Reposition\\Metadata\\EntityMetadata");

        /** @var \Mockery\Mock|\Silktide\Reposition\Metadata\EntityMetadataFactoryInterface $metadataFactory */
        $metadataFactory = \Mockery::mock("Silktide\\Reposition\\Metadata\\EntityMetadataFactoryInterface");
        $metadataFactory->shouldReceive("createEmptyMetadata")->andReturn($metadata);

        // define the values to be set on the metadata
        $metaStore = [];
        $metadata->shouldReceive("setCollection")->with("fmt_" . $table)->once();
        $metadata->shouldReceive("setPrimaryKey");
        $metadata->shouldReceive("getPrimaryKey")->andReturn("id");
        $metadata->shouldReceive("addFieldMetadata")->andReturnUsing(function ($name, $meta) use (&$metaStore) {
            if (isset($metaStore[$name])) {
                $this->fail("Tried to call addFieldMetadata for '$name' more than once");
                return;
            }
            $metaStore[$name] = $meta[EntityMetadata::METADATA_FIELD_TYPE];
        });

        $query = \Mockery::mock("Silktide\\Reposition\\QueryBuilder\\TokenSequencerInterface");

        /** @var \Mockery\Mock|\Silktide\Reposition\QueryBuilder\QueryBuilderInterface $builder */
        $builder = \Mockery::mock("Silktide\\Reposition\\QueryBuilder\\QueryBuilderInterface");
        $builder->shouldReceive("find->where->ref->op->val")->andReturn($query);

        /** @var \Mockery\Mock|\Silktide\Reposition\Storage\StorageInterface $storage */
        $storage = \Mockery::mock("Silktide\\Reposition\\Storage\\StorageInterface");
        $storage->shouldIgnoreMissing(true);

        $repo = new LogRepository($metadataFactory, $builder, $storage);
        $repo->setFormat($format);
        
        $repo->find(1);
        // repeat to check if we use the cache
        $repo->find(1);

        // validate the added field metadata
        $expected = $fields;
        $expected["description"] = "string"; // DB "text" type should be converted to "string"

        $this->assertEquals($expected, $metaStore);
    }

}
 