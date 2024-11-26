This PHP Script will copy a clean version of all the files from a Microsoft Windows File History folder, removing the time stamps from the filenames

I recommend to use it on specific folder, not on the full backup if you have hundred thousands of files.

Just edit the PHP file to set the souce path (note the double backslash notation) in the $mainfolder variable.
Example: d:\\FileHistory\\folder

Files will be copied to an "output" folder in the same directory of the php script.
