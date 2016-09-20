<?php

namespace UJM\ExoBundle\Tests\Validator\JsonSchema\Question\Type;

use UJM\ExoBundle\Library\Testing\Json\JsonSchemaTestCase;
use UJM\ExoBundle\Validator\JsonSchema\Question\Type\OpenTypeValidator;

class OpenTypeValidatorTest extends JsonSchemaTestCase
{
    /**
     * @var OpenTypeValidator
     */
    private $validator;

    protected function setUp()
    {
        parent::setUp();

        $this->validator = $this->injectJsonSchemaMock(
            new OpenTypeValidator()
        );
    }

    public function testNoSolutionWithPositiveScoreThrowsError()
    {
    }

    public function testIncoherentIdsInSolutionThrowErrors()
    {

    }
}
