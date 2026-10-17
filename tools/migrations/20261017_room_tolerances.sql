-- Migration 20261017_room_tolerances; see README.md for required preflight.
-- Kilometres, nullable FLOAT; existing rows retain legacy defaults.
-- Rerunnable under exclusive maintenance; never overwrites existing columns/data.
SET @lup_migration_sql = IF(
    EXISTS (SELECT 1 FROM information_schema.columns
            WHERE table_schema = DATABASE() AND table_name = 'lup_room'
              AND column_name = 'room_join_tolerance'),
    'SELECT 1 AS column_already_exists',
    'ALTER TABLE lup_room ADD COLUMN room_join_tolerance FLOAT NULL DEFAULT NULL');
PREPARE lup_migration_stmt FROM @lup_migration_sql;
EXECUTE lup_migration_stmt;
DEALLOCATE PREPARE lup_migration_stmt;
SET @lup_migration_sql = IF(
    EXISTS (SELECT 1 FROM information_schema.columns
            WHERE table_schema = DATABASE() AND table_name = 'lup_room'
              AND column_name = 'room_leave_tolerance'),
    'SELECT 1 AS column_already_exists',
    'ALTER TABLE lup_room ADD COLUMN room_leave_tolerance FLOAT NULL DEFAULT NULL');
PREPARE lup_migration_stmt FROM @lup_migration_sql;
EXECUTE lup_migration_stmt;
DEALLOCATE PREPARE lup_migration_stmt;
