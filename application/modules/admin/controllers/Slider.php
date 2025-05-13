<?php

use MX\MX_Controller;

/**
 * 轮播图控制器类
 * @property slider_model $slider_model 轮播图模型类
 */
class Slider extends MX_Controller
{
    public function __construct()
    {
        // 务必加载管理员库！
        $this->load->library('administrator');
        $this->load->model('slider_model');

        parent::__construct();

        require_once('application/libraries/ConfigEditor.php');

        requirePermission("viewSlider");
    }

    public function index()
    {
        // 修改标题
        $this->administrator->setTitle("管理轮播图");

        $slides = $this->cms_model->getSlides();

        if ($slides) {
            foreach ($slides as $key => $value) {
                $slides[$key]['image'] = preg_replace("/{image_path}/", "", $value['image']);

                if (strlen($slides[$key]['image']) > 15) {
                    $slides[$key]['image'] = "..." . mb_substr($slides[$key]['image'], strlen($slides[$key]['image']) - 15, 15);
                }

                if (strlen($value['header']) > 16) {
                    $slides[$key]['header'] = mb_substr($value['header'], 0, 16) . '...';
                }
            }
        }

        // 准备数据
        $data = array(
            'url' => $this->template->page_url,
            'slides' => $slides,
            "轮播图" => $this->config->item('slider'),
            "首页轮播图" => $this->config->item('slider_home'),
            "轮播间隔" => $this->config->item('slider_interval'),
            "轮播样式" => $this->config->item('slider_style')
        );

        // 加载视图
        $output = $this->template->loadPage("slider/slider.tpl", $data);

        // 将视图放入主框架中
        $content = $this->administrator->box('幻灯片', $output);

        // 输出内容
        $this->administrator->view($content, false, "modules/admin/js/slider.js");
    }

    public function create()
    {
        requirePermission("addSlider");

        $data["image"] = $this->input->post("image");
        $data["header"] = $this->input->post("text_header");
        $data["body"] = $this->input->post("text_body");
        $data["footer"] = $this->input->post("text_footer");

        if (empty($data["image"])) {
            die("图片不能为空");
        }

        if(!filter_var($data['image'], FILTER_VALIDATE_URL) && strpos($data['image'], '{image_path}') === false)
            $data['image'] = '{image_path}' . $data['image'];

        $this->slider_model->add($data);

        die("yes");
    }

    public function edit($id = false)
    {
        if (!is_numeric($id) || !$id) {
            die();
        }

        $slide = $this->slider_model->getSlide($id);

        if (!$slide) {
            show_error("找不到ID为 " . $id . " 的幻灯片", 400);

            die();
        }

        // 修改标题
        $this->administrator->setTitle('幻灯片 #' . $slide['id']);

        // 准备数据
        $data = array(
            'url' => $this->template->page_url,
            'slide' => $slide
        );

        // 加载视图
        $output = $this->template->loadPage("slider/edit_slider.tpl", $data);

        // 将视图放入主框架中
        $content = $this->administrator->box('<a href="' . $this->template->page_url . 'admin/slider">管理轮播图</a> &rarr; 幻灯片 #' . $slide['id'], $output);

        // 输出内容
        $this->administrator->view($content, false, "modules/admin/js/slider.js");
    }

    public function new()
    {
        // 修改标题
        $this->administrator->setTitle('添加轮播图');

        // 准备数据
        $data = array(
            'url' => $this->template->page_url,
        );

        // 加载视图
        $output = $this->template->loadPage("slider/add_slider.tpl", $data);

        // 将视图放入主框架中
        $content = $this->administrator->box('添加轮播图', $output);

        // 输出内容
        $this->administrator->view($content, false, "modules/admin/js/slider.js");
    }

    public function move($id = false, $direction = false)
    {
        requirePermission("editSlider");

        if (!$id || !$direction) {
            die();
        } else {
            $order = $this->slider_model->getOrder($id);

            if (!$order) {
                die();
            } else {
                if ($direction == "up") {
                    $target = $this->slider_model->getPreviousOrder($order);
                } else {
                    $target = $this->slider_model->getNextOrder($order);
                }

                if (!$target) {
                    die();
                } else {
                    $this->slider_model->setOrder($id, $target['order']);
                    $this->slider_model->setOrder($target['id'], $order);
                }
            }
        }
    }

    public function saveSettings()
    {
        requirePermission("editSlider");

        $slider = $this->input->post("show_slider");

        if (!is_numeric($this->input->post("slider_interval")) || !$this->input->post("slider_interval")) {
            $slider_interval = 3000;
        } else {
            $slider_interval = (int)$this->input->post("slider_interval") * 1000;
        }

        $slider_style = $this->input->post("slider_style");

        if ($slider == "always") {
            $slider = true;
            $slider_home = false;
        } elseif ($slider == "home") {
            $slider = true;
            $slider_home = true;
        } else {
            $slider = false;
            $slider_home = false;
        }

        $fusionConfig = new ConfigEditor("application/config/fusion.php");

        $fusionConfig->set('slider', $slider);
        $fusionConfig->set('slider_interval', $slider_interval);
        $fusionConfig->set('slider_home', $slider_home);
        $fusionConfig->set('slider_style', $slider_style);

        $fusionConfig->save();

        die("yes");
    }

    public function save($id = false)
    {
        requirePermission("editSlider");

        if (!$id || !is_numeric($id)) {
            die();
        }

        $data["image"] = $this->input->post("image");
        $data["header"] = $this->input->post("text_header");
        $data["body"] = $this->input->post("text_body");
        $data["footer"] = $this->input->post("text_footer");

        if(!filter_var($data['image'], FILTER_VALIDATE_URL) && strpos($data['image'], '{image_path}') === false)
            $data['image'] = '{image_path}' . $data['image'];

        $this->slider_model->edit($id, $data);

        die("yes");
    }

    public function delete($id = false)
    {
        requirePermission("deleteSlider");

        if (!$id || !is_numeric($id)) {
            die();
        }

        $this->slider_model->delete($id);
    }
}
