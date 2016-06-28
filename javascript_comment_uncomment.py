#! /usr/bin/env python
import sys
import StringIO
import re
block = sys.stdin.read()
block = StringIO.StringIO(block)
msg = ''
for line in block:
	if "//~" in line:
		line = re.sub(r"(\s*)(\S.*)", r"\1//~\2", line)
		line = line.replace('//~','')
		msg = "All lines in selection uncommented"
	else:
		line = re.sub(r"(\s*)(\S.*)", r"\1//~\2", line)
		## line = "//~" + line
		msg = "All lines in selection commented"
	sys.stdout.write(line)
exit(msg)
