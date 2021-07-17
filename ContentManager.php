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

use Arikaim\Core\Content\ContentTypeRegistry;
use Arikaim\Core\Utils\Path;
use Arikaim\Core\System\Traits\PhpConfigFile;
use Arikaim\Core\Interfaces\Content\ContentManagerInterface;
use Arikaim\Core\Interfaces\Content\ContentProviderInterface;
use Arikaim\Core\Utils\Uuid;
use Exception;

/**
 *  Content providers registry manager
 */
class ContentManager implements ContentManagerInterface
{
    use PhpConfigFile;

    /**
     *  Default providers config file name
     */
    const PROVIDERS_FILE_NAME = Path::CONFIG_PATH . 'content-providers.php';

    /**
     * Content providers
     *
     * @var array|null
     */
    protected $contentProviders = null;

    /**
     * Content type registry
     *
     * @var null
     */
    protected $contentTypeRegistry = null;

    /**
     * Content providers config file
     *
     * @var string
     */
    private $providersFileName;

    /**
     * Constructor
     * 
     * @param string|null $providersFileName
     */
    public function __construct(?string $providersFileName = null)
    {
        $this->providersFileName = $providersFileName ?? Self::PROVIDERS_FILE_NAME;                 
    }

    /**
     * Return true if content typ eexists
     *
     * @param string $name
     * @return boolean
     */
    public function hasContentType(string $name): bool
    {
        return $this->typeRegistry()->has($name);
    } 

    /**
     * Get content type from registry
     *
     * @param string $name
     * @return ContentProviderInterface|null
     */
    public function type(string $name, ?string $providerName = null): ?ContentProviderInterface
    {        
        $contentType = $this->typeRegistry()->get($name);
        if (empty($providerName) == true) {
            $providers = $this->typeRegistry()->getPoviders($name);
            $providerName = $providers[0] ?? null;
        }      
        if (empty($providerName) == true || $contentType == null) {
            return null;
        }

        $provider = $this->provider($providerName);
        if ($provider == null) {
            return null;
        }

        $provider->setContentType($contentType);

        return $provider;
    }

    /**
     * Get content type registry
     *
     * @return object
     */
    public function typeRegistry()
    {
        if ($this->contentTypeRegistry == null) {
            $this->contentTypeRegistry = new ContentTypeRegistry();
        }

        return $this->contentTypeRegistry;
    }

    /**
     * Create content item
     *
     * @param mixed $data
     * @param string $contentType
     * @return mixed
     */
    public function createItem($data, string $contentType)
    {
        $type = $this->typeRegistry()->get($contentType);
        if ($type == null) {
            return null;
        }
        if (\is_object($data) == true) {
            $data = $data->toArray();
        }
        if (\is_array($data) == false) {
            $data = [$data];
        }

        // resolve content Id
        $id = $data['uuid'] ?? $data['id'] ?? Uuid::create();

        return ContentItem::create($data,$type,(string)$id);
    }

    /**
     * Load content providers and content types
     *
     * @param boolean $reload
     * @return void
     */
    public function load(bool $reload = false): void
    {
        if ((\is_null($this->contentProviders) == true) || ($reload == true)) {
            $this->contentProviders = $this->include($this->providersFileName);
        }        
    }

    /**
     * Get content providers list
     *
     * @param string|null $category
     * @param string|null $contentType
     * @return array
     */
    public function getProviders(?string $category = null, ?string $contentType = null): array
    {
        $this->load();     
        if ((empty($category) == true) && (empty($contentType) == true)) {
            return $this->contentProviders ?? [];
        }
        
        $result = [];
        foreach ($this->contentProviders as $item) {
            if ($item['category'] == $category) {
                $result[] = $item;
            }
        }
        
        return $result;      
    }

    /**
     * Get content provider
     * 
     * @param string $name
     * @return ContentProviderInterface|null
     */
    public function provider(string $name): ?ContentProviderInterface
    {
        $this->load();     
        $item = $this->contentProviders[$name] ?? null;
        if (empty($item) == true) {
            return null;
        }

        $provider = new $item['handler']();
        // resolve content type

        return $provider;
    }

    /**
     * Check if provider exists
     *
     * @param string $name
     * @return boolean
     */
    public function hasProvider(string $name): bool
    {
        return !empty($this->provider($name));
    } 

    /**
     * Register content provider
     *
     * @param object|string $provider
     * @return boolean
     */
    public function registerProvider($provider): bool
    { 
        if (($provider instanceof ContentProviderInterface) == false) {
            throw new Exception('Not valid content provider class.');
            return false;
        }

        $details = $this->resolveProviderDetails($provider);

        // load current array
        $this->contentProviders = $this->includePhpArray($this->providersFileName);        
        // add content provider
        $this->contentProviders[$details['name']] = $details;

        // register provider in content type
        $supportedContentTypes = $details['type'] ?? [];
        foreach($supportedContentTypes as $type) {
            $this->typeRegistry()->addProvider($type,$details['name']);
        }

        return $this->saveConfigFile($this->providersFileName,$this->contentProviders);    
    }

    /**
     * Unregister content provider
     *
     * @param string $name
     * @return boolean
     */
    public function unRegisterProvider(string $name): bool
    {
        $provider = $this->provider($name);
        if (\is_object($provider) == false) {
            return true;   
        }
        $name = $provider->getProviderName();

        unset($this->contentProviders[$name]);

        return $this->saveConfigFile($this->providersFileName,$this->contentProviders);     
    }

    /**
     * Resolve provider edetails
     *
     * @param ContentProviderInterface $details
     * @throws Exception
     * @return array
     */
    protected function resolveProviderDetails(ContentProviderInterface $provider): array
    {
        return [
            'handler'  => \get_class($provider),
            'name'     => $provider->getProviderName(),
            'title'    => $provider->getProviderTitle(),
            'type'     => $provider->getSupportedContentTypes(),
            'category' => $provider->getProviderCategory()
        ];       
    }
}
