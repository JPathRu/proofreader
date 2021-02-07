DROP INDEX IF EXISTS "#__proofreader_idx_language";

ALTER TABLE "#__proofreader_typos" RENAME COLUMN "comment" TO "typo_comment";
ALTER TABLE "#__proofreader_typos" RENAME COLUMN "ip" TO "created_by_ip";
ALTER TABLE "#__proofreader_typos" RENAME COLUMN "language" TO "page_language";

CREATE INDEX "#__proofreader_idx_page_language" ON "#__proofreader_typos" ("page_language");
