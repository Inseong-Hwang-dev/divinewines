<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddTimestampColumnsToCoupons extends AbstractMigration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');
        // Add columns as nullable
        $this->execute("ALTER TABLE coupons ADD COLUMN created DATETIME NULL, ADD COLUMN modified DATETIME NULL");
        // Set current timestamp for existing records
        $this->execute("UPDATE coupons SET created = '{$now}', modified = '{$now}' WHERE created IS NULL");
        // Make columns NOT NULL
        $this->execute("ALTER TABLE coupons MODIFY created DATETIME NOT NULL, MODIFY modified DATETIME NOT NULL");
    }

    public function down()
    {
        $this->execute("ALTER TABLE coupons DROP COLUMN created, DROP COLUMN modified");
    }
} 