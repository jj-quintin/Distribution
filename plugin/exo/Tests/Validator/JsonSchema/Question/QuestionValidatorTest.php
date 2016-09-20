<?php

namespace UJM\ExoBundle\Tests\Validator\JsonSchema\Question;

use UJM\ExoBundle\Library\Testing\Json\JsonSchemaTestCase;
use UJM\ExoBundle\Validator\JsonSchema\Question\QuestionValidator;

class QuestionValidatorTest extends JsonSchemaTestCase
{
    /**
     * @var QuestionValidator
     */
    private $validator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $hintValidator;

    protected function setUp()
    {
        parent::setUp();

        // Mock Question Type validation (it's tested individually)
        $validatorCollector = $this->mock('UJM\ExoBundle\Validator\JsonSchema\Question\QuestionValidatorCollector');

        // Do not check if the Question Type is supported
        $validatorCollector
            ->expects($this->any())
            ->method('hasHandlerForMimeType')
            ->willReturn(true);

        // Do not validate Question Type specific data
        $validatorCollector
            ->expects($this->any())
            ->method('validateMimeType')
            ->willReturn([]);

        // Do not validate Hints
        $this->hintValidator = $this->mock('UJM\ExoBundle\Validator\JsonSchema\HintValidator');
        $this->hintValidator->expects($this->any())
            ->method('validateAfterSchema')
            ->willReturn([]);

        $this->validator = $this->injectJsonSchemaMock(
            new QuestionValidator($validatorCollector, $this->hintValidator)
        );
    }

    /**
     * The validator MUST return an error if question has an invalid type.
     */
    public function testUnknownQuestionTypeThrowsError()
    {
        // We don't use `$this->validator` as we have mocked this part for other tests
        $validator = $this->client->getContainer()->get('ujm_exo.validator.question');

        $questionData = $this->loadTestData('question/base/unknown-type.json');

        $errors = $validator->validate($questionData);

        $this->assertGreaterThan(0, count($errors));
        $this->assertTrue(in_array([
            'path' => '/type',
            'message' => 'Unknown question type "'.$questionData->type.'"',
        ], $errors));
    }

    /**
     * The validator MUST return an error if question has no solution and the `solutionRequired` option is set to true.
     */
    public function testMissingSolutionsWhenRequiredThrowsError()
    {
        $questionData = $this->loadTestData('question/base/unknown-type.json');

        $errors = $this->validator->validate($questionData, ['solutionRequired' => true]);

        $this->assertGreaterThan(0, count($errors));
        $this->assertTrue(in_array([
            'path' => '/solutions',
            'message' => 'Question requires a "solutions" property',
        ], $errors));
    }

    /**
     * The validator MUST execute custom validation for the hints.
     */
    public function testHintsAreValidatedToo()
    {
        $questionData = $this->loadExampleData('question/base/examples/valid/with-hints.json');

        $this->hintValidator->expects($this->exactly(count($questionData->hints)))
            ->method('validateAfterSchema');

        $this->validator->validate($questionData);
    }
}
