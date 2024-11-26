<?php

$mainFolder = "d:\\farenga\\faren\\FARENGA\\Data\\C\\work\\";
if (!file_exists("output")) mkdir("output");

//Create a list of all the folders
$folders = array();
getFolders($mainFolder);

function getFolders($folder) {
    global $folders;
    global $mainFolder;
    $f = scandir($folder);

    foreach ($f as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (is_dir($folder.$file)) {
            getFolders($folder.$file."\\");
            //$folders[] = str_replace($mainFolder, "", $folder.$file);
            $folders[] = $folder.$file;
        } else {
            //$files[] = $folder.$file;
        }
    }    
}

processFolder($mainFolder);
foreach ($folders as $folder) {
    processFolder($folder);
}

function processFolder($folder) {
global $mainFolder;
    $files = scandir($folder);
    
    //Create an array of all the files and dates
    $foundfiles = array();

    foreach ($files as $file) {

        //Check if the file has an UTC time at the end like this: Reminder (2024_11_12 17_18_14 UTC).html
        if (preg_match('/\(\d{4}_\d{2}_\d{2} \d{2}_\d{2}_\d{2} UTC\)/', $file)) {
            //Extract the date from the file name
            preg_match('/\d{4}_\d{2}_\d{2} \d{2}_\d{2}_\d{2}/', $file, $matches);
            $date = $matches[0];
            $date = str_replace('_', '', $date);
            $date = str_replace(' ', '', $date);
            
            //get the filename without extension
            $filename = pathinfo($file, PATHINFO_FILENAME);

            //get the extension
            $extension = pathinfo($file, PATHINFO_EXTENSION);

            //Get also the original string date
            $originalDate = substr($filename, strlen($filename)-26, 26);

            //remove the original date from the filename
            $filename = str_replace($originalDate, '', $filename);

            //echo $filename.".".$extension."\n";
        
            $obj = new stdClass();
            if ($extension!="") $obj->filename = $filename.".".$extension;
			else $obj->filename = $filename;
            $obj->date = $date;
            $obj->originalFile = $file;
            $foundfiles[] = $obj;

        }
    }

    $goodFiles = array();
    //relist files avoiding duplicates
    foreach ($foundfiles as $file) {

        //check if the file is already in the array
        $found = false;
        foreach ($goodFiles as $goodFile) {
            if ($goodFile->filename == $file->filename) {
                //check if the date is more recent
                if ($file->date > $goodFile->date) {
                    $found = true;
                    //remove the old file
                    echo "Removing ".$goodFile->originalFile."\n";
    //               print_r($goodFiles);
    //               echo "\n";
                    $goodFiles = array_filter($goodFiles, function($f) use ($goodFile) {
                        return $f->filename != $goodFile->filename;
                    });
    //               print_r($goodFiles);
    //               echo "\n";
    //               exit;
                    $goodFiles[] = $file;
                }// else $goodFiles[] = $file;
            }
        }
        if (!$found) {
            $goodFiles[] = $file;
        }
    }

    //print_r($goodFiles);
    //exit;

    $outfolder = str_replace($mainFolder, "", $folder)."\\";
    //make the folder
    if (!file_exists("output\\".$outfolder)) {
        mkdir("output\\".$outfolder, 0777, true);
    }

    //copy the good files to the output folder
    foreach ($goodFiles as $file) {
        //echo "Copying ".$folder."\\".$file->originalFile." to output\\".$outfolder.$file->filename." \n";
        echo "+";
        //copy($folder."\\".$file->originalFile, "output\\xx");
		//echo "-output\\".$outfolder.$file->filename."-\n";
		copy($folder."\\".$file->originalFile, "output\\".$outfolder.$file->filename);
    }
}

echo "done!";
