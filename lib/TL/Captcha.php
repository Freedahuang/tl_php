<?php
/**
 * 验证码处理类
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */

/**
 * 生成 以及验证 验证码等
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_Captcha
{
    /**
     * 验证码的宽度
     *
     * @var number
     */
    public $width = 64;

    /**
     * 验证码的高度
     *
     * @var number
     */
    public $height = 32;

    /**
     * 引入的存储器 用来存储验证码 默认 session
     *
     * @var obj
     */
    private $_backend;

    public function __construct($width=60, $height=22)
    {
        $this->width = $width;
        $this->height = $height;
        $this->_backend = new TL_Session('captcha');
    }

    /**
     * 生成验证码图片 normal 普通验证 math 算术验证 可以2种组合着用
     *
     */
    public function generate($type = 'normal')
    {
        switch ($type) {
            case 'math':
                $this->output($this->math());
                break;
            default:
                $this->output($this->normal());
                break;
        }
    }

    /**
     * 生成算术验证码图片 并存入session 便于验证
     *
     * @return string
     */
    public function math()
    {
        $this->width    = 82;
        $math1          = mt_rand(1, 99);
        $math2          = mt_rand(1, 99);

        $max            = $math1 > $math2 ? $math1 : $math2;
        $min            = $math1 > $math2 ? $math2 : $math1;
        if ($max == $min) {
            $max++;
        }

        $math1          = $max;
        $math2          = $min;
        
        // 算术方法 +-x
        $expr = mt_rand(0, 1);
        
        switch ($expr) {
            case 0:
                $string = $math1.'+'.$math2;
                $result = $math1 + $math2;
                break;
            case 1:
                $string = $math1.'-'.$math2;
                $result = $math1 - $math2;
                break;
            default:
                $string = $math1.'x'.$math2;
                $result = $math1 * $math2;
                break;
        }

        $this->_backend->setValue('code', $result);
        return $string;
    }

    /**
     * 生成普通验证码图片 并存入session 便于验证
     *
     * @return string
     */
    public function normal()
    {
        // 验证码的字母组合 去除混淆字符 如英文I 和数字 1
        $source     = 'ABCDEFGHIJKLMNPQRSTUVWXYZ2345679';

        // 验证码的位数 长度
        $length     = 4;

        $strlen     = strlen($source);
        $get_code   = '';

        for($i = 0; $i < $length; $i++ ) {
            $str_rand = mt_rand(0, $strlen - 1);
            $get_code .= $source{$str_rand};
        }

        $this->_backend->setValue('code', strtolower($get_code));
        return $get_code;
    }

    /**
     * 输出验证码图片
     *
     * @param string $string
     * @return resource
     */
    public function output($string)
    {
        $img = imagecreate($this->width, $this->height); 

        //图片底色,  ImageColorAllocate第1次定义颜色PHP就认为是底色了
        imagecolorallocate($img, 255, 255, 255); 

        //下面该生成雪花背景了,  其实就是在图片上生成一些符号
        $spam = "!*^$`";

        for ($i = 1; $i <= 3; $i++) {
            //在图片背景中添加“!*^$`”，使它杂乱无章, 5颜6色"
            for ($j = 0; $j < strlen($spam); $j++) {
                $font_color = imagecolorallocate($img, 
                    mt_rand(120, 255), 
                    mt_rand(120, 255), 
                    mt_rand(120, 255)
                    );
                imagestring($img, 1, 
                    mt_rand(1, $this->width), 
                    mt_rand(1, $this->height), 
                    $spam[$j], $font_color  
                    );
            }

            //在图片背景中添加杂曲线条 随机颜色
            $line_color = imagecolorallocate($img, 
                mt_rand(150, 255), 
                mt_rand(150, 255), 
                mt_rand(150, 255)
                );
            imageellipse( $img,
                mt_rand(1 - ($this->width / 2), $this->width + ($this->width / 2)),
                mt_rand(1 - ($this->height / 2), $this->height + ($this->height / 2)),
                mt_rand($this->width / 2, 2 * $this->width),
                mt_rand($this->height / 2, 2 * $this->height),
                $line_color
                );
        }

        //将随机字符串嵌入图片中，文字位置水平6px随机偏移 垂值5px，颜色随机
        $len = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $font_color = imagecolorallocate($img, 
                mt_rand(0, 150), 
                mt_rand(0, 150), 
                mt_rand(0, 150)
                );
            $size = rand(3, 5);
            imagestring($img, $size, 
                $size + $i * $this->width / $len + mt_rand(1, $size-1), 
                mt_rand(1, ($this->height - $size)/2-1),  
                $string[$i], 
                $font_color
                );
        }

        //最后定义需要的黑色 边框 让图片看起来更好看
        //$border = imagecolorallocate($img, 101, 195, 228);    
        $border = imagecolorallocate($img, 125, 125, 125);    

        //先成一黑色的矩形把图片包围
        imagerectangle($img, 0, 0, $this->width - 1, $this->height - 1, $border); 

        TL_FSO::headerNoCache();
        header("Content-type: image/png");    
        imagepng($img);                   
        imagedestroy($img);
        exit;
    }

    /**
     * 验证验证码
     *
     * @return boolean
     */
    public function auth($code)
    {
        if ($code && strtolower($code) == $this->_backend->getValue('code')) {
            //$this->_backend->setValue('code', '');
            return true;
        }

        return false;
    }
    
    public function del()
    {
        $this->_backend->setValue('code', '');
    }
    
    public function getCode()
    {
        return $this->_backend->getValue('code');
    }
}

