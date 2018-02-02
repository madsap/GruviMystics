<?php

namespace app\components\widgets;

use app\models\search\User as UserSearch;
use yii\base\Widget;

class ReadersTeaser extends Widget
{
    public $page = 0;
	public $limit = 12;
    public $filter = [];
    public $recent_md5 = "";
    /**
     * @return string
     */
    public function run()
    {
        if(!isset($this->filter['activity']))$this->filter['activity'] = '';
        if(!isset($this->filter['keyword']))$this->filter['keyword'] = '';
        
        $readers = (new UserSearch)->getTeaserReaders($this->page, $this->limit, $this->filter);
        $html = $this->render('readers_teaser.php', [
            'readers' => $readers,
            'filter' => $this->filter,
            'limit' => $this->limit,
            'page' => $this->page
        ]);
        
        $new_md5 = md5($html);
        if($new_md5 == $this->recent_md5)return "";
        $html .= '<input type="hidden" id="readers_teaser_current_md5" value='.$new_md5.'>';
        return $html;
        
	}
}