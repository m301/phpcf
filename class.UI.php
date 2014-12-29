<?PHP

class UI
{
    public static function printLoadingSrc()
    {

?>
    <div id="loading" class="box" style="width:300px;height:178px;padding-top:30px;display:none;" >
        <div class="circle" style=""></div>
        <div class="circle1"  style="top:-97px;left:-0px;height:75px;width:75px;"></div>
    	<div class="circle" style="top:-170px;left:-0px;height:50px;width:50px;-webkit-animation-direction:alternate;position:relative; -moz-animation: spinPulse 1s infinite ease-in-out;
        -webkit-animation: spinPulse 1s infinite linear;" ></div>
    	<span class="h11" style="position:relative;top:-120px;left:25px;font-size:x-large;">Seems page is loading...</span>
    </div>
<?PHP

    }
    
    public static function msgbox($text,$options='green'){
        if(!is_array($options)){
            echo "<div class='box' style='border-color:$options;'>$text</div>";
        }else{
            $classes='box ';
            $tag="div";
            $style='';
            if(array_key_exists('closable',$options))  $classes.="closable ";
            if(array_key_exists('style',$options)) $style=$options['style'];
            if(array_key_exists('href',$options)) {$href=$options['href'];$tag="a";}
            if(array_key_exists('color',$options)) $style.=";border-color:".$options['color'].";";
            $code  ="<$tag";
            $code .=' class="'.$classes.'"';
            $code .=(($style!='')?' style="'.$style.'"':'');
            $code .=(isset($href)?' href="'.$href.'"':'');
            $code .=(array_key_exists('onclick',$options)?' onclick="'.$options['onclick'].'"':'');
            $code .='>'.$text."</$tag>";
            echo $code;
        }
    }
}