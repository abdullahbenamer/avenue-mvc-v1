UPDATE quotations SET introduction = REPLACE(REPLACE(REPLACE1(introduction, '\\r\\n', '\n'), '\r\n', '\n'), '\r', '\n'), objectives = REPLACE(REPLACE(REPLACE(objectives, '\\r\\n', '\n'), '\r\n', '\n'), '\r', '\n'), audiences = REPLACE(REPLACE(REPLACE(audiences, '\\r\\n', '\n'), '\r\n', '\n'), '\r', '\n'), outlines = REPLACE(REPLACE(REPLACE(outlines, '\\r\\n', '\n'), '\r\n', '\n'), '\r', '\n');

for more cleaned entries:

UPDATE quotations 
SET 
  introduction = REPLACE(REPLACE(REPLACE(REPLACE(introduction, '\\\\r\\\\n', '\n'), '\\\\n', '\n'), '\\\\r', '\n'), '\\\\', ''),
  objectives   = REPLACE(REPLACE(REPLACE(REPLACE(objectives,   '\\\\r\\\\n', '\n'), '\\\\n', '\n'), '\\\\r', '\n'), '\\\\', ''),
  audiences    = REPLACE(REPLACE(REPLACE(REPLACE(audiences,    '\\\\r\\\\n', '\n'), '\\\\n', '\n'), '\\\\r', '\n'), '\\\\', ''),
  outlines     = REPLACE(REPLACE(REPLACE(REPLACE(outlines,     '\\\\r\\\\n', '\n'), '\\\\n', '\n'), '\\\\r', '\n'), '\\\\', '');
