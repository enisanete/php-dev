<?php
class SScan {
	/**
	 * �洢��ͷ����б�
	 * @var array[]
	 */
	protected static $list = array();

	/**
	 * ���� rule ����Ŀ¼�е���ͷ���
	 *
	 * @todo ��ͬ��dir����ͬ��rule�Ĵ����ٶȺ�������Ҫ��cache
	 *
	 * @param $dir
	 * @param array $rule
	 * @return array
	 * @throws SException
	 */
	public static function classes($dir, array $rule=array()) {
		if (!is_dir($dir)) {
			throw new SException('Not a directory: '.$dir, -1);
		}

		if (isset(self::$list[$dir])) {
			return self::$list[$dir];
		}
		self::$list[$dir] = array();

		// Ŀ¼���ļ��б�
		$ls		= SScan_File::filelist($dir);

		//$php = shell_exec('/usr/bin/which php');
		if (is_array($ls)) {
			foreach($ls as $v) {
				// �����ٶ�̫���������﷨��飬���п���һ���ļ����ִ�����ִ��ʧ��
				//$output = shell_exec($php.' -l '.CONTROLLER_ROOT.'/'.$v);
				//if (preg_match('/Errors parsing.*$/', '', $output)) {
				//	continue;
				//}

				$tmp = SScan_Reflecation::classes($v, $rule);
				// ���˷ǿ�����
				if (is_array($tmp)) {
					self::$list[$dir] += $tmp;
				}
			}
		}

		return self::$list[$dir];
	}

}
