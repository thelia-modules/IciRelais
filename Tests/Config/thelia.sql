
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- order_address_icirelais
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `order_address_icirelais`
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

-- ---------------------------------------------------------------------
-- address_icirelais
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `address_icirelais`
(
    `id` INTEGER NOT NULL,
    `title_id` INTEGER NOT NULL,
    `company` VARCHAR(255),
    `firstname` VARCHAR(255) NOT NULL,
    `lastname` VARCHAR(255) NOT NULL,
    `address1` VARCHAR(255) NOT NULL,
    `address2` VARCHAR(255) NOT NULL,
    `address3` VARCHAR(255) NOT NULL,
    `zipcode` VARCHAR(10) NOT NULL,
    `city` VARCHAR(255) NOT NULL,
    `country_id` INTEGER NOT NULL,
    `code` VARCHAR(10) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `FI_address_icirelais_customer_title_id` (`title_id`),
    INDEX `FI_address_country_id` (`country_id`),
    CONSTRAINT `fk_address_icirelais_customer_title_id`
        FOREIGN KEY (`title_id`)
        REFERENCES `customer_title` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT `fk_address_icirelais_country_id`
        FOREIGN KEY (`country_id`)
        REFERENCES `country` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
