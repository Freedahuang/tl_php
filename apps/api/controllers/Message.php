<?php

/**
* 控制器类 
*
* 控制器类必须放置在制定目录下 如 /application/controller/
* 视图路径默认 /application/view/controller/action.tpl
* 并命名为 indexController.php 文件名
* IndexController 类名
*
* @author Liang Tan <tanjnr@gmail.com>
* @version 1.0
*/

class Message_Controller extends Ext_Api_Auth_Redis
{           
    /**
     * 评论审核 通过/不通过，回复默认通过
     */
    public function reviewAction()
    {
        $time = TL_Tools::safeInput('time', 'digit');
        $target_uid = TL_Tools::safeInput('uid');
        $to = new User($target_uid);
        if (empty($to->uid)) {
            $this->output(102);
        }
        
        $params = array(
            'comment' => array(
                'Keys' => array(
                    array(
                        'uid'  => ['S' => $target_uid],
                        'time_add' => ['S' => strval($time)]
                    )
                )
            )
        );
        
        $comment = new Comment();
        $res = $comment->get($params);
        if (!$res['ok'] || !isset($res['data']['comment'][$target_uid.$time])) {
            $this->output(103);
        }
        
        $id_article = $res['data']['comment'][$target_uid.$time]['id']['S'];
        $memo = $res['data']['comment'][$target_uid.$time]['content']['S'];
        
        $content = TL_Tools::safeInput('content');
        if (!empty($content)) {
            $content = mb_substr($content, 0, 300);        
            // upd to user record
            $data = array(
                'uid'  => ['S' => $target_uid],
                'time_add' => ['S' => strval($time)],
                'cate' => ['S' => 'article'],
                'id' => ['S' => strval($id_article)],
                'content' => ['S' => $memo],
                'reply' => ['S' => $content]
            );
    
            $comment->fromArray($data);
            $res = $comment->save();
            if (!$res['ok']) {
                $this->output(501);
            }
        }
        
        // add comment index with user record
        $hash_id = 'ca'.$id_article;
        $data = new Data($hash_id);
        $data->hash_id = $hash_id;
        $data->time_add = $time;
        $data->uid = $target_uid;
        $data->memo = time();
        $res = $data->insert();
        
        if ($res) {
            $article = new Article($id_article);
            $message = '审核通过您在#'.$article->name.'#的评论';
            if (!empty($content)) {
                $message .= '，回复如下：'.$content;
            }
            $res = $this->sendMessage($target_uid, 'review', $message);
            $this->result['ok'] = $res ? 1 : 0;
        }
    }
    
    public function commentAction()
    {
        if ($this->user->valid > date('Y-m-d')) {
            $this->result['errmsg'] = '您被多人举报，禁言至'.$this->user->valid.'，如有问题，请与客服联系';
            $this->output(407);
        }
        
        $memo = TL_Tools::safeInput('content');
        if (empty($memo)) {
            $this->output(103);
        }
        $memo = mb_substr($memo, 0, 300);
        
        $id_article = TL_Tools::safeInput('id', 'digit');
        $article = new Article($id_article);
        $target_uid = $article->uid;
        if (empty($target_uid)) {
            $cfg = Ext_Tools::getCfg();
            $target_uid = $cfg['comment_uid'];
        }
        
        $banned = new Banned($target_uid);
        $score = intval($banned->score($this->user->uid));
        if ($score) {
            $this->output(408);
        }
        
        // 判断发送者次数是否超限
        $limit = $this->user_class->countLimit();
        if ($limit >= 5) {
            $this->output(409);
        }

        $time = time();
        
        $comment = new Comment();
        $params = array(
            'uid'  => ['S' => $this->user->uid],
            'time_add' => ['S' => strval($time)],
            'cate' => ['S' => 'article'],
            'id' => ['S' => strval($id_article)],
            'content' => ['S' => $memo]
        );
    
        $comment->fromArray($params);
        $res = $comment->save();
        if (!$res['ok']) {
            $this->output(501);
        }
        
        // add comment index with user record
        $hash_id = 'ca'.$id_article;
        $data = new Data($hash_id);
        $data->hash_id = $hash_id;
        $data->time_add = $time;
        $data->uid = $this->user->uid;
        $res = $data->insert();

        if ($res) {
            $content = '评论了#'.$article->name.'#'.$memo;
            $res = $this->sendMessage($target_uid, 'comment', $content, $time);
        
            $this->result['ok'] = $res ? 1 : 0;
            if ($res) {
                $this->result['data'] = $this->user_class->updLimit();
            }
        }
    }
    
    public function replyAction()
    {
        $content = TL_Tools::safeInput('content');
        if (!empty($content)) {
            $content = mb_substr($content, 0, 300);
        } else {
            $this->output(103);
        }
        
        $target_uid = TL_Tools::safeInput('uid');
        if ($target_uid != 'admin') {
            $to = new User($target_uid);
            if (empty($to->uid)) {
                $this->output(102);
            }
        }
        
        $res = $this->sendMessage($target_uid, 'dialog', $content);
        
        $this->result['ok'] = $res ? 1 : 0;
    }
    
    public function latestAction() 
    {
        $date_upd = TL_Tools::safeInput('article_upd');
        $date_max = date('Y-m-d H:i:s');
        if ($date_upd > $date_max || empty($date_upd)) {
            $date_upd = '';
        }
        $day_on = date('Y-m-d H:i:s');

        $area = $this->user->area;
        $params = compact('date_upd', 'day_on', 'area');
        $article = new Article();
        $explore = $article->getAllByCache($params, 3600);
        $message = new Message($this->user->uid);
        $notify = $message->get(0, 9);

        $this->result = array(
            'ok' => 1,
            'data' => compact('explore', 'notify')
        );        
    }
}