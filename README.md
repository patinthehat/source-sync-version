## source version synchronizer
---

If you are working on a medium-to-large project, you may have
`@version` tags in the header of many or all of the source files.
You also may have something like `const VERSION = '1.2';`.  

This utility takes care of keeping the version synchronized across all
source files. 

---

### Usage:  

Place a text file named `VERSION` in your project directory.  The contents
should be only the new version to be updated to, i.e. _"1.7.9"  (no quotes)_.
- or -
`$ printf "1.0.0" > VERSION`

If using v0.1.1, copy the php file into your project directory, edit the `$opts`
configuration, and when ready run from the command line.


_NOTE: during the conversion from v0.1.1, you will have to manually edit the source 
paths in your project directory, i.e., 'src/' or 'tests/'.  It works recursively._

