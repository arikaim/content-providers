<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Content\Type;

use Arikaim\Core\Content\Type\Field;
use Arikaim\Core\Interfaces\Content\ContentTypeInterface;
use Arikaim\Core\Interfaces\Content\FieldInterface;

/**
 *  Content type abstract class
 */
abstract class ContentType implements ContentTypeInterface
{
    /**
     * Content fields
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Set action handlers
     *
     * @var array
     */
    protected $actionHandlers = []; 

    /**
     * Content type name
     *
     * @var string
     */
    protected $name;

    /**
     * Content type title
     *
     * @var string|null
     */
    protected $title = null;

    /**
     * Category
     *
     * @var string|null
     */
    protected $category = null;

    /**
     * Definie content type
     *
     * @return void
     */
    abstract protected function define(): void;

    /**
     * Constructor
     */
    public function __construct()
    {      
        $this->define();
    }

    /**
     * Get class name
     */
    public function getClass(): string
    {
        return \get_class($this);
    }

    /**
     * Set action handlers
     *
     * @param array $handlers
     * @return void
     */
    public function setActionHandlers(array $handlers): void
    {
        $this->actionHandlers = $handlers;
    }

    /**
     * Get action handlers list
     *
     * @return array
     */
    public function getActionHandlers(): array
    {
        return $this->actionHandlers;
    }

    /**
     * Get content type category
     *
     * @return string|null
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * Get content type name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get content type title
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Resolve action handlers (create array with actions object)
     *
     * @return array
     */
    public function getActions(): array
    {      
        $result = [];
        foreach ($this->actionHandlers as $handler) {
            $action = new $handler();
            $result[$action->getName()] = $action;
        }
        
        return $result;
    }

    /**
     * Get content type fields
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Get field
     *
     * @param string $name
     * @return FieldInterface|null
    */
    public function getField(string $name): ?FieldInterface
    {
        return $this->fields[$name] ?? null;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Add field 
     *
     * @param string $name
     * @param string $type
     * @param string|null $title
     * @return void
     */
    protected function addField(string $name, string $type, ?string $title = null): void
    {
        $field = Field::create($name,$type,$title);

        $this->fields[$name] = $field;
    }

    /**
     * Add action class
     *
     * @param string $class
     * @return void
     */
    protected function addActionHandler(string $class): void
    {
        if (\class_exists($class) == true) {
            $this->actionHandlers[] = $class;
        }
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return void
     */
    protected function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Set category
     *
     * @param string|null $category
     * @return void
     */
    protected function setCategory(?string $category): void
    {
        $this->category = $category;
    }
}
