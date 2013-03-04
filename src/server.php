<?php
/**
 * Copyright (c) 2012 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace SMB;

class Server {
	const CLIENT = 'smbclient';
	const LOCALE = 'en_US.UTF-8';

	const CACHING_ENABLED = true;
	const CACHING_DISABLED = false;

	/**
	 * @var string $host
	 */
	private $host;

	/**
	 * @var string $user
	 */
	private $user;

	/**
	 * @var string $password
	 */
	private $password;

	/**
	 * @var bool $caching
	 */
	private $caching;

	/**
	 * @param string $host
	 * @param string $user
	 * @param string $password
	 */
	public function __construct($host, $user, $password, $caching = self::CACHING_ENABLED) {
		$this->host = $host;
		$this->user = $user;
		$this->password = $password;
		$this->caching = $caching;
	}

	/**
	 * @return string
	 */
	public function getAuthString() {
		return $this->user . '%' . $this->password;
	}

	/**
	 * @return string
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * return string
	 */
	public function getHost() {
		return $this->host;
	}

	/**
	 * @return Share[]
	 */
	public function listShares() {
		$cmd = new Command\ListShares($this);
		$shareNames = $cmd->run(null);
		$shares = array();
		foreach ($shareNames as $name => $description) {
			$shares[] = $this->getShare($name);
		}
		return $shares;
	}

	/**
	 * @param string $name
	 * @return Share
	 */
	public function getShare($name) {
		if ($this->caching === self::CACHING_ENABLED) {
			return new CachingShare($this, $name);
		} else {
			return new Share($this, $name);
		}
	}
}