<?php

namespace UJM\ExoBundle\Serializer\Question\Type;

use JMS\DiExtraBundle\Annotation as DI;
use UJM\ExoBundle\Library\Question\Handler\QuestionHandlerInterface;
use UJM\ExoBundle\Library\Question\QuestionType;
use UJM\ExoBundle\Library\Serializer\SerializerInterface;

/**
 * @DI\Service("ujm_exo.serializer.question_choice")
 * @DI\Tag("ujm_exo.question.serializer")
 */
class ChoiceTypeSerializer implements QuestionHandlerInterface, SerializerInterface
{
    public function getQuestionMimeType()
    {
        return QuestionType::CHOICE;
    }

    public function serialize($entity, array $options = [])
    {
        // Load QuestionType from Question definition
        $questionType = '';

        $questionData = new \stdClass();

        if (isset($options['includeSolutions']) && $options['includeSolutions']) {
            $questionData->solutions = $this->serializeSolutions($questionType);
        }

        return $questionData;
    }

    public function deserialize($entity, array $options = [])
    {
        // TODO: Implement deserialize() method.
    }

    private function serializeSolutions($questionType)
    {

    }
}
