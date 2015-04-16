DROP PROCEDURE IF exists get_users;
DELIMITER $$
CREATE PROCEDURE `get_users`(
    IN poffset INT(11), 
    IN plimit INT(11), 
    IN psort VARCHAR(63),
    IN porder CHAR(4),
    IN pkt VARCHAR(63),
    IN pkw VARCHAR(255),
    IN pfuzzy TINYINT(4)
)
BEGIN
DECLARE t_error INTEGER DEFAULT 0;
DECLARE no_more_users INTEGER DEFAULT 0;
DECLARE user_id INTEGER DEFAULT 0;
DECLARE rs CURSOR FOR SELECT `id` FROM `fateak_tmp_table`;
DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error = 1;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET no_more_users = 1; 

CREATE TEMPORARY TABLE IF NOT EXISTS fateak_tmp_table (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `roles` varchar(255) NULL
) ENGINE = MEMORY;

START TRANSACTION;

SET @ctsql = 'INSERT INTO `fateak_tmp_table` (`id`, `email`, `name`) SELECT u.id, u.email, up.name FROM users AS u JOIN user_profiles AS up ON u.id = up.id';

IF pkw <> '' THEN
    IF pfuzzy = 1 THEN
        SET @ctwov = CONCAT(' LIKE ''%', pkw, '%'' ');
    ELSE
        SET @ctwov = CONCAT(' = ''', pkw, '''');
    END IF;
    SET @ctsql = CONCAT(@ctsql, ' WHERE `', pkt, '` ', @ctwov);
END IF;

IF psort <> '' THEN
    SET @ctsql = CONCAT(@ctsql, ' ORDER BY ', psort, ' ', porder);
END IF;

SET @ctsql = CONCAT(@ctsql, ' LIMIT ', poffset, ',', plimit, ';');

PREPARE cts FROM @ctsql;
EXECUTE cts;
    
OPEN rs;

roles:REPEAT
    FETCH NEXT FROM rs INTO user_id;

    IF no_more_users = 0 THEN
        BEGIN
        DECLARE no_more_roles INTEGER DEFAULT 0;
        DECLARE roles_list VARCHAR(255) DEFAULT '[';
        DECLARE role_id INTEGER DEFAULT 0;
        DECLARE role_name VARCHAR(255) DEFAULT '';
        DECLARE rs_r CURSOR FOR SELECT r.id, r.name FROM `roles` AS r JOIN `roles_users` AS ru ON ru.role_id = r.id WHERE ru.user_id = user_id;
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET no_more_roles = 1; 

        OPEN rs_r;
        role:REPEAT
            FETCH NEXT FROM rs_r INTO role_id, role_name; 

            IF no_more_roles = 0 THEN
                SET roles_list  = CONCAT(roles_list, '{"id":"', role_id, '","name":"', role_name, '"},');
            END IF;
        UNTIL no_more_roles = 1 END REPEAT;
        UPDATE `fateak_tmp_table` SET `roles` = CONCAT(TRIM(',' FROM roles_list), ']') WHERE `id` = user_id;
        END;
    END IF;
UNTIL no_more_users = 1 END REPEAT;

IF t_error = 1 THEN
    ROLLBACK;
ELSE
    COMMIT;
END IF;

SELECT * FROM `fateak_tmp_table`;

END;
