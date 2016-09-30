<?php

namespace UJM\ExoBundle\Validator\JsonSchema\Question\Type;

use JMS\DiExtraBundle\Annotation as DI;
use UJM\ExoBundle\Library\Question\Handler\QuestionHandlerInterface;
use UJM\ExoBundle\Library\Question\QuestionType;
use UJM\ExoBundle\Library\Validator\JsonSchemaValidator;

/**
 * @DI\Service("ujm_exo.validator.question_choice")
 * @DI\Tag("ujm_exo.question.validator")
 */
class ChoiceTypeValidator extends JsonSchemaValidator implements QuestionHandlerInterface
{
    public function getQuestionMimeType()
    {
        return QuestionType::CHOICE;
    }

    public function getJsonSchemaUri()
    {
        return 'question/choice/schema.json';
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
     *  - The solutions IDs are consistent with choices IDs
     *  - There is at least one solution with a positive score.
     *
     * @param \stdClass $question
     *
     * @return array
     */
    protected function validateSolutions(\stdClass $question)
    {
        $errors = [];

        // check solution IDs are consistent with choice IDs
        $choiceIds = array_map(function ($choice) {
            return $choice->id;
        }, $question->choices);

        $maxScore = -1;
        foreach ($question->solutions as $index => $solution) {
            if (!in_array($solution->id, $choiceIds)) {
                $errors[] = [
                    'path' => "/solutions[{$index}]",
                    'message' => "id {$solution->id} doesn't match any choice id",
                ];
            }

            if ($solution->score > $maxScore) {
                $maxScore = $solution->score;
            }
        }

        // check there is a positive score solution
        if ($maxScore <= 0) {
            $errors[] = [
                'path' => '/solutions',
                'message' => 'There is no solution with a positive score',
            ];
        }

        return $errors;
    }
}
