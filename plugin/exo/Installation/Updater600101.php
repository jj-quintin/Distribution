<?php

namespace UJM\ExoBundle\Installation;

use Claroline\BundleRecorder\Log\LoggableTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Updater600101
{
    use LoggableTrait;

    private $connection;

    public function __construct(ContainerInterface $container)
    {
        $this->connection = $container->get('doctrine.dbal.default_connection');
    }

    public function preUpdate()
    {
        $this->upSchema();
        $this->upDoctrineExo();
    }

    public function upDoctrineExo()
    {
        $this->log('20150923181250');
        //if no exist, case if the first installation is an v6.0.0
        $query = '
            SELECT *
            FROM doctrine_ujmexobundle_versions
            WHERE version = \'20150923181250\'
        ';
        $res = $this->connection->query($query);
        if ($res->rowCount() == 0) {
            $this->connection->exec("
                INSERT INTO doctrine_ujmexobundle_versions VALUES('20150923181250')
            ");
        }
    }

    /**
     * This cascad exist in V5 and in V6 > 6.0.0.
     */
    public function upSchema()
    {
        $this->connection->exec('
            ALTER TABLE ujm_subscription
            DROP FOREIGN KEY FK_A17BA225E934951A
        ');
        $this->connection->exec('
            ALTER TABLE ujm_subscription
            ADD CONSTRAINT FK_A17BA225E934951A FOREIGN KEY (exercise_id)
            REFERENCES ujm_exercise (id)
            ON DELETE CASCADE
        ');
    }
}
