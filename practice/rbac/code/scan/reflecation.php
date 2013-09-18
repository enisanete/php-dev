<?php
/**
 * Class SScan_Reflecation
 * @todo ��namespace֧��
 */
class SScan_Reflecation {
	const RULE_EXTENDS      = 'extends',        // array �������ƣ���implements�ǻ�Ĺ�ϵ
		RULE_IMPLEMENTS     = 'implements',     // array �ӿ����ƣ���extends�ǻ�Ĺ�ϵ
		RULE_CLASSES        = 'classes',        // array �������ƣ���������������Ĺ�ϵ
		RULE_CLASS_PREFIX   = 'class_prefix',   // string ����ǳ��
		RULE_METHODS        = 'methods';        // array ����������

	private static $_filter_class_postfix = array('abstract', 'base');

	/**
	 * ��������ļ���ȷ����������ȡ���пɵ��õķ����������ö�̬��д��
	 *
	 * @param string $class
	 * @param array  $rule
	 * @return array|bool
	 */
	public static function classes($class, array $rule=array()) {
		$class  = substr($class, 0, strpos($class, '.'));
		try {
			//TODO �಻����|�����д���...
			$class = isset($rule[self::RULE_CLASS_PREFIX]) && $rule[self::RULE_CLASS_PREFIX] ? $rule[self::RULE_CLASS_PREFIX].'_'.$class : $class;
			$reflection = new ReflectionClass($class);

			if (!self::is_ok_class($reflection, $rule)) {
				return false;
			}

			$comment = $reflection->getDocComment();
			//$methods = isset($rule[self::RULE_METHODS]) && $rule[self::RULE_METHODS] ? self::get_mectod_name($reflection, $rule) : array();
			return array(
				$class=>array(
					'name'	    => self::get_name($comment, $class),
					'methods'   => self::get_mectod_name($reflection, $rule)
				)
			);
		} catch (Exception $e) {
			//TODO log
		}
		return false;
	}

	/**
	 * ȷ�ϵ�ǰ�����Ƿ���������
	 * @param ReflectionClass $class
	 * @param array $rule
	 * @return bool
	 */
	public static function is_ok_class(ReflectionClass $class, array $rule) {
		$class_name = $class->name;

		// �����Ĺ�������
		$filter     = isset($rule[self::RULE_CLASSES]) ? (array)$rule[self::RULE_CLASSES] : array();
		$is_ok      = true;
		foreach (self::$_filter_class_postfix as $fc) {
			if (substr($class_name, -strlen($fc)) === $fc) {
				return false;
			}
		}
		if ($filter) {
			$is_ok = false;
			foreach ($filter as $r) {
				if (preg_match($r, $class_name)) {
					$is_ok = true;
					break;
				}
			}
			if (!$is_ok) {
				return false;
			}
		}

		// �̳й�ϵ�ͽӿ�ʵ�ֹ�ϵ
		$extends    = isset($rule[self::RULE_EXTENDS]) ? (array)$rule[self::RULE_EXTENDS] : array();
		if ($extends) {
			foreach ($extends as $c) {
				if ($class->isSubclassOf($c)) {
					return true;
				}
			}
		}
		$implements = isset($rule[self::RULE_IMPLEMENTS]) ? (array)$rule[self::RULE_IMPLEMENTS] : array();
		if ($implements) {
			foreach ($implements as $if) {
				if ($class->implementsInterface($if)) {
					return true;
				}
			}
		}

		return $is_ok;
	}

	public static function is_ok_method(ReflectionMethod $method, array $rule) {
		$filter = isset($rule[self::RULE_METHODS]) ? (array)$rule[self::RULE_METHODS] : array();
		if (!$filter) {
			return true;
		}

		foreach ($filter as $r) {
			if (preg_match($r, $method->name)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * ������������ѯ��������rule������public������
	 *
	 * @param ReflectionClass $reflection
	 * @param array $rule
	 * @return array
	 */
	public static function get_mectod_name(ReflectionClass $reflection, array $rule) {
		$methods	= $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
		$results	= array();
		if (is_array($methods)) {
			/** @var ReflectionMethod $m */
			foreach ($methods as $m) {
				if (self::is_ok_method($m, $rule)) {
					$results[$m->name]= array(
						'name'	=> self::get_name($m->getDocComment(), $m->name),
					);
				}
			}
		}
		return $results;
	}

	/**
	 *
	 * @param string $comment
	 * @param string $default
	 * @return string
	 */
	public static function get_name($comment, $default='') {
		$matche		= array();
		$name		= '';
		if (preg_match('/\@name\s+(.+)/i', $comment, $matche)) {
			$name = strtolower(trim($matche[1]));
		}
		return $name ? $name : $default;
	}

	/**
	 *
	 * @param string $comment
	 * @param string $default
	 * @return string
	 */
	public static function get_response_format($comment, $default='') {
		$match		= array();
		$showformat	= '';
		if (preg_match('/\@property\s+showformat\s+(.+)/i', $comment, $match)) {
			$showformat = strtolower(trim($match[1]));
		}
		return $showformat ? $showformat : $default;
	}
}