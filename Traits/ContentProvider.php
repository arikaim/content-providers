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

use Exception;

/**
 * Content provider trait
 */
trait ContentProvider
{
    /**
     * Get supported content types
     *    
     * @return array
     */
    public function getContentTypes(): array
    {
        return (\is_array($this->contentTypes) == false) ? ['text'] : $this->contentTypes;
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
     * @return string
     */
    public function getProviderTitle(): string
    {
        if (\is_null($this->contentProviderName) == true) {
            throw new Exception('Not valid content provider title',1); 
        }

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
}
