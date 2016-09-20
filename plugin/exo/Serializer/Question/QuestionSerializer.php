<?php

namespace UJM\ExoBundle\Serializer\Question;

use JMS\DiExtraBundle\Annotation as DI;
use UJM\ExoBundle\Entity\QuestionObject;
use UJM\ExoBundle\Entity\Question;
use UJM\ExoBundle\Entity\QuestionResource;
use UJM\ExoBundle\Library\Question\QuestionType;
use UJM\ExoBundle\Library\Serializer\SerializerInterface;
use UJM\ExoBundle\Serializer\HintSerializer;
use UJM\ExoBundle\Serializer\ResourceContentSerializer;

/**
 * Serializer for question data.
 *
 * @DI\Service("ujm_exo.serializer.question")
 */
class QuestionSerializer implements SerializerInterface
{
    /**
     * @var QuestionSerializerCollector
     */
    private $serializerCollector;

    /**
     * @var HintSerializer
     */
    private $hintSerializer;

    /**
     * @var ResourceContentSerializer
     */
    private $resourceContentSerializer;

    /**
     * QuestionSerializer constructor.
     *
     * @param QuestionSerializerCollector $serializerCollector
     * @param HintSerializer $hintSerializer
     * @param ResourceContentSerializer $resourceContentSerializer
     *
     * @DI\InjectParams({
     *     "serializerCollector"       = @DI\Inject("ujm_exo.serializer.question_collector"),
     *     "hintSerializer"            = @DI\Inject("ujm_exo.serializer.hint"),
     *     "resourceContentSerializer" = @DI\Inject("ujm_exo.serializer.resource_content")
     * })
     */
    public function __construct(
        QuestionSerializerCollector $serializerCollector,
        HintSerializer $hintSerializer,
        ResourceContentSerializer $resourceContentSerializer)
    {
        $this->serializerCollector = $serializerCollector;
        $this->hintSerializer = $hintSerializer;
        $this->resourceContentSerializer = $resourceContentSerializer;
    }

    /**
     * Converts a Question into a JSON-encodable structure.
     *
     * @param Question $question
     * @param array $options
     *
     * @return \stdClass
     */
    public function serialize($question, array $options = [])
    {
        $questionType = QuestionType::getFromName($question->getType());

        /** @var SerializerInterface $typeSerializer */
        $typeSerializer = $this->serializerCollector->getHandlerForMimeType($questionType);

        // Serialize question type data
        $questionData = $typeSerializer->serialize($question, $options);

        $questionData->meta = $this->serializeMetadata($question, $options);

        // Add generic question information
        $questionData->id = (string) $question->getId();
        $questionData->type = $questionType;
        $questionData->title = $question->getTitle();
        $questionData->description = $question->getTitle();
        $questionData->invite = $question->getInvite();
        $questionData->supplementary = $question->getSupplementary();
        $questionData->specification = $question->getSpecification();

        // Serialize Hints
        if (0 !== $question->getHints()->count()) {
            $questionData->hints = $this->serializeHints($question, $options);
        }

        // Serialize Objects
        if (0 !== $question->getObjects()->count()) {
            $questionData->objects = $this->serializeObjects($question);
        }

        // Serialize Resources
        if (0 !== $question->getResources()->count()) {
            $questionData->resources = $this->serializeResources($question);
        }

        // Serialize feedback
        if (isset($options['includeSolutions']) && $options['includeSolutions'] && $question->getFeedback()) {
            $questionData->feedback = $question->getFeedback();
        }

        return $questionData;
    }

    /**
     * Converts raw data into a Question entity.
     *
     * @param \stdClass $data
     * @param array $options
     *
     * @return Question
     */
    public function deserialize($data, array $options = [])
    {
        $question = !empty($options['entity']) ? $options['entity'] : new Question();

        return $question;
    }

    /**
     * Serializes Question metadata.
     *
     * @param Question $question
     * @param array $options
     *
     * @return \stdClass
     */
    private function serializeMetadata(Question $question, array $options = [])
    {
        $creator = $question->getUser();
        $author = new \stdClass();
        $author->name = sprintf('%s %s', $creator->getFirstName(), $creator->getLastName());

        $metadata = new \stdClass();
        $metadata->authors = [$author];
        $metadata->created = $question->getDateCreate()->format('Y-m-d\TH:i:s');
        $metadata->updated = $question->getDateModify()->format('Y-m-d\TH:i:s');

        if (isset($options['includeUsedBy']) && $options['includeUsedBy']) {
            // Includes the list of Exercises using this question
        }

        return $metadata;
    }

    /**
     * Serializes Question hints.
     * Forwards the hint serialization to HintSerializer.
     *
     * @param Question $question
     * @param array $options
     *
     * @return array
     */
    private function serializeHints(Question $question, array $options = [])
    {
        return array_map(function ($hint) use ($options) {
            return $this->hintSerializer->serialize($hint, $options);
        }, $question->getHints()->toArray());
    }

    /**
     * Serializes Question objects.
     *
     * @param Question $question
     * @param array $options
     *
     * @return array
     */
    private function serializeObjects(Question $question, array $options = [])
    {
        return array_map(function ($object) use ($options) {
            /** @var QuestionObject $object */
            return $this->resourceContentSerializer->serialize($object->getResourceNode(), $options);
        }, $question->getObjects()->toArray());
    }

    /**
     * Serializes Question resources.
     *
     * @param Question $question
     * @param array $options
     *
     * @return array
     */
    private function serializeResources(Question $question, array $options = [])
    {
        return array_map(function ($resource) use ($options) {
            /** @var QuestionResource $resource */
            return $this->resourceContentSerializer->serialize($resource->getResourceNode(), $options);
        }, $question->getResources()->toArray());
    }
}
