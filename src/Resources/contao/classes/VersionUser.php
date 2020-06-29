<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Versions;


use HeimrichHannot\Haste\Security\CodeGenerator;

class VersionUser
{
	const VERSION_USER_NAME  = 'contao-versions-robot';
	const VERSION_USER_EMAIL = 'robot@contao-versions.local';

	/**
	 * Object instance (Singleton)
	 *
	 * @var VersionUser
	 */
	protected static $objInstance;


	/**
	 * The user data
	 *
	 * @var
	 */
	protected $arrData;

	/**
	 * Prevent direct instantiation (Singleton)
	 *
	 */
	protected function __construct()
	{
		if (($objUser = \UserModel::findByUsername(static::VERSION_USER_EMAIL)) === null)
		{
			$objUser           = new \UserModel();
			$objUser->username = $objUser->email = static::VERSION_USER_EMAIL;
			$objUser->name     = static::VERSION_USER_NAME;
			// at least something must be in there
			$objUser->password  = md5(rand(0, 999999999));
			$objUser->disable   = true;
			$objUser->dateAdded = $objUser->tstamp = time();
			$objUser->save();
		}

		$this->arrData = $objUser->row();
	}


	/**
	 * Prevent cloning of the object (Singleton)
	 *
	 * @deprecated Environment is now a static class
	 */
	final public function __clone() { }


	/**
	 * Return the object instance (Singleton)
	 *
	 * @return VersionUser The object instance
	 *
	 */
	public static function getInstance()
	{
		if (static::$objInstance === null)
		{
			static::$objInstance = new static();
		}

		return static::$objInstance;
	}

	/**
	 * Set an object property
	 *
	 * @param string $strKey   The property name
	 * @param mixed  $varValue The property value
	 */
	public function __set($strKey, $varValue)
	{
		$this->arrData[$strKey] = $varValue;
	}


	/**
	 * Return an object property
	 *
	 * @param string $strKey The property name
	 *
	 * @return mixed The property value
	 */
	public function __get($strKey)
	{
		if (isset($this->arrData[$strKey]))
		{
			if (is_object($this->arrData[$strKey]) && is_callable($this->arrData[$strKey]))
			{
				return $this->arrData[$strKey]();
			}

			return $this->arrData[$strKey];
		}
	}

	/**
	 * Check whether a property is set
	 *
	 * @param string $strKey The property name
	 *
	 * @return boolean True if the property is set
	 */
	public function __isset($strKey)
	{
		return isset($this->arrData[$strKey]);
	}


	/**
	 * Return the user data as array
	 *
	 * @return array The data array
	 */
	public function getData()
	{
		return $this->arrData;
	}
}
