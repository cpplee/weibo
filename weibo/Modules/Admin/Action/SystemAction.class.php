<?php
/**
 * 系统设置控制器
 */
Class SystemAction extends CommonAction {

	/**
	 * 网站设置
	 */
	Public function index () {
		$config = include './weibo/Conf/system.php';

		$this->configWeb =$config;
       p($this->configWeb);
		$this->display();
	}

	/**
	 * 修改网站设置
	 */
	Public function runEdit () {
		$path = './weibo/Conf/system.php';
		$config = include $path;
	//	p($config);
		$config['WEBNAME'] = $_POST['webname'];
		$config['COPY'] = $_POST['copy'];
		$config['REGIS_ON'] = $_POST['regis_on'];
        // p($_POST);

		$data = "<?php\r\nreturn " . var_export($config, true) . ";\r\n?>";

		if (file_put_contents($path, $data)) {
			$this->success('修改成功', U('index'));
		} else {
			$this->error('修改失败， 请修改' . $path . '的写入权限');
		}
	}

	/**
	 * 关键设置视图
	 */
	Public function filter () {
		$config = include './weibo/Conf/system.php';
		p($config);
		$this->filter = implode('|', $config['FILTER']);
		$this->display();
	}

	/**
	 * 执行修改关键词
	 */
	Public function runEditFilter () {
		$path = './weibo/Conf/system.php';
		$config = include $path;
		$config['FILTER'] = explode('|', $_POST['filter']);

		$data = "<?php\r\nreturn " . var_export($config, true) . ";\r\n?>";


		if (file_put_contents($path, $data)) {
			$this->success('修改成功', U(GROUP_NAME.'/System/filter'));
		} else {
			$this->error('修改失败， 请修改' . $path . '的写入权限');
		}
	}
}
?>