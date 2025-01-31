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

use Exception;

/**
 *  Content selector
 */
class ContentSelector  
{
    const CONTENT_PROVIDER_TYPE = 'content';
    const DB_MODEL_TYPE         = 'model';
    const ARRAY_TYPE            = 'array';

    const SELECTOR_TYPES = [
        Self::CONTENT_PROVIDER_TYPE,
        Self::DB_MODEL_TYPE,
        Self::ARRAY_TYPE
    ];

    /**
     * Create content selector
     *
     * @param string $provider
     * @param string $contentType
     * @param string $keyFields
     * @param string $key
     * @param string $type
     * @return string
    */
    public static function create(
        string $provider,
        string $contentType,
        string $keyFields,
        string $key, 
        string $type = 'content'
    ): string
    {
        return $type . '>' . $provider . ',' . $contentType . ':' . $keyFields . ':' . $key; 
    }

    /**
     * Parse content selector
     *  
     *  {type} > {provider name|model name,type name|extension name} : {key_fields...} : {key_values...} 
     * 
     *  result array keys - type, provider, content_type, key_fields, key_values 
     *       
     * @param string $selector
     * @throws Exception
     * @return array|null
     */
    public static function parse(string $selector): ?array
    {      
        if (empty($selector) == true) {
            return null;
        }
  
        $tokens = \explode('>',$selector);
        $type = $tokens[0] ?? null;
        $params = $tokens[1] ?? '';

        if (\in_array($type,Self::SELECTOR_TYPES) == false) {
            throw new Exception('Not vlaid content selector type ' . $type,1);                 
        }
        
        $tokens = \explode(':',$params);
        $provider = $tokens[0] ?? '';
        $keyFields = $tokens[1] ?? '';
        $keyValues = $tokens[2] ?? '';
      
        $tokens = \array_pad(\explode(',',$provider),2,null);
        
        return [
            'type'         => $type,
            'provider'     => $tokens[0] ?? null,
            'content_type' => $tokens[1] ?? null,
            'key_fields'   => \explode(',',$keyFields),
            'key_values'   => \explode(',',$keyValues)
        ];
    }

    /**
     * Return true if content selector is valid
     *
     * @param string|null $selector
     * @return boolean
     */
    public static function isValid(?string $selector): bool
    {
        if (empty($selector) == true) {
            return false;
        }

        $tokens = \explode('>',$selector);
        $type = $tokens[0] ?? null;
        $params = $tokens[1] ?? null;

        if (\in_array($type,Self::SELECTOR_TYPES) == false) {
            return false;
        }
        if (empty($params) == true) {
            return false;
        }

        return true;
    }
}
