<?php
/**
 * @package loggerhead-app
 * @copyright Copyright © 2015 Danny Smart
 */

namespace Downsider\LoggerheadApp\Format;

use Downsider\Clay\Model\ModelTrait;
use Silktide\Reposition\Collection\Collection;
use Silktide\Reposition\Collection\CollectionFactory;

class Format
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
     * @var string
     */
    protected $collection;

    /**
     * @var Template
     */
    protected $template;

    /**
     * @var Collection
     */
    protected $fields = [];

    /**
     * @var int
     */
    protected $logMaxAge = 0;

    public function __construct(array $data = [], CollectionFactory $collectionFactory = null)
    {
        if ($collectionFactory !== null) {
            $this->modelConstructorArgs[] = $collectionFactory;
            $this->fields = $collectionFactory->create();
        }
        $this->loadData($data);
    }

    /**
     * @param string $collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return string
     */
    public function getCollection()
    {
        return $this->collection;
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
     * @param int $logMaxAge
     */
    public function setLogMaxAge($logMaxAge)
    {
        $this->logMaxAge = (int) $logMaxAge;
    }

    /**
     * @return int
     */
    public function getLogMaxAge()
    {
        return $this->logMaxAge;
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

    /**
     * @param Template $template
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;
    }

    /**
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }



} 