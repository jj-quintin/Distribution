<?php

namespace UJM\ExoBundle\Tests\Validator\JsonSchema\Question\Type;

use UJM\ExoBundle\Library\Testing\Json\JsonSchemaTestCase;
use UJM\ExoBundle\Validator\JsonSchema\Question\Type\ChoiceTypeValidator;

class ChoiceTypeValidatorTest extends JsonSchemaTestCase
{
    /**
     * @var ChoiceTypeValidator
     */
    private $validator;

    protected function setUp()
    {
        parent::setUp();

        $this->validator = $this->injectJsonSchemaMock(
            new ChoiceTypeValidator()
        );
    }

    public function testNoSolutionWithPositiveScoreThrowsError()
    {
    }

    public function testIncoherentIdsInSolutionThrowErrors()
    {
        
    }
}
