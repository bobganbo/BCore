<?php
/**
 * 分页类
 * Created by PhpStorm.
 * User: ganbo
 * Date: 2015/10/18
 * Time: 22:48
 * Desc: 分页导航条用的bootstrap的样式，因此要使用本分页组件，必须引入bootstrap样式
 * -----demo-----
 * $page = $this->I('page', 1);
 * $page_size = PAGESIZE;
 * $count=100;//总的记录数
 * $obj_page=new LibPage($count, $page, $page_size);
 * $page_html = $obj_page->html();
 * $this->assign('pager',$page_html);
 * $this->display('demo.tpl');
 * -----demo-----
 */
class LibPage{
    // 起始行数
    public $firstRow	;
    // 列表每页显示行数
    public $listRows	;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页栏每页显示的页数
    protected $rollPage = 3;
    // 分页显示定制
    protected $config  =	array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'首页','last'=>'末页','theme'=>'%first%  %upPage%  %linkPage%  %downPage%  %end% ');

    public function __construct($count,$page, $page_size,$parameter='')
    {
        $this->totalRows = $count;
        $this->parameter = $parameter;
        $this->listRows = $page_size;
        $this->totalPages = ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages  = ceil($this->totalPages/$this->rollPage);
        $this->nowPage = $page;
        if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }

    public function setConfig($name,$value)
    {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    public function html()
    {
        if(0 == $this->totalRows) return '';
        $p = 'page';
        $nowCoolPage      = ceil($this->nowPage/$this->rollPage);

        $url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
        $parse = parse_url($url);
        $goParamsString = '';
        if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$p]);
            $url   =  $parse['path'].'?'.http_build_query($params);
            //下面的几行是为了让有条件地搜索结果在点击去到某一页的时候能正确地带上相应的参数
            foreach ($params as $key => $value) {
                $goParamsString =  $goParamsString."<input type='hidden' name='".$key."' value='".$value."'>";
            }
        }
        //上下翻页字符串
        $upRow   = $this->nowPage-1;
        $downRow = $this->nowPage+1;
        if ($upRow>0) {
            $upPage="<li><a href='".$url."&".$p."=$upRow'>".$this->config['prev']."</a></li>";
            $mobUpPage="<a href='".$url."&".$p."=$upRow' class='btn btn-outline btn-primary'>
                            <i class='fa fa-backward'></i>
                        </a>";
        } else {
            $upPage="";
            $mobUpPage="";
        }

        if ($downRow <= $this->totalPages) {
            $downPage="<li><a href='".$url."&".$p."=$downRow'>".$this->config['next']."</a></li>";
            $mobDownPage="<a href='".$url."&".$p."=$downRow' class='btn btn-outline btn-primary'>
                            <i class='fa fa-forward'></i>
                        </a>";
        } else {
            $downPage="";
            $mobDownPage="";
        }

        if($nowCoolPage == 1) {
            $theFirst = "";
            $prePage = "";
            $pageto = "";
        } else {
            $preRow =  $this->nowPage-$this->rollPage;
            // $prePage = "<li><a href='".$url."&".$p."=$preRow' >上".$this->rollPage."页</a></li>";
            $theFirst = "<li><a href='".$url."&".$p."=1' >".$this->config['first']."</a></li>";

        }
        if($nowCoolPage == $this->coolPages) {
            $nextPage = "";
            $theEnd="";
        } else {
            $nextRow = $this->nowPage+$this->rollPage;
            $theEndRow = $this->totalPages;
            // $nextPage = "<li><a href='".$url."&".$p."=$nextRow' >下".$this->rollPage."页</a></li>";
            $theEnd = "<li><a href='".$url."&".$p."=$theEndRow' >".$this->config['last']."</a></li>";
        }
        // 1 2 3 4 5 6 7
        $linkPage = "";
        $start = $this->nowPage - $this->rollPage;//这里决定当前页的左右两边出现多少个数字
        $end = $this->nowPage + $this->rollPage;

        if($end > $this->totalPages) {
            $end = $this->totalPages;
        }
        if($start<1) {
            $start = 1;
        }
        for($page=$start;$page<=$end;$page++) {
            if($page!=$this->nowPage) {
                if($page<=$this->totalPages) {
                    $linkPage .= "<li><a href='".$url."&".$p."=$page'>&nbsp;".$page."&nbsp;</a></li>";
                } else {
                    break;
                }
            } else {
                if($this->totalPages != 1) {
                    $linkPage .= "<li class='active'><a href='".$url."&".$p."=$page'>&nbsp;".$page."&nbsp;</a></li>";
                }
            }
        }
        //根据总页数决定是否显示跳转到某页的输入框
        if($this->totalPages > ($this->rollPage + 1)) {
            $pageto = "<li class='page-to'><form action='index.php' method='get'><input name='page' class='sq-input' type='text' value='' min='1'><button class='sq-btn' type='submit'>Go</button>".$goParamsString."</form></li>";
        } else {
            $pageto = "";
        }
        $pageStr	 =	 str_replace(
            array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%linkPage%','%end%'),
            array($this->config['header'],$this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst,$linkPage,$theEnd),$this->config['theme']);
        //组合新的分页样式
        $pageStr = "<nav class='page-pc'>
            <ul class='pagination'>{$pageStr}
            ".$pageto."</ul><div class='bs-page-status' style='display: block'>
                <span>".$this->nowPage."/".$this->totalPages." 页</span><span>".$this->totalRows." 条记录</span></nav>
            <div class='pager-phone text-center'>
                <nav class='form-inline'>
                    <div class='form-group'>
                        ".$mobUpPage."
                        <input type='text' class='form-control text-center' value='".$this->nowPage."'>
                        <span>/".$this->totalPages."</span>
                        ".$mobDownPage."
                    </div>
                </nav>
            </div>
            ";
        return $pageStr;
    }
}
