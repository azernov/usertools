#!/usr/bin/php
<?

$processDir = array(
    //dirname(dirname(__FILE__)).'/elements/snippets',
    //dirname(dirname(__FILE__)).'/model',
    //dirname(dirname(__FILE__)).'/model/usertools/utformvalidation'
    //dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/assets/components/usertools'
    dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/_development/usertools'
);
$from = array('cs','carolesmokes','kc');
$to = array('ut','usertools','ut');


/**
 * @param $dirname
 * @param $filesPattern
 * @param $fileFunction - имя функции, принимающей на входе 1 параметр - полный путь к файлу
 */
function processDirectory($dirname, $filesPattern, $fileFunction)
{
    global $from,$to;
    $cDir = opendir($dirname);
    while($filename = readdir($cDir))
    {
        if(is_dir($dirname.'/'.$filename) && $filename != '.' && $filename != '..')
        {
            $filenameNew = str_replace($from,$to,$filename);
            rename($dirname.'/'.$filename,$dirname.'/'.$filenameNew);
            if($filenameNew != $filename)
            {
                echo $filename.' -> '.$filenameNew."\n";
            }
            processDirectory($dirname.'/'.$filenameNew,$filesPattern, $fileFunction);
        }
        elseif(is_file($dirname.'/'.$filename) && preg_match($filesPattern,$filename))
        {
            $filenameNew = str_replace($from,$to,$filename);
            rename($dirname.'/'.$filename,$dirname.'/'.$filenameNew);
            if($filenameNew != $filename)
            {
                echo $filename.' -> '.$filenameNew."\n";
            }
            /*$fileFunction($dirname.'/'.$filenameNew);
            echo 'Замена в файле :'.$filenameNew."\n";*/
        }
    }
    closedir($cDir);
}

function processFileFunction($filePath)
{
    global $from,$to;
    $content = file_get_contents($filePath);
    $content = str_replace($from,$to,$content);
    file_put_contents($filePath,$content);
}

foreach($processDir as $dir){
    processDirectory($dir,'#\.(php|js)$#i','processFileFunction');
}