<?php
/** Todo :
 * Option to print codes.
 * Add code samples
 * External libraries
 * Optimize page
 * Cache page/ Page section
 * Generate MD5
 * Expiry of cache
 * (ignore/), ignore line/section
 * folder override options.
 */
/**
 * Cons :
 * 1. You cannot flush the output.If you did template engine wont work properly.
 *    Suggested method is to use ajax. To send unbuffered output.
 *
 * Pros :
 * 1. Type less do more :)
 *
 */
/**
 * Call this function at beginning of page
 * Starts output buffer
 */
function _begin(){
    ob_start();
}

/**
 * At end of page.
 * Gets the buffer data. Send it fro processing. Cleans the buffer.
 *
 */
function _end(){
   $page=ob_get_contents();
   ob_end_clean() ;
   TE::processPage($page);
   return "";
}




//Short name template engine
/**
 * Define template engine
 *
 * This class has reduced usage of functions because of time taken in functions calls.
 * Makes template engine faster.
 *
 *
 * *********
 * Modes
 * *********
 * There can be modes.
 * 1. Debug mode
 *      Default mode. This shows all unknown tags.
 * 2. Secure Mode
 *      Hides all unknown tags & if content found inside them. They are also hidden
 * 3. Normal Mode.
 *      Just hides unknown tags. Content inside them is visible.
 *
 * A single tag can also force a mode by defining mode attribute
 * mode=1|2|3|D|S|N|Debug|Secure|Normal
 *
 * Or globally mode can be activated by
 * TE::setMode(1|2|3|D|S|N|Debug|Secure|Normal);
 *
 * **********
 * Tags
 * **********
 * * Container tag
 * (tag_name)   // tag name is between round brackets
 * (/tag_name)  // tag name end indication
 *
 * * Open tag
 * (tag_name/)  //Tag name
 *
 * *Defining attribute example
 * (tag_name attr1="Yoooo" attr2='Yooooo'/)     //Just Like html
 * *
 */
class TE{
    public static function processPage($page){
        //Tag names
        //$tag = array(   'info.http_domain_name' => 'http://' . DOMAIN_NAME,
        //                'info.domain_name' => DOMAIN_NAME);

        //Short name
        //$tag['dn']=$tag['info.domain_name'];



        //Generate tree
        //A bare html parser
        //HTML as standard. Defining custom tags will put programmer in doubts.
        //Date/Time :  10-06-2014 15:00 UTC 5300
        $tree=new TE_TAG();
        $_buff=array(&$tree);

        for($i=0;$i<strlen($page);$i++){
            $c=count($_buff)-1;
            //store tag data in it
            if($page[$i]=="<"){
                $i++;
                if($page[$i]=='/'){
                    //At tag close...............
                }else {
                    $_buff[$c]->tag_name = '';
                    $_buff[$c]->attr = '';
                    $_buff[$c]->empty_tag = 0;


                    //get the tag name
                    while (!($page[$i] == ' ' || $page[$i] == '>'))
                        $_buff[$c]->tag_name .= $page[$i++];

                    //contains spaces probably it has attributes
                    if ($page[$i] == ' ')
                        while ($page[$i] != '>')
                            $_buff[$c]->attr .= $page[$i++];


                    //Check for />
                    if ($page[$i] == '>' && $page[$i - 1] == '/') {
                        $_buff[$c]->empty_tag = 1;
                        //at empty tag close here .................
                    }

                    echo $_buff[$c]->tag_name . "|" . $_buff[$c]->attr . '|' . $_buff[$c]->empty_tag . "\n";
                }
            }

        }


        //Parse tree


        //Echo processed page
        echo $page;
    }

    public static function setMode($mode){

    }
}

class TE_TAG{
    public $tag_name;

    //Contains inner data of tag if container tag
    public $inner;

    //Complete html data of tag
    public  $outer;

    //array of all child elements
    public  $child;

    //Array of attributes of element
    public  $attr;

    //Type of tag if empty or container
    public $empty_tag;
}



