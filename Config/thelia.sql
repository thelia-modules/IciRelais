
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- order_address_icirelais
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `order_address_icirelais`;

CREATE TABLE `order_address_icirelais`
(
    `id` INTEGER NOT NULL,
    `code` VARCHAR(10) NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_order_address_icirelais_order_address_id`
        FOREIGN KEY (`id`)
        REFERENCES `order_address` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
