<?php
/**
 * Class SScan_File
 * @todo �ļ���������
 */
class SScan_File {
	private static $ls = array();

	/**
	 * list dirs and files
	 *
	 * @param string $path      ·��
	 * @param int $deeplimit    ���Ƶݹ����
	 * @param int $deep         ��ȼ���
	 * @return array
	 */
	public static function dir($path, $deeplimit=0, $deep=0) {
		$deep   += 1;
		if ($deeplimit != 0 && $deep > $deeplimit) {
			return array();
		}
		$name	= basename($path);
		$dir	= dir($path);
		$ls		= array();
		$ls[$name] = array();

		while ($item = $dir->read()) {
			$tmp = $path. DIRECTORY_SEPARATOR .$item;
			if (is_file($tmp)) {
				$ls[$name][] = $item;
			} else {
				if ($item != '.' && $item != '..') {
					$ls[$name] = array_merge($ls[$name], self::dir($tmp, $deeplimit, $deep));
				}
			}
		}
		return $deep === 1 ? $ls[$name] : $ls;
	}

	/**
	 * list all files in a dir using separator _
	 * @param $path             ����Ŀ¼
	 * @param int $deeplimit    ����������ƣ���1��ʼ����
	 * @param int $deep         >= 0
	 * @param string $fullpath  ���ԣ������ѱ�������·����Ϣ
	 * @return array һά����
	 */
	public static function filelist($path, $deeplimit=0, $deep=0, $fullpath='') {
		if ($deep == 0) {
			self::$ls = array();
			$fullpath = $fullpath ? $fullpath : basename($path);
		}

		$deep   += 1;
		if ($deeplimit != 0 && $deep > $deeplimit) {
			return null;
		}
		$dir	= dir($path);

		while ($item = $dir->read()) {
			$tmp = $path. DIRECTORY_SEPARATOR .$item;
			if (is_file($tmp)) {
				self::$ls[] = $fullpath.'_'.$item;
			} else {
				if ($item != '.' && $item != '..') {
					self::filelist($tmp, $deeplimit, $deep, $fullpath.'_'.$item);
				}
			}
		}
		return self::$ls;
	}
}