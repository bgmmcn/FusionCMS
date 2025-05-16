<?php

class News_model extends CI_Model
{
    /**
     * 获取新闻条目
     *
     * @param  Int $start 起始位置
     * @param  Int $limit 获取数量
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
                    'headline'     => '',
                    'content'      => '',
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
     * 获取指定ID的文章
     *
     * @param  $id 文章ID
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
     * 获取给定文章ID的标签
     *
     * @param  $articleId 文章ID
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
     * 计算文章数量
     *
     * @return int|string
     */
    public function countArticles(): int|string
    {
        return $this->db->table('articles')->countAll();
    }

    /**
     * 检查文章是否存在
     *
     * @param  Int $id 文章ID
     * @param  Boolean $comment 检查评论是否启用
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
     * 创建新闻文章
     *
     * @param $type 文章类型
     * @param $type_content 文章类型内容
     * @param  $comments 评论状态
     * @param  $headline 文章标题
     * @param  $content 文章内容
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
     * 更新指定ID的文章
     *
     * @param  $id 文章ID
     * @param $type 文章类型
     * @param $type_content 文章类型内容
     * @param  $comments 评论状态
     * @param  $headline 文章标题
     * @param  $content 文章内容
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
     * 删除指定ID的文章
     *
     * @param  $articleId 文章ID
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
