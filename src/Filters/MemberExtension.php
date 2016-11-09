<?php
/**
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/slimphp/Twig-View
 * @copyright Copyright (c) 2011-2015 Josh Lockhart
 * @license   https://github.com/slimphp/Twig-View/blob/master/LICENSE.md (MIT License)
 */

class MemberExtension extends \Twig_Extension
{
	public function __construct($ci)
    {
        $this->ci = $ci;
    }
    public function memberFilter($number)
    {
		if(is_numeric($number)) {
			 return 'jc'.sprintf("%06d", $number);
		} else {
			return $number;
		}
    }
    public function cartypeFilter($number)
    {
		if(is_numeric($number)) {
			if($cartype=$this->ci->db->table('cartype')->where('id',$number)->first()) {
				return $cartype->name;
			} else {
				return '未填写';
			}
		} else {
			return $number;
		}
    }
    public function getName()
    {
        return 'Member_Extension';
    }
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('member', array($this, 'memberFilter')),
            new \Twig_SimpleFilter('cartype', array($this, 'cartypeFilter')),
        );
    }
}
