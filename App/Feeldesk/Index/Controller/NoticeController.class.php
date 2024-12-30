<?php
namespace Index\Controller;
use Think\Controller;

class NoticeController extends Controller {

    // 公告列表页面
    public function index() {
        $notices = M('Notices')->order('updated_at DESC')->select(); // 添加排序条件
        $this->assign('notices', $notices);
        $this->display();
    }
    

    // 公告详情页面
    public function detail($id) {
        $notice = M('Notices')->where(['id' => $id])->find();
        if (!$notice) {
            $this->error('公告不存在！');
            return;
        }
        $this->assign('notice', $notice);
        $this->display();
    }

    // 创建公告
    public function add() {
        if (IS_POST) {
            $data = [
                'title' => I('post.title'),  // 获取POST请求中的标题
                'content' => I('post.content'),  // 获取POST请求中的内容
                'created_at' => date('Y-m-d H:i:s'),  // 设置当前时间为创建时间
                'updated_at' => date('Y-m-d H:i:s')  // 设置当前时间为更新时间
            ];
            // 插入数据到数据库
            $result = M('Notices')->add($data);
            if ($result) {
                $this->success('公告添加成功', U('/u-home'));  // 添加成功后跳转到公告列表页面
            } else {
                $this->error('公告添加失败');  // 添加失败显示错误信息
            }
        } else {
            $this->display();  // 如果不是POST请求，则显示添加页面
        }
    }

    // 编辑公告
    public function edit($id) {
        if (IS_POST) {
            $data = I('post.');
            $data['updated_at'] = date('Y-m-d H:i:s'); // 添加当前时间为更新时间
            $result = M('Notices')->where(['id' => $id])->save($data);
            if ($result !== false) {
                $this->success('公告更新成功！', U('/u-home'));
            } else {
                $this->error('公告更新失败！');
            }
        } else {
            $notice = M('Notices')->where(['id' => $id])->find();
            $this->assign('notice', $notice);
            $this->display();
        }
    }

    // 删除公告
    public function delete($id) {
        $result = M('Notices')->where(['id' => $id])->delete();
        if ($result) {
            $this->success('公告删除成功！');
        } else {
            $this->error('公告删除失败！');
        }
    }
}
