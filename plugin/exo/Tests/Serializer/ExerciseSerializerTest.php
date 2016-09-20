<?php

namespace UJM\ExoBundle\Tests\Serializer;

use UJM\ExoBundle\Entity\Exercise;
use UJM\ExoBundle\Entity\Step;
use UJM\ExoBundle\Library\Testing\Json\JsonDataTestCase;

class ExerciseSerializerTest extends JsonDataTestCase
{
    private $validator;

    private $serializer;

    protected function setUp()
    {
        parent::setUp();

        // We trust validator service as it is fully tested
        $this->validator = $this->client->getContainer()->get('ujm_exo.validator.exercise');
        $this->serializer = $this->client->getContainer()->get('ujm_exo.serializer.exercise');
    }

    public function testSerializedDataAreSchemaValid()
    {

    }

    public function testSerializedDataAreCorrectlySet()
    {

    }

    public function testDeserializedDataAreCorrectlySet()
    {

        $om = $this->client->getContainer()->get('claroline.persistence.object_manager');

        $exercise = new Exercise();

        $step = new Step();
        /*$step->setTi*/
    }
}
