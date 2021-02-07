SET QUOTED_IDENTIFIER ON;

IF EXISTS(SELECT *
              FROM sys.objects
              WHERE object_id = OBJECT_ID(N'[#__proofreader_typos]') AND type IN (N'U'))
  BEGIN
    DROP TABLE [#__proofreader_typos];
  END;