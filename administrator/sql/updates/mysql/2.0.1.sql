DROP INDEX `idx_language` ON `#__proofreader_typos`;

ALTER TABLE `#__proofreader_typos` CHANGE COLUMN `language` `page_language` VARCHAR(255) NOT NULL DEFAULT '' AFTER `page_title`;
ALTER TABLE `#__proofreader_typos` CHANGE COLUMN `ip` `created_by_ip` VARCHAR(39) NOT NULL DEFAULT '' AFTER `created_by`;
ALTER TABLE `#__proofreader_typos` CHANGE COLUMN `comment` `typo_comment` TEXT NOT NULL DEFAULT '';

CREATE INDEX `idx_page_language` ON `#__proofreader_typos`(`page_language`);
