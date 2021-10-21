<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Content\Traits;

use Arikaim\Core\Interfaces\Content\ContentTypeInterface;
use Arikaim\Core\Content\ContentItem;
use Exception;

/**
 * Content provider trait
 */
trait ContentProvider
{
    /**
     * Create new content item
     *
     * @param array $data
     * @param string|null $contentType  Content type name
     * @return array|null
     */
    public function createItem(array $data, ?string $contentType = null): ?array
    {
        return null;
    }

    /**
     * Save content item
     *
     * @param string|int $key
     * @param array $data
     * @param string|null $contentType  Content type name
     * @return boolean
     */
    public function saveItem($key, array $data, ?string $contentType = null): bool
    {
        return false;
    }

    /**
     * Get total data items
     *
     * @return integer|null
     */
    public function getItemsCount(): ?int
    {
        return null;
    }

    /**
     * Get class name
     *
     * @return string
     */
    public function getClass(): string
    {
        return \get_class($this);
    }

    /**
     * Get supported content type
     *    
     * @return ContentTypeInterface|null
     */
    public function getContentType(): ?ContentTypeInterface
    {
        return $this->contentType;
    }

    /**
     * Get supported content types
     *
     * @return array
    */
    public function getSupportedContentTypes(): array
    {
        return $this->supportedContentTypes ?? [];
    }

    /**
     * Set content type
     *    
     * @param ContentTypeInterface $contentType
     * @return void
     */
    public function setContentType(ContentTypeInterface $contentType): void
    {
        $this->contentType = $contentType;
    }

    /**
     * Get provider name
     *
     * @return string
     * @throws Exception
     */
    public function getProviderName(): string
    {
        if (\is_null($this->contentProviderName) == true) {
            throw new Exception('Not valid content provider name',1); 
        }

        return $this->contentProviderName;
    }

    /**
     * Get provider title
     *
     * @return string|null
     */
    public function getProviderTitle(): ?string
    {
        return $this->contentProviderTitle;
    }

    /**
     * Get provider category
     *
     * @return string|null
     */
    public function getProviderCategory(): ?string
    {
        return $this->contentProviderCategory;
    }

    /**
     * Get content
     *
     * @param mixed $key  Id, Uuid or content name slug
     * @return ContentItemInterface|null
     */
    public function get($key)
    {
        $contentType = $this->getContentType();
        $data = $this->getContent($key);
        if ($data == null) {
            return null;
        }
        // resolve content Id
        $id = $data['uuid'] ?? $data['id'] ?? $key;

        return ContentItem::create($data,$contentType,(string)$id);
    }
    
    /**
     * Get content list
     *
     * @param mixed|null $filter
     * @param integer $page
     * @param integer $perPage
     * @return array[ContentItemInterface]|null
    */
    public function getContentItems($filter = null, int $page = 1, int $perPage = 20): ?array
    {
        $contentType = $this->getContentType();
        $keyFields = $filter['key_fields'] ?? null;
        if ($keyFields == null) {
            return null;
        }
       
        $model = $this->whereNotNull('id');

        foreach($keyFields as $field) {
            $value = $filter['query'] ?? $filter['key_values'][$field] ?? '';        
            $model = $model->orWhereRaw('UPPER(' . $field . ') LIKE ?',['%' . $value . '%']);
        }
    
        $data = $model->get()->toArray();

        $items = [];
        foreach ($data as $row) {
            $items[] = ContentItem::create($row,$contentType,(string)$row['uuid']);
        }

        return $items;
    }
}
