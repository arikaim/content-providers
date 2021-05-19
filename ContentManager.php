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

use Arikaim\Core\Utils\Path;
use Arikaim\Core\System\Traits\PhpConfigFile;
use Arikaim\Core\Interfaces\Content\ContentManagerInterface;
use Arikaim\Core\Interfaces\Content\ContentProviderInterface;
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
    const CONFIG_FILE_NAME = Path::CONFIG_PATH . 'content-providers.php';

    /**
     * Content providers
     *
     * @var array|null
     */
    protected $contentProviders = null;

    /**
     * Content providers config file
     *
     * @var string
     */
    private $configFileName;

    /**
     * Constructor
     * 
     * @param string|null $configFileName
     */
    public function __construct(?string $configFileName = null)
    {
        $this->configFileName = $configFileName ?? Self::CONFIG_FILE_NAME;          
    }

    /**
     * Load content providers
     *
     * @param boolean $reload
     * @return void
     */
    public function load(bool $reload = false): void
    {
        if ((\is_null($this->contentProviders) == true) || ($reload == true)) {
            $this->contentProviders = $this->include($this->configFileName);
        }
    }

    /**
     * Get content providers list
     *
     * @param string|null $category
     * @param string|null $contentType
     * @return array
     */
    public function getProviders(?string $category, ?string $contentType = null): array
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

        return new $item['handler']();
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
        $this->contentProviders = $this->includePhpArray($this->configFileName);        
        // add content provider
        $this->contentProviders[$details['name']] = $details;

        return $this->saveConfigFile($this->configFileName,$this->contentProviders);    
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

        return $this->saveConfigFile($this->configFileName,$this->contentProviders);     
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
            'type'     => $provider->getContentTypes(),
            'category' => $provider->getProviderCategory()
        ];       
    }
}
