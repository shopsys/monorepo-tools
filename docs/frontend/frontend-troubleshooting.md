# Frontend Troubleshooting
This document servers for most common bugs found during developing at Shopsys Framework.

## Icons from SVG generated document are not visible in Mozilla browsers properly
In case you access SVG icons placed in directory `project-base/docs/generated` with browsers from Mozilla you will not be able to see SVG icons properly.
### Solution
In order to show SVG icons, it is strongly recommended access website with different browser. If you persist and you want to access websites with Mozilla browsers, you will have to do following steps.
1. Type to URL bar `about:config`.
1. Find in list `security.fileuri.strict_origin_policy`
1. Set the value to `false`

After you leave website we highly recommend to set the value of `security.fileuri.strict_origin_policy` back to `true`.
