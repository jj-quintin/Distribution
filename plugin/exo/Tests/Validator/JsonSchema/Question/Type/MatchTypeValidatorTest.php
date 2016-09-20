<?php

namespace UJM\ExoBundle\Tests\Validator\JsonSchema\Question\Type;

use UJM\ExoBundle\Library\Testing\Json\JsonSchemaTestCase;
use UJM\ExoBundle\Validator\JsonSchema\Question\Type\MatchTypeValidator;

class MatchTypeValidatorTest extends JsonSchemaTestCase
{
    /**
     * @var MatchTypeValidator
     */
    private $validator;

    protected function setUp()
    {
        parent::setUp();

        $this->validator = $this->injectJsonSchemaMock(
            new MatchTypeValidator()
        );
    }

    public function testNoSolutionWithPositiveScoreThrowsError()
    {
    }

    public function testIncoherentIdsInSolutionThrowErrors()
    {

    }
}
