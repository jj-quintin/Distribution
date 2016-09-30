<?php

namespace UJM\ExoBundle\Tests\Validator\JsonSchema;

use UJM\ExoBundle\Library\Testing\Json\JsonSchemaTestCase;
use UJM\ExoBundle\Validator\JsonSchema\HintValidator;

class HintValidatorTest extends JsonSchemaTestCase
{
    /**
     * @var HintValidator
     */
    private $validator = null;

    protected function setUp()
    {
        parent::setUp();

        $this->validator = $this->injectJsonSchemaMock(new HintValidator());
    }

    /**
     * The validator MUST return an error if hint has no value and the `solutionRequired` option is set to true.
     */
    public function testMissingSolutionsWhenRequiredThrowsError()
    {
        $hintData = $this->loadExampleData('hint/examples/valid/no-value.json');

        $errors = $this->validator->validate($hintData, ['solutionRequired' => true]);

        $this->assertGreaterThan(0, count($errors));
        $this->assertTrue(in_array([
            'path' => '/value',
            'message' => 'Hint requires a "value" property',
        ], $errors));
    }
}
