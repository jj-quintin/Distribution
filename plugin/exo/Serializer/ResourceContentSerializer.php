<?php

namespace UJM\ExoBundle\Serializer;

use Claroline\CoreBundle\Entity\Resource\File;
use Claroline\CoreBundle\Entity\Resource\ResourceNode;
use Claroline\CoreBundle\Entity\Resource\Text;
use Claroline\CoreBundle\Manager\ResourceManager;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Routing\RouterInterface;
use UJM\ExoBundle\Library\Serializer\SerializerInterface;

/**
 * Serializer for resource content.
 *
 * @DI\Service("ujm_exo.serializer.resource_content")
 */
class ResourceContentSerializer implements SerializerInterface
{
    /**
     * @var string
     */
    private $fileDir;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ResourceManager
     */
    private $resourceManager;

    /**
     * ResourceContentSerializer constructor.
     *
     * @DI\InjectParams({
     *     "fileDir"         = @DI\Inject("%claroline.param.files_directory%"),
     *     "router"          = @DI\Inject("router"),
     *     "resourceManager" = @DI\Inject("claroline.manager.resource_manager")
     * })
     *
     * @param string $fileDir
     * @param RouterInterface $router
     * @param ResourceManager $resourceManager
     */
    public function __construct(
        $fileDir,
        RouterInterface $router,
        ResourceManager $resourceManager)
    {
        $this->fileDir = $fileDir;
        $this->router = $router;
        $this->resourceManager = $resourceManager;
    }

    /**
     * @param ResourceNode $node
     * @param array $options
     *
     * @return \stdClass
     */
    public function serialize($node, array $options = [])
    {
        // Load Resource from Node
        $resource = $this->resourceManager->getResourceFromNode($node);
        $resourceType = $node->getResourceType()->getName();

        $resourceData = new \stdClass();
        $resourceData->id = (string) $node->getId();

        if ('text' === $resourceType) {
            /** @var Text $resource */
            $resourceData->data = $resource->getContent();
            $resourceData->type = 'text/html';
        } else {
            $resourceData->type = $node->getMimeType();

            if ('file' === $resourceType
                && 1 === preg_match('#^([image|audio|video]+\/[^\/]+)$#', $resourceData->type)) {
                // the file is directly understandable by the browser (img, audio, video) return the file URL

                /** @var File $resource */
                $resourceData->url = $this->fileDir.DIRECTORY_SEPARATOR.$resource->getHashName();
            } else {
                // return the url to access the resource

                $resourceData->url = $this->router->generate(
                    'claro_resource_open',
                    ['resourceType' => $resourceType, 'node' => $node->getId()]
                );
            }
        }

        return $resourceData;
    }

    /**
     * Not implemented.
     * The only purpose of this serializer is to expose a common data representation of a resource,
     * it's not made to create/update them
     *
     * @param \stdClass $data
     * @param array $options
     *
     * @return mixed
     *
     * @throws \LogicException
     */
    public function deserialize($data, array $options = [])
    {
        throw new \LogicException('Resource content is not deserializable.');
    }
}
