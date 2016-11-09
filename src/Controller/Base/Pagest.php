<?php
class Pagest {
	protected $ci;
	public $firstRow;
	public $listRows;
	public $allPage;
	public $nextPage;
	public $firstPage;
	public $lastPage;
	public $thisPage;
	public function __construct($ci, $count, $limit) {
		$this->ci = $ci;
		$p=$ci->request->getQueryParam('p');
		$this->thisPage=$p?$p:1;
		$this->firstPage=1;
		$config = $this->ci->db->table('config')->where('id',1)->select('page_limit')->first();
		if($config->page_limit) {
			$limit=$config->page_limit;
		}
		$this->firstRow=($this->thisPage-1)*$limit;
		$this->listRows=$limit;
		$this->allPage=ceil($count / $limit);
		if($this->thisPage>=$this->allPage) {
			$this->nextPage=$this->allPage;
		} else {
			$this->nextPage=$this->thisPage+1;
		}
		if($this->thisPage<=1) {
			$this->lastPage=1;
		} else {
			$this->lastPage=$this->thisPage-1;
		}
	}
	public function Show() { 
		//获取Members列表
		//get
		if($this->allPage<=1) {
			return '';
		}
		$this->ci->request->getUri()->getPath();
		$page='<ul class="cd-pagination">
			<li class="button"><a href="'.$this->ci->request->getUri()->getPath().'?p='.$this->lastPage.'">上一页</a></li>';
			$page.='<li><a href="'.$this->ci->request->getUri()->getPath().'?p='.$this->firstPage.'"';
			if($this->firstPage==$this->thisPage) $page.=' class="current" ';
			$page.='>1</a></li>';
			if($this->thisPage > 1&&$this->thisPage<$this->allPage) {
				if($this->thisPage>2&&$this->thisPage<$this->allPage) {
					$page.='<li><span>...</span></li>';
				}
				$i=$this->thisPage-1;
				if($i>1) {
					$page.='<li><a ';
					$page.='href="'.$this->ci->request->getUri()->getPath().'?p='.$i.'">'.$i.'</a></li>';
				}
				$page.='<li><a href="'.$this->ci->request->getUri()->getPath().'?p='.$this->thisPage.'" class="current" >'.$this->thisPage.'</a></li>';
				$i=$this->thisPage+1;
				if($i<$this->allPage) {
					$page.='<li><a ';
					$page.='href="'.$this->ci->request->getUri()->getPath().'?p='.$i.'">'.$i.'</a></li>';
				}
			}
			if(($this->allPage > 2&&($this->thisPage+1)<$this->allPage)||($this->allPage==$this->thisPage&&$this->thisPage>2)) {
				$page.='<li><span>...</span></li>';
			}
			$page.='<li><a href="'.$this->ci->request->getUri()->getPath().'?p='.$this->allPage.'"';
			if($this->allPage==$this->thisPage&&$this->allPage>1) $page.=' class="current" ';
			$page.='>'.$this->allPage.'</a></li>';
			$page.='<li class="button"><a href="'.$this->ci->request->getUri()->getPath().'?p='.$this->nextPage.'">下一页</a></li></ul>';
		return $page;
	}
}