<?php

/* **************************************************************
 *  File: Router.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

class Router
{
	protected $_requestUri;

	const SCHEME_HTTP  = 'http';
	const SCHEME_HTTPS = 'https';

//	private $_controller_dir = 'controller';

	protected $_urlDelimiter = '/';

	public $_isTranslated = false;

	private $controller = null;

	private $_variables = array();


    /**
     * Translatable variables
     *
     * @var array
     */
    protected $_translatable = array();

    /**
     * Translator
     *
     * @var Zend_Translate
     */
    protected $_translator;






	public function __construct()
	{
		$this->getRequestUri();

		$this->Build($this->_requestUri);

		//first is controller

		//other url parameters (.../foo/bar/...) are value pairs in the form of foo=bar




	}


/**
* Frontend Router
*
* @access	public
*/
	public function controller(){

		//global $translate;


		//example routes
		//model:
			//http://domain.com/model/slug/
		//video
			//http://domain.com/video/slug/
		//pics
			//http://domain.com/set/slug/
		//pictures
			//http://domain.com/photos/page/XXX/sort/YYY/
		//videos
			//http://domain.com/videos/page/XXX/sort/YYY/
		//about and other pages
			//http://domain.com/about/
		//members (create special route for members directory to allow subdomain)
			//http://domain.com/members/

		//common requests from mobile phones
			//http://domain.com/doc(/)?
			//http://domain.com/mobi(/)?
			//http://domain.com/m(/)?
			//http://domain.com/iphone(/)?
			//http://domain.com/ipod(/)?
			//http://domain.com/pda(/)?

		global $tpl, $translate;

		$controller = $this->getController();

		if($controller)
			$controller = $tpl->getTemplateDir().DIR_SEP._controller_dir.DIR_SEP.$controller.'.php';
		else
			$controller = $tpl->getTemplateDir().DIR_SEP._controller_dir.DIR_SEP.$tpl->templatename.'.php';

		if(file_exists($controller))
			include_once $controller;
		else
			require_once $tpl->getTemplateDir().DIR_SEP._controller_dir.DIR_SEP."_404.php";

	} //controller

	public function getController()
	{
		return $this->controller;
	}

	public function GetControllerDir()
	{
		return _controller_dir;
	}


    /**
     * Returns the REQUEST_URI taking into account
     * platform differences between Apache and IIS
     *
     * @return string
     */
	public function getRequestUri()
	{
		if (empty($this->_requestUri)) {
		    $this->setRequestUri();
		}

		return $this->_requestUri;
	}

    /**
     * Set the REQUEST_URI on which the instance operates
     *
     * If no request URI is passed, uses the value in $_SERVER['REQUEST_URI'],
     * $_SERVER['HTTP_X_REWRITE_URL'], or $_SERVER['ORIG_PATH_INFO'] + $_SERVER['QUERY_STRING'].
     *
     * @param string $requestUri
     * @return Zend_Controller_Request_Http
     */
    public function setRequestUri($requestUri = null)
    {
        if ($requestUri === null) {
            if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
                $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
            } elseif (
                // IIS7 with URL Rewrite: make sure we get the unencoded url (double slash problem)
                isset($_SERVER['IIS_WasUrlRewritten'])
                && $_SERVER['IIS_WasUrlRewritten'] == '1'
                && isset($_SERVER['UNENCODED_URL'])
                && $_SERVER['UNENCODED_URL'] != ''
                ) {
                $requestUri = $_SERVER['UNENCODED_URL'];
            } elseif (isset($_SERVER['REQUEST_URI'])) {
                $requestUri = $_SERVER['REQUEST_URI'];
                // Http proxy reqs setup request uri with scheme and host [and port] + the url path, only use url path
                $schemeAndHttpHost = $this->getScheme() . '://' . $this->getHttpHost();
                if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                    $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
                }
            } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
                $requestUri = $_SERVER['ORIG_PATH_INFO'];
                if (!empty($_SERVER['QUERY_STRING'])) {
                    $requestUri .= '?' . $_SERVER['QUERY_STRING'];
                }
            } else {
                return null;
            }
        } elseif (!is_string($requestUri)) {
            return null;
        } else {
            // Set GET items, if available
            if (false !== ($pos = strpos($requestUri, '?'))) {
                // Get key => value pairs and set $_GET
                $query = substr($requestUri, $pos + 1);
                parse_str($query, $vars);
                $this->setQuery($vars);
            }
        }

        $this->_requestUri = $requestUri;
        return $this->_requestUri;
    }


	//fill the $_GET Array with the request variables
    private function Build($path)
    {






	    //todo
        if ($this->_isTranslated) {
            $translateMessages = $this->getTranslator()->getMessages();
        }









        $pathStaticCount = 0;
        $values          = array();
        $matchedPath     = '';

	$path = trim($path, $this->_urlDelimiter);

        if ($path !== '')
        {
		//check if there are regular request variables
		$path_check = explode("?", $path);
		//discard the part with the regular variables
		if(count($path_check) > 1) $path = $path_check[0];

            $path = explode($this->_urlDelimiter, $path);

		$this->controller = $_GET['controller'] = Input::clean($path[0], "FILENAME");

		//important -> the other $_GET variables still need to be validated manually
		//(using for example Input::clean_array())

		$num_args = count($path);
		if($num_args === 1)
			return;

		$skip_next = false;
		for($pos = 1; $pos < $num_args; $pos++)
		{
			if($skip_next == true)
			{
				$skip_next = false;
				continue;
			}

			$var = $this->_variables[$pos] = urldecode($path[$pos]);

			if(isset($path[$pos+1]))
			{
				$_GET[$var] = urldecode($path[$pos+1]);
				$skip_next = true;
				continue;
			}
			else
			{
				//empty get used for example for success messages
				//example: http:://domain.com/join/success (= domain.com?success)
				$_GET[$var] = '';
				//also register the empty request as 'slug'
				$_GET['slug'] = $var;
				$_GET['id']   = $var;
			}


			// Translate query variable if required
			if (1==2 && $this->_isTranslated && $var !== null && in_array($var, $this->_translatable)) {
			    if (($partpos = array_search($pathPart, $translateMessages)) !== false) {
				$var = 'xxx';

//todo







			    }
			}

		} //for loop


        }

    }












    /**
     * Get the translator
     *
     * @throws Zend_Controller_Router_Exception When no translator can be found
     * @return Zend_Translate
     */
    public function getTranslator()
    {
        if ($this->_translator !== null) {
            return $this->_translator;
        } else if (($translator = self::getDefaultTranslator()) !== null) {
            return $translator;
        } else {
            try {
                $translator = Zend_Registry::get('Zend_Translate');
            } catch (Zend_Exception $e) {
                $translator = null;
            }

            if ($translator instanceof Zend_Translate) {
                return $translator;
            }
        }

        require_once 'Zend/Controller/Router/Exception.php';
        throw new Zend_Controller_Router_Exception('Could not find a translator');
    }

    /**
     * Get the HTTP host.
     *
     * "Host" ":" host [ ":" port ] ; Section 3.2.2
     * Note the HTTP Host header is not the same as the URI host.
     * It includes the port while the URI host doesn't.
     *
     * @return string
     */
    public function getHttpHost()
    {
        $host = $_SERVER['HTTP_HOST'];
        if (!empty($host)) {
            return $host;
        }

        $scheme = $this->getScheme();
        $name   = $this->getServer('SERVER_NAME');
        $port   = $this->getServer('SERVER_PORT');

        if (($scheme == self::SCHEME_HTTP && $port == 80) || ($scheme == self::SCHEME_HTTPS && $port == 443)) {
            return $name;
        } else {
            return $name . ':' . $port;
        }
    }

    /**
     * Get the request URI scheme
     *
     * @return string
     */
    public function getScheme()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? self::SCHEME_HTTPS : self::SCHEME_HTTP;
    }

} //Router