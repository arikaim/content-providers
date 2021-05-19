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

use Arikaim\Core\Content\Traits\ContentProvider;
use Arikaim\Core\Interfaces\Content\ContentProviderInterface;

/**
 *  Abstract content provider class
 */
abstract class AbstractContentProvider implements ContentProviderInterface
{
    use ContentProvider;

    /**
     * Content provider name
     *
     * @var string
     */
    protected $contentProviderName;

    /**
     * Content provider title
     *
     * @var string
     */
    protected $contentProviderTitle;

    /**
     * Content provider category
     *
     * @var string|null
     */
    protected $contentProviderCategory = null;

    /**
     * Supported content types
     *
     * @var array
     */
    protected $contentTypes;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Get content
     *
     * @param string|int|array $key  Id, Uuid or content name slug
     * @return ContentItemInterface|null
     */
    abstract public function getContent($key);

    /**
     * Get content list
     *
     * @param mixed|null $filter
     * @param integer $page
     * @param integer $perPage
     * @return array ContentItemInterface
    */
    abstract public function getContentList($filter = null, int $page = 1, int $perPage = 20): array;
}
