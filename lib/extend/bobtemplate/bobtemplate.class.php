<?php
/**
 * @name xxx
 * Created by PhpStorm.
 * User: bo
 * Date: 2016/7/18
 * Time: 15:03
 */
class bobtemplate{
    public $view_dir;
    public $compile_dir;
    public $_options = array();
    public $left_delimiter  = '{';
    public $right_delimiter = '}';
    public $tpl_extension='tpl';//模板扩展名称,建议编码的时候写上
    public $plugins_dir=array();//插件目录
    protected static $_instance;

    public  function __construct(){

    }
    /**
     * 获取视图文件的路径
     */
    protected function get_view_file($file_name){
        return $this->view_dir.DIRECTORY_SEPARATOR.$file_name.(strpos($file_name, '.'.$this->tpl_extension) ? '' : ".{$this->tpl_extension}");
    }

    /**
     * 获取编译文件的路径
     * @param $file_name
     * @return string
     */
    protected function get_compile_file($file_name){
        return $this->compile_dir .DIRECTORY_SEPARATOR. md5($file_name) . '.cache.php';//内容是否有变,有变化会进行重新编译
    }
    /**
     * 缓存重写分析,判断下是否需要重新编译,编译文件的时间戳大于等于视图文件的时间戳则不需要重新编译
     */
    public function is_compile($view_file, $compile_file){
        return (is_file($view_file) && is_file($compile_file) && (filemtime($compile_file) >= filemtime($view_file)))?false:true;
    }

    /**
     * 生成视图编译文件
     * @param $compile_file
     * @param $content
     */
    public function compile($compile_file, $content){
        $compile_dir = dirname($compile_file);
        if (!is_dir($compile_dir)) {
            mkdir($compile_dir) or die('权限不够，无法创建目录');
        }else if (!is_writable($compile_dir)) {
            die('文件权限不够，无法写入');
        }
        file_put_contents($compile_file, $content, LOCK_EX) or die('无法生成视图编译文件');
    }

    /**
     * 设置视图变量
     */
    public function assign($key, $value = null) {
        if(!$key) return false;
        if(is_array($key)) {
            foreach ($key as $k => $v) {
                $this->_options[$k] = $v;//数组循环赋值
            }
        } else {
            $this->_options[$key] = $value;
        }
        return true;
    }

    /**
     * 分析视图文件名
     */
    protected function parse_file_name($file_name = null) {
        //return $this->theme ? SYS_THEME_DIR . $file_name : $file_name;
        return $file_name;
    }

    /**
     * 加载视图文件
     */
    protected function load_view_file($view_file) {
        if (!is_file($view_file)) {
            die('视图文件不存在');
        }
        $view_content = file_get_contents($view_file);
        return $this->handle_view_file($view_content);
    }

    /**
     * 编译视图标签
     */
    protected function handle_view_file($view_content) {
        if (!$view_content) return false;
        //正则表达式匹配的模板标签
        $regex_array = array(
            '#'.$this->left_delimiter.'([a-z_0-9]+)\((.*)\)'.$this->right_delimiter.'#Ui',
            '#'.$this->left_delimiter.'([A-Z_]+)'.$this->right_delimiter.'#',
            '#'.$this->left_delimiter.'\$(.+?)'.$this->right_delimiter.'#i',
            '#'.$this->left_delimiter.'\s*include\s+(.+?)\s*'.$this->right_delimiter.'#is',
            '#'.$this->left_delimiter.'\s*template\s+(.+?)\s*'.$this->right_delimiter.'#is',
            '#'.$this->left_delimiter.'php\s+(.+?)'.$this->right_delimiter.'#is',

            '#'.$this->left_delimiter.'\s?if\s+(.+?)\s?'.$this->right_delimiter.'#i',
            '#'.$this->left_delimiter.'\s?else\sif\s+(.+?)\s?'.$this->right_delimiter.'#i',
            '#'.$this->left_delimiter.'\s?else\s?'.$this->right_delimiter.'#i',
            '#'.$this->left_delimiter.'\s?\/if\s?'.$this->right_delimiter.'#i',

            '#'.$this->left_delimiter.'\s?loop\s+\$(.+?)\s+\$(\w+?)\s?'.$this->right_delimiter.'#i',
            '#'.$this->left_delimiter.'\s?loop\s+\$(.+?)\s+\$(\w+?)\s?=>\s?\$(\w+?)\s?'.$this->right_delimiter.'#i',
            '#'.$this->left_delimiter.'\s?\/loop\s?'.$this->right_delimiter.'#i',

            '#'.$this->left_delimiter.'\s?foreach\s+\$(.+?)\s+as\s+\$(\w+?)\s?'.$this->right_delimiter.'#i',
            '#'.$this->left_delimiter.'\s?foreach\s+\$(.+?)\s+as\s+\$(\w+?)\s?=>\s?\$(\w+?)\s?'.$this->right_delimiter.'#i',
            '#'.$this->left_delimiter.'\s?\/foreach\s?'.$this->right_delimiter.'#i',

            '#'.$this->left_delimiter.'\s?php\s?'.$this->right_delimiter.'#i',
            '#'.$this->left_delimiter.'\s?\/php\s?'.$this->right_delimiter.'#i',

            '#\?\>\s*\<\?php\s#s',
        );

        ///替换直接变量输出
        $replace_array = array(
            "<?php echo \\1(\\2); ?>",
            "<?php echo \\1; ?>",
            "<?php echo \$\\1; ?>",
            "<?php include \$this->_include('\\1'); ?>",
            "<?php include \$this->_include('\\1'); ?>",
            "<?php \\1 ?>",

            "<?php if (\\1) { ?>",
            "<?php } else if (\\1) { ?>",
            "<?php } else { ?>",
            "<?php } ?>",

            "<?php if (is_array(\$\\1)) { \$count=count(\$\\1);foreach (\$\\1 as \$\\2) { ?>",
            "<?php if (is_array(\$\\1)) { \$count=count(\$\\1);foreach (\$\\1 as \$\\2=>\$\\3) { ?>",
            "<?php } } ?>",

            "<?php if (is_array(\$\\1)) { \$count=count(\$\\1);foreach (\$\\1 as \$\\2) { ?>",
            "<?php if (is_array(\$\\1)) { \$count=count(\$\\1);foreach (\$\\1 as \$\\2=>\$\\3) { ?>",
            "<?php } } ?>",

            "<?php ",
            " ?>",

            " ",
        );
        //正则匹配，然后输出
        $parse_re=preg_replace($regex_array, $replace_array, $view_content);
        //可以进一步替换
        return $parse_re;
    }


    /**
     * 加载include视图
     */
    protected function _include($file_name) {
        if (!$file_name) {
            return false;
        }
        $file_name = $this->parse_file_name($file_name);
        $view_file = $this->get_view_file($file_name);
        $compile_file = $this->get_compile_file($file_name);
        if ($this->is_compile($view_file, $compile_file)) {
            $view_content = $this->load_view_file($view_file);
            $this->compile($compile_file, $view_content);
        }
        return $compile_file;
    }


    /**
     * @param $content_id 内容id
     */
    protected function _content($content_id){



    }


    /**
     * 显示视图文件
     */
    public function display($file_name = null) {
        $file_name = $this->parse_file_name($file_name);
        $view_file = $this->get_view_file($file_name);
        $compile_file = $this->get_compile_file($file_name);
        if ($this->is_compile($view_file, $compile_file)) {
            $view_content = $this->load_view_file($view_file);
            $this->compile($compile_file, $view_content);//创建编译文件
        }
        if ($this->_options != NULL) {
            extract($this->_options);
        }
        include $compile_file;
    }

    /**
     * 返回文件内容
     * @param string $file_name
     * @return mixed
     */
    public function fetch($file_name=''){
        $file_name = $this->parse_file_name($file_name);
        $view_file = $this->get_view_file($file_name);
        $compile_file = $this->get_compile_file($file_name);
        if ($this->is_compile($view_file, $compile_file)) {
            $view_content = $this->load_view_file($view_file);
            $this->compile($compile_file, $view_content);//创建编译文件
        }
        return $view_content;
    }


    /**
     * 析构函数
     */
    public function __destruct() {
        $this->_options = array();
    }
}