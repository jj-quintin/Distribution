<?php

namespace UJM\ExoBundle\Validator\JsonSchema\Question\Type;

use JMS\DiExtraBundle\Annotation as DI;
use UJM\ExoBundle\Library\Question\Handler\QuestionHandlerInterface;
use UJM\ExoBundle\Library\Question\QuestionType;
use UJM\ExoBundle\Library\Validator\JsonSchemaValidator;

/**
 * @DI\Service("ujm_exo.validator.question_cloze")
 * @DI\Tag("ujm_exo.question.validator")
 */
class ClozeTypeValidator extends JsonSchemaValidator implements QuestionHandlerInterface
{
    public function getQuestionMimeType()
    {
        return QuestionType::CLOZE;
    }

    public function getJsonSchemaUri()
    {
        return 'question/cloze/schema.json';
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
     *  - The solutions IDs are consistent with holes IDs
     *  - There is at least one solution with a positive score for each Hole.
     *
     * @param \stdClass $question
     *
     * @return array
     */
    public function validateSolutions(\stdClass $question)
    {
        $errors = [];

        // check solution IDs are consistent with hole IDs
        $holeIds = array_map(function ($hole) {
            return $hole->id;
        }, $question->holes);

        foreach ($question->solutions as $index => $solution) {
            if (!in_array($solution->holeId, $holeIds)) {
                $errors[] = [
                    'path' => "/solutions[{$index}]",
                    'message' => "id {$solution->holeId} doesn't match any hole id",
                ];
            }

            $maxScore = -1;
            foreach ($solution->answers as $answer) {
                if ($answer->score > $maxScore) {
                    $maxScore = $answer->score;
                }
            }

            if ($maxScore <= 0) {
                $errors[] = [
                    'path' => "/solutions[{$index}]/answers",
                    'message' => 'there is no answer with a positive score for the hole',
                ];
            }
        }

        return $errors;
    }
}
