<?php

class Article_Controller extends Ext_Api
{
    public function indexAction()
    {
        $id = TL_Tools::safeInput('id', 'digit');
        $article = new Article($id);
        if ($article->active && $article->day_on < date('Y-m-d H:i:s')) {
            $data = $article->apiOut();
            $this->result = array(
                'ok' => 1,
                'data' => $data
            );
        }
    }
}
