<?php
/**
 * Mobile Detect Library    http://www.yunec.cn 云EC
 * @author      Current authors: Serban Ghita <serbanghita@gmail.com>
 *                               Nick Ilyin <nick.ilyin@gmail.com>
 *              Original author: Victor Stanciu <vic.stanciu@gmail.com>
 * @license     Code and contributions have 'MIT License'
 *              More details: https://github.com/serbanghita/Mobile-Detect/blob/master/LICENSE.txt
 * @link        Homepage:     http://mobiledetect.net
 *              GitHub Repo:  https://github.com/serbanghita/Mobile-Detect
 *              Google Code:  http://code.google.com/p/php-mobile-detect/
 *              README:       https://github.com/serbanghita/Mobile-Detect/blob/master/README.md
 *              HOWTO:        https://github.com/serbanghita/Mobile-Detect/wiki/Code-examples
 *
 * @version     2.8.22
 */

/*检测访问者设备*/
class Mobile_Detect
{
	const DETECTION_TYPE_MOBILE = 'mobile';
	const DETECTION_TYPE_EXTENDED = 'extended';
	const VER = '([\w._\+]+)';
	const VERSION = '2.8.22';
	const VERSION_TYPE_STRING = 'text';
	const VERSION_TYPE_FLOAT = 'float';
	protected $cache = array();
	protected $userAgent = null;
	protected $httpHeaders = array();
	protected $cloudfrontHeaders = array();
	protected $matchingRegex = null;
	protected $matchesArray = null;
	protected $detectionType = self::DETECTION_TYPE_MOBILE;

	protected static $mobileHeaders = array('HTTP_ACCEPT' => array('matches' => array('application/x-obml2d', 'application/vnd.rim.html', 'text/vnd.wap.wml', 'application/vnd.wap.xhtml+xml')), 'HTTP_X_WAP_PROFILE' => null, 'HTTP_X_WAP_CLIENTID' => null, 'HTTP_WAP_CONNECTION' => null, 'HTTP_PROFILE' => null, 'HTTP_X_OPERAMINI_PHONE_UA' => null, 'HTTP_X_NOKIA_GATEWAY_ID' => null, 'HTTP_X_ORANGE_ID' => null, 'HTTP_X_VODAFONE_3GPDPCONTEXT' => null, 'HTTP_X_HUAWEI_USERID' => null, 'HTTP_UA_OS' => null, 'HTTP_X_MOBILE_GATEWAY' => null, 'HTTP_X_ATT_DEVICEID' => null, 'HTTP_UA_CPU' => array('matches' => array('ARM')), );

	protected static $operatingSystems = array('AndroidOS' => 'Android', 'BlackBerryOS' => 'blackberry|\bBB10\b|rim tablet os', 'PalmOS' => 'PalmOS|avantgo|blazer|elaine|hiptop|palm|plucker|xiino', 'SymbianOS' => 'Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b', 'WindowsMobileOS' => 'Windows CE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|Window Mobile|Windows Phone [0-9.]+|WCE;', 'WindowsPhoneOS' => 'Windows Phone 10.0|Windows Phone 8.1|Windows Phone 8.0|Windows Phone OS|XBLWP7|ZuneWP7|Windows NT 6.[23]; ARM;', 'iOS' => '\biPhone.*Mobile|\biPod|\biPad', 'MeeGoOS' => 'MeeGo', 'MaemoOS' => 'Maemo', 'JavaOS' => 'J2ME/|\bMIDP\b|\bCLDC\b', 'webOS' => 'webOS|hpwOS', 'badaOS' => '\bBada\b', 'BREWOS' => 'BREW', );

	protected static $browsers = array('Chrome' => '\bCrMo\b|CriOS|Android.*Chrome/[.0-9]* (Mobile)?', 'Dolfin' => '\bDolfin\b', 'Opera' => 'Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR/[0-9.]+|Coast/[0-9.]+', 'Skyfire' => 'Skyfire', 'IE' => 'IEMobile|MSIEMobile', // |Trident/[.0-9]+
	'Firefox' => 'fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile', 'Bolt' => 'bolt', 'TeaShark' => 'teashark', 'Blazer' => 'Blazer', 'Safari' => 'Version.*Mobile.*Safari|Safari.*Mobile|MobileSafari', 'Tizen' => 'Tizen', 'UCBrowser' => 'UC.*Browser|UCWEB', 'baiduboxapp' => 'baiduboxapp', 'baidubrowser' => 'baidubrowser', 'DiigoBrowser' => 'DiigoBrowser', 'Puffin' => 'Puffin', 'Mercury' => '\bMercury\b', 'ObigoBrowser' => 'Obigo', 'NetFront' => 'NF-Browser', 'GenericBrowser' => 'NokiaBrowser|OviBrowser|OneBrowser|TwonkyBeamBrowser|SEMC.*Browser|FlyFlow|Minimo|NetFront|Novarra-Vision|MQQBrowser|MicroMessenger', 'PaleMoon' => 'Android.*PaleMoon|Mobile.*PaleMoon', );

	protected static $uaHttpHeaders = array('HTTP_USER_AGENT', 'HTTP_X_OPERAMINI_PHONE_UA', 'HTTP_X_DEVICE_USER_AGENT', 'HTTP_X_ORIGINAL_USER_AGENT', 'HTTP_X_SKYFIRE_PHONE', 'HTTP_X_BOLT_PHONE_UA', 'HTTP_DEVICE_STOCK_UA', 'HTTP_X_UCBROWSER_DEVICE_UA');

	public function __construct(array $headers = null, $userAgent = null)
	{
		$this -> setHttpHeaders($headers);
		$this -> setUserAgent($userAgent);
	}

	public function setHttpHeaders($httpHeaders = null)
	{
		if (!is_array($httpHeaders) || !count($httpHeaders))
		{
			$httpHeaders = $_SERVER;
		}

		$this -> httpHeaders = array();
		foreach ($httpHeaders as $key => $value)
		{
			if (substr($key, 0, 5) === 'HTTP_')
			{
				$this -> httpHeaders[$key] = $value;
			}
		}
	}

	public function getHttpHeaders()
	{
		return $this -> httpHeaders;
	}

	public function getHttpHeader($header)
	{
		if (strpos($header, '_') === false)
		{
			$header = str_replace('-', '_', $header);
			$header = strtoupper($header);
		}

		$altHeader = 'HTTP_' . $header;
		if (isset($this -> httpHeaders[$header]))
		{
			return $this -> httpHeaders[$header];
		}
		elseif (isset($this -> httpHeaders[$altHeader]))
		{
			return $this -> httpHeaders[$altHeader];
		}

		return null;
	}

	public function getMobileHeaders()
	{
		return self::$mobileHeaders;
	}

	public function getUaHttpHeaders()
	{
		return self::$uaHttpHeaders;
	}

	public function setUserAgent($userAgent = null)
	{
		$this -> cache = array();
		if (false === empty($userAgent))
		{
			return $this -> userAgent = $userAgent;
		}
		else
		{
			$this -> userAgent = null;
			foreach ($this->getUaHttpHeaders() as $altHeader)
			{
				if (false === empty($this -> httpHeaders[$altHeader]))
				{
					$this -> userAgent .= $this -> httpHeaders[$altHeader] . " ";
				}
			}

			if (!empty($this -> userAgent))
			{
				return $this -> userAgent = trim($this -> userAgent);
			}
		}

		return $this -> userAgent = null;
	}

	public function getUserAgent()
	{
		return $this -> userAgent;
	}

	public function setDetectionType($type = null)
	{
		if ($type === null)
		{
			$type = self::DETECTION_TYPE_MOBILE;
		}
		$this -> detectionType = $type;
	}

	public function getMatchingRegex()
	{
		return $this -> matchingRegex;
	}

	public function getMatchesArray()
	{
		return $this -> matchesArray;
	}

	public static function getUserAgents()
	{
		return self::getBrowsers();
	}

	public static function getBrowsers()
	{
		return self::$browsers;
	}

	public static function getMobileDetectionRules()
	{
		static $rules;
		if (!$rules)
		{
			$rules = array_merge(self::$operatingSystems, self::$browsers);
		}
		return $rules;
	}

	public function getRules()
	{
		return self::getMobileDetectionRules();
	}

	public function checkHttpHeadersForMobile()
	{
		foreach ($this->getMobileHeaders() as $mobileHeader => $matchType)
		{
			if (isset($this -> httpHeaders[$mobileHeader]))
			{
				if (is_array($matchType['matches']))
				{
					foreach ($matchType['matches'] as $_match)
					{
						if (strpos($this -> httpHeaders[$mobileHeader], $_match) !== false)
						{
							return true;
						}
					}
					return false;
				}
				else
				{
					return true;
				}
			}
		}
		return false;
	}

	public function __call($name, $arguments)
	{
		if (substr($name, 0, 2) !== 'is')
		{
			throw new BadMethodCallException("No such method exists: $name");
		}
		$this -> setDetectionType(self::DETECTION_TYPE_MOBILE);
		$key = substr($name, 2);
		return $this -> matchUAAgainstKey($key);
	}

	protected function matchDetectionRulesAgainstUA($userAgent = null)
	{
		foreach ($this->getRules() as $_regex)
		{
			if (empty($_regex))
			{
				continue;
			}

			if ($this -> match($_regex, $userAgent))
			{
				return true;
			}
		}
		return false;
	}

	/** Search for a certain key in the rules array.=*/
	protected function matchUAAgainstKey($key)
	{
		$key = strtolower($key);
		if (false === isset($this -> cache[$key]))
		{
			$_rules = array_change_key_case($this -> getRules());

			if (false === empty($_rules[$key]))
			{
				$this -> cache[$key] = $this -> match($_rules[$key]);
			}
			if (false === isset($this -> cache[$key]))
			{
				$this -> cache[$key] = false;
			}
		}

		return $this -> cache[$key];
	}

	/**检测是否手机*/
	public function isMobile()
	{
		$this -> setDetectionType(self::DETECTION_TYPE_MOBILE);

		if ($this -> checkHttpHeadersForMobile())
		{
			return true;
		}
		else
		{
			return $this -> matchDetectionRulesAgainstUA();
		}
	}

	public function match($regex, $userAgent = null)
	{
		$match = (bool) preg_match(sprintf('#%s#is', $regex), (false === empty($userAgent) ? $userAgent : $this -> userAgent), $matches);
		if ($match)
		{
			$this -> matchingRegex = $regex;
			$this -> matchesArray = $matches;
		}

		return $match;
	}

}