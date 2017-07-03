<?PHP
/******************************************
 * Todo:
 * public function rearrangeStyle()
 * public function rearrangeScript()
 * public function compressStyle()
 * public function compressScript()
 * public function optimizePage($options)
 *
 * CDN : <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
 *******************************************/

include_once("config.php");

class Template
{
    var $VIEW_TYPES = array(
        'desktop' => 0,
        'desktop_basic_html' => 1,
        'mobile' => 2,
        'mobile_basic_html' => 3,
        'ajax' => 4,
        'iframe' => 5);
    private $head = '', $body = '';
    private $info, $isVisible, $settings, $constants, $includeRoot;
    private $menus = array();

    //Constants for view type
    private $incStyle = array(), $incScript = array();

    //eval()

    function __construct($options)
    {
        //Page Constant Variable :)

        if(!array_key_exists('disable_mobile_site', $options))
            $this->mobileCheck();
        $this->includeRoot = 'template/';
        $this->constants = array('pre_tag' => '(?',
            'post_tag' => '/)');
        $this->info = array(
            'domain_name' => DOMAIN_NAME,
            'page_title' => $this->processData($options['page_title'], "str", ''),
            'pre_title' => $this->processData($_SESSION['template.pre_title'],'str',''),
            'post_title' =>$this->processData($_SESSION['template.post_title'],'str',''),
            'pre_body' => '',
            'post_body' => '',
            'pre_head' => '',
            'post_head' => '',
            'meta' => (array_key_exists('meta', $options) ? array_merge(array('description' => $options['page_title'],
                    'keyword' => $options['page_title'],
                    'robot' => 'index,follow',
                    'revisit-after' => '1'), $options['meta']) : array()));

        //Settings
        $this->settings = array(
            'page_compression' => $this->processOptions('page_compression', $options),
            'view_type' => 0,
            'no_append_title' => $this->processOptions('no_append_title', $options, false),
            'title_section'=> $this->processOptions('title_section_enabled', $options, true),
            'navbar_enabled'=> $this->processOptions('navbar_enabled', $options, false)
        );
        //Visible elements
        $this->isVisible = array(
            'sidebar' => $this->processOptions('sidebar', $options),
            'topbar' => $this->processOptions('topbar', $options),
            'navbar' => $this->processOptions('navbar', $options),
            'footer' => $this->processOptions('footer', $options)
        );

        $this->menus['sidebar'] = array();
        $this->menus['topbar'] = array();
        $this->menus['navbar'] = array();
        $this->menus['footer'] = array();
    }

    public function beginHead()
    {
        ob_start();
    }

    public function beginBody()
    {
        $this->head = ob_get_contents();
        ob_clean();
        //ob_flush(),sends to output
        ob_start();
    }

    public function printPage()
    {
        $this->body = ob_get_contents();
        ob_clean();

        //Echo the template
        $this->processTemplate();
    }

    public function setPageTitle($data, $options = array())
    {
        $this->info->page_title = $this->processData($data, "str", '');
        if (isset($options['noappend']))
            $this->settings->no_append_title = $this->processData($options['override'],
                "bool", false);

    }

    /*
    public function findAndReplace($to,$from,$options=array())
    {
    
    }
    */

    public function includeLib($to, $options = array())
    {
        include($this->includeRoot . 'libraries.php');
    }

    public function includeScript($to, $options = array())
    {
        /* $source=array('jquery'=>array('ver'=> array('2.1.0', '2.0.3', '2.0.2', '2.0.1', '2.0.0', '1.11.0', '1.10.2', '1.10.1', '1.10.0', '1.9.1', '1.9.0', '1.8.3', '1.8.2', '1.8.1', '1.8.0', '1.7.2', '1.7.1', '1.7.0', '1.6.4', '1.6.3', '1.6.2', '1.6.1', '1.6.0', '1.5.2', '1.5.1', '1.5.0', '1.4.4', '1.4.3', '1.4.2', '1.4.1', '1.4.0', '1.3.2', '1.3.1', '1.3.0', '1.2.6', '1.2.3'),
        'google'=>array('pre'=>'//ajax.googleapis.com/ajax/libs/jquery/',
        'post'=>'/jquery.min.js'),
        ''));

        $source=array(  'jquery'=>array('google'=>'//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js'),
        'jquery-ui'=>array('google'=>));
        $count=count($to);
        if($count==0)
        $to=array($to);
        */
        $this->incScript[] = $to;

    }

    public function includeStyle($to, $options = array())
    {
        $this->incStyle[] = $to;
    }

    public function redirectTo($url, $options = array())
    {
        $code = (isset($options['permanent']) ? 301 : 302);
        if (isset($options['staus_code']))
            $code = $options['staus_code'];

        if ($code == "303")
            header('HTTP/1.1 303 See Other');

        if (isset($options['time']))
            header('Refresh: ' . $options['time'] . ';url=' . $url);
        else
            header('Location: ' . $url, true, $code);
        die();

    }

    public function sidebarMenuAdd($name, $href, $order = -1, $options = array())
    {
        if ($order == -1)
            $this->menus['sidebar'][] = array('name' => $name, 'href' => $href);
        else
            $this->menus['sidebar'][$order] = array('name' => $name, 'href' => $href);

    }

    /*
    Example array:
    array('About'=>array(   'href'=>'#',
    'onclick'=>'alert',
    '+style'=>'',
    'style'=> ''));


    */

    public function navbarAdd($name, $href, $order = -1, $options = array())
    {
        if ($order == -1)
            $this->menus['navbar'][] = array('name' => $name, 'href' => $href);
        else
            $this->menus['navbar'][$order] = array('name' => $name, 'href' => $href);
    }

    public function footerMenuAdd($menu = array())
    {
        $this->menus['footer'] = "<a href='" . DOMAIN_NAME . "'>Home</a> | <a href='/contactus.php'>Contact us</a> | <a href='/aboutus.php'>About us</a>";
    }

    /*
    Depreciated,Nearly constant 
    */

    public function topBarAdd($name, $href, $order = -1, $options = array())
    {
        if ($order == -1)
            $this->menus['topbar'][] = array('name' => $name, 'href' => $href);
        else
            $this->menus['topbar'][$order] = array('name' => $name, 'href' => $href);
    }

    public function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    public function buildCache()
    {

    }

    public function getCurrentPageURL()
    {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    /**
     * mobileCheck()
     *
     * Returns $_SESSION['ismobile'] which is assigned value of toMobile();
     *
     * @return integer
     */
    function mobileCheck()
    {
        $_SESSION['ismobile'] = $this->toMobile();
        return $_SESSION['ismobile'];
    }

    /**
     * toMobile()
     *
     * Returns 0 if use wants desktop site, 1 if use wants a mobile site
     * & redirects according to value return by isMobile();
     * appends & removes m before anysubdomain according to UA & variables with exception of www ;
     *
     * @return integer
     */
    function toMobile()
    {
        //no limitation
        $pieces = explode(".", $_SESSION['url']['host']);
        if ($this->isMobile()) {
            if ($pieces[0] == 'm') return 1; //if m subdomain found.Already on mobile site
            if ($pieces[0] == 'www') array_shift($pieces); //if subdomain is www.Remove www
            $this->redirectTo('http://m.' . implode($pieces, '.') . $_SESSION['request_uri'], array('status_code' => 303));
        } else if ($pieces[0] == 'm') {
            array_shift($pieces); //remove m from beginning
            //$this->redirectTo('http://'.implode($pieces,'.').$_SESSION['request_uri'],array('status_code'=>303));
        } else if ($pieces[0] != "www")

            return 0;
        //want a desktop site
    }

    /**
     * isMobile()
     *
     * Returns 0 if use wants desktop site, 1 if use wants a mobile site
     * & returns according to check in sessions variables ;
     * $_SESSION['wantMobile'] = 1 if user wants mobile site forcefully in desktop ;
     * $_SESSION['wantDesktop'] = 1 if user wants desktop site forcefully in mobile ;
     *
     * @return integer
     */
    function isMobile()
    {
        //if(session_status() == PHP_SESSION_NONE)session_start();
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (isset($_SESSION['wantDesktop']) && $_SESSION['wantDesktop'] == 1) return 0; //forcefully desktop site
        if (isset($_SESSION['wantMobile']) && $_SESSION['wantMobile'] == 1) return 1; //forcefully mobile site
        //@link http://detectmobilebrowsers.com/
        //Great regex :)
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4)))
            return 1;
        return 0;
    }

    private function processOptions($index, $options, $default = true, $type = "bool")
    {
        if (array_key_exists($index, $options))
            return $this->processData($options[$index], $type, $default);
        else
            return $default;
    }

    private function processTemplate()
    { //global $template_0;
        include($this->includeRoot . 'template.desktop.php');
        $template = $template_0;
        $meta = '';
        foreach ($this->info['meta'] as $key => $value)
            $meta .= '<META NAME="' . $key . '" CONTENT="' . $value . '">';

        //Content of title tag
        //defined : (?title/)
        $title = '';
        if ($this->settings['no_append_title'] == false)
            $title .= $this->info['pre_title'] . $this->info['page_title'] . $this->info['post_title'];
        else
            $title .= $this->info['page_title'];

        //Style tags
        //defined : (?head_end/)
        $style = '';
        foreach ($this->incStyle as $value)
            $style .= '<link href="' . $value . '" rel="stylesheet" type="text/css">';

        //Content of page title
        //defined : (?page_title/)
        $page_title = $this->info['page_title'] . $this->info['post_title'];

        //Navigation bar
        //defined : (?navbar/)
        $navbar = '';
        foreach ($this->menus['navbar'] as $value)
            $navbar .= " <a href='" . $value['href'] . "'>" . $value['name'] . "</a> &gt";
        $navbar = rtrim($navbar, "&gt");

        //Topmenu
        //defined : (?topbar/)
        $topbar = '';
        foreach ($this->menus['topbar'] as $value)
            $topbar .= "<div><a href='" . $value['href'] . "'>" . $value['name'] . "</a></div>";

        //Sidebar
        //defined : (?Side_menu/)
        $sidebar = '';
        foreach ($this->menus['sidebar'] as $value)
            $sidebar .= "<div><a href='" . $value['href'] . "'>" . $value['name'] . "</a></div>";

        //Page footer
        //defined : (?footer/)
        //$footer='<div id="footer">'.$this->menus['footer'] .'</div>';
        //$template=$this->replaceTag('footer',$footer,$template);
        //Content of title tag
        //defined : (?body_end/)
        $script = '';
        foreach ($this->incScript as $value)
            $script .= '<script type="text/javascript" src="' . $value . '"></script>';

        //Pre & post constant
        $preTag = $this->constants['pre_tag'];
        $postTag = $this->constants['post_tag'];
        //Main Constant
        $tag = array(
            'title_section'=>($this->settings['title_section'])? '<div id="title"> <h1>(?page_title/)</h1>'.(($this->settings['navbar_enabled'])?'<div id="navbar">(?navbar/)</div>':'').'</div>':'',
            'head_begin' => $this->info['pre_head'] . $meta,
            'title' => $title,
            'head_end' => $style . $this->head . $this->info['post_head'],
            'body_begin' => $this->info['pre_body'],
            'page_title' => $page_title,
            'navbar' => $navbar,
            'topbar' => $topbar,
            'sidebar' => $sidebar,
            'content' => $this->body,
            'body_end' => $this->info['post_body']
        );
        foreach ($tag as $tag_name => $value)
            $template = str_replace($preTag . $tag_name . $postTag, $value, $template);


        //Alias for Constant
        $tag = array('dn' => 'info.http_domain_name'
        );
        foreach ($tag as $tag_name => $value)
            $template = str_replace($preTag . $tag_name . $postTag, $preTag . $value . $postTag, $template);

        //Constant
        $tag = array('info.http_domain_name' => 'http://' . $this->info['domain_name'],
            'info.domain_name' => $this->info['domain_name']);

        foreach ($tag as $tag_name => $value)
            $template = str_replace($preTag . $tag_name . $postTag, $value, $template);

        echo $template;
    }

    private function pageCheckSum()
    {

    }

    private function log($string)
    {
        echo "Log :" . $string;
    }

    private function processData($data, $type, $default = 0, $options = array())
    {
        //return $data;
        $wasArray = 1;
        $eleCount = count($data);
        if (!is_array($data)) {
            $data = array($data);
            $wasArray = 0;
            $eleCount = 1;
        }

        //Sanitization as option
        $sanitize = (isset($options['sanitize']) ? null : 1);

        //Integer
        if (in_array($type, array(
            'int',
            'integer',
            'num',
            'number'))
        ) {

            $flag = null;
            if (isset($options['octal']))
                $flag = FILTER_FLAG_ALLOW_OCTAL;
            elseif (isset($options['hex']))
                $flag = FILTER_FLAG_ALLOW_HEX;
            for ($i = 0; $i < $eleCount; $i++) {
                if ($sanitize === null) {
                    if ($flag === null)
                        $data[$i] = filter_var($data[$i], FILTER_VALIDATE_INT, array('default' => $default));
                    else
                        $data[$i] = filter_var($data[$i], FILTER_VALIDATE_INT, $flag);
                } else {
                    $data[$i] = filter_var($data[$i], FILTER_SANITIZE_NUMBER_INT);
                }
            }

            //String
        } elseif (in_array($type, array('str', 'string'))) {
            //nocode :(

            //float
        } elseif (in_array($type, array('float', 'decimal'))) {

            $flag = null;
            if (isset($options['allow_fraction']))
                $flag = FILTER_FLAG_ALLOW_FRACTION;
            elseif (isset($options['allow_thousand']))
                $flag = FILTER_FLAG_ALLOW_THOUSAND;
            elseif (isset($options['allow_scientific']))
                $flag = FILTER_FLAG_ALLOW_SCIENTIFIC;

            for ($i = 0; $i < $eleCount; $i++) {
                if ($sanitize === null) {
                    if ($flag === null)
                        $data[$i] = filter_var($data[$i], FILTER_VALIDATE_FLOAT, array('default' => $default));
                    else
                        $data[$i] = filter_var($data[$i], FILTER_VALIDATE_FLOAT, $flag);
                } else {
                    $data[$i] = filter_var($data[$i], FILTER_SANITIZE_NUMBER_FLOAT, $flag);
                }
            }

            //email
        } elseif (in_array($type, array(
            'email',
            'e_mail',
            'e-mail'))
        ) {
            for ($i = 0; $i < $eleCount; $i++) {
                if ($sanitize === null)
                    $data[$i] = filter_var($data[$i], FILTER_VALIDATE_EMAIL, array('default' => $default));
                else
                    $data[$i] = filter_var($data[$i], FILTER_SANITIZE_EMAIL);

            }
        } elseif (in_array($type, array('bool', 'boolean'))) {
            for ($i = 0; $i < $eleCount; $i++) {
                if (isset($options['false_mode']))
                    $data[$i] = filter_var($data[$i], FILTER_VALIDATE_BOOLEAN,
                        FILTER_NULL_ON_FAILURE);
                else
                    $data[$i] = filter_var($data[$i], FILTER_VALIDATE_BOOLEAN, array('default' => $default));
            }
        }

        if ($wasArray == 0)
            return $data[0];
        return $data;
    }

    private function getBetweenTags($str, $begin, $end)
    {

    }

}
