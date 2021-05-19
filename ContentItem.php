<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Content;

use Arikaim\Core\Interfaces\Content\ContentItemInterface;

/**
 *  Content item
 */
class ContentItem implements ContentItemInterface
{
    /**
     * Content value
     *
     * @var array
     */
    protected $content;

    /**
     * Content item title
     *
     * @var string
     */
    protected $title;

    /**
     * Constructor
     * 
     * @param array $content
     * @param string $title
     * @param string|int $id
     * @param 
     */
    public function __construct(array $content, string $title, $id)
    {
        $this->content = $content;
        $this->title = $title;
        $this->id = $id;
    }

    public static function create($content, string $title, $id): ContentItemInterface
    {
        return new Self($content,$title,$id);
    } 

    /**
     * Get item content
     *
     * @return mixed
     */
    public function content(?string $type = null)
    {
        $type = $type ?? ContentItemInterface::TEXT_TYPE;

        return $this->content;
    }

    /**
     * Get content item title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get content item id
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }
}
