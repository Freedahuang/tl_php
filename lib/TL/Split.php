<?php
/**
 * Split 中文分词处理 用于对文章 title 进行分词 得到 tag
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */

/**
 * 用于对文章 title 进行分词 得到 tag 使用 <b>utf-8</b> 字符集
 *
 * 用法 $str = "wマラトサフィンqrqw北京天安门皛犇焺,經常輸入繁體字【】as-fda經常輸入繁";
 * print_r(TL_Split::getInstance()->getResult($str));exit;
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_Split
{
    private $dict;
    /**
     * 用来存储当前单件模式的实例对象
     *
     * @var obj
     */
    private static $_instance;

    /**
     * 分词的长度 默认最长 4 字词
     *
     * @var number
     */
    public $length = 4;

    /**
     * 常用词 去除的对象
     *
     * @var string
     */
    public $common = '在|的|与|或|就|你|我|他|她|有|了|是|其|能|对|地|　|【|】|。|，|；|：|？|！|……|—|～|〔|〕|《|》|‘|’|“|”|"|.|,|;|:|?|!|…|-|~|(|)|<|>|';

    /**
     * 获取类的实例对象 该类使用了单件模式 返回对象实例
     * 便于全局调用
     *
     * @return obj
     */
    public static function getInstance()
    {
        if(!isset(self::$_instance)) {
            self::$_instance = new TL_Split();
        }
        return self::$_instance;
    }

    /**
     * 私有构造器 防止 ...
     */
    private function __construct()
    {
        $this->dict = new Dict();
    }

    /**
     * 传递要分词的字符串 返回分词成功后的数组
     *
     * @param string $string
     * @return array
     */
    public function getResult($string)
    {
        // 用于获取结果集
        $result = array();
        
        // 初步处理 包括去除标点 以及常用字等等
        foreach (explode('|', $this->common) as $item) {
            $string = str_replace($item, ' ', $string);
        }


        // 获取字符串中的双字节字符子串 包括日文等中亚语言
        preg_match_all("/[^\b\s,\n\r]+/", $string, $substr);
        //var_dump($substr);exit;
        
        /**
         * 对于每个子串 转成单字的数组 便于匹配分词
         * 匹配unicode格式的中文
         * 如果文件是gb2312的，用/[\xa0-\xff]{2}/
         * 如果是utf8的，用/[\xe0-\xef][\x80-\xbf]{2}/
         */
        foreach ($substr[0] as $str) {
            preg_match_all("/[\xe0-\xef][\x80-\xbf]{2}/", $str, $out);
            // 如果单字数组少于2 直接跳过
            if (count($out[0]) < 2) {
                continue;
            }
            // 将分词的结果 push 入结果集
            foreach ($this->split($out[0]) as $item) {
                $result[$item] = 1;
            }
        }

        // 获取字符串中的单字节字符子串 
        preg_match_all("/[a-z0-9A-Z_]+/", $string, $substr);
        foreach ($substr[0] as $str) {
            if (strlen($str) < 4) {
                continue;
            }
            $result[$str] = 1;
        }

        $result = array_keys($result);
        usort($result, function($a, $b){return strlen($a) <= strlen($b);});
        return $result;
    }

    /**
     * 执行具体的分词操作 逆向匹配
     *
     * @param array $words
     * @return array
     */
    private function split($words)
    {
        $result = array();

        // 翻转字串
        $words = array_reverse($words);

        for ($j = 0; $j < count($words); $j++) {
            // 按照 最长的 4 字词 到 2 字词 进行词组取值
            for ($i = $this->length; $i >= 2; $i--) {
                $word = array_slice($words, $j, $i);
                $word = array_reverse($word);
                //var_dump($word);exit;
                if (count($word) < 2) {
                    continue;
                }
                // 与词库进行匹配 成功的则计入结果集
                //$word = implode('', $word);
                $res = $this->inDict($word);
                if ($res) {
                    $result[$res] = 1;
                }
            }//exit;
        }
        //exit;
        //var_dump($result);exit;
        return array_keys($result);
    }

    private function inDict($word)
    {
        //var_dump($word);
        $params = array('name'=>implode('', $word), 'len'=>count($word));
        $res = $this->dict->getItemByParams($params);
        //var_dump($word);exit;
        return isset($res['id']) ? $res['name'] : '';
    }
}
