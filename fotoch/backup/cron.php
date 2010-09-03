<?php
error_reporting(!(E_ALL));
if ($_REQUEST['password']!='piruwaki') exit;

//echo getcwd();
//passthru("dir >ccc");
$time	= date('Ymd_His');
$fn='foto-ch-'.$time.'.sql';
//passthru('dir >acc');
//passthru('"D:/MySQL/MySQL Server 5.1/bin/mysqldump" --help');
passthru('"D:/MySQL/MySQL Server 5.1/bin/mysqldump" --host=localhost --user=fotobern --password=fotobern2006! --lock-tables=FALSE fotobe >'.$fn);
passthru('"D:/7-Zip/7z.exe" a '.$fn.'.zip '.$fn);
passthru('del '.$fn);
//passthru('D:/MySQL/MySQL Server 5.1/bin/mysqldump.exe --user=fotobern --password=fotobern2006! fotobe'); a -r -tgzip c:\backupordner\datenbank.sql.gz
//passthru('dir >bcc');
//echo "ende";
//$fn="foto-ch-20100818_160749.sql.zip";
$to      = "markus.schuerpf@foto-ch.ch";
//$to      = "chrigu@lorraine.ch";
$email   = "backup@catatec.ch";
$name    = "backup";
$subject = "Foto-CH backup";
$comment = "das automatische Backup der Datenbank";

$To          = strip_tags($to);
$TextMessage =strip_tags(nl2br($comment),"<br>");
$HTMLMessage =nl2br($comment);
$FromName    =strip_tags($name);
$FromEmail   =strip_tags($email);
$Subject     =strip_tags($subject);

$boundary1   =rand(0,9)."-"
.rand(10000000000,9999999999)."-"
.rand(10000000000,9999999999)."=:"
.rand(10000,99999);
$boundary2   =rand(0,9)."-".rand(10000000000,9999999999)."-"
.rand(10000000000,9999999999)."=:"
.rand(10000,99999);

 
    
$attach      ='yes';
$end         ='';

   $handle      =fopen($fn.'.zip', 'rb');
   $f_contents  =fread($handle, filesize( $fn.'.zip' ));
   $attachment=chunk_split(base64_encode($f_contents));
   fclose($handle);

$ftype       ='application/zip';
$fname       =$fn.'.zip';



$attachments='';
$Headers     =<<<AKAM
From: $FromName <$FromEmail>
Reply-To: $FromEmail
MIME-Version: 1.0
Content-Type: multipart/mixed;
    boundary="$boundary1"
AKAM;

$attachments.=<<<ATTA
--$boundary1
Content-Type: $ftype;
    name="$fname"
Content-Transfer-Encoding: base64
Content-Disposition: attachment;
    filename="$fname"

$attachment

ATTA;


$Body        =<<<AKAM
This is a multi-part message in MIME format.

--$boundary1
Content-Type: multipart/alternative;
    boundary="$boundary2"

--$boundary2
Content-Type: text/plain;
    charset="windows-1256"
Content-Transfer-Encoding: quoted-printable

$TextMessage
--$boundary2
Content-Type: text/html;
    charset="windows-1256"
Content-Transfer-Encoding: quoted-printable

$HTMLMessage

--$boundary2--

$attachments
--$boundary1--
AKAM;


/***************************************************************
 Sending Email
 ***************************************************************/
$ok=mail($To, $Subject, $Body, $Headers);
passthru('del '.$fn.'.zip');
echo $ok?"<h1> Mail Sent</h1>":"<h1> Mail not SEND</h1>";
?> 
