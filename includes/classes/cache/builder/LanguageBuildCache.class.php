<?php

/**
 *  2Moons
 *  Copyright (C) 2012 Jan Kr�pke
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package 2Moons
 * @author Jan Kr�pke <info@2moons.cc>
 * @copyright 2012 Jan Kr�pke <info@2moons.cc>
 * @license http://www.gnu.org/licenses/gpl.html GNU GPLv3 License
 * @version 1.7.2 (2013-03-18)
 * @info $Id$
 * @link http://2moons.cc/
 */

class LanguageBuildCache implements BuildCache
{
	public function buildCache()
	{
		$languages	= array();
		/** @var $fileInfo SplFileObject */
		foreach (new DirectoryIterator('language/') as $fileInfo)
		{
			if(!$fileInfo->isDir()) continue;

			$Lang	= $fileInfo->getBasename();

			if(!file_exists('language/'.$Lang.'/LANG.cfg')) continue;

			// Fixed BOM problems.
			ob_start();
			$path	 = 'language/'.$Lang.'/LANG.cfg';
			require $path;
			ob_end_clean();
			if(!isset($Language['name']))
			{
				$languages[$Lang]	= $Language['name'];
			}
		}
		return $languages;
	}
}