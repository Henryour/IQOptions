<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class JsonbNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS raw_numbers
(
    id BIGSERIAL PRIMARY KEY,
    number BIGINT NOT NULL
);
CREATE UNIQUE INDEX IF NOT EXISTS raw_numbers_id_uindex ON raw_numbers (id);
CREATE INDEX IF NOT EXISTS raw_numbers_id_desc ON raw_numbers(id DESC);

CREATE TABLE IF NOT EXISTS jsonb_numbers
(
    numbers JSONB NOT NULL,
    is_new BOOLEAN DEFAULT TRUE
);
CREATE INDEX IF NOT EXISTS jsonb_numbers_is_new_true ON jsonb_numbers(is_new) WHERE is_new IS TRUE;

CREATE OR REPLACE FUNCTION move_to_jsonb() RETURNS trigger AS
$$
BEGIN
  INSERT INTO jsonb_numbers(numbers) VALUES((
    SELECT json_agg(sub.number)::jsonb
    FROM (SELECT rn.number
          FROM raw_numbers as rn
          ORDER BY id
          DESC LIMIT 4) as sub
  ));
  RETURN NULL;
END;
$$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS update_json_after_raw ON raw_numbers;

CREATE TRIGGER update_json_after_raw
AFTER INSERT 
ON raw_numbers
FOR EACH ROW
EXECUTE PROCEDURE move_to_jsonb();

SQL;
        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS raw_numbers, jsonb_numbers;
DROP TRIGGER IF EXISTS update_json_after_raw ON raw_numbers;
DROP FUNCTION IF EXISTS move_to_jsonb();
SQL;
        DB::unprepared($sql);

    }
}
