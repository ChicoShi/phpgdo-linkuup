-- Local additive migration; execute once after checking information_schema.columns.
-- Values are kilometres. Existing rows receive NULL and retain global tolerances.
-- GDT_Length inherits GDT_Float, which maps to FLOAT in Module_DBMS.
ALTER TABLE lup_room
    ADD COLUMN room_join_tolerance FLOAT NULL DEFAULT NULL,
    ADD COLUMN room_leave_tolerance FLOAT NULL DEFAULT NULL;
