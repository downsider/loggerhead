<?php
/**
 * @package loggerhead-app
 * @copyright Copyright © 2015 Danny Smart
 */

namespace Downsider\LoggerheadApp\Format;

use Downsider\Clay\Model\ModelTrait;
use Silktide\Reposition\Collection\Collection;
use Silktide\Reposition\Collection\CollectionFactory;

class Template
{
    use ModelTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Collection
     */
    protected $formats;

    /**
     * @var Collection
     */
    protected $fields;

    public function __construct(array $data = [], CollectionFactory $collectionFactory = null)
    {
        if ($collectionFactory !== null) {
            $this->fields = $collectionFactory->create();
            $this->formats = $collectionFactory->create();
        }
        $this->loadData($data);
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        $this->fields->clear();
        foreach ($fields as $field) {
            $this->addFields($field);
        }
        $this->fields->setChangeTracking();
    }

    /**
     * @param Field $field
     */
    public function addFields(Field $field)
    {
        $this->fields->add($field);
    }

    /**
     * @return Collection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $formats
     */
    public function setFormats(array $formats)
    {
        $this->formats->clear();
        foreach ($formats as $format) {
            $this->addFields($format);
        }
        $this->formats->setChangeTracking();
    }

    /**
     * @param Format $format
     */
    public function addFormats(Format $format)
    {
        $this->formats->add($format);
    }

    /**
     * @return Collection
     */
    public function getFormats()
    {
        return $this->formats;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

} 