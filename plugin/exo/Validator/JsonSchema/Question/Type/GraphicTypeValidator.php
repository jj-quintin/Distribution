<?php

namespace UJM\ExoBundle\Validator\JsonSchema\Question\Type;

use JMS\DiExtraBundle\Annotation as DI;
use UJM\ExoBundle\Library\Question\Handler\QuestionHandlerInterface;
use UJM\ExoBundle\Library\Question\QuestionType;
use UJM\ExoBundle\Library\Validator\JsonSchemaValidator;

/**
 * @DI\Service("ujm_exo.validator.question_graphic")
 * @DI\Tag("ujm_exo.question.validator")
 */
class GraphicTypeValidator extends JsonSchemaValidator implements QuestionHandlerInterface
{
    public function getQuestionMimeType()
    {
        return QuestionType::GRAPHIC;
    }

    public function getJsonSchemaUri()
    {
        return 'question/graphic/schema.json';
    }

    public function validateAfterSchema($question, array $options = [])
    {
        $errors = [];

        if (isset($options['solutionRequired']) && $options['solutionRequired']) {
            $this->validateSolutions($question);
        }

        return $errors;
    }

    /**
     * Checks :
     *  - The solutions IDs are consistent with coords IDs
     *  - There is at least one solution with a positive score.
     *
     * @param \stdClass $question
     *
     * @return array
     */
    protected function validateSolutions(\stdClass $question)
    {
        $errors = [];

        // check solution ids are consistent with coords ids
        $coordsIds = array_map(function ($coords) {
            return $coords->id;
        }, $question->coords);

        $maxScore = -1;
        foreach ($question->solutions as $index => $solution) {
            if (!in_array($solution->id, $coordsIds)) {
                $errors[] = [
                    'path' => "solutions[{$index}]",
                    'message' => "id {$solution->id} doesn't match any coords id",
                ];
            }

            if ($solution->score > $maxScore) {
                $maxScore = $solution->score;
            }
        }

        // check there is a positive score solution
        if ($maxScore <= 0) {
            $errors[] = [
                'path' => 'solutions',
                'message' => 'there is no solution with a positive score',
            ];
        }

        return $errors;
    }
}
