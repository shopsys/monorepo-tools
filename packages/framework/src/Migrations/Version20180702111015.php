<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180702111015 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $administratorsCount = $this->sql('SELECT count(*) FROM administrators')->fetchColumn(0);
        if ($administratorsCount > 0) {
            return;
        }

        // passwords are the same
        // admin123

        $this->sql(
            'INSERT INTO administrators (id, username, real_name, password, login_token, email, superadmin) VALUES '
            . '(1, \'superadmin\', \'superadmin\', \'$2y$12$ppwYj/By0pDkiLlE.ssf6uuwCvtfdDfsJJNr84fU59HmxSfj0luSC\', \'\', \'no-reply@shopsys.com\', true),'
            . '(2, \'admin\', \'admin\', \'$2y$12$tRU86hi0UxWEMQzP08nl..hKiClF.Pj3D1oIcKDL.aA7ph2Vomwh2\', \'\', \'no-reply@shopsys.com\', false)'
        );

        $this->sql('ALTER SEQUENCE administrators_id_seq RESTART WITH 3');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
