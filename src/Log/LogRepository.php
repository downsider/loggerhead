<?php
/**
 * @package loggerhead-app
 * @copyright Copyright © 2015 Danny Smart
 */

namespace Downsider\LoggerheadApp\Log;

use Downsider\LoggerheadApp\Exception\LogException;
use Downsider\LoggerheadApp\Format\Field;
use Downsider\LoggerheadApp\Format\Format;
use Downsider\LoggerheadApp\Format\Template;
use Silktide\Reposition\Collection\Collection;
use Silktide\Reposition\Metadata\EntityMetadata;
use Silktide\Reposition\Metadata\EntityMetadataFactoryInterface;
use Silktide\Reposition\QueryBuilder\QueryBuilderInterface;
use Silktide\Reposition\QueryBuilder\TokenSequencerInterface;
use Silktide\Reposition\Repository\RepositoryInterface;
use Silktide\Reposition\Storage\StorageInterface;

class LogRepository implements RepositoryInterface
{

    /**
     * @var EntityMetadataFactoryInterface
     */
    protected $metadataFactory;

    /**
     * @var QueryBuilderInterface
     */
    protected $queryBuilder;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var Format
     */
    protected $format;

    /**
     * @var array
     */
    protected $metadataCache = [];

    public function __construct(EntityMetadataFactoryInterface $metadataFactory, QueryBuilderInterface $queryBuilder, StorageInterface $storage)
    {
        $this->metadataFactory = $metadataFactory;
        $this->queryBuilder = $queryBuilder;
        $this->storage = $storage;
    }

    public function setFormat(Format $format)
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return "Downsider\\LoggerHead\\Log\\Log";
    }

    /**
     * @return string
     */
    public function getCollectionName()
    {
        $this->checkFormat();
        return "fmt_" . $this->format->getCollection();
    }

    protected function checkFormat()
    {
        if (empty($this->format)) {
            throw new LogException("Can't perform operation. The format has not been set on the LogRepository");
        }
    }

    protected function getMetadata()
    {
        $formatName = $this->format->getName();
        if (!empty($this->metadataCache[$formatName])) {
            return $this->metadataCache[$formatName];
        }

        $metadata = $this->metadataFactory->createEmptyMetadata();
        $metadata->setCollection($this->getCollectionName());
        $metadata->setPrimaryKey("id");

        echo "\nformat: " . print_r($this->format, true);

        // get fields, from both the format and the template
        $fields = $this->format->getFields()->toArray();
        $template = $this->format->getTemplate();
        if ($template instanceof Template) {
            $templateFields = $template->getFields();
            if ($templateFields instanceof Collection) {
                $fields = array_merge($fields, $template->getFields()->toArray(false));
            }
        }

        // add field metadata
        foreach ($fields as $field) {
            /** @var Field $field */
            // convert database field types to metadata field types
            $type = $field->getType();
            if ($type == "text") {
                $type = "string";
            }

            $metadata->addFieldMetadata(
                $field->getFieldName(),
                [EntityMetadata::METADATA_FIELD_TYPE => $type]
            );
        }

        // save to the cache
        $this->metadataCache[$formatName] = $metadata;

        return $metadata;
    }

    /**
     * @param string|int $id
     * @param bool $includeRelationships
     * @return object
     */
    public function find($id, $includeRelationships = null)
    {
        $this->checkFormat();
        $metadata = $this->getMetadata();
        $query = $this->queryBuilder->find($metadata)
            ->where()
            ->ref($metadata->getPrimaryKey())
            ->op("=")
            ->val($id);
        return $this->doQuery($query);
    }

    /**
     * @param array $filters
     * @param array $sort
     * @param int $limit
     * @param array $options
     * @param bool $includeRelationships
     * @return array
     */
    public function filter(array $filters, array $sort = [], $limit = 0, array $options = [], $includeRelationships = null)
    {
        $this->checkFormat();
        $metadata = $this->getMetadata();
        $query = $this->queryBuilder->find($metadata);

        $this->createWhereFromFilters($query, $filters);

        if (!empty($sort)) {
            $query->sort($sort);
        }

        if (!empty($limit)) {
            $query->limit($limit);
        }

        return $this->doQuery($query);
    }

    /**
     * @param object $entity
     * @param array $options
     * @return string|int - ID of the entity
     */
    public function save($entity, array $options = [])
    {
        $this->checkFormat();
        $metadata = $this->getMetadata();
        echo "\nMetadata: " . print_r($metadata, true);
        $query = $this->queryBuilder->save($metadata)->entity($entity);
        $result = $this->doQuery($query, false);

        return !empty($result[StorageInterface::NEW_INSERT_ID_RETURN_FIELD])
            ? $result[StorageInterface::NEW_INSERT_ID_RETURN_FIELD]
            : null;
    }

    /**
     * {@inheritDoc}
     */
    public function delete($id)
    {
        $this->checkFormat();
        $metadata = $this->getMetadata();
        $query = $this->queryBuilder->delete($metadata)
            ->where()
            ->ref($metadata->getPrimaryKey())
            ->op("=")
            ->val($id);
        return $this->doQuery($query, false);
    }

    public function deleteWithFilter(array $filters)
    {
        $this->checkFormat();
        $metadata = $this->getMetadata();
        $query = $this->queryBuilder->delete($metadata);
        $this->createWhereFromFilters($query, $filters);
        return $this->doQuery($query, false);
    }

    /**
     * {@inheritDoc}
     */
    public function count(array $conditions = [], array $groupBy = [])
    {
        $this->checkFormat();
        $metadata = $this->getMetadata();

        $query = $this->queryBuilder->find($metadata)->aggregate("count", "*");

        $this->createWhereFromFilters($query, $conditions);

        if (!empty($groupBy)) {
            $query->group($groupBy);
        }

        return $this->doQuery($query, false);
    }

    /**
     * @param TokenSequencerInterface $query
     * @param bool $createEntity
     *
     * @return object|array
     */
    protected function doQuery(TokenSequencerInterface $query, $createEntity = true)
    {
        return $this->storage->query($query, $createEntity? $this->getEntityName(): "");
    }

    protected function createWhereFromFilters(TokenSequencerInterface $query, array $filters, $startWithWhere = true)
    {
        if (empty($filters)) {
            return;
        }

        if ($startWithWhere) {
            $query->where();
        }

        // we need to prepend "andL" to all but the first field, so
        // get the values for the last field and remove it from the array
        reset($filters);
        $firstField = key($filters);
        $firstValue = array_shift($filters);

        // filter first field
        $this->addComparisonToQuery($query, $firstField, $firstValue);

        // create filters
        foreach ($filters as $field => $value) {
            $query->andL();
            $this->addComparisonToQuery($query, $field, $value);
        }

    }

    protected function addComparisonToQuery(TokenSequencerInterface $query, $field, $value)
    {
        $query->ref($field)->op("=")->val($value);
    }

} 