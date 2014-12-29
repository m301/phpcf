<?PHP
/**
 * Youtube
 *
 * @package default
 * @author Madhurendra Sachan
 * @copyright 2014 Madhurendra Sachan
 * @version v1.01
 * @access public
 */
 /**
 * Todo :
 * https://www.youtube.com/c4_browse_ajax?action_load_more_videos=1&paging=6&view=0&sort=dd&channel_id=UCK8sQmJBp8GCxrOtXWBpyEA&flow=grid&fluid=True
 */
include_once("class.DataHandler.php");
include_once("class.URLUtils.php");

class Youtube
{
    public static function getDownloadURLs($raw)
    {
        $list = array();

        //if url is sent
        if (URLValidator::validate(trim($raw))){
            echo "URL";
            $tmp='';
            $raw=explode("?",$raw);
            parse_str($raw[1],$tmp);
            $raw=$tmp['v'];
        }
        //if id is sent or raw is id
        if (strlen($raw) < 15) {
            $list['video_id']=trim($raw);
            $raw = DataHandler::getContent("http://www.youtube.com/get_video_info?&video_id=" . $raw);
        }
         //Thumbnailes
        $u_dn="http://img.youtube.com/vi/".$list['video_id']."/";
        $list['thumbnails']=array(
            'default'=>$u_dn."default.jpg",
            'hq'=>$u_dn."hqdefault.jpg",
            'mq'=>$u_dn."mqdefault.jpg",
            'sd'=>$u_dn."sddefault.jpg",
            'max'=>$u_dn."maxresdefault.jpg",
            '0'=>$u_dn."0.jpg",
            '1'=>$u_dn."1.jpg",
            '2'=>$u_dn."2.jpg",
            '3'=>$u_dn."3.jpg");

        $data='';
        parse_str($raw,$data);
        //Streamable URLs
        $raw=array();
        $tmp=explode(",",$data['url_encoded_fmt_stream_map']);
        for($i=0;$i<count($tmp);$i++)
            parse_str($tmp[$i], $raw[$i]);
        $list['streams']=$raw;

        //Downloadable URLs,Only video or audio
        $raw=array();
        $tmp=explode(",",$data['adaptive_fmts']);
        for($i=0;$i<count($tmp);$i++)
            parse_str($tmp[$i], $raw[$i]);
        $list['download']=$raw;
        $list['title']=$data['title'];
        $list['views']=$data['view_count'];
        $list['length']=$data['length_seconds'];
        $list['keywords']=$data['keywords'];
        //print_r($data);
        return $list;
    }
}

//Example code
$d=Youtube::getDownloadURLs($_POST['url']);

//print_r($d);
//Example of using custom name.For better compatibility use your own server.
//Supported browsers : http://caniuse.com/#feat=download
foreach($d['streams'] as $sd)
    echo "<a href='".$sd['url']."' download='".$d['title']."'>Download ".$sd['quality']." ( ".$sd['type'].")</a><br/>";

?>
<form method="post">
    <input name="url"/>
    <input type="Submit" value="GET"/>
</form>

