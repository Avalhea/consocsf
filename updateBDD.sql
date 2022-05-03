DROP PROCEDURE IF EXISTS delete_annuel;


DELIMITER //
CREATE PROCEDURE delete_annuel()
BEGIN
DELETE FROM action_justice;
DELETE FROM atelier;
DELETE FROM communication;
DELETE FROM dossier;
DELETE FROM dossiers_autre;
DELETE FROM evenement;
DELETE FROM formations;
DELETE FROM lieu;
DELETE FROM permanence;
END //



DELIMITER //
CREATE EVENT resetBdd
    ON SCHEDULE EVERY 5 MINUTE
    ON COMPLETION PRESERVE
    DO
BEGIN
CALL delete_annuel();
END //

SET GLOBAL event_scheduler='ON';