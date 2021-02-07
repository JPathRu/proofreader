SET QUOTED_IDENTIFIER ON;

IF EXISTS(SELECT *
          FROM sys.indexes
          WHERE object_id = OBJECT_ID(N'[#__proofreader_typos]') AND name = N'idx_language')
  BEGIN
    DROP INDEX [idx_language] ON [#__proofreader_typos];
  END;


EXEC sp_rename @objname = '#__proofreader_typos.comment', @newname = 'typo_comment', @objtype = 'COLUMN';
EXEC sp_rename @objname = '#__proofreader_typos.language', @newname = 'page_language', @objtype = 'COLUMN';
EXEC sp_rename @objname = '#__proofreader_typos.ip', @newname = 'created_by_ip', @objtype = 'COLUMN';

IF NOT EXISTS(SELECT *
              FROM sys.indexes
              WHERE object_id = OBJECT_ID(N'[#__proofreader_typos]') AND name = N'idx_page_language')
  BEGIN
    CREATE NONCLUSTERED INDEX [idx_page_language] ON [#__proofreader_typos] ([page_language] ASC)
      WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF);
  END;
