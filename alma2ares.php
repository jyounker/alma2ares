<?php
//this file uses an undocumented API URL to match on ARES barcode, pull availability and display location, call#, and status (in/out), and display in ARES
//in ARES, have to call this PHP file using JS, and urlencode the barcode
$apikey = PUTYOURAPIKEYHERE
$barcode = $_GET['barcode']; //have to comment out for testing
if($barcode != '') { //no barcode passed from ARES, show text at bottom
        $url = "https://api-ca.hosted.exlibrisgroup.com/almaws/v1/items?apikey=$apikey&item_barcode=$barcode";
        $content = file_get_contents($url);
        $xml=simplexml_load_string($content) or die("Error: cannot create object");
        if ($xml->item_data->base_status < 1) { //if it's 0, it's checked out
                        $mms_id = $xml->bib_data->mms_id;
                        $url2 = "https://api-ca.hosted.exlibrisgroup.com/almaws/v1/bibs/$mms_id/loans?apikey=$apikey"; //status is a different URL
                        $content2 = file_get_contents($url2);
                        $xml2=simplexml_load_string($content2) or die("Error: cannot create object");
                        $due_date = $xml2->item_loan->due_date;
                        $item_status = "DUE BACK: ".date('Y-m-d G:i T', strtotime($due_date)); //Alma time is GMT, have to convert
        } else { //if it's 1, it's checked in
                        $item_status = $xml->item_data->base_status[0]['desc'];
        }
        $output = "<strong>Location: </strong>Main floor of the Library - " . strtoupper($xml->holding_data->temp_policy) . "<br /><strong>Call Number: </strong>" .$xml->holding_data->call_number. "<br /><strong>Status: </strong>" .$item_status;
echo $output;
} else {
        echo "<strong>Inquire at the Ask Us Desk for availability</strong>";
}
?>

