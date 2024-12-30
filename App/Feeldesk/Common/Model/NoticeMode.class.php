<?php
namespace Common\Model;
use Think\Model;

class NoticeModel extends Model {
    protected $tableName = 'notices';  // 指定数据表名

    /**
     * 获取公告列表
     * @return mixed
     */
    public function getNotices() {
        return $this->select();
    }

    /**
     * 通过 ID 获取公告
     * @param int $id
     * @return mixed
     */
    public function getNoticeById($id) {
        return $this->where(['id' => $id])->find();
    }

    /**
     * 添加公告
     * @param array $data
     * @return mixed
     */
    public function addNotice($data) {
        return $this->add($data);
    }

    /**
     * 更新公告
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateNotice($id, $data) {
        return $this->where(['id' => $id])->save($data);
    }

    /**
     * 删除公告
     * @param int $id
     * @return mixed
     */
    public function deleteNotice($id) {
        return $this->where(['id' => $id])->delete();
    }
}
