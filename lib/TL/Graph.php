<?php
/**
 * 画图形
 *
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
 
TL_Loader::loadFile('lib_phplot_phplot');

class TL_Graph
{
    private $_obj;
    
    public function __construct($width, $height, $type='lines')
    {
        $this->_obj = new PHPlot($width, $height);
        
        $this->_obj->SetTTFPath(_ROOT_DIR_.'lib'.DIRECTORY_SEPARATOR.'fonts');
        $this->_obj->SetDefaultTTFont('simhei.ttf'); //设置字体，还是支持中文的吧
        $this->_obj->SetPlotType($type); //选择图表类型为线性.可以是bars,lines,linepoints,area,points,pie等。
    }
    
    public function setData($data, $max=100, $title='', $legend=array())
    {
        if (!empty($title)) {
            $this->_obj->SetTitle(TL_Tools::iconv2Html($title)); //设置标题，还是用iconv_arr来解决中文
        }
        $max += 100-intval($max)%100;
        # Select the data array representation and store the data:
        $this->_obj->SetDataType('text-data'); //设置使用的数据类型，在这个里面可以使用多种类型。
        $this->_obj->SetDataValues($data); //把一个数组$data赋给类的一个变量$this->data_values.要开始作图之前调用。
        $this->_obj->SetPlotAreaWorld(0, 0, count($data), $max);  //设置图表边距
        
        # Select an overall image background color and another color under the plot:
        $this->_obj->SetBackgroundColor('#ffffcc'); //设置整个图象的背景颜色。
        $this->_obj->SetDrawPlotAreaBackground(True); //设置节点区域的背景
        $this->_obj->SetPlotBgColor('#ffffff'); //设置使用SetPlotAreaPixels()函数设定的区域的颜色。
        $this->_obj->SetLineWidth(3);  //线条宽度
        # Draw lines on all 4 sides of the plot:
        $this->_obj->SetPlotBorderType('full');  //设置线条类型
        
        # Set a 3 line legend, and position it in the upper left corner:
        if (!empty($legend)) {
            $this->_obj->SetLegend(TL_Tools::iconv2Html($legend)); //显示在一个图列框中的说明        
            //$this->_obj->SetLegendWorld(0.3, $max-0.3); //设定这个文本框位置
            $this->_obj->SetLegendPixels(40, 40);
        }
    }
    
    public function draw()
    {
        $this->_obj->DrawGraph();
    }
}