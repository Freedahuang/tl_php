<?php
/**
 * 服务器端用户上传附件处理类 
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */

/**
 * 该类处理用户上传数据，包括 图片缩放、文件上传 等等
 *
 * 用例解析 如 上传color材质图片 只支持单文件上传<br />
 * 步骤：<br />
 *      1、传至 /writable/temp 目录 并写入 session 值<br />
 *      2、在提交的时候把 session 值写入数据库<br />
 *
 * 具体用例 参考 <br />
 * 1、<br />
 * /application/controller/AjaxController.php <br />
 * 中的 uploadAction 用法 以及<br />
 * 2、<br />
 * /application/controller/AttributeController.php 中的 colorSave 用法<br />
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */

define('_IMG_TMP_', 'tmp');
class TL_Attachment
{
    /** 
     * 临时存储器的命名空间 防止冲突
     *
     * @var string
     */
    private $_namespace = NULL;

    /** 
     * 引入的临时存储器对象
     *
     * @var obj
     */
    private $_storage = NULL;

    /** 
     * 附件上传的临时目录
     *
     * @var string
     */
    public $temp_dir = NULL;

    /**
     * 构造器 引入存储器(session) 并设置命名空间 以及临时可写目录等
     *
     * @param string $namespace
     */
    public function __construct($namespace)
    {
        $this->_namespace   = $namespace;
        $this->_storage     = new TL_Session($this->_namespace);
        $this->temp_dir     = TL_FSO::getMultDir(_UPLOAD_DIR_, _IMG_TMP_);
        
    }

    /**
     * 调用同类的 upload 函数 处理图片上传 并根据规格生成缩略图
     *
     * @param string $file 上传的文件
     * @param boolean $thumb 决定是否生成缩略图
     * @return mixed array|false
     */
    public function imageUpload($file, $thumb = true)
    {
        /* 删除以前的图片 */
        self::delPrevious();

        /* 上传文件 */
        $result = $this->upload($file, $this->temp_dir);

        /**
         * 需要缩放的图片的规格
         * $thumb决定是否做缩略图 如果格式为空 则直接跳过
         */
        $file_name = _ROOT_DIR_.'config'.DIRECTORY_SEPARATOR.'json.thumb';
        $thumb_format = json_decode(file_get_contents($file_name));
        $thumb_size   = array();
        if (isset($thumb_format[$this->_namespace])) {
            $thumb_size   = $thumb_format[$this->_namespace];
        }
        if ($thumb && $result && $thumb_size) {
            $result = $this->imageResizedThumb($result, $thumb_size);
        }

        /*
        * 缩略图操作会根据格式 自动生成
        * 如 /temp/color_0_small.jpg
        * 成功会更改原始文件为color_0_.jpg 并改变session值
        *
        */
        if ($result) {
            $result['tmp_name'] = _UPLOAD_URI_._IMG_TMP_.DIRECTORY_SEPARATOR.$result['new_name'].$result['ext_name'];
            $result['tmp_path'] = _UPLOAD_DIR_._IMG_TMP_.DIRECTORY_SEPARATOR.$result['new_name'].$result['ext_name'];
            $this->_storage->setValue('temp', $result);
        }
        return $result;    
    }

    /**
     * 文件上传操作 成功返回包含临时文件名的数组 失败返回false
     *
     * @param string $file 上传的文件
     * @return mixed array|false
     */
    public function upload($file, $dir)
    {
        /* 图片临时存放目录 */
        $size_limit = 524288000;

        /* 判断文件上传途径 以及文件体积 */
        if ($file['error'] || $file['size'] > $size_limit || 
            !is_uploaded_file($file['tmp_name'])) {
            return false;
        }

        /* 放文件至临时目录 */
        $ext_name   = strtolower(TL_FSO::getFileExtension($file['name']));
        $new_name   = md5(time().mt_rand());
        $tmp_name   = $dir.$new_name.$ext_name;
        
        //var_dump($tmp_name);exit;

        if(!move_uploaded_file($file['tmp_name'], $tmp_name)) {
            return false;
        }

        /* 返回上传成功后的变量 */
        return array(
            /* 旧文件名 */
            'old_name'  => $file['name'], 
            /* 新文件名 标识符 */
            'new_name'  => $new_name, 
            /* 扩展名 如 .jpg */
            'ext_name'  => $ext_name,
            /* 上传后 转存的临时文件全名 */
            'tmp_name'  => $tmp_name,
            'size'      => $file['size'],
            );
    }
    
    /**
     * 删除临时存储器中(session)前次上传的文件 及其缩略图
     * 调用同类的 _handlePrevious 函数执行
     */
    public function delPrevious()
    {
        $this->_handlePrevious();
    }

    /**
     * 把临时文件 转入正式目录 并删除临时文件
     * 调用同类的 _handlePrevious 函数执行
     */
    public function moveTempFile($dir = '')
    {
        $res = $this->_namespace.DIRECTORY_SEPARATOR.date('Ym').DIRECTORY_SEPARATOR;
        $default = TL_FSO::getMultDir(_UPLOAD_DIR_, $res);
        $dir = $dir ? $dir:$default;
        $this->_handlePrevious($dir);
        return $res;
    }

    /**
     * 删除临时文件 并决定是否在删除前把它们转移至正式目录
     *
     * @param path $dir dirname|NULL
     */
    private function _handlePrevious($dir = NULL)
    {
        $previous = $this->_storage->getValue('temp');

        if ($previous) {
            $files = TL_FSO::findFile($this->temp_dir, $previous['new_name']);

            foreach ($files as $file) {
                if ($dir != NULL) {
                    $this->moveImageTo($file, $dir);
                }
                TL_FSO::deleteFile($file);
            }
            $this->_storage->setValue('temp', NULL);
        }
    }

    /**
     * 把图像转移至正式目录
     *
     * @param path $file 必须是完整路径的文件名
     * @param path $dir
     */
    private function moveImageTo($file, $dir)
    {
        $filename               = basename($file);
        list($name, $ext)       = explode('.', $filename);
        list($key, $sub_dir)    = explode('_', $name);
        $target                 = $dir.($sub_dir ? $sub_dir.DIRECTORY_SEPARATOR:'').$key.'.'.$ext;

        @copy($file, $target);
    }

    /**
     * 从存储器中(session) 获取前次上传的临时图片
     *
     */
    public function getPrevious()
    {
        $previous = $this->_storage->getValue('temp');

        if ($previous) {
            $filename = _UPLOAD_URI_._IMG_TMP_.DIRECTORY_SEPARATOR.$previous['new_name'].$previous['ext_name'];
            return TL_FSO::isPic($filename) ? $filename : $previous['old_name'];
        }else {
            return '';
        }
    }

    private function getImageParam($image_mime)
    {
        switch ($image_mime) {
            case 'image/jpeg':
                $image_create_from  = function_exists('imagecreatefromjpeg') ? 'imagecreatefromjpeg' : '';
                $image_output       = function_exists('imagejpeg') ? 'imagejpeg' : '';
                break;
            case 'image/gif':
                $image_create_from  = function_exists('imagecreatefromgif') ? 'imagecreatefromgif' : '';
                $image_output       = function_exists('imagegif') ? 'imagegif' : '';
                break;
            case 'image/png':
                $image_create_from  = function_exists('imagecreatefrompng') ? 'imagecreatefrompng' : '';
                $image_output       = function_exists('imagepng') ? 'imagepng' : '';
                break;
            default: 
                return false;
        }
        return array($image_create_from, $image_output);
    }

    /**
     * 对图片做缩略图处理 并返回文件名 失败返回false
     *
     * @param string $file
     * @param array $thumb_size
     * @return string
     */
    public function imageResizedThumb($file, $thumb_size)
    {
        /* 用相应的图像处理函数 创建需要缩放的源文件流 */
        $created_file       = NULL;
        $image_create_from  = NULL;
        $image_output       = NULL;
        $image_resized      = NULL;
        $image_created      = NULL;

        $image              = @getimagesize($file['tmp_name']);
        $image['width']     = $image[0];
        $image['height']    = $image[1];

        if (!$image) {
            return false;
        }

        list($image_create_from, $image_output) = $this->getImageParam($image['mime']);
        
        $image_resiezd  = function_exists('imagecopyresampled') ? 'imagecopyresampled' : 'imagecopyresized';
        $image_created  = function_exists('imagecreatetruecolor') ? 
            'imagecreatetruecolor' : 'imagecreate';

        $created_file   = $image_create_from($file['tmp_name']);

        /* 根据选项规格 用一个源文件流 逐一生成缩略图 */
        foreach ($thumb_size as $thumb => $format) {
            /* 如果目标图像小于规格尺寸 返回失败 */
            if ($image['width'] < $format['width'] || $image['height'] < $format['height']) {
                @imagedestroy($created_file);
                return false;
            }
            
            /* 比例宽度、高度 以及初始比例 */
            $rato_width     = $image['width'] / $format['width'];
            $rato_height    = $image['height'] / $format['height'];
            $rato           = $rato_width;

            /* 临时高宽 */
            $max_width      = $image['width'];
            $max_height     = $image['height'];
            
            /* 判断高宽的比例 取小的那个 用于尽可能大的获取图像区域 */
            if ($rato_width > $rato_height) {
                $rato       = $rato_height;
                $max_width  = $format['width'] * $rato;
            }else if ($rato_width < $rato_height) {
                $rato       = $rato_width;
                $max_height = $format['height'] * $rato;        
            }else {
                /* 规格尺寸乘以最小公共比例以获取最大图像区域 */
                $max_width  = $format['width'] * $rato;
                $max_height = $format['height'] * $rato;        
            }

            $thumb_image = $image_created($format['width'], $format['height']);
            
            /* 
            * 指定源文件流图像区域的开始坐标
            * 即是(原始长度-最大获取区域)/2 = 最大区域至左面的距离 = 坐标X
            * 坐标Y同上
            */
            $pos_x = ($image['width'] - $max_width) / 2;
            $pos_y = ($image['height'] - $max_height) / 2;

            $image_resiezd($thumb_image, $created_file, 0, 0, $pos_x, $pos_y, $format['width'], $format['width'], $max_width, $max_height);

            $thumb_name = $this->temp_dir.$file['new_name'].'_'.$thumb.$file['ext_name'];
            $image_output($thumb_image, $thumb_name);
            imagedestroy($thumb_image);
            
            /* 在当前缩略图的基础上增加水印效果 */
            $this->watermark($thumb_name);
        }
        @imagedestroy($created_file);

        @fclose($file['tmp_name']);
        @unlink($file['tmp_name']);
        @copy($this->temp_dir.$file['new_name'].'_small'.$file['ext_name'], $file['tmp_name']);
        return $file;
    }

    /**
     * 对图片做水印效果 并替换原文件 失败返回false
     *
     * @param string $filename
     * @return mixed
     */
    public function watermark($thumb_name)
    {
        /* 如果有水印文件 则加上 */
        $watermark_file = _UPLOAD_DIR_.'watermark.gif';
        if (file_exists($watermark_file) && file_exists($thumb_name)) {
            $watermark_image = @getimagesize($watermark_file);
            $thumb_image     = @getimagesize($thumb_name);

            $src_w = $watermark_image[0];
            $src_h = $watermark_image[1];
            $dst_w = $thumb_image[0];
            $dst_h = $thumb_image[1];
            
            /* 如果水印图片尺寸大于等于背景图片 */
            if ($src_w > $dst_w || $src_h > $dst_h) {
                return false;
            }

            $waterPos = 5;

            switch($waterPos) {    
                case 1://1为顶端居左    
                    $posX = 0;    
                    $posY = 0;    
                    break;    
                case 2://2为顶端居中    
                    $posX = ($dst_w - $src_w) / 2;    
                    $posY = 0;    
                    break;    
                case 3://3为顶端居右    
                    $posX = $dst_w - $src_w;    
                    $posY = 0;    
                    break;    
                case 4://4为中部居左    
                    $posX = 0;    
                    $posY = ($dst_h - $src_h) / 2;    
                    break;    
                case 5://5为中部居中    
                    $posX = ($dst_w - $src_w) / 2;    
                    $posY = ($dst_h - $src_h) / 2;    
                    break;    
                case 6://6为中部居右    
                    $posX = $dst_w - $src_w;    
                    $posY = ($dst_h - $src_h) / 2;    
                    break;    
                case 7://7为底端居左    
                    $posX = 0;    
                    $posY = $dst_h - $src_h;    
                    break;    
                case 8://8为底端居中    
                    $posX = ($dst_w - $src_w) / 2;    
                    $posY = $dst_h - $src_h;    
                    break;    
                case 9://9为底端居右    
                    $posX = $dst_w - $src_w;    
                    $posY = $dst_h - $src_h;    
                    break;    
                default://随机    
                    $posX = rand(0, ($dst_w - $src_w));    
                    $posY = rand(0, ($dst_h - $src_h));    
                    break;      
            }

            list($image_create_from, $image_output) = $this->getImageParam($watermark_image['mime']);
            $watermark_handle = $image_create_from($watermark_file);

            list($image_create_from, $image_output) = $this->getImageParam($thumb_image['mime']);
            $thumb_handle = $image_create_from($thumb_name);

            @imagealphablending($thumb_handle, true);
            //拷贝水印到目标文件
            imagecopymerge($thumb_handle, $watermark_handle, $posX, $posY, 0, 0, $src_w, $src_h, 40);
            @fclose($thumb_name);
            @unlink($thumb_name);
            $image_output($thumb_handle, $thumb_name);
            imagedestroy($watermark_handle);
            imagedestroy($thumb_handle);
        }
    }
}

