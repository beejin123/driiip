<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

class Article extends Controller {
	public function index() {
		return $this->fetch();
	}
	public function list() {
		$list = Db::name('article')->where('deleted', 1)->order('art_id', 'desc')->paginate(10);
		$this->assign('list', $list);
		return $this->fetch();
	}
	public function del() {
		$art_id = request()->param('id');
		$res = Db::name('article')->where('art_id', $art_id)->update(array('deleted' => 2));
		if ($res) {
			$this->success('删除成功', 'admin/article/list');
		} else {
			$this->error('删除失败，稍后重试');
		}
	}
	public function add() {
		$param = request()->post();
		$file = request()->file('file');
		$info = $file->move(__DIR__ . '/../../../public/articleImage');
		if ($info) {
			$thumb = 'public/articleImage/' . $info->getSaveName();
			$param['thumb'] = $thumb;
			$param['add_time'] = date('Y-m-d H:i:s', time());
			$res = Db::name('article')->insert($param);
			if ($res) {
				return json_encode(array(
					'code' => 200,
					'message' => '文章添加成功',
				));
			} else {
				return json_encode(array(
					'code' => 401,
					'message' => '服务器错误，请稍后重试',
				));
			}
		} else {
			return json_encode(array(
				'code' => 401,
				'message' => '文件上传失败，原因是：' . $file->getError(),
			));
		}
	}
}