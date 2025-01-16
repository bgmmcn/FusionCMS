<?php

class News_model extends CI_Model
{
    /**
     * Get news entries
     *
     * @param  Int $start
     * @param  Int $limit
     * @return Array
     */
    public function getArticles($start = 0, $limit = 1)
    {
        $builder = $this->db->table('articles');
        $builder->select();
        if ($start !== true) {
            $builder->limit($limit, $start);
        }

        $builder->orderBy('id', 'desc');
        $query = $builder->get();
        $result = $query->getResultArray();

        // Did we have any results?
        if ($result) {
            return $this->template->format($result);
        } else {
            // Instead of showing a blank space, we show a default article
            return [
                [
                    'id'           => 0,
                    'headline'     => '欢迎来到单刷.中国！',
                    'content'      => '服务器同步国服正式服版本，日常修复问题，资料片渐进锁定，当前进度经典旧世，等级上限30级，不支持Win7和老CPU架构，拾取绑定改装备绑定或使用绑定。无冷却、无消耗、无读条、无充能、无公共冷却。',
                    'author_id'    => 0,
                    'timestamp'    => time(),
                    'type'         => 0,
                    'type_content' => null,
                    'comments'     => -1
                ]
            ];
        }
    }

    /**
     * Get the article with the specified id.
     *
     * @param  $id
     * @return bool|array
     */
    public function getArticle($id): bool|array
    {
        $query = $this->db->table('articles')->select()->where('id', $id)->get();

        if ($query->getNumRows() > 0) {
            $result = $query->getResultArray();

            return $result[0];
        } else {
            return false;
        }
    }

    /**
     * Get the tags for the given article id
     *
     * @param  $articleId
     * @return array|bool
     */
    public function getTags($articleId): bool|array
    {
        $query = $this->db->table('tag t, article_tag at')->select('t.name')
                            ->where('at.article_id', $articleId)
                            ->where('at.tag_id = t.id')
                            ->get();

        if ($query->getNumRows() > 0) {
            return $query->getResultArray();
        } else {
            return false;
        }
    }

    /**
     * Count the articles
     *
     * @return int|string
     */
    public function countArticles(): int|string
    {
        return $this->db->table('articles')->countAll();
    }

    /**
     * Check whether an article exists or not
     *
     * @param  Int $id
     * @param  Boolean $comment Check if comments are enabled
     * @return bool
     */
    public function articleExists($id, $comment = false)
    {
        if (!$id) {
            return false;
        }

        $query = $this->db->table('articles')->select('comments')->where('id', $id)->get();

        $result = $query->getResultArray();

        // If comments are enabled
        if ($comment && count($result) && $result[0]['comments'] != -1) {
            return true;
        }
        // If article exists
        elseif (!$comment && count($result)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Create a news article
     *
     * @param $type
     * @param $type_content
     * @param  $comments
     * @param  $headline
     * @param  $content
     * @return bool
     */
    public function create($type, $type_content, $comments, $headline, $content)
    {
        $data = array(
            'type' => $type,
            'type_content' => $type_content,
            'comments' => $comments,
            'timestamp' => time(),
            'author_id' => $this->user->getId(),
            'headline'  => $headline,
            'content'   => $content,
        );

        $this->db->table('articles')->insert($data);

        return true;
    }

    /**
     * Update the article with the given id
     *
     * @param  $id
     * @param $type
     * @param $type_content
     * @param  $comments
     * @param  $headline
     * @param  $content
     * @return bool
     */
    public function update($id, $type, $type_content, $comments, $headline, $content)
    {
        if (!is_numeric($id)) {
            return false;
        }

        $data = array(
            'type'         => $type,
            'type_content' => $type_content,
            'comments'     => $comments,
            'headline'     => $headline,
            'content'      => $content,
        );

        if ($data['comments'] == 0) {
            $query = $this->db->query("SELECT COUNT(*) as `total` FROM comments WHERE article_id=?", array($id));
            $result = $query->getResultArray();

            if ($result[0]['total'] != 0) {
                $data['comments'] = $result[0]['total'];
            }
        }

        $this->db->table('articles')->where('id', $id)->update($data);

        return true;
    }

    /**
     * Delete the article with the given id.
     *
     * @param  $articleId
     * @return bool
     */
    public function delete($articleId)
    {
        if (!is_numeric($articleId)) {
            return false;
        }

        $this->db->table('articles')->where('id', $articleId)->delete();

        return true;
    }
}
